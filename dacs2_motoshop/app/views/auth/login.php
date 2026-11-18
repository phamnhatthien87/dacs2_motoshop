<h1>Đăng nhập</h1>
<?php if ($notice): ?><p role="status"><?= e($notice) ?></p><?php endif; ?>
<?php if ($error): ?><p role="alert"><?= e($error) ?></p><?php endif; ?>
<form action="<?= e(url('/dang-nhap')) ?>" method="post">
    <?= csrf_field() ?>
    <p><label for="email">Email</label><br>
        <input id="email" type="email" name="email" autocomplete="username" maxlength="150" value="<?= e($email) ?>"
            required>
    </p>
    <p><label for="password">Mật khẩu</label><br>
        <input id="password" type="password" name="password" autocomplete="current-password" maxlength="72" required>
    </p>
    <button type="submit">Đăng nhập</button>
</form>
<p>Chưa có tài khoản? <a href="<?= e(url('/dang-ky')) ?>">Đăng ký</a></p>