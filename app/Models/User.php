<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, name, username, email, role, is_active, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->query("SELECT id, name, username, email, role, is_active, created_at FROM users ORDER BY role ASC, name ASC");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, username, email, password, role, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $data['name'],
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'] ?? 'kasir',
            $data['is_active'] ?? 1
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare("
                UPDATE users SET name = ?, username = ?, email = ?, password = ?, role = ?, is_active = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['name'],
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['role'],
                $data['is_active'],
                $id
            ]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE users SET name = ?, username = ?, email = ?, role = ?, is_active = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['name'],
                $data['username'],
                $data['email'],
                $data['role'],
                $data['is_active'],
                $id
            ]);
        }
    }

    public function toggleStatus(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
