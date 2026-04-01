<?php
class BannerViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public static function formatDate($date): string
    {
        if (empty($date)) return '-';
        return date('d/m/Y H:i', strtotime($date));
    }

    public static function getViTriLabel($viTri): string
    {
        $labels = [
            'HOME_HERO' => 'Trang chủ - Hero',
            'HOME_SIDE' => 'Trang chủ - Sidebar',
            'FLOATING_BOTTOM_LEFT' => 'Nổi góc dưới trái',
            'POPUP' => 'Popup',
            'CATEGORY_TOP' => 'Đầu trang danh mục',
        ];
        return $labels[$viTri] ?? $viTri;
    }
}

$successMessages = [
    'created' => 'Thêm banner thành công.',
    'updated' => 'Cập nhật banner thành công.',
    'deleted' => 'Xóa banner thành công.',
];

$errorMessages = [
    'invalid_id' => 'ID banner không hợp lệ.',
    'not_found' => 'Không tìm thấy banner.',
];

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <?php
            $breadcrumbs = [
                ['url' => '/admin/dashboard', 'label' => 'Dashboard'],
                ['url' => '', 'label' => 'Banner Quảng Cáo']
            ];
            require_once dirname(__DIR__) . '/layouts/breadcrumb.php';
            ?>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Quản lý banner quảng cáo</h3>
                                <a class="btn btn-primary" href="/admin/banner/them">
                                    <i class="bi bi-plus-circle me-1"></i>Thêm banner
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <?php if (!empty($success) && isset($successMessages[$success])): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <?= BannerViewHelper::e($successMessages[$success]) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($error) && isset($errorMessages[$error])): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <?= BannerViewHelper::e($errorMessages[$error]) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form class="row g-3 mb-3" method="GET" action="/admin/banner">
                                <div class="col-md-5">
                                    <select class="form-select" name="vi_tri">
                                        <option value="">Tất cả vị trí</option>
                                        <option value="HOME_HERO" <?= $viTri === 'HOME_HERO' ? 'selected' : '' ?>>Trang chủ - Hero</option>
                                        <option value="HOME_SIDE" <?= $viTri === 'HOME_SIDE' ? 'selected' : '' ?>>Trang chủ - Sidebar</option>
                                        <option value="FLOATING_BOTTOM_LEFT" <?= $viTri === 'FLOATING_BOTTOM_LEFT' ? 'selected' : '' ?>>Nổi góc dưới trái</option>
                                        <option value="POPUP" <?= $viTri === 'POPUP' ? 'selected' : '' ?>>Popup</option>
                                        <option value="CATEGORY_TOP" <?= $viTri === 'CATEGORY_TOP' ? 'selected' : '' ?>>Đầu trang danh mục</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="trang_thai">
                                        <option value="-1">Tất cả trạng thái</option>
                                        <option value="1" <?= $trangThai === 1 ? 'selected' : '' ?>>Hiển thị</option>
                                        <option value="0" <?= $trangThai === 0 ? 'selected' : '' ?>>Ẩn</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-secondary w-100" type="submit">
                                        <i class="bi bi-funnel me-1"></i>Lọc
                                    </button>
                                </div>
                            </form>

                            <?php if (empty($danhSachBanner)): ?>
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Không có banner nào.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Tiêu đề</th>
                                                <th>Hình ảnh</th>
                                                <th>Vị trí</th>
                                                <th>Thứ tự</th>
                                                <th>Thời gian</th>
                                                <th>Trạng thái</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($danhSachBanner as $item): ?>
                                                <tr>
                                                    <td><?= (int)$item['id'] ?></td>
                                                    <td>
                                                        <div class="fw-semibold"><?= BannerViewHelper::e($item['tieu_de']) ?></div>
                                                        <small class="text-muted"><?= BannerViewHelper::e($item['link_dich']) ?></small>
                                                    </td>
                                                    <td>
                                                        <img src="<?= BannerViewHelper::e($item['hinh_anh_desktop']) ?>" 
                                                             alt="Banner" 
                                                             style="max-width: 100px; max-height: 60px; object-fit: cover;"
                                                             class="rounded">
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info"><?= BannerViewHelper::getViTriLabel($item['vi_tri']) ?></span>
                                                    </td>
                                                    <td><?= (int)$item['thu_tu'] ?></td>
                                                    <td>
                                                        <small>
                                                            <?= BannerViewHelper::formatDate($item['ngay_bat_dau']) ?><br>
                                                            đến<br>
                                                            <?= BannerViewHelper::formatDate($item['ngay_ket_thuc']) ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <?php if ($item['trang_thai'] == 1): ?>
                                                            <span class="badge bg-success">Hiển thị</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Ẩn</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a class="btn btn-sm btn-outline-primary" href="/admin/banner/sua?id=<?= (int)$item['id'] ?>" title="Sửa">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <form method="POST" action="/admin/banner/xoa?id=<?= (int)$item['id'] ?>" onsubmit="return confirm('Xóa banner này?');" class="d-inline">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if ($totalPages > 1): ?>
                                    <div class="mt-3">
                                        <nav>
                                            <ul class="pagination justify-content-center mb-0">
                                                <?php if ($currentPage > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= !empty($viTri) ? '&vi_tri=' . urlencode($viTri) : '' ?><?= $trangThai !== -1 ? '&trang_thai=' . $trangThai : '' ?>">Trước</a>
                                                    </li>
                                                <?php endif; ?>

                                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $i ?><?= !empty($viTri) ? '&vi_tri=' . urlencode($viTri) : '' ?><?= $trangThai !== -1 ? '&trang_thai=' . $trangThai : '' ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>

                                                <?php if ($currentPage < $totalPages): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= !empty($viTri) ? '&vi_tri=' . urlencode($viTri) : '' ?><?= $trangThai !== -1 ? '&trang_thai=' . $trangThai : '' ?>">Sau</a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
