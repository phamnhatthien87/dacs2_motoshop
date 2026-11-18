<h1>Tài khoản của bạn</h1>
<p>Họ tên: <?= e($user['full_name']) ?></p>
<p>Email: <?= e($user['email']) ?></p>
<p>Vai trò: <?= $user['role'] === 'admin' ? 'Quản trị viên' : 'Khách hàng' ?></p>