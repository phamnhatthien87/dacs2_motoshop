<?php

namespace App\Middleware;

use App\Models\User;
use Core\View;

class AuthMiddleware
{
    public static function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        // Đọc quyền và trạng thái từ database mỗi request, không tin role trong session.
        $user = (new User())->findById((int) $_SESSION['user_id']);
        if (!$user || $user['status'] !== 'active') {
            unset($_SESSION['user_id']);
            return null;
        }
        return $user;
    }

    public static function requireLogin(): array
    {
        $user = self::user();
        if (!$user) {
            header('Location: ' . url('/dang-nhap'));
            exit;
        }
        return $user;
    }

    public static function requireRole(string $role): array
    {
        $user = self::requireLogin();
        if ($user['role'] !== $role) {
            http_response_code(403);
            View::render('customer/error', ['title' => 'Không có quyền truy cập', 'status' => 403, 'message' => 'Bạn không có quyền truy cập trang này.']);
            exit;
        }
        return $user;
    }
}
