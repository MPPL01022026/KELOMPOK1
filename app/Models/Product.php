<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Product extends Model
{
    public function getAll(?int $categoryId = null, ?string $keyword = null, ?string $status = null): array
    {
        $sql = "
            SELECT 
                p.*, 
                c.category_name,
                COALESCE(SUM(v.stock), 0) AS total_stock,
                COUNT(v.id) AS variant_count,
                MIN(v.stock) AS min_variant_stock
            FROM products p
            JOIN categories c ON c.id = p.category_id
            LEFT JOIN product_variants v ON v.product_id = p.id
            WHERE 1=1
        ";
        $params = [];

        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        if ($status) {
            $sql .= " AND p.status = ?";
            $params[] = $status;
        }

        if ($keyword) {
            $sql .= " AND (p.product_name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $sql .= " GROUP BY p.id ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.category_name 
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO products (category_id, product_name, description, image, price, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $data['category_id'],
            $data['product_name'],
            $data['description'] ?? null,
            $data['image'] ?? null,
            $data['price'],
            $data['status'] ?? 'active'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [
            'category_id = ?',
            'product_name = ?',
            'description = ?',
            'price = ?',
            'status = ?'
        ];
        $params = [
            $data['category_id'],
            $data['product_name'],
            $data['description'] ?? null,
            $data['price'],
            $data['status'] ?? 'active'
        ];

        if (isset($data['image']) && $data['image'] !== null) {
            $fields[] = 'image = ?';
            $params[] = $data['image'];
        }

        $params[] = $id;

        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        // Periksa apakah varian dari produk ini sudah pernah terjual di sale_items
        $check = $this->db->prepare("
            SELECT COUNT(*) 
            FROM sale_items si 
            JOIN product_variants v ON v.id = si.variant_id 
            WHERE v.product_id = ?
        ");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Mengambil produk aktif beserta varian untuk modul POS
     */
    public function getProductsForPos(?int $categoryId = null, ?string $keyword = null): array
    {
        $sql = "
            SELECT p.id, p.category_id, p.product_name, p.price, p.image, c.category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.status = 'active'
        ";
        $params = [];

        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        if ($keyword) {
            $sql .= " AND (p.product_name LIKE ? OR c.category_name LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $sql .= " ORDER BY p.product_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        // Gabungkan varian untuk masing-masing produk
        $variantStmt = $this->db->prepare("
            SELECT id, product_id, size, color, stock, min_stock
            FROM product_variants
            WHERE product_id = ?
            ORDER BY size ASC, color ASC
        ");

        foreach ($products as &$product) {
            $variantStmt->execute([$product['id']]);
            $product['variants'] = $variantStmt->fetchAll();
        }

        return $products;
    }

    /**
     * Mengambil representasi Digital Twin lengkap beserta status stok
     */
    public function getDigitalTwinData(?int $categoryId = null, ?string $keyword = null, ?string $stockStatus = null): array
    {
        $sql = "
            SELECT 
                p.id, p.product_name, p.price, p.image, p.status,
                c.id AS category_id, c.category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE 1=1
        ";
        $params = [];

        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        if ($keyword) {
            $sql .= " AND (p.product_name LIKE ? OR c.category_name LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        $variantStmt = $this->db->prepare("
            SELECT id, size, color, stock, min_stock
            FROM product_variants
            WHERE product_id = ?
            ORDER BY size ASC, color ASC
        ");

        $result = [];

        foreach ($products as $p) {
            $variantStmt->execute([$p['id']]);
            $variants = $variantStmt->fetchAll();

            $totalStock = 0;
            $hasKritis = false;
            $hasMenipis = false;

            foreach ($variants as &$v) {
                $totalStock += (int)$v['stock'];
                
                // Aturan status: Kritis <= 3 atau <= min_stock, Menipis 4-9, Aman >= 10
                if ($v['stock'] <= 3 || $v['stock'] <= $v['min_stock']) {
                    $v['status_indicator'] = 'kritis';
                    $v['status_label'] = 'Kritis';
                    $v['badge_class'] = 'danger';
                    $hasKritis = true;
                } elseif ($v['stock'] <= 9) {
                    $v['status_indicator'] = 'menipis';
                    $v['status_label'] = 'Menipis';
                    $v['badge_class'] = 'warning';
                    $hasMenipis = true;
                } else {
                    $v['status_indicator'] = 'aman';
                    $v['status_label'] = 'Aman';
                    $v['badge_class'] = 'success';
                }
            }

            // Status produk secara agregat
            if ($hasKritis) {
                $overallStatus = 'kritis';
                $overallBadge = 'danger';
                $overallLabel = 'Perlu Restok Kritis';
            } elseif ($hasMenipis) {
                $overallStatus = 'menipis';
                $overallBadge = 'warning';
                $overallLabel = 'Persediaan Menipis';
            } else {
                $overallStatus = 'aman';
                $overallBadge = 'success';
                $overallLabel = 'Persediaan Aman';
            }

            $p['total_stock'] = $totalStock;
            $p['overall_status'] = $overallStatus;
            $p['overall_badge'] = $overallBadge;
            $p['overall_label'] = $overallLabel;
            $p['variants'] = $variants;

            // Filter status stok jika diminta
            if ($stockStatus && $stockStatus !== 'all') {
                if ($overallStatus !== $stockStatus) {
                    continue;
                }
            }

            $result[] = $p;
        }

        return $result;
    }
}
