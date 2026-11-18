<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'MotoShop') ?></title>
</head>

<body>
    <header>
        <a href="<?= e(url('/')) ?>">MotoShop</a>
        <p>Phụ kiện xe máy</p>
        <?php $currentUser = \App\Middleware\AuthMiddleware::user(); ?>
        <nav aria-label="Điều hướng chính">
            <?php if ($currentUser): ?>
            <a href="<?= e(url('/tai-khoan')) ?>"><?= e($currentUser['full_name']) ?></a>
            <?php if ($currentUser['role'] === 'admin'): ?>
            <a href="<?= e(url('/admin')) ?>">Quản trị</a>
            <?php endif; ?>
            <form action="<?= e(url('/dang-xuat')) ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit">Đăng xuất</button>
            </form>
            <?php else: ?>
            <a href="<?= e(url('/dang-nhap')) ?>">Đăng nhập</a>
            <a href="<?= e(url('/dang-ky')) ?>">Đăng ký</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?= $content ?>
    </main>
    <footer>
        <p>&copy; <?= date('Y') ?> MotoShop</p>
    </footer>
</body>

</html>