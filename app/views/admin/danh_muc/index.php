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

require_once dirname(__DIR__) . '/layouts/header.php';
?>

<?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Danh Mục', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">
            <!-- Action Bar -->
            <div class="d-flex justify-content-end mb-3">
                <a href="/admin/danh-muc/them" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm Danh Mục
                </a>
            </div>

            <div class="card">
                <div class="card-body">

        <?php if (!empty($success) && isset($successMessages[$success])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= DanhMucViewHelper::e($successMessages[$success]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error) && isset($errorMessages[$error])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= DanhMucViewHelper::e($errorMessages[$error]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Search and Filter Form -->
        <form class="row g-3 mb-3" method="GET" action="/admin/danh-muc">
            <div class="col-md-5">
                <label for="keyword" class="form-label">Tìm Kiếm</label>
                <input
                    class="form-control"
                    id="keyword"
                    type="text"
                    name="keyword"
                    placeholder="Tên hoặc slug..."
                    value="<?= DanhMucViewHelper::e($keyword ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label for="trang_thai" class="form-label">Trạng Thái</label>
                <select class="form-select" id="trang_thai" name="trang_thai">
                    <option value="all" <?= ($statusFilter ?? 'all') === 'all' ? 'selected' : '' ?>>Tất cả</option>
                    <option value="1" <?= ($statusFilter ?? 'all') === '1' ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= ($statusFilter ?? 'all') === '0' ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <?php if (empty($danhSachDanhMuc)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Không có danh mục nào</p>
                </div>
            <?php else: ?>
                <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên Danh Mục</th>
                                <th>Slug</th>
                                <th>Danh Mục Cha</th>
                                <th>Thứ Tự</th>
                                <th>Trạng Thái</th>
                                <th>Sản Phẩm</th>
                                <th>Thao Tác</th>
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
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a class="btn btn-outline-primary" href="/admin/danh-muc/sua?id=<?= (int)$item['id'] ?>" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <?php if ((int)$item['trang_thai'] === 1): ?>
                                                <form method="POST" action="/admin/danh-muc/xoa?id=<?= (int)$item['id'] ?>" style="display: inline;" onsubmit="return confirm('Ẩn danh mục này?');">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Ẩn">
                                                        <i class="bi bi-eye-slash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" action="/admin/danh-muc/hien?id=<?= (int)$item['id'] ?>" style="display: inline;">
                                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Hiển thị">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>