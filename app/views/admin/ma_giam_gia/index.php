<?php
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <?php
            $breadcrumbs = [
                ['url' => '/admin/dashboard', 'label' => 'Dashboard'],
                ['url' => '', 'label' => 'Mã Giảm Giá']
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
                                <h3 class="card-title mb-0">Quản lý mã giảm giá</h3>
                                <a href="/admin/ma-giam-gia/them" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Thêm mã giảm giá
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php
                                $messages = [
                                    'created' => 'Tạo mã giảm giá thành công.',
                                    'updated' => 'Cập nhật mã giảm giá thành công.',
                                    'deleted' => 'Xóa mã giảm giá thành công.',
                                ];
                                echo htmlspecialchars($messages[$success] ?? $success);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php
                                $messages = [
                                    'invalid_id' => 'ID không hợp lệ.',
                                    'not_found' => 'Không tìm thấy mã giảm giá.',
                                ];
                                echo htmlspecialchars($messages[$error] ?? $error);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>

                            <!-- Filter Form -->
                            <form method="GET" action="/admin/ma-giam-gia" class="mb-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="trang_thai" class="form-label">Trạng Thái</label>
                                        <select name="trang_thai" id="trang_thai" class="form-select">
                                            <option value="">Tất cả</option>
                                            <option value="HOAT_DONG"
                                                <?= $trangThai === 'HOAT_DONG' ? 'selected' : '' ?>>Hoạt động</option>
                                            <option value="DA_HET_HAN"
                                                <?= $trangThai === 'DA_HET_HAN' ? 'selected' : '' ?>>Đã hết hạn</option>
                                            <option value="HET_LUOT" <?= $trangThai === 'HET_LUOT' ? 'selected' : '' ?>>
                                                Hết lượt</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-funnel"></i> Lọc
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Mã Code</th>
                                            <th>Mô Tả</th>
                                            <th>Loại Giảm</th>
                                            <th>Giá Trị</th>
                                            <th>Đơn Tối Thiểu</th>
                                            <th>Lượt Sử Dụng</th>
                                            <th>Thời Gian</th>
                                            <th>Trạng Thái</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($danhSachMaGiamGia)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center">Không có mã giảm giá nào.</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($danhSachMaGiamGia as $ma): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($ma['id']) ?></td>
                                            <td><strong><?= htmlspecialchars($ma['ma_code']) ?></strong></td>
                                            <td><?= htmlspecialchars($ma['mo_ta'] ?? '') ?></td>
                                            <td>
                                                <?php if ($ma['loai_giam'] === 'PHAN_TRAM'): ?>
                                                <span class="badge bg-info">Phần trăm</span>
                                                <?php else: ?>
                                                <span class="badge bg-success">Số tiền</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($ma['loai_giam'] === 'PHAN_TRAM'): ?>
                                                <?= number_format($ma['gia_tri_giam'], 0) ?>%
                                                <?php if ($ma['giam_toi_da']): ?>
                                                <br><small>(Tối đa:
                                                    <?= number_format($ma['giam_toi_da'], 0) ?>đ)</small>
                                                <?php endif; ?>
                                                <?php else: ?>
                                                <?= number_format($ma['gia_tri_giam'], 0) ?>đ
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($ma['don_toi_thieu'], 0) ?>đ</td>
                                            <td>
                                                <?= $ma['so_luot_da_dung'] ?> /
                                                <?= $ma['gioi_han_su_dung'] !== null ? $ma['gioi_han_su_dung'] : '∞' ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <?= date('d/m/Y', strtotime($ma['ngay_bat_dau'])) ?><br>
                                                    đến<br>
                                                    <?= date('d/m/Y', strtotime($ma['ngay_ket_thuc'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php
                                                    $statusBadges = [
                                                        'HOAT_DONG' => '<span class="badge bg-success">Hoạt động</span>',
                                                        'DA_HET_HAN' => '<span class="badge bg-secondary">Đã hết hạn</span>',
                                                        'HET_LUOT' => '<span class="badge bg-warning">Hết lượt</span>',
                                                    ];
                                                    echo $statusBadges[$ma['trang_thai']] ?? htmlspecialchars($ma['trang_thai']);
                                                    ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="/admin/ma-giam-gia/sua?id=<?= $ma['id'] ?>"
                                                        class="btn btn-sm btn-warning" title="Sửa">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="/admin/ma-giam-gia/xoa?id=<?= $ma['id'] ?>"
                                                        class="btn btn-sm btn-danger" title="Xóa"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?page=<?= $currentPage - 1 ?><?= $trangThai ? '&trang_thai=' . urlencode($trangThai) : '' ?>">
                                            Trước
                                        </a>
                                    </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="?page=<?= $i ?><?= $trangThai ? '&trang_thai=' . urlencode($trangThai) : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                    <?php endfor; ?>

                                    <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?page=<?= $currentPage + 1 ?><?= $trangThai ? '&trang_thai=' . urlencode($trangThai) : '' ?>">
                                            Sau
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <p class="text-center text-muted">
                                Hiển thị <?= ($currentPage - 1) * $limit + 1 ?> -
                                <?= min($currentPage * $limit, $totalMaGiamGia) ?>
                                trong tổng số <?= $totalMaGiamGia ?> mã giảm giá
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>