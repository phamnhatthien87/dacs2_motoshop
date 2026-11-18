<?php

use App\Controllers\Customer\HomeController;
use App\Controllers\Auth\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\BrandController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\ProductVariantController;

/** @var \Core\Router $router */
$router->get('/', [HomeController::class, 'index']);
$router->get('/dang-ky', [AuthController::class, 'registerForm']);
$router->post('/dang-ky', [AuthController::class, 'register']);
$router->get('/dang-nhap', [AuthController::class, 'loginForm']);
$router->post('/dang-nhap', [AuthController::class, 'login']);
$router->post('/dang-xuat', [AuthController::class, 'logout']);
$router->get('/tai-khoan', [AuthController::class, 'account']);
$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/danh-muc', [CategoryController::class, 'index']);
$router->get('/admin/danh-muc/them', [CategoryController::class, 'create']);
$router->post('/admin/danh-muc/them', [CategoryController::class, 'store']);
$router->get('/admin/danh-muc/{id}/sua', [CategoryController::class, 'edit']);
$router->post('/admin/danh-muc/{id}/sua', [CategoryController::class, 'update']);
$router->post('/admin/danh-muc/{id}/trang-thai', [CategoryController::class, 'status']);

// Khai báo từng route khi triển khai chức năng tương ứng.
$router->get('/admin/thuong-hieu', [BrandController::class, 'index']);
$router->get('/admin/thuong-hieu/them', [BrandController::class, 'create']);
$router->post('/admin/thuong-hieu/them', [BrandController::class, 'store']);
$router->get('/admin/thuong-hieu/{id}/sua', [BrandController::class, 'edit']);
$router->post('/admin/thuong-hieu/{id}/sua', [BrandController::class, 'update']);
$router->post('/admin/thuong-hieu/{id}/trang-thai', [BrandController::class, 'status']);

$router->get('/admin/san-pham', [ProductController::class, 'index']);
$router->get('/admin/san-pham/them', [ProductController::class, 'create']);
$router->post('/admin/san-pham/them', [ProductController::class, 'store']);
$router->get('/admin/san-pham/{id}/sua', [ProductController::class, 'edit']);
$router->post('/admin/san-pham/{id}/sua', [ProductController::class, 'update']);
$router->post('/admin/san-pham/{id}/trang-thai', [ProductController::class, 'status']);

$router->get('/admin/san-pham/{productId}/phien-ban', [ProductVariantController::class, 'index']);
$router->get('/admin/san-pham/{productId}/phien-ban/them', [ProductVariantController::class, 'create']);
$router->post('/admin/san-pham/{productId}/phien-ban/them', [ProductVariantController::class, 'store']);
$router->get('/admin/san-pham/{productId}/phien-ban/{id}/sua', [ProductVariantController::class, 'edit']);
$router->post('/admin/san-pham/{productId}/phien-ban/{id}/sua', [ProductVariantController::class, 'update']);
$router->post('/admin/san-pham/{productId}/phien-ban/{id}/trang-thai', [ProductVariantController::class, 'status']);
// $router->get('/san-pham/{id}', [ProductController::class, 'show']);