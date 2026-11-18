<?php
namespace App\Models;

class Product
{
    public function all(): array
    {
        return \Database::getConnection()->query('SELECT p.*, c.name AS category_name, b.name AS brand_name, (SELECT COUNT(*) FROM product_variants v WHERE v.product_id = p.id) AS variant_count FROM products p JOIN categories c ON c.id = p.category_id LEFT JOIN brands b ON b.id = p.brand_id ORDER BY p.id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = \Database::getConnection()->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): void
    {
        $values = [$data['name'], $data['slug'], $data['category_id'], $data['brand_id'], $data['short_description'], $data['description'], $data['status']];
        if ($id === null) {
            $sql = 'INSERT INTO products (name, slug, category_id, brand_id, short_description, description, status) VALUES (?, ?, ?, ?, ?, ?, ?)';
        } else {
            $sql = 'UPDATE products SET name = ?, slug = ?, category_id = ?, brand_id = ?, short_description = ?, description = ?, status = ? WHERE id = ?';
            $values[] = $id;
        }
        \Database::getConnection()->prepare($sql)->execute($values);
    }

    public function setStatus(int $id, string $status): void
    {
        \Database::getConnection()->prepare('UPDATE products SET status = ? WHERE id = ?')->execute([$status, $id]);
    }
}
