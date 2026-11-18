<?php

namespace App\Controllers\Auth;

use App\Middleware\AuthMiddleware;
use App\Models\User;
use Core\Controller;

class AuthController extends Controller
{
    private function guestOnly(): void
    {
        if (AuthMiddleware::user()) {
            $this->redirect('/tai-khoan');
        }
    }

    private function input(string $key): string
    {
        return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : '';
    }

    public function registerForm(): void
    {
        $this->guestOnly();
        $this->view('auth/register', ['title' => 'Đăng ký', 'errors' => [], 'old' => []]);
    }

    public function register(): void
    {
        $this->guestOnly();
        $name = trim($this->input('full_name'));
        $email = strtolower(trim($this->input('email')));
        $password = $this->input('password');
        $errors = [];
        if ($name === '' || mb_strlen($name, 'UTF-8') > 100) {
            $errors[] = 'Họ tên phải có từ 1 đến 100 ký tự.';
        }
        if (strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ hoặc dài quá 150 ký tự.';
        }
        // Giới hạn 72 byte để tránh bcrypt bỏ qua phần cuối mật khẩu.
        if (strlen($password) < 8 || strlen($password) > 72 || strpos($password, "\0") !== false) {
            $errors[] = 'Mật khẩu cần từ 8 đến 72 byte (ký tự tiếng Việt có thể chiếm nhiều byte).';
        }
        if ($password !== $this->input('password_confirmation')) {
            $errors[] = 'Mật khẩu xác nhận không khớp.';
        }
        if (!$errors) {
            try {
                (new User())->createCustomer($name, $email, $password);
                $_SESSION['auth_notice'] = 'Đăng ký thành công. Bạn có thể đăng nhập.';
                $this->redirect('/dang-nhap', 303);
            } catch (\PDOException $exception) {
                // UNIQUE(email) xử lý cả hai request đăng ký đồng thời.
                if (($exception->errorInfo[1] ?? null) !== 1062) {
                    throw $exception;
                }
                $errors[] = 'Email này đã được sử dụng.';
            }
        }
        http_response_code(422);
        $this->view('auth/register', ['title' => 'Đăng ký', 'errors' => $errors, 'old' => ['full_name' => $name, 'email' => $email]]);
    }

    public function loginForm(): void
    {
        $this->guestOnly();
        $notice = $_SESSION['auth_notice'] ?? null;
        unset($_SESSION['auth_notice']);
        $this->view('auth/login', ['title' => 'Đăng nhập', 'error' => null, 'email' => '', 'notice' => $notice]);
    }

    public function login(): void
    {
        $this->guestOnly();
        $email = strtolower(trim($this->input('email')));
        $password = $this->input('password');
        $user = null;
        if (strlen($email) <= 150 && filter_var($email, FILTER_VALIDATE_EMAIL)
            && strlen($password) >= 8 && strlen($password) <= 72 && strpos($password, "\0") === false) {
            $user = (new User())->findByEmail($email);
        }
        // Hash giả giúp vẫn thực hiện phép kiểm tra mật khẩu khi không có email.
        $hash = $user['password'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        $verified = password_verify($password, $hash);
        if (!$user || !$verified || $user['status'] !== 'active') {
            http_response_code(422);
            $this->view('auth/login', ['title' => 'Đăng nhập', 'error' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.', 'email' => $email, 'notice' => null]);
            return;
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        unset($_SESSION['_csrf_token']);
        $this->redirect($user['role'] === 'admin' ? '/admin' : '/tai-khoan', 303);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 3600, 'path' => $params['path'],
                'domain' => $params['domain'], 'secure' => $params['secure'],
                'httponly' => $params['httponly'], 'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }
        session_destroy();
        $this->redirect('/dang-nhap', 303);
    }

    public function account(): void
    {
        $user = AuthMiddleware::requireLogin();
        $this->view('auth/account', ['title' => 'Tài khoản', 'user' => $user]);
    }
}