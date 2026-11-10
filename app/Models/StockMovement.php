<?php
namespace App\Models;

use App\Core\Model;
use Exception;
use PDO;

class StockMovement extends Model
{
    public function getRecentMovements(int $limit = 100, ?int $variantId = null, ?string $movementType = null): array
    {
        $sql = "
            SELECT 
                sm.*,
                v.size, v.color,
                p.product_name,
                c.category_name,
                u.name AS user_name,
                u.username
            FROM stock_movements sm
            JOIN product_variants v ON v.id = sm.variant_id
            JOIN products p ON p.id = v.product_id
            JOIN categories c ON c.id = p.category_id
            JOIN users u ON u.id = sm.user_id
            WHERE 1=1
        ";
        $params = [];

        if ($variantId) {
            $sql .= " AND sm.variant_id = ?";
            $params[] = $variantId;
        }

        if ($movementType) {
            $sql .= " AND sm.movement_type = ?";
            $params[] = $movementType;
        }

        $sql .= " ORDER BY sm.id DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $idx => $val) {
            $type = ($idx === count($params) - 1) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($idx + 1, $val, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Memproses Barang Masuk (Restock)
     * FR-08 A: Menambahkan jumlah stok produk, memperbarui stok dan mencatat ke stock_movements
     */
    public function restock(int $variantId, int $userId, int $addedQty, ?string $notes = null): array
    {
        if ($addedQty <= 0) {
            throw new Exception("Jumlah barang masuk harus lebih dari 0.");
        }

        $this->db->beginTransaction();

        try {
            // Lock variant row
            $stmt = $this->db->prepare("SELECT id, stock, size, color FROM product_variants WHERE id = ? FOR UPDATE");
            $stmt->execute([$variantId]);
            $variant = $stmt->fetch();

            if (!$variant) {
                throw new Exception("Varian produk tidak ditemukan.");
            }

            $stockBefore = (int)$variant['stock'];
            $stockAfter = $stockBefore + $addedQty;

            // Update stok
            $upd = $this->db->prepare("UPDATE product_variants SET stock = ? WHERE id = ?");
            $upd->execute([$stockAfter, $variantId]);

            // Catat movement
            $refType = !empty($notes) ? "restock: " . substr($notes, 0, 40) : "restock";
            $mov = $this->db->prepare("
                INSERT INTO stock_movements 
                (variant_id, user_id, movement_type, quantity, stock_before, stock_after, reference_type, created_at)
                VALUES (?, ?, 'restock', ?, ?, ?, ?, NOW())
            ");
            $mov->execute([
                $variantId,
                $userId,
                $addedQty,
                $stockBefore,
                $stockAfter,
                $refType
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'message' => "Stok berhasil ditambahkan sebanyak {$addedQty} pcs."
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Memproses Penyesuaian Stok Fisik (Stock Adjustment)
     * FR-08 B: Mengubah jumlah stok berdasarkan pemeriksaan fisik
     */
    public function adjustStock(int $variantId, int $userId, int $newPhysicalStock, string $reason): array
    {
        if ($newPhysicalStock < 0) {
            throw new Exception("Jumlah stok fisik tidak boleh negatif.");
        }

        $this->db->beginTransaction();

        try {
            // Lock variant row
            $stmt = $this->db->prepare("SELECT id, stock, size, color FROM product_variants WHERE id = ? FOR UPDATE");
            $stmt->execute([$variantId]);
            $variant = $stmt->fetch();

            if (!$variant) {
                throw new Exception("Varian produk tidak ditemukan.");
            }

            $stockBefore = (int)$variant['stock'];
            $diffQty = $newPhysicalStock - $stockBefore;

            // Update stok fisik
            $upd = $this->db->prepare("UPDATE product_variants SET stock = ? WHERE id = ?");
            $upd->execute([$newPhysicalStock, $variantId]);

            // Catat ke stock_movements
            $refNote = "penyesuaian: " . substr($reason, 0, 40);
            $mov = $this->db->prepare("
                INSERT INTO stock_movements 
                (variant_id, user_id, movement_type, quantity, stock_before, stock_after, reference_type, created_at)
                VALUES (?, ?, 'adjustment', ?, ?, ?, ?, NOW())
            ");
            $mov->execute([
                $variantId,
                $userId,
                $diffQty,
                $stockBefore,
                $newPhysicalStock,
                $refNote
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'stock_before' => $stockBefore,
                'stock_after' => $newPhysicalStock,
                'diff' => $diffQty,
                'message' => "Stok berhasil disesuaikan menjadi {$newPhysicalStock} pcs."
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
