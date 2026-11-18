<h1><?= e($title) ?></h1>
<?php if ($errors): ?><ul role="alert"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li>
    <?php endforeach; ?></ul><?php endif; ?>
<form method="post"
    action="<?= e(url(isset($item['id']) ? '/admin/thuong-hieu/' . $item['id'] . '/sua' : '/admin/thuong-hieu/them')) ?>">
    <?= csrf_field() ?>
    <p><label for="name">Tên thương hiệu</label><br><input id="name" name="name" maxlength="150"
            value="<?= e($item['name']) ?>" required></p>
    <p><label for="slug">Slug (ví dụ: thuong-hieu-a)</label><br><input id="slug" name="slug" maxlength="180"
            pattern="[a-z0-9]+(-[a-z0-9]+)*" value="<?= e($item['slug']) ?>" required></p>

    <p><label for="description">Mô tả</label><br><textarea id="description" name="description" maxlength="5000" rows="5"
            cols="50"><?= e($item['description'] ?? '') ?></textarea></p>
    <p><label for="status">Trạng thái</label><select id="status" name="status">
            <?php foreach (['active' => 'Hiện', 'inactive' => 'Ẩn'] as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= $item['status'] === $value ? 'selected' : '' ?>><?= e($label) ?>
            </option>
            <?php endforeach; ?>
        </select></p>
    <button type="submit">Lưu thương hiệu</button> <a href="<?= e(url('/admin/thuong-hieu')) ?>">Hủy</a>
</form>