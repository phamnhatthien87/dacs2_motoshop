<?php
/** @var string $title */
/** @var array $product */
/** @var array $items */
/** @var string|null $notice */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>phiên bản</title>
</head>

<body>
    <h1>Phiên bản: <?= e($product['name']) ?></h1>
    <?php $base = '/admin/san-pham/' . $product['id'] . '/phien-ban'; ?>
    <p><a href="<?= e(url('/admin/san-pham')) ?>">Về sản phẩm</a> | <a href="<?= e(url($base . '/them')) ?>">Thêm phiên
            bản</a></p>
    <?php if ($notice): ?><p role="status"><?= e($notice) ?></p><?php endif; ?>
    <?php if (!$items): ?><p>Chưa có phiên bản. Hãy thêm ít nhất một phiên bản để nhập giá và tồn kho.</p><?php else: ?>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Phiên bản</th>
                <th>Màu</th>
                <th>Kích thước</th>
                <th>SKU</th>
                <th>Giá (VND)</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php $stt = 1; foreach ($items as $item): ?>
            <tr>
                <td><?= $stt++ ?></td>
                <td><?= e($item['variant_name']) ?></td>
                <td><?= e($item['color']) ?></td>
                <td><?= e($item['size']) ?></td>
                <td><?= e($item['sku']) ?></td>
                <td><?= e(number_format((float) $item['price'], 2, ',', '.')) ?></td>
                <td><?= e($item['stock_quantity']) ?></td>
                <td><?= $item['status'] === 'active' ? 'Đang hiện' : 'Đã ẩn' ?></td>
                <td><a href="<?= e(url($base . '/' . $item['id'] . '/sua')) ?>">Sửa</a>
                    <form method="post" action="<?= e(url($base . '/' . $item['id'] . '/trang-thai')) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status"
                            value="<?= $item['status'] === 'active' ? 'inactive' : 'active' ?>">
                        <button type="submit"><?= $item['status'] === 'active' ? 'Ẩn' : 'Hiện' ?></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>

</html>