<h1>Đăng ký tài khoản</h1>
<?php if ($errors): ?>
<ul role="alert">
    <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
</ul>
<?php endif; ?>
<form action="<?= e(url('/dang-ky')) ?>" method="post">
    <?= csrf_field() ?>
    <p><label for="full_name">Họ tên</label><br>
        <input id="full_name" name="full_name" autocomplete="name" maxlength="100"
            value="<?= e($old['full_name'] ?? '') ?>" required>
    </p>
    <p><label for="email">Email</label><br>
        <input id="email" type="email" name="email" autocomplete="email" maxlength="150"
            value="<?= e($old['email'] ?? '') ?>" required>
    </p>
    <p><label for="password">Mật khẩu</label><br>
        <input id="password" type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72"
            required>
    </p>
    <p><label for="password_confirmation">Nhập lại mật khẩu</label><br>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
            minlength="8" maxlength="72" required>
    </p>
    <p>Mật khẩu dài từ 8 đến 72 byte.</p>
    <button type="submit">Đăng ký</button>
</form>
<p>Đã có tài khoản? <a href="<?= e(url('/dang-nhap')) ?>">Đăng nhập</a></p>