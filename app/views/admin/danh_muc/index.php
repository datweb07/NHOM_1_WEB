<?php
class DanhMucViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$successMessages = [
    'created' => 'Thêm danh mục thành công.',
    'updated' => 'Cập nhật danh mục thành công.',
    'hidden' => 'Đã ẩn danh mục.',
    'shown' => 'Đã hiển thị lại danh mục.',
];

$errorMessages = [
    'invalid_id' => 'ID danh mục không hợp lệ.',
    'not_found' => 'Không tìm thấy danh mục.',
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4 py-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h1 class="h3 mb-0">Quản lý danh mục</h1>
            <a class="btn btn-primary" href="/admin/danh-muc/them">Thêm danh mục</a>
        </div>

        <?php if (!empty($success) && isset($successMessages[$success])): ?>
            <div class="alert alert-success"><?= DanhMucViewHelper::e($successMessages[$success]) ?></div>
        <?php endif; ?>

        <?php if (!empty($error) && isset($errorMessages[$error])): ?>
            <div class="alert alert-danger"><?= DanhMucViewHelper::e($errorMessages[$error]) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body border-bottom">
                <form class="row g-2" method="GET" action="/admin/danh-muc">
                    <div class="col-12 col-lg-6">
                        <input
                            class="form-control"
                            type="text"
                            name="keyword"
                            placeholder="Tìm theo tên hoặc slug..."
                            value="<?= DanhMucViewHelper::e($keyword ?? '') ?>">
                    </div>
                    <div class="col-12 col-lg-4">
                        <select class="form-select" name="trang_thai">
                            <option value="all" <?= ($statusFilter ?? 'all') === 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                            <option value="1" <?= ($statusFilter ?? 'all') === '1' ? 'selected' : '' ?>>Đang hiển thị</option>
                            <option value="0" <?= ($statusFilter ?? 'all') === '0' ? 'selected' : '' ?>>Đang ẩn </option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-2 d-grid">
                        <button class="btn btn-outline-secondary" type="submit">Lọc</button>
                    </div>
                </form>
            </div>

            <?php if (empty($danhSachDanhMuc)): ?>
                <div class="card-body text-center text-secondary py-5">Không có dữ liệu danh mục.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên danh mục</th>
                                <th>Slug</th>
                                <th>Danh mục cha</th>
                                <th>Thứ tự</th>
                                <th>Trạng thái</th>
                                <th>Sản phẩm</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhSachDanhMuc as $item): ?>
                                <tr>
                                    <td><?= (int)$item['id'] ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= DanhMucViewHelper::e($item['ten']) ?></div>
                                        <?php if (!empty($item['icon_url'])): ?>
                                            <small class="text-secondary"><?= DanhMucViewHelper::e($item['icon_url']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= DanhMucViewHelper::e($item['slug']) ?></td>
                                    <td><?= DanhMucViewHelper::e($item['ten_danh_muc_cha'] ?? '-') ?></td>
                                    <td><?= (int)$item['thu_tu'] ?></td>
                                    <td>
                                        <?php if ((int)$item['trang_thai'] === 1): ?>
                                            <span class="badge text-bg-success">Hiển thị</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary">Đang ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= (int)($item['tong_san_pham'] ?? 0) ?></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a class="btn btn-sm btn-outline-primary" href="/admin/danh-muc/sua?id=<?= (int)$item['id'] ?>">Sửa</a>

                                            <?php if ((int)$item['trang_thai'] === 1): ?>
                                                <form method="POST" action="/admin/danh-muc/xoa?id=<?= (int)$item['id'] ?>" onsubmit="return confirm('Ẩn danh mục này?');">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Ẩn</button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" action="/admin/danh-muc/hien?id=<?= (int)$item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Hiển thị lại</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>