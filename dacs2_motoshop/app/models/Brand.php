<?php
namespace App\Models;

class Brand
{
    public function all(): array
    {
        return \Database::getConnection()->query('SELECT * FROM brands ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = \Database::getConnection()->prepare('SELECT * FROM brands WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): void
    {
        $values = [$data['name'], $data['slug'], $data['description'], $data['status']];
        if ($id === null) {
            $sql = 'INSERT INTO brands (name, slug, description, status) VALUES (?, ?, ?, ?)';
        } else {
            $sql = 'UPDATE brands SET name = ?, slug = ?, description = ?, status = ? WHERE id = ?';
            $values[] = $id;
        }
        \Database::getConnection()->prepare($sql)->execute($values);
    }

    public function setStatus(int $id, string $status): void
    {
        \Database::getConnection()->prepare('UPDATE brands SET status = ? WHERE id = ?')->execute([$status, $id]);
    }
}