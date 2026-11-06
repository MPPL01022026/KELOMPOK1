<?php
namespace App\Models;

use App\Core\Model;
use Exception;
use PDO;

class Sale extends Model
{
    /**
     * Menghasilkan nomor invoice unik berurutan harian: INV-YYYYMMDD-XXXX
     */
    public function generateInvoiceNumber(): string
    {
        $dateStr = date('Ymd');
        $prefix = "INV-{$dateStr}-";

        $stmt = $this->db->prepare("
            SELECT invoice_number 
            FROM sales 
            WHERE invoice_number LIKE ? 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute([$prefix . '%']);
        $lastInvoice = $stmt->fetchColumn();

        if ($lastInvoice) {
            $lastSeq = (int)substr($lastInvoice, -4);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Memproses transaksi penjualan POS dengan Database Transaction
     * Menjamin atomicity, pengecekan stok FOR UPDATE, dan pencatatan stock_movements
     */
    public function processSale(int $userId, float $totalAmount, float $paidAmount, float $changeAmount, array $items): array
    {
        if (empty($items)) {
            throw new Exception("Keranjang belanja masih kosong.");
        }

        if ($paidAmount < $totalAmount) {
            throw new Exception("Jumlah uang pembayaran kurang dari total belanja.");
        }

        // Mulai MySQL Transaction
        $this->db->beginTransaction();

        try {
            $invoiceNumber = $this->generateInvoiceNumber();

            // 1. Simpan header penjualan (Sales)
            $saleStmt = $this->db->prepare("
                INSERT INTO sales (invoice_number, user_id, total_amount, paid_amount, change_amount, payment_method, created_at)
                VALUES (?, ?, ?, ?, ?, 'cash', NOW())
            ");
            $saleStmt->execute([
                $invoiceNumber,
                $userId,
                $totalAmount,
                $paidAmount,
                $changeAmount
            ]);
            $saleId = (int)$this->db->lastInsertId();

            // Statement yang akan digunakan berulang dalam loop
            $lockStmt = $this->db->prepare("
                SELECT v.id, v.stock, v.size, v.color, p.product_name, p.price
                FROM product_variants v
                JOIN products p ON p.id = v.product_id
                WHERE v.id = ?
                FOR UPDATE
            ");

            $updateStockStmt = $this->db->prepare("
                UPDATE product_variants 
                SET stock = ? 
                WHERE id = ?
            ");

            $itemStmt = $this->db->prepare("
                INSERT INTO sale_items (sale_id, variant_id, quantity, unit_price, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");

            $movementStmt = $this->db->prepare("
                INSERT INTO stock_movements 
                (variant_id, user_id, movement_type, quantity, stock_before, stock_after, reference_type, reference_id, created_at)
                VALUES (?, ?, 'sale', ?, ?, ?, 'sale', ?, NOW())
            ");

            $calculatedTotal = 0;

            // 2. Loop setiap item dalam keranjang
            foreach ($items as $item) {
                $variantId = (int)$item['variant_id'];
                $qty = (int)$item['quantity'];

                if ($qty <= 0) {
                    throw new Exception("Jumlah item tidak valid.");
                }

                // Kunci baris varian untuk mencegah race condition (FOR UPDATE)
                $lockStmt->execute([$variantId]);
                $variant = $lockStmt->fetch();

                if (!$variant) {
                    throw new Exception("Varian produk ID #{$variantId} tidak ditemukan.");
                }

                $currentStock = (int)$variant['stock'];

                // TC-06: Membeli produk melebihi stok -> Transaksi ditolak
                if ($qty > $currentStock) {
                    throw new Exception("Stok tidak mencukupi untuk {$variant['product_name']} (Ukuran: {$variant['size']}, Warna: {$variant['color']}). Tersedia: {$currentStock} pcs, Diminta: {$qty} pcs.");
                }

                $newStock = $currentStock - $qty;
                $unitPrice = (float)$variant['price'];
                $subtotal = $unitPrice * $qty;
                $calculatedTotal += $subtotal;

                // Update stok varian
                $updateStockStmt->execute([$newStock, $variantId]);

                // Simpan detail item penjualan
                $itemStmt->execute([
                    $saleId,
                    $variantId,
                    $qty,
                    $unitPrice,
                    $subtotal
                ]);

                // Catat ke log stock_movements
                $movementStmt->execute([
                    $variantId,
                    $userId,
                    -$qty,
                    $currentStock,
                    $newStock,
                    $saleId
                ]);
            }

            // Commit transaksi jika semua valid
            $this->db->commit();

            return [
                'success' => true,
                'sale_id' => $saleId,
                'invoice_number' => $invoiceNumber,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'message' => 'Transaksi berhasil diproses.'
            ];
        } catch (Exception $e) {
            // Rollback jika terjadi kesalahan atau stok tidak cukup
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, u.name AS cashier_name, u.username AS cashier_username
            FROM sales s
            JOIN users u ON u.id = s.user_id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        $sale = $stmt->fetch();

        if ($sale) {
            $itemsStmt = $this->db->prepare("
                SELECT 
                    si.*, 
                    v.size, v.color,
                    p.product_name,
                    c.category_name
                FROM sale_items si
                JOIN product_variants v ON v.id = si.variant_id
                JOIN products p ON p.id = v.product_id
                JOIN categories c ON c.id = p.category_id
                WHERE si.sale_id = ?
            ");
            $itemsStmt->execute([$id]);
            $sale['items'] = $itemsStmt->fetchAll();
        }

        return $sale ?: null;
    }

    public function getSalesHistory(?string $startDate = null, ?string $endDate = null, ?int $userId = null, int $limit = 100): array
    {
        $sql = "
            SELECT 
                s.*, 
                u.name AS cashier_name,
                COUNT(si.id) AS total_items_count,
                COALESCE(SUM(si.quantity), 0) AS total_qty_sold
            FROM sales s
            JOIN users u ON u.id = s.user_id
            LEFT JOIN sale_items si ON si.sale_id = s.id
            WHERE 1=1
        ";
        $params = [];

        if ($startDate) {
            $sql .= " AND DATE(s.created_at) >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND DATE(s.created_at) <= ?";
            $params[] = $endDate;
        }

        if ($userId) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        $sql .= " GROUP BY s.id ORDER BY s.id DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $this->db->prepare($sql);
        // Bind integer parameter untuk LIMIT
        foreach ($params as $idx => $val) {
            $paramType = ($idx === count($params) - 1) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($idx + 1, $val, $paramType);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTodaySummary(): array
    {
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(total_amount), 0) AS today_omzet,
                COUNT(id) AS today_transactions
            FROM sales
            WHERE DATE(created_at) = CURDATE()
        ");
        $today = $stmt->fetch();

        // Ambil juga total produk terjual hari ini
        $qtyStmt = $this->db->query("
            SELECT COALESCE(SUM(si.quantity), 0) AS today_qty
            FROM sale_items si
            JOIN sales s ON s.id = si.sale_id
            WHERE DATE(s.created_at) = CURDATE()
        ");
        $todayQty = $qtyStmt->fetchColumn();

        $today['today_qty'] = (int)$todayQty;
        return $today;
    }

    public function getWeeklySalesChartData(): array
    {
        // 7 hari terakhir
        $stmt = $this->db->query("
            SELECT 
                DATE(created_at) AS sale_date,
                DATE_FORMAT(created_at, '%d %b') AS label_date,
                COALESCE(SUM(total_amount), 0) AS daily_revenue,
                COUNT(id) AS transaction_count
            FROM sales
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ");
        $rows = $stmt->fetchAll();

        // Buat array 7 hari berurutan lengkap
        $chartLabels = [];
        $chartRevenues = [];
        $chartTransactions = [];

        $map = [];
        foreach ($rows as $r) {
            $map[$r['sale_date']] = $r;
        }

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('d M', strtotime("-$i days"));
            $chartLabels[] = $label;
            $chartRevenues[] = (float)($map[$date]['daily_revenue'] ?? 0);
            $chartTransactions[] = (int)($map[$date]['transaction_count'] ?? 0);
        }

        return [
            'labels' => $chartLabels,
            'revenues' => $chartRevenues,
            'transactions' => $chartTransactions
        ];
    }

    public function getTopSellingProducts(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.id,
                p.product_name,
                c.category_name,
                p.price,
                COALESCE(SUM(si.quantity), 0) AS total_sold,
                COALESCE(SUM(si.subtotal), 0) AS total_revenue
            FROM sale_items si
            JOIN product_variants v ON v.id = si.variant_id
            JOIN products p ON p.id = v.product_id
            JOIN categories c ON c.id = p.category_id
            GROUP BY p.id
            ORDER BY total_sold DESC, total_revenue DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
