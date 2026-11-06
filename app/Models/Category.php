<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Category extends Model
{
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) AS product_count 
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            GROUP BY c.id
            ORDER BY c.category_name ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(string $name): int
    {
        $stmt = $this->db->prepare("INSERT INTO categories (category_name, created_at) VALUES (?, NOW())");
        $stmt->execute([trim($name)]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $name): bool
    {
        $stmt = $this->db->prepare("UPDATE categories SET category_name = ? WHERE id = ?");
        return $stmt->execute([trim($name), $id]);
    }

    public function delete(int $id): bool
    {
        // Periksa apakah kategori masih digunakan produk
        $check = $this->db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
