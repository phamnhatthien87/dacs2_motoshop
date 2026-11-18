<?php

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $path = $this->normalize($path);
        // Ví dụ /san-pham/{id}: mỗi tham số khớp một đoạn URL.
        $pattern = preg_replace('/\\\\\{[a-zA-Z_][a-zA-Z0-9_]*\\\\\}/', '([^/]+)', preg_quote($path, '~'));
        $this->routes[] = [
            'method' => $method,
            'pattern' => '~^' . $pattern . '$~D',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        // Tách query string trước khi decode; giữ dấu / đã encode trong tham số.
        $path = explode('?', $uri, 2)[0];
        if (BASE_URL !== '') {
            if ($path !== BASE_URL && strpos($path, BASE_URL . '/') !== 0) {
                $this->error(404, 'Không tìm thấy trang.');
                return;
            }
            $path = substr($path, strlen(BASE_URL));
        }
        $path = $this->normalize($path);
        if ($path === '/index.php') {
            $path = '/';
        }

        $allowed = [];
        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }
            $allowed[] = $route['method'];
            if ($route['method'] !== $method) {
                continue;
            }

            // Mọi POST phải có token; phân quyền nghiệp vụ bổ sung trong middleware/controller.
            if ($method === 'POST' && !csrf_is_valid($_POST['_token'] ?? null)) {
                $this->error(403, 'Phiên làm việc không hợp lệ. Vui lòng tải lại trang.');
                return;
            }

            [$class, $action] = $route['handler'];
            $controller = new $class();
            if (!is_callable([$controller, $action])) {
                throw new \RuntimeException('Controller action không hợp lệ.');
            }
            array_shift($matches);
            call_user_func_array([$controller, $action], array_map('rawurldecode', $matches));
            return;
        }

        if ($allowed !== []) {
            header('Allow: ' . implode(', ', array_unique($allowed)));
            $this->error(405, 'Phương thức yêu cầu không được hỗ trợ.');
            return;
        }
        $this->error(404, 'Không tìm thấy trang.');
    }

    private function normalize(string $path): string
    {
        return '/' . trim($path, '/');
    }

    private function error(int $status, string $message): void
    {
        http_response_code($status);
        View::render('customer/error', ['title' => 'Lỗi ' . $status, 'status' => $status, 'message' => $message]);
    }
}
