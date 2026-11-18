<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\Brand;

use Core\Controller;

class BrandController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireRole('admin');
    }

    public function index(): void
    {
        $notice = $_SESSION['brands_notice'] ?? null;
        unset($_SESSION['brands_notice']);
        $this->view('admin/brands', ['title' => 'Quản lý thương hiệu', 'items' => (new Brand())->all(), 'notice' => $notice]);
    }

    public function create(): void
    {
        $this->form(['name' => '', 'slug' => '', 'description' => '', 'status' => 'active']);
    }

    public function store(): void { $this->persist(); }

    public function edit(string $id): void
    {
        $item = $this->lookup($id);
        if ($item) { $this->form($item); }
    }

    public function update(string $id): void
    {
        $item = $this->lookup($id);
        if ($item) { $this->persist((int) $item['id']); }
    }

    public function status(string $id): void
    {
        $item = $this->lookup($id);
        if (!$item) { return; }
        $status = $this->input('status');
        if (!in_array($status, ['active', 'inactive'], true)) {
            http_response_code(422);
            $this->view('customer/error', ['title' => 'Dữ liệu không hợp lệ', 'status' => 422, 'message' => 'Trạng thái không hợp lệ.']);
            return;
        }
        (new Brand())->setStatus((int) $item['id'], $status);
        $_SESSION['brands_notice'] = 'Đã cập nhật trạng thái thương hiệu.';
        $this->redirect('/admin/thuong-hieu', 303);
    }

    private function lookup(string $id): ?array
    {
        $value = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $item = $value === false ? null : (new Brand())->find($value);
        if (!$item) {
            http_response_code(404);
            $this->view('customer/error', ['title' => 'Không tìm thấy', 'status' => 404, 'message' => 'Không tìm thấy thương hiệu.']);
        }
        return $item;
    }

    private function input(string $key): string
    {
        return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    private function persist(?int $id = null): void
    {
        $data = ['name' => $this->input('name'), 'slug' => $this->input('slug'), 'description' => $this->input('description'), 'status' => $this->input('status')];
        $errors = [];
        if ($data['name'] === '' || mb_strlen($data['name'], 'UTF-8') > 150) {
            $errors[] = 'Tên phải có từ 1 đến 150 ký tự.';
        }
        if (strlen($data['slug']) > 180 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $data['slug'])) {
            $errors[] = 'Slug tối đa 180 ký tự, dùng chữ thường không dấu, số và gạch nối giữa các từ.';
        }
        if (mb_strlen($data['description'], 'UTF-8') > 5000) { $errors[] = 'Mô tả tối đa 5000 ký tự.'; }
        if (!in_array($data['status'], ['active', 'inactive'], true)) { $errors[] = 'Trạng thái không hợp lệ.'; }
        
        if (!$errors) {
            try {
                (new Brand())->save($data, $id);
                $_SESSION['brands_notice'] = 'Đã lưu thương hiệu.';
                $this->redirect('/admin/thuong-hieu', 303);
            } catch (\PDOException $exception) {
                $code = $exception->errorInfo[1] ?? null;
                if ($code === 1062) { $errors[] = 'Slug đã được sử dụng.'; }
                elseif ($code === 1452) { $errors[] = 'Dữ liệu liên kết đã thay đổi. Vui lòng chọn lại.'; }
                else { throw $exception; }
            }
        }
        http_response_code(422);
        if ($id !== null) { $data['id'] = $id; }
        $this->form($data, $errors);
    }

    private function form(array $item, array $errors = []): void
    {
        $this->view('admin/brands_form', ['title' => (isset($item['id']) ? 'Sửa' : 'Thêm') . ' thương hiệu', 'item' => $item, 'errors' => $errors]);
    }
}