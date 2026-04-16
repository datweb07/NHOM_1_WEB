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


<div class="card">
    <div class="card-header">
        <div class="card-title">Quản Lý Thanh Toán</div>
    </div>
    <div class="card-body">

        <form method="GET" action="/admin/thanh-toan" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">Phương thức</label>
                    <select name="payment_method" id="payment_method" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="COD" <?= ($paymentMethod ?? '') === 'COD' ? 'selected' : '' ?>>COD</option>
                        <option value="CHUYEN_KHOAN" <?= ($paymentMethod ?? '') === 'CHUYEN_KHOAN' ? 'selected' : '' ?>>VNPay</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="CHO_DUYET" <?= ($status ?? '') === 'CHO_DUYET' ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="THANH_CONG" <?= ($status ?? '') === 'THANH_CONG' ? 'selected' : '' ?>>Thành công</option>
                        <option value="THAT_BAI" <?= ($status ?? '') === 'THAT_BAI' ? 'selected' : '' ?>>Thất bại</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Mã đơn, mã giao dịch, tên khách hàng..." 
                           value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Lọc
                    </button>
                </div>
            </div>
        </form>


        <div class="mb-3">
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-file-earmark-spreadsheet"></i> Xuất CSV
            </button>
        </div>


        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Phương thức</th>
                        <th>Mã giao dịch</th>
                        <th>Số tiền</th>
                        <th>Ngày thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($danhSachThanhToan)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Không tìm thấy thanh toán nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSachThanhToan as $item): ?>
                            <?php
                            $statusBadge = [
                                'CHO_DUYET' => '<span class="badge bg-warning">Chờ duyệt</span>',
                                'THANH_CONG' => '<span class="badge bg-success">Thành công</span>',
                                'THAT_BAI' => '<span class="badge bg-danger">Thất bại</span>',
                            ];
                            
                            $methodLabels = [
                                'COD' => 'COD',
                                'CHUYEN_KHOAN' => 'VNPay',
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
                                <td>
                                    <?php if (!empty($item['gateway_transaction_id'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars($item['gateway_transaction_id']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
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


        <?php if (($totalPages ?? 1) > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php
                    $currentPage = $currentPage ?? 1;
                    $totalPages = $totalPages ?? 1;
                    
                    $queryParams = [];
                    if (!empty($paymentMethod)) $queryParams['payment_method'] = $paymentMethod;
                    if (!empty($status)) $queryParams['status'] = $status;
                    if (!empty($search)) $queryParams['search'] = $search;
                    $queryString = !empty($queryParams) ? '&' . http_build_query($queryParams) : '';
                    ?>
                    

                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage > 1 ? '/admin/thanh-toan?page=' . ($currentPage - 1) . $queryString : '#' ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    if ($startPage > 1): ?>
                        <li class="page-item"><a class="page-link" href="/admin/thanh-toan?page=1<?= $queryString ?>">1</a></li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="/admin/thanh-toan?page=<?= $i ?><?= $queryString ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item"><a class="page-link" href="/admin/thanh-toan?page=<?= $totalPages ?><?= $queryString ?>"><?= $totalPages ?></a></li>
                    <?php endif; ?>
                    

                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage < $totalPages ? '/admin/thanh-toan?page=' . ($currentPage + 1) . $queryString : '#' ?>">
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


<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Xuất dữ liệu CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="/admin/thanh-toan/xuat-csv">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="from_date" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" 
                               value="<?= date('Y-m-d', strtotime('-30 days')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="to_date" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <p class="text-muted small">
                        <i class="bi bi-info-circle"></i> 
                        File CSV sẽ chứa tất cả giao dịch trong khoảng thời gian đã chọn.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-download"></i> Tải xuống
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
