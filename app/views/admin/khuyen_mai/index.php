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
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khuyến mãi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4 py-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h1 class="h3 mb-0">Quản lý khuyến mãi</h1>
            <a class="btn btn-primary" href="/admin/khuyen-mai/them">Thêm khuyến mãi</a>
        </div>

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

        <div class="card shadow-sm border-0">
            <div class="card-body border-bottom">
                <form class="row g-2" method="GET" action="/admin/khuyen-mai">
                    <div class="col-12 col-lg-8">
                        <select class="form-select" name="trang_thai">
                            <option value="" <?= ($trangThai ?? '') === '' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                            <option value="HOAT_DONG" <?= ($trangThai ?? '') === 'HOAT_DONG' ? 'selected' : '' ?>>Đang hoạt động</option>
                            <option value="DA_HET_HAN" <?= ($trangThai ?? '') === 'DA_HET_HAN' ? 'selected' : '' ?>>Đã hết hạn</option>
                            <option value="TAM_DUNG" <?= ($trangThai ?? '') === 'TAM_DUNG' ? 'selected' : '' ?>>Tạm dừng</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-4 d-grid">
                        <button class="btn btn-outline-secondary" type="submit">Lọc</button>
                    </div>
                </form>
            </div>

            <?php if (empty($danhSachKhuyenMai)): ?>
                <div class="card-body text-center text-secondary py-5">Không có dữ liệu khuyến mãi.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
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
                                            <span class="badge text-bg-info">Phần trăm</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-warning">Số tiền</span>
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
                                            <span class="badge text-bg-success">Hoạt động</span>
                                        <?php elseif ($item['trang_thai'] === 'DA_HET_HAN'): ?>
                                            <span class="badge text-bg-secondary">Đã hết hạn</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-warning">Tạm dừng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a class="btn btn-sm btn-outline-primary" href="/admin/khuyen-mai/sua?id=<?= (int)$item['id'] ?>">Sửa</a>
                                            <a class="btn btn-sm btn-outline-info" href="/admin/khuyen-mai/lien-ket-san-pham?id=<?= (int)$item['id'] ?>">Sản phẩm</a>
                                            <form method="POST" action="/admin/khuyen-mai/xoa?id=<?= (int)$item['id'] ?>" onsubmit="return confirm('Xóa khuyến mãi này?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="card-body border-top">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
