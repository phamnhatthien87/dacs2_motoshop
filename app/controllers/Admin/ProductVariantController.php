<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\Product;
use App\Models\ProductVariant;
use Core\Controller;

class ProductVariantController extends Controller
{
    public function __construct() { AuthMiddleware::requireRole('admin'); }

    public function index(string $productId): void
    {
        $product = $this->product($productId);
        if (!$product) { return; }
        $notice = $_SESSION['variant_notice'] ?? null;
        unset($_SESSION['variant_notice']);
        $this->view('admin/variants', ['title' => 'Phiên bản sản phẩm', 'product' => $product,
            'items' => (new ProductVariant())->forProduct((int) $product['id']), 'notice' => $notice]);
    }

    public function create(string $productId): void
    {
        $product = $this->product($productId);
        if ($product) {
            $this->form($product, ['variant_name' => 'Tiêu chuẩn', 'color' => '', 'size' => '', 'sku' => '', 'price' => '', 'stock_quantity' => '0', 'status' => 'active']);
        }
    }

    public function store(string $productId): void
    {
        $product = $this->product($productId);
        if ($product) { $this->persist($product); }
    }

    public function edit(string $productId, string $id): void
    {
        $product = $this->product($productId);
        if (!$product) { return; }
        $item = $this->variant((int) $product['id'], $id);
        if ($item) { $this->form($product, $item); }
    }

    public function update(string $productId, string $id): void
    {
        $product = $this->product($productId);
        if (!$product) { return; }
        $item = $this->variant((int) $product['id'], $id);
        if ($item) { $this->persist($product, (int) $item['id']); }
    }

    public function status(string $productId, string $id): void
    {
        $product = $this->product($productId);
        if (!$product) { return; }
        $item = $this->variant((int) $product['id'], $id);
        if (!$item) { return; }
        $status = $this->input('status');
        if (!in_array($status, ['active', 'inactive'], true)) {
            $this->error(422, 'Trạng thái không hợp lệ.');
            return;
        }
        (new ProductVariant())->setStatus((int) $product['id'], (int) $item['id'], $status);
        $_SESSION['variant_notice'] = 'Đã cập nhật trạng thái phiên bản.';
        $this->redirect('/admin/san-pham/' . $product['id'] . '/phien-ban', 303);
    }

    private function product(string $id): ?array
    {
        $value = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $item = $value === false ? null : (new Product())->find($value);
        if (!$item) { $this->error(404, 'Không tìm thấy sản phẩm.'); }
        return $item;
    }

    private function variant(int $productId, string $id): ?array
    {
        $value = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        // Luôn kiểm tra phiên bản thuộc sản phẩm trong URL.
        $item = $value === false ? null : (new ProductVariant())->find($productId, $value);
        if (!$item) { $this->error(404, 'Không tìm thấy phiên bản của sản phẩm này.'); }
        return $item;
    }

    private function input(string $key): string
    {
        return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    private function persist(array $product, ?int $id = null): void
    {
        $data = [];
        foreach (['variant_name', 'color', 'size', 'sku', 'price', 'stock_quantity', 'status'] as $field) {
            $data[$field] = $this->input($field);
        }
        $errors = [];
        if ($data['variant_name'] === '' || mb_strlen($data['variant_name'], 'UTF-8') > 150) { $errors[] = 'Tên phiên bản phải có từ 1 đến 150 ký tự.'; }
        if (mb_strlen($data['color'], 'UTF-8') > 50 || mb_strlen($data['size'], 'UTF-8') > 50) { $errors[] = 'Màu sắc và kích thước tối đa 50 ký tự.'; }
        if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]{0,99}$/D', $data['sku'])) { $errors[] = 'SKU gồm 1–100 ký tự chữ không dấu, số, gạch nối hoặc gạch dưới; bắt đầu bằng chữ hoặc số.'; }
        // Kiểm tra chuỗi thập phân để không làm tròn tiền bằng float.
        if (!preg_match('/^(?:0|[1-9][0-9]{0,9})(?:\.[0-9]{1,2})?$/D', $data['price'])) { $errors[] = 'Giá từ 0 đến 9999999999.99, tối đa 2 chữ số thập phân, không dùng dấu phân cách hàng nghìn.'; }
        if (!preg_match('/^(?:0|[1-9][0-9]{0,9})$/D', $data['stock_quantity']) || (int) $data['stock_quantity'] > 4294967295) { $errors[] = 'Tồn kho phải là số nguyên từ 0 đến 4294967295.'; }
        if (!in_array($data['status'], ['active', 'inactive'], true)) { $errors[] = 'Trạng thái không hợp lệ.'; }
        if (!$errors) {
            try {
                (new ProductVariant())->save((int) $product['id'], $data, $id);
                $_SESSION['variant_notice'] = 'Đã lưu phiên bản sản phẩm.';
                $this->redirect('/admin/san-pham/' . $product['id'] . '/phien-ban', 303);
            } catch (\PDOException $exception) {
                $code = $exception->errorInfo[1] ?? null;
                if ($code === 1062) { $errors[] = 'SKU đã được sử dụng hoặc tổ hợp màu sắc/kích thước đã tồn tại trong sản phẩm này.'; }
                elseif ($code === 1452) { $errors[] = 'Sản phẩm không còn tồn tại. Vui lòng tải lại danh sách.'; }
                else { throw $exception; }
            }
        }
        http_response_code(422);
        if ($id !== null) { $data['id'] = $id; }
        $this->form($product, $data, $errors);
    }

    private function form(array $product, array $item, array $errors = []): void
    {
        $this->view('admin/variants_form', ['title' => isset($item['id']) ? 'Sửa phiên bản' : 'Thêm phiên bản', 'product' => $product, 'item' => $item, 'errors' => $errors]);
    }

    private function error(int $status, string $message): void
    {
        http_response_code($status);
        $this->view('customer/error', ['title' => 'Lỗi ' . $status, 'status' => $status, 'message' => $message]);
    }
}