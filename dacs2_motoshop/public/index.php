<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
// Hoạt động khi chạy trong thư mục con XAMPP hoặc dùng public làm web root.
$scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
define('BASE_URL', $scriptDirectory === '/' || $scriptDirectory === '.' ? '' : rtrim($scriptDirectory, '/'));

require_once BASE_PATH . '/app/helpers/functions.php';

// Nạp các lớp theo namespace, không cần Composer.
spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Core\\' => BASE_PATH . '/core/',
        'App\\Controllers\\' => BASE_PATH . '/app/controllers/',
        'App\\Models\\' => BASE_PATH . '/app/models/',
        'App\\Middleware\\' => BASE_PATH . '/app/middleware/',
        'App\\Services\\' => BASE_PATH . '/app/services/',
    ];

    if ($class === 'Database') {
        require_once BASE_PATH . '/config/Database.php';
        return;
    }

    foreach ($prefixes as $prefix => $directory) {
        if (strncmp($class, $prefix, strlen($prefix)) === 0) {
            $file = $directory . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) {
                require_once $file;
            }
            return;
        }
    }
});

try {
    session_start([
        'use_strict_mode' => true,
        'use_only_cookies' => true,
        'cookie_httponly' => true,
        'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'cookie_samesite' => 'Lax',
        'cookie_path' => BASE_URL === '' ? '/' : BASE_URL . '/',
    ]);

    $router = new Core\Router();
    require BASE_PATH . '/routes/web.php';
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
} catch (Throwable $exception) {
    error_log('MotoShop: ' . $exception);
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Lỗi hệ thống</h1><p>Vui lòng thử lại sau.</p>';
}
