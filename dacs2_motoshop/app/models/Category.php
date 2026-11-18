<?php

namespace App\Models;

class Category
{
    public function all(): array
    {
        return \Database::getConnection()->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = \Database::getConnection()->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): void
    {
        $values = [$data['name'], $data['slug'], $data['description'], $data['status']];
        if ($id === null) {
            $sql = 'INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, ?)';
        } else {
            $sql = 'UPDATE categories SET name = ?, slug = ?, description = ?, status = ? WHERE id = ?';
            $values[] = $id;
        }
        \Database::getConnection()->prepare($sql)->execute($values);
    }

    public function setStatus(int $id, string $status): void
    {
        \Database::getConnection()->prepare('UPDATE categories SET status = ? WHERE id = ?')->execute([$status, $id]);
    }
}
