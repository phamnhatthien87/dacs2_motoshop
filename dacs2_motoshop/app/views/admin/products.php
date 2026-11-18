<h1>Quản lý sản phẩm</h1>
<p><a href="<?= e(url('/admin')) ?>">Về quản trị</a> | <a href="<?= e(url('/admin/san-pham/them')) ?>">Thêm sản phẩm</a>
</p>
<?php if ($notice): ?><p role="status"><?= e($notice) ?></p><?php endif; ?>
<?php if (!$items): ?><p>Chưa có sản phẩm.</p><?php else: ?>
<table>
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Danh mục</th>
            <th>Thương hiệu</th>
            <th>Phiên bản</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php $stt = 1; foreach ($items as $item): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td><?= e($item['name']) ?></td>
            <td><?= e($item['slug']) ?></td>
            <td><?= e($item['category_name']) ?></td>
            <td><?= e($item['brand_name'] ?? 'Không có') ?></td>
            <td><a href="<?= e(url('/admin/san-pham/' . $item['id'] . '/phien-ban')) ?>"><?= e($item['variant_count']) ?>
                    phiên bản</a></td>
            <td><?= e(['active' => 'Đang hiện', 'inactive' => 'Đã ẩn', 'draft' => 'Bản nháp'][$item['status']]) ?></td>
            <td><a href="<?= e(url('/admin/san-pham/' . $item['id'] . '/sua')) ?>">Sửa</a>
                <form method="post" action="<?= e(url('/admin/san-pham/' . $item['id'] . '/trang-thai')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status"
                        value="<?= $item['status'] === 'active' ? 'inactive' : 'active' ?>">
                    <button type="submit"><?= $item['status'] === 'active' ? 'Ẩn' : 'Hiện' ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>