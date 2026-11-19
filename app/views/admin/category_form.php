<?php
/** @var array $user */
/** @var array $title */
/** @var array $item */
/** @var array $category */
/** @var array $errors */
/** @var string|null $notice */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>danh mục sản phẩm</title>
</head>

<body>
    <h1><?= e($title) ?></h1>
    <?php if ($errors): ?>
    <ul role="alert"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
    <form method="post"
        action="<?= e(url(isset($category['id']) ? '/admin/danh-muc/' . $category['id'] . '/sua' : '/admin/danh-muc/them')) ?>">
        <?= csrf_field() ?>
        <p><label for="name">Tên danh mục</label><br>
            <input id="name" name="name" maxlength="150" value="<?= e($category['name']) ?>" required>
        </p>
        <p><label for="slug">Slug</label><br>
            <input id="slug" name="slug" maxlength="180" pattern="[a-z0-9]+(-[a-z0-9]+)*"
                value="<?= e($category['slug']) ?>" aria-describedby="slug-help" required>
        </p>
        <p id="slug-help">Tên dùng trong đường dẫn, không trùng danh mục khác. Ví dụ: mu-bao-hiem.</p>
        <p><label for="description">Mô tả</label><br>
            <textarea id="description" name="description" rows="5" cols="50"
                maxlength="5000"><?= e($category['description'] ?? '') ?></textarea>
        </p>
        <p><label for="status">Trạng thái</label>
            <select id="status" name="status" required>
                <option value="active" <?= $category['status'] === 'active' ? 'selected' : '' ?>>Hiện</option>
                <option value="inactive" <?= $category['status'] === 'inactive' ? 'selected' : '' ?>>Ẩn</option>
            </select>
        </p>
        <button type="submit">Lưu danh mục</button>
        <a href="<?= e(url('/admin/danh-muc')) ?>">Hủy</a>
    </form>
</body>

</html>