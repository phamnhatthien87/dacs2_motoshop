<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\Category;
use Core\Controller;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Áp dụng cho tất cả action, kể cả các POST gửi trực tiếp.
        AuthMiddleware::requireRole('admin');
    }

    public function index(): void
    {
        $notice = $_SESSION['category_notice'] ?? null;
        unset($_SESSION['category_notice']);
        $this->view('admin/categories', ['title' => 'Quản lý danh mục', 'categories' => (new Category())->all(), 'notice' => $notice]);
    }

    public function create(): void
    {
        $this->form(['name' => '', 'slug' => '', 'description' => '', 'status' => 'active']);
    }

    public function store(): void
    {
        $this->persist();
    }

    public function edit(string $id): void
    {
        $category = $this->lookup($id);
        if ($category) {
            $this->form($category);
        }
    }

    public function update(string $id): void
    {
        $category = $this->lookup($id);
        if ($category) {
            $this->persist((int) $category['id']);
        }
    }

    public function status(string $id): void
    {
        $category = $this->lookup($id);
        if (!$category) {
            return;
        }
        $status = $this->input('status');
        if (!in_array($status, ['active', 'inactive'], true)) {
            http_response_code(422);
            $this->view('customer/error', ['title' => 'Dữ liệu không hợp lệ', 'status' => 422, 'message' => 'Trạng thái danh mục không hợp lệ.']);
            return;
        }
        // Gán trạng thái cụ thể để gửi lại cùng request không đảo trạng thái lần nữa.
        (new Category())->setStatus((int) $category['id'], $status);
        $_SESSION['category_notice'] = $status === 'active' ? 'Đã hiện danh mục.' : 'Đã ẩn danh mục.';
        $this->redirect('/admin/danh-muc', 303);
    }

    private function lookup(string $id): ?array
    {
        $value = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $category = $value === false ? null : (new Category())->find($value);
        if (!$category) {
            http_response_code(404);
            $this->view('customer/error', ['title' => 'Không tìm thấy danh mục', 'status' => 404, 'message' => 'Danh mục không tồn tại.']);
        }
        return $category;
    }

    private function input(string $key): string
    {
        return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    private function persist(?int $id = null): void
    {
        $data = ['name' => $this->input('name'), 'slug' => $this->input('slug'),
            'description' => $this->input('description'), 'status' => $this->input('status')];
        $errors = [];
        if ($data['name'] === '' || mb_strlen($data['name'], 'UTF-8') > 150) {
            $errors[] = 'Tên danh mục phải có từ 1 đến 150 ký tự.';
        }
        if (strlen($data['slug']) > 180 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $data['slug'])) {
            $errors[] = 'Slug tối đa 180 ký tự, chỉ gồm chữ thường không dấu, số và dấu gạch nối giữa các từ.';
        }
        if (mb_strlen($data['description'], 'UTF-8') > 5000) {
            $errors[] = 'Mô tả tối đa 5000 ký tự.';
        }
        if (!in_array($data['status'], ['active', 'inactive'], true)) {
            $errors[] = 'Trạng thái danh mục không hợp lệ.';
        }
        if (!$errors) {
            try {
                (new Category())->save($data, $id);
                $_SESSION['category_notice'] = $id === null ? 'Đã thêm danh mục.' : 'Đã cập nhật danh mục.';
                $this->redirect('/admin/danh-muc', 303);
            } catch (\PDOException $exception) {
                if (($exception->errorInfo[1] ?? null) !== 1062) {
                    throw $exception;
                }
                $errors[] = 'Slug đã được sử dụng. Vui lòng chọn slug khác.';
            }
        }
        http_response_code(422);
        if ($id !== null) {
            $data['id'] = $id;
        }
        $this->form($data, $errors);
    }

    private function form(array $category, array $errors = []): void
    {
        $this->view('admin/category_form', ['title' => isset($category['id']) ? 'Sửa danh mục' : 'Thêm danh mục', 'category' => $category, 'errors' => $errors]);
    }
}