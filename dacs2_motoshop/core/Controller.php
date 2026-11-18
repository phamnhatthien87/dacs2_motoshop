<?php

namespace Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'customer'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path = '/', int $status = 302): void
    {
        header('Location: ' . url($path), true, $status);
        exit;
    }
}
