<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class ProductVariant extends Model
{
    public function getByProductId(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM product_variants 
            WHERE product_id = ? 
            ORDER BY size ASC, color ASC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT v.*, p.product_name, p.price, p.category_id, c.category_name
            FROM product_variants v
            JOIN products p ON p.id = v.product_id
            JOIN categories c ON c.id = p.category_id
            WHERE v.id = ?
        ");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO product_variants (product_id, size, color, stock, min_stock)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['product_id'],
            trim($data['size']),
            trim($data['color']),
            (int)($data['stock'] ?? 0),
            (int)($data['min_stock'] ?? 3)
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE product_variants 
            SET size = ?, color = ?, min_stock = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            trim($data['size']),
            trim($data['color']),
            (int)$data['min_stock'],
            $id
        ]);
    }

    public function updateStock(int $id, int $newStock): bool
    {
        $stmt = $this->db->prepare("UPDATE product_variants SET stock = ? WHERE id = ?");
        return $stmt->execute([$newStock, $id]);
    }

    public function delete(int $id): bool
    {
        $check = $this->db->prepare("SELECT COUNT(*) FROM sale_items WHERE variant_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM product_variants WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAllFlatWithProduct(): array
    {
        $stmt = $this->db->query("
            SELECT 
                v.id AS variant_id,
                v.product_id,
                v.size,
                v.color,
                v.stock,
                v.min_stock,
                p.product_name,
                p.price,
                c.category_name
            FROM product_variants v
            JOIN products p ON p.id = v.product_id
            JOIN categories c ON c.id = p.category_id
            ORDER BY p.product_name ASC, v.size ASC, v.color ASC
        ");
        return $stmt->fetchAll();
    }

    public function getCriticalVariants(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                v.id, v.size, v.color, v.stock, v.min_stock,
                p.id AS product_id, p.product_name, p.price,
                c.category_name
            FROM product_variants v
            JOIN products p ON p.id = v.product_id
            JOIN categories c ON c.id = p.category_id
            WHERE v.stock <= 3 OR v.stock <= v.min_stock
            ORDER BY v.stock ASC, p.product_name ASC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
