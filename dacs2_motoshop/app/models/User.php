<?php

namespace App\Models;

class User
{
    public function findByEmail(string $email): ?array
    {
        $stmt = \Database::getConnection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = \Database::getConnection()->prepare('SELECT id, full_name, email, role, status FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function createCustomer(string $name, string $email, string $password): void
    {
        // Không nhận role/status từ form để tránh tự nâng quyền.
        $stmt = \Database::getConnection()->prepare(
            "INSERT INTO users (full_name, email, password, role, status) VALUES (:name, :email, :password, 'customer', 'active')"
        );
        $stmt->execute(['name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
    }
}
