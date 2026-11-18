<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Core\Controller;

class ProductController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireRole('admin');
    }

    public function index(): void
    {
        $notice = $_SESSION['products_notice'] ?? null;
        unset($_SESSION['products_notice']);
        $this->view('admin/products', ['title' => 'Quản lý sản phẩm', 'items' => (new Product())->all(), 'notice' => $notice]);
    }

    public function create(): void
    {
        $this->form(['name' => '', 'slug' => '', 'category_id' => '', 'brand_id' => '', 'short_description' => '', 'description' => '', 'status' => 'draft']);
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
        if (!in_array($status, ['draft', 'active', 'inactive'], true)) {
            http_response_code(422);
            $this->view('customer/error', ['title' => 'Dữ liệu không hợp lệ', 'status' => 422, 'message' => 'Trạng thái không hợp lệ.']);
            return;
        }
        (new Product())->setStatus((int) $item['id'], $status);
        $_SESSION['products_notice'] = 'Đã cập nhật trạng thái sản phẩm.';
        $this->redirect('/admin/san-pham', 303);
    }

    private function lookup(string $id): ?array
    {
        $value = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $item = $value === false ? null : (new Product())->find($value);
        if (!$item) {
            http_response_code(404);
            $this->view('customer/error', ['title' => 'Không tìm thấy', 'status' => 404, 'message' => 'Không tìm thấy sản phẩm.']);
        }
        return $item;
    }

    private function input(string $key): string
    {
        return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    private function persist(?int $id = null): void
    {
        $data = ['name' => $this->input('name'), 'slug' => $this->input('slug'), 'category_id' => $this->input('category_id'), 'brand_id' => $this->input('brand_id'), 'short_description' => $this->input('short_description'), 'description' => $this->input('description'), 'status' => $this->input('status')];
        $errors = [];
        if ($data['name'] === '' || mb_strlen($data['name'], 'UTF-8') > 200) {
            $errors[] = 'Tên phải có từ 1 đến 200 ký tự.';
        }
        if (strlen($data['slug']) > 230 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $data['slug'])) {
            $errors[] = 'Slug tối đa 230 ký tự, dùng chữ thường không dấu, số và gạch nối giữa các từ.';
        }
        if (mb_strlen($data['description'], 'UTF-8') > 5000) { $errors[] = 'Mô tả tối đa 5000 ký tự.'; }
        if (!in_array($data['status'], ['draft', 'active', 'inactive'], true)) { $errors[] = 'Trạng thái không hợp lệ.'; }
        $categoryId = filter_var($data['category_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($categoryId === false || !(new Category())->find($categoryId)) { $errors[] = 'Vui lòng chọn danh mục tồn tại.'; }
        if ($data['brand_id'] === '') {
            $data['brand_id'] = null;
        } else {
            $brandId = filter_var($data['brand_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($brandId === false || !(new Brand())->find($brandId)) { $errors[] = 'Thương hiệu không tồn tại.'; }
        }
        if (mb_strlen($data['short_description'], 'UTF-8') > 500) { $errors[] = 'Mô tả ngắn tối đa 500 ký tự.'; }
        if (!$errors) {
            try {
                (new Product())->save($data, $id);
                $_SESSION['products_notice'] = 'Đã lưu sản phẩm.';
                $this->redirect('/admin/san-pham', 303);
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
        $this->view('admin/products_form', ['title' => (isset($item['id']) ? 'Sửa' : 'Thêm') . ' sản phẩm', 'item' => $item, 'errors' => $errors, 'categories' => (new Category())->all(), 'brands' => (new Brand())->all()]);
    }
}