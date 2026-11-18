<h1><?= e($title) ?></h1>
<?php if ($errors): ?><ul role="alert"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li>
    <?php endforeach; ?></ul><?php endif; ?>
<form method="post"
    action="<?= e(url(isset($item['id']) ? '/admin/san-pham/' . $item['id'] . '/sua' : '/admin/san-pham/them')) ?>">
    <?= csrf_field() ?>
    <p><label for="name">Tên sản phẩm</label><br><input id="name" name="name" maxlength="200"
            value="<?= e($item['name']) ?>" required></p>
    <p><label for="slug">Slug (ví dụ: gang-tay-di-xe)</label><br><input id="slug" name="slug" maxlength="230"
            pattern="[a-z0-9]+(-[a-z0-9]+)*" value="<?= e($item['slug']) ?>" required></p>
    <p><label for="category_id">Danh mục</label>
        <select id="category_id" name="category_id" required>
            <option value="">Chọn danh mục</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?= e($category['id']) ?>"
                <?= (string) $item['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
                <?= e($category['name']) ?><?= $category['status'] === 'inactive' ? ' (đã ẩn)' : '' ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><label for="brand_id">Thương hiệu</label>
        <select id="brand_id" name="brand_id">
            <option value="">Không có thương hiệu</option>
            <?php foreach ($brands as $brand): ?>
            <option value="<?= e($brand['id']) ?>"
                <?= (string) $item['brand_id'] === (string) $brand['id'] ? 'selected' : '' ?>>
                <?= e($brand['name']) ?><?= $brand['status'] === 'inactive' ? ' (đã ẩn)' : '' ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><label for="short_description">Mô tả ngắn</label><br><textarea id="short_description" name="short_description"
            maxlength="500" rows="3"><?= e($item['short_description']) ?></textarea></p>
    <p>Giá, SKU và tồn kho được nhập tại mục Phiên bản sau khi lưu sản phẩm. Sản phẩm không có màu/kích thước vẫn cần
        một phiên bản tiêu chuẩn.</p>
    <p><label for="description">Mô tả</label><br><textarea id="description" name="description" maxlength="5000" rows="5"
            cols="50"><?= e($item['description'] ?? '') ?></textarea></p>
    <p><label for="status">Trạng thái</label><select id="status" name="status">
            <?php foreach (['draft' => 'Bản nháp', 'active' => 'Hiện', 'inactive' => 'Ẩn'] as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= $item['status'] === $value ? 'selected' : '' ?>><?= e($label) ?>
            </option>
            <?php endforeach; ?>
        </select></p>
    <button type="submit">Lưu sản phẩm</button> <a href="<?= e(url('/admin/san-pham')) ?>">Hủy</a>
</form>