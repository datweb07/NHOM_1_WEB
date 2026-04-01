<?php
require_once dirname(__DIR__) . '/layouts/header.php';
?>

<?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Đơn Hàng', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <div>
        <!-- Có thể thêm nút export hoặc các action khác ở đây -->
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        $successMessages = [
            'status_updated' => 'Cập nhật trạng thái thành công.',
        ];
        echo htmlspecialchars($successMessages[$_GET['success']] ?? 'Thao tác thành công.');
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php
        $errorMessages = [
            'invalid_id' => 'ID đơn hàng không hợp lệ.',
            'not_found' => 'Không tìm thấy đơn hàng.',
            'invalid_status' => 'Trạng thái không hợp lệ.',
        ];
        echo htmlspecialchars($errorMessages[$_GET['error']] ?? 'Có lỗi xảy ra.');
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Main Card -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Danh Sách Đơn Hàng</div>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="/admin/don-hang" class="row g-3 mb-4">
            <div class="col-md-3">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Tìm theo mã đơn hoặc tên KH..." 
                    value="<?= htmlspecialchars($searchFilter ?? '') ?>"
                >
            </div>
            <div class="col-md-2">
                <select name="trang_thai" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="CHO_DUYET" <?= ($trangThaiFilter ?? '') === 'CHO_DUYET' ? 'selected' : '' ?>>Chờ duyệt</option>
                    <option value="DA_XAC_NHAN" <?= ($trangThaiFilter ?? '') === 'DA_XAC_NHAN' ? 'selected' : '' ?>>Đã xác nhận</option>
                    <option value="DANG_GIAO" <?= ($trangThaiFilter ?? '') === 'DANG_GIAO' ? 'selected' : '' ?>>Đang giao</option>
                    <option value="DA_GIAO" <?= ($trangThaiFilter ?? '') === 'DA_GIAO' ? 'selected' : '' ?>>Đã giao</option>
                    <option value="HOAN_THANH" <?= ($trangThaiFilter ?? '') === 'HOAN_THANH' ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="DA_HUY" <?= ($trangThaiFilter ?? '') === 'DA_HUY' ? 'selected' : '' ?>>Đã hủy</option>
                    <option value="TRA_HANG" <?= ($trangThaiFilter ?? '') === 'TRA_HANG' ? 'selected' : '' ?>>Trả hàng</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="phuong_thuc" class="form-select">
                    <option value="">Tất cả phương thức</option>
                    <option value="COD" <?= ($phuongThucFilter ?? '') === 'COD' ? 'selected' : '' ?>>COD</option>
                    <option value="CHUYEN_KHOAN" <?= ($phuongThucFilter ?? '') === 'CHUYEN_KHOAN' ? 'selected' : '' ?>>Chuyển khoản</option>
                    <option value="QR" <?= ($phuongThucFilter ?? '') === 'QR' ? 'selected' : '' ?>>QR Code</option>
                    <option value="TRA_GOP" <?= ($phuongThucFilter ?? '') === 'TRA_GOP' ? 'selected' : '' ?>>Trả góp</option>
                    <option value="VI_DIEN_TU" <?= ($phuongThucFilter ?? '') === 'VI_DIEN_TU' ? 'selected' : '' ?>>Ví điện tử</option>
                </select>
            </div>
            <div class="col-md-2">
                <input 
                    type="date" 
                    name="date_from" 
                    class="form-control" 
                    value="<?= htmlspecialchars($dateFromFilter ?? '') ?>"
                >
            </div>
            <div class="col-md-2">
                <input 
                    type="date" 
                    name="date_to" 
                    class="form-control" 
                    value="<?= htmlspecialchars($dateToFilter ?? '') ?>"
                >
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
            </div>
        </form>

        <!-- Orders Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($danhSachDonHang)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Không có đơn hàng nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSachDonHang as $item): ?>
                            <?php
                            $statusBadge = [
                                'CHO_DUYET' => '<span class="badge bg-warning">Chờ duyệt</span>',
                                'DA_XAC_NHAN' => '<span class="badge bg-info">Đã xác nhận</span>',
                                'DANG_GIAO' => '<span class="badge bg-primary">Đang giao</span>',
                                'DA_GIAO' => '<span class="badge bg-success">Đã giao</span>',
                                'HOAN_THANH' => '<span class="badge bg-success">Hoàn thành</span>',
                                'DA_HUY' => '<span class="badge bg-danger">Đã hủy</span>',
                                'TRA_HANG' => '<span class="badge bg-secondary">Trả hàng</span>',
                            ];
                            ?>
                            <tr>
                                <td>#<?= (int)$item['id'] ?></td>
                                <td><strong><?= htmlspecialchars($item['ma_don_hang'] ?? '-') ?></strong></td>
                                <td>
                                    <div><?= htmlspecialchars($item['ho_ten'] ?? 'Khách vãng lai') ?></div>
                                    <?php if (!empty($item['email'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars($item['email']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= $statusBadge[$item['trang_thai']] ?? htmlspecialchars($item['trang_thai']) ?></td>
                                <td><strong><?= number_format((float)($item['tong_thanh_toan'] ?? 0), 0, ',', '.') ?> ₫</strong></td>
                                <td><?= htmlspecialchars($item['ngay_tao'] ?? '-') ?></td>
                                <td>
                                    <a href="/admin/don-hang/chi-tiet?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (($totalPages ?? 1) > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php
                    // Build query string for pagination links
                    $queryParams = [];
                    if (!empty($searchFilter)) $queryParams['search'] = $searchFilter;
                    if (!empty($trangThaiFilter)) $queryParams['trang_thai'] = $trangThaiFilter;
                    if (!empty($phuongThucFilter)) $queryParams['phuong_thuc'] = $phuongThucFilter;
                    if (!empty($dateFromFilter)) $queryParams['date_from'] = $dateFromFilter;
                    if (!empty($dateToFilter)) $queryParams['date_to'] = $dateToFilter;
                    
                    $buildUrl = function($page) use ($queryParams) {
                        $queryParams['page'] = $page;
                        return '/admin/don-hang?' . http_build_query($queryParams);
                    };
                    
                    $currentPage = $currentPage ?? 1;
                    $totalPages = $totalPages ?? 1;
                    ?>
                    
                    <!-- Previous Button -->
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage > 1 ? $buildUrl($currentPage - 1) : '#' ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php
                    // Show page numbers
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    if ($startPage > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= $buildUrl(1) ?>">1</a></li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $buildUrl($i) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= $buildUrl($totalPages) ?>"><?= $totalPages ?></a></li>
                    <?php endif; ?>
                    
                    <!-- Next Button -->
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage < $totalPages ? $buildUrl($currentPage + 1) : '#' ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="text-center text-muted mt-2">
                <small>Hiển thị <?= count($danhSachDonHang) ?> / <?= $totalRecords ?? 0 ?> đơn hàng</small>
            </div>
        <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
