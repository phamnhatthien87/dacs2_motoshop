<?php
/** @var array $user */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Khu vực quản trị</h1>
    <p>Xin chào, <?= e($user['full_name']) ?>.</p>
    <p><a href="<?= e(url('/admin/danh-muc')) ?>">Quản lý danh mục</a></p>
    <p><a href="<?= e(url('/admin/thuong-hieu')) ?>">Quản lý thương hiệu</a></p>
    <p><a href="<?= e(url('/admin/san-pham')) ?>">Quản lý sản phẩm và phiên bản</a></p>
</body>

</html>