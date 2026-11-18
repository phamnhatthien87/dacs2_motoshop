<?php
namespace App\Models;

class ProductVariant
{
    public function forProduct(int $productId): array
    {
        $stmt = \Database::getConnection()->prepare('SELECT * FROM product_variants WHERE product_id = ? ORDER BY id DESC');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function find(int $productId, int $id): ?array
    {
        $stmt = \Database::getConnection()->prepare('SELECT * FROM product_variants WHERE product_id = ? AND id = ?');
        $stmt->execute([$productId, $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(int $productId, array $data, ?int $id = null): void
    {
        $values = [$data['variant_name'], $data['color'], $data['size'], $data['sku'], $data['price'], $data['stock_quantity'], $data['status'], $productId];
        if ($id === null) {
            $sql = 'INSERT INTO product_variants (variant_name, color, size, sku, price, stock_quantity, status, product_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        } else {
            $sql = 'UPDATE product_variants SET variant_name = ?, color = ?, size = ?, sku = ?, price = ?, stock_quantity = ?, status = ? WHERE product_id = ? AND id = ?';
            $values[] = $id;
        }
        \Database::getConnection()->prepare($sql)->execute($values);
    }

    public function setStatus(int $productId, int $id, string $status): void
    {
        \Database::getConnection()->prepare('UPDATE product_variants SET status = ? WHERE product_id = ? AND id = ?')->execute([$status, $productId, $id]);
    }
}
