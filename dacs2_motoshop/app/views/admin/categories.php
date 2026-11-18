<h1>Quản lý danh mục</h1>
<p><a href="<?= e(url('/admin')) ?>">Về quản trị</a> | <a href="<?= e(url('/admin/danh-muc/them')) ?>">Thêm danh mục</a>
</p>
<?php if ($notice): ?><p role="status"><?= e($notice) ?></p><?php endif; ?>
<p>Ẩn danh mục giữ lại dữ liệu và liên kết với sản phẩm. Bạn có thể hiện lại bất cứ lúc nào.</p>
<?php if (!$categories): ?>
<p>Chưa có danh mục nào. Hãy thêm danh mục đầu tiên.</p>
<?php else: ?>
<table>
    <caption>Danh sách danh mục</caption>
    <thead>
        <tr>
            <th scope="col">STT</th>
            <th scope="col">Tên</th>
            <th scope="col">Slug</th>
            <th scope="col">Trạng thái</th>
            <th scope="col">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php $stt = 1; ?>
        <?php foreach ($categories as $category): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td><?= e($category['name']) ?></td>
            <td><?= e($category['slug']) ?></td>
            <td><?= $category['status'] === 'active' ? 'Đang hiện' : 'Đã ẩn' ?></td>
            <td>
                <a href="<?= e(url('/admin/danh-muc/' . $category['id'] . '/sua')) ?>">Sửa</a>
                <form method="post" action="<?= e(url('/admin/danh-muc/' . $category['id'] . '/trang-thai')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status"
                        value="<?= $category['status'] === 'active' ? 'inactive' : 'active' ?>">
                    <button type="submit"><?= $category['status'] === 'active' ? 'Ẩn' : 'Hiện' ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>