<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = AuthMiddleware::requireRole('admin');
        $this->view('admin/dashboard', ['title' => 'Quản trị', 'user' => $user]);
    }
}