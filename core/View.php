<?php

namespace Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'customer'): void
    {
        $content = self::capture(self::resolve($view), $data);
        header('Content-Type: text/html; charset=utf-8');
        if ($layout === null) {
            echo $content;
            return;
        }
        // content là HTML của view nội bộ, không lấy từ dữ liệu người dùng.
        $data['content'] = $content;
        echo self::capture(self::resolve('layouts/' . $layout), $data);
    }

    private static function resolve(string $name): string
    {
        if (!preg_match('~^[a-zA-Z0-9_-]+(?:/[a-zA-Z0-9_-]+)*$~D', $name)) {
            throw new \InvalidArgumentException('Tên view không hợp lệ.');
        }
        $file = BASE_PATH . '/app/views/' . $name . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('Không tìm thấy view: ' . $name);
        }
        return $file;
    }

    private static function capture(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            require $file;
            return (string) ob_get_clean();
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }
    }
}
