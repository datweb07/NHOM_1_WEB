<?php
require_once dirname(__DIR__) . '/layouts/header.php';
?>

<?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Thanh Toán', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">

<!-- Success/Error Messages -->
<?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        $successMessages = [
            'approved' => 'Đã duyệt thanh toán thành công.',
            'rejected' => 'Đã từ chối thanh toán.',
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
            'invalid_id' => 'ID thanh toán không hợp lệ.',
            'not_found' => 'Không tìm thấy thanh toán.',
        ];
        echo htmlspecialchars($errorMessages[$_GET['error']] ?? 'Có lỗi xảy ra.');
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Main Card -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Danh Sách Thanh Toán Chờ Duyệt</div>
    </div>
    <div class="card-body">
        <!-- Payments Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Phương thức</th>
                        <th>Số tiền</th>
                        <th>Ngày thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($danhSachThanhToan)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Không có thanh toán chờ duyệt.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSachThanhToan as $item): ?>
                            <?php
                            $statusBadge = [
                                'CHO_DUYET' => '<span class="badge bg-warning">Chờ duyệt</span>',
                                'DA_DUYET' => '<span class="badge bg-success">Đã duyệt</span>',
                                'TU_CHOI' => '<span class="badge bg-danger">Từ chối</span>',
                            ];
                            
                            $methodLabels = [
                                'COD' => 'COD',
                                'CHUYEN_KHOAN' => 'Chuyển khoản',
                                'QR' => 'QR Code',
                                'TRA_GOP' => 'Trả góp',
                                'VI_DIEN_TU' => 'Ví điện tử',
                            ];
                            ?>
                            <tr>
                                <td>#<?= (int)$item['id'] ?></td>
                                <td><strong><?= htmlspecialchars($item['ma_don_hang'] ?? '-') ?></strong></td>
                                <td>
                                    <div><?= htmlspecialchars($item['customer_name'] ?? 'Khách vãng lai') ?></div>
                                    <?php if (!empty($item['customer_email'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars($item['customer_email']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-credit-card"></i>
                                        <?= $methodLabels[$item['phuong_thuc']] ?? htmlspecialchars($item['phuong_thuc']) ?>
                                    </span>
                                </td>
                                <td><strong><?= number_format((float)($item['so_tien'] ?? 0), 0, ',', '.') ?> ₫</strong></td>
                                <td><?= htmlspecialchars($item['ngay_thanh_toan'] ?? '-') ?></td>
                                <td><?= $statusBadge[$item['trang_thai_duyet']] ?? htmlspecialchars($item['trang_thai_duyet']) ?></td>
                                <td>
                                    <a href="/admin/thanh-toan/chi-tiet?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-primary">
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
                    $currentPage = $currentPage ?? 1;
                    $totalPages = $totalPages ?? 1;
                    ?>
                    
                    <!-- Previous Button -->
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage > 1 ? '/admin/thanh-toan?page=' . ($currentPage - 1) : '#' ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php
                    // Show page numbers
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    if ($startPage > 1): ?>
                        <li class="page-item"><a class="page-link" href="/admin/thanh-toan?page=1">1</a></li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="/admin/thanh-toan?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item"><a class="page-link" href="/admin/thanh-toan?page=<?= $totalPages ?>"><?= $totalPages ?></a></li>
                    <?php endif; ?>
                    
                    <!-- Next Button -->
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage < $totalPages ? '/admin/thanh-toan?page=' . ($currentPage + 1) : '#' ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="text-center text-muted mt-2">
                <small>Hiển thị <?= count($danhSachThanhToan) ?> / <?= $totalRecords ?? 0 ?> thanh toán</small>
            </div>
        <?php endif; ?>
    </div>
</div>

        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
