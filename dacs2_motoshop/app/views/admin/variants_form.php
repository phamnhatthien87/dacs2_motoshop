<?php
// Giá trị mặc định giúp form thêm mới luôn có dữ liệu và IDE suy luận được biến.
$item = is_array($item ?? null) ? $item : [];
$item += [
    'variant_name' => '', 'color' => '', 'size' => '', 'sku' => '',
    'price' => '', 'stock_quantity' => '0', 'status' => 'active',
];
?>
<h1><?= e($title) ?>: <?= e($product['name']) ?></h1>
<?php $base = '/admin/san-pham/' . $product['id'] . '/phien-ban'; ?>
<?php if ($errors): ?><ul role="alert"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li>
    <?php endforeach; ?></ul><?php endif; ?>
<form method="post" action="<?= e(url($base . (isset($item['id']) ? '/' . $item['id'] . '/sua' : '/them'))) ?>">
    <?= csrf_field() ?>
    <p><label for="variant_name">Tên phiên bản</label><br><input id="variant_name" name="variant_name" maxlength="150"
            value="<?= e($item['variant_name']) ?>" required></p>
    <p><label for="color">Màu sắc</label><br><input id="color" name="color" maxlength="50"
            value="<?= e($item['color']) ?>"></p>
    <p><label for="size">Kích thước</label><br><input id="size" name="size" maxlength="50"
            value="<?= e($item['size']) ?>"></p>
    <p>Để trống màu/kích thước nếu không áp dụng. Mỗi tổ hợp chỉ được tạo một lần trong cùng sản phẩm.</p>
    <p><label for="sku">SKU (mã duy nhất toàn cửa hàng)</label><br><input id="sku" name="sku" maxlength="100"
            value="<?= e($item['sku']) ?>" required></p>
    <p><label for="price">Giá (VND)</label><br><input id="price" name="price" type="number" min="0" max="9999999999.99"
            step="0.01" value="<?= e($item['price']) ?>" required></p>
    <p><label for="stock_quantity">Tồn kho</label><br><input id="stock_quantity" name="stock_quantity" type="number"
            min="0" max="4294967295" step="1" value="<?= e($item['stock_quantity']) ?>" required></p>
    <p><label for="status">Trạng thái</label><select id="status" name="status">
            <option value="active" <?= $item['status'] === 'active' ? 'selected' : '' ?>>Hiện</option>
            <option value="inactive" <?= $item['status'] === 'inactive' ? 'selected' : '' ?>>Ẩn</option>
        </select></p>
    <button type="submit">Lưu phiên bản</button> <a href="<?= e(url($base)) ?>">Hủy</a>
</form>
