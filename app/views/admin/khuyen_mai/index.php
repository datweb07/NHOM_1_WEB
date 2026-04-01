<?php
class KhuyenMaiViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public static function formatCurrency($value): string
    {
        return number_format((float)$value, 0, ',', '.') . ' ₫';
    }

    public static function formatDate($date): string
    {
        if (empty($date)) return '-';
        return date('d/m/Y H:i', strtotime($date));
    }
}

$successMessages = [
    'created' => 'Thêm khuyến mãi thành công.',
    'updated' => 'Cập nhật khuyến mãi thành công.',
    'deleted' => 'Xóa khuyến mãi thành công.',
];

$errorMessages = [
    'invalid_id' => 'ID khuyến mãi không hợp lệ.',
    'not_found' => 'Không tìm thấy khuyến mãi.',
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
                ['url' => '', 'label' => 'Khuyến Mãi']
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
                                <h3 class="card-title mb-0">Quản lý khuyến mãi</h3>
                                <a class="btn btn-primary" href="/admin/khuyen-mai/them">
                                    <i class="bi bi-plus-circle me-1"></i>Thêm khuyến mãi
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <?php if (!empty($success) && isset($successMessages[$success])): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <?= KhuyenMaiViewHelper::e($successMessages[$success]) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($error) && isset($errorMessages[$error])): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <?= KhuyenMaiViewHelper::e($errorMessages[$error]) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form class="row g-3 mb-3" method="GET" action="/admin/khuyen-mai">
                                <div class="col-md-10">
                                    <select class="form-select" name="trang_thai">
                                        <option value="" <?= ($trangThai ?? '') === '' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                                        <option value="HOAT_DONG" <?= ($trangThai ?? '') === 'HOAT_DONG' ? 'selected' : '' ?>>Đang hoạt động</option>
                                        <option value="DA_HET_HAN" <?= ($trangThai ?? '') === 'DA_HET_HAN' ? 'selected' : '' ?>>Đã hết hạn</option>
                                        <option value="TAM_DUNG" <?= ($trangThai ?? '') === 'TAM_DUNG' ? 'selected' : '' ?>>Tạm dừng</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-secondary w-100" type="submit">
                                        <i class="bi bi-funnel me-1"></i>Lọc
                                    </button>
                                </div>
                            </form>

                            <?php if (empty($danhSachKhuyenMai)): ?>
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Không có dữ liệu khuyến mãi.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên chương trình</th>
                                                <th>Loại giảm</th>
                                                <th>Giá trị giảm</th>
                                                <th>Giảm tối đa</th>
                                                <th>Thời gian</th>
                                                <th>Trạng thái</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($danhSachKhuyenMai as $item): ?>
                                                <tr>
                                                    <td><?= (int)$item['id'] ?></td>
                                                    <td>
                                                        <div class="fw-semibold"><?= KhuyenMaiViewHelper::e($item['ten_chuong_trinh']) ?></div>
                                                    </td>
                                                    <td>
                                                        <?php if ($item['loai_giam'] === 'PHAN_TRAM'): ?>
                                                            <span class="badge bg-info">Phần trăm</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Số tiền</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($item['loai_giam'] === 'PHAN_TRAM'): ?>
                                                            <?= number_format((float)$item['gia_tri_giam'], 0) ?>%
                                                        <?php else: ?>
                                                            <?= KhuyenMaiViewHelper::formatCurrency($item['gia_tri_giam']) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= !empty($item['giam_toi_da']) ? KhuyenMaiViewHelper::formatCurrency($item['giam_toi_da']) : '-' ?>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <?= KhuyenMaiViewHelper::formatDate($item['ngay_bat_dau']) ?><br>
                                                            đến<br>
                                                            <?= KhuyenMaiViewHelper::formatDate($item['ngay_ket_thuc']) ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <?php if ($item['trang_thai'] === 'HOAT_DONG'): ?>
                                                            <span class="badge bg-success">Hoạt động</span>
                                                        <?php elseif ($item['trang_thai'] === 'DA_HET_HAN'): ?>
                                                            <span class="badge bg-secondary">Đã hết hạn</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Tạm dừng</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a class="btn btn-sm btn-outline-primary" href="/admin/khuyen-mai/sua?id=<?= (int)$item['id'] ?>" title="Sửa">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <a class="btn btn-sm btn-outline-info" href="/admin/khuyen-mai/lien-ket-san-pham?id=<?= (int)$item['id'] ?>" title="Sản phẩm">
                                                                <i class="bi bi-box"></i>
                                                            </a>
                                                            <form method="POST" action="/admin/khuyen-mai/xoa?id=<?= (int)$item['id'] ?>" onsubmit="return confirm('Xóa khuyến mãi này?');" class="d-inline">
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
                                                        <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= !empty($trangThai) ? '&trang_thai=' . urlencode($trangThai) : '' ?>">Trước</a>
                                                    </li>
                                                <?php endif; ?>

                                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $i ?><?= !empty($trangThai) ? '&trang_thai=' . urlencode($trangThai) : '' ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>

                                                <?php if ($currentPage < $totalPages): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= !empty($trangThai) ? '&trang_thai=' . urlencode($trangThai) : '' ?>">Sau</a>
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
