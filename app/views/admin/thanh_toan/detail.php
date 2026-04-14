<?php
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$successMessages = [
    'approved' => 'Đã duyệt thanh toán thành công.',
    'rejected' => 'Đã từ chối thanh toán.',
    'cod_confirmed' => 'Đã xác nhận thanh toán COD thành công.',
];

$errorMessages = [
    'invalid_id' => 'ID thanh toán không hợp lệ.',
    'not_found' => 'Không tìm thấy thanh toán.',
    'not_cod' => 'Chỉ có thể xác nhận thanh toán COD.',
    'already_processed' => 'Thanh toán đã được xử lý.',
];

$thanhToan = (isset($thanhToan) && is_array($thanhToan)) ? $thanhToan : [];
$donHang = (isset($donHang) && is_array($donHang)) ? $donHang : [];
$transactionLogs = (isset($transactionLogs) && is_array($transactionLogs)) ? $transactionLogs : [];
$paymentId = (int)($thanhToan['id'] ?? 0);
$trangThaiDuyet = (string)($thanhToan['trang_thai_duyet'] ?? '');

// Include Master Layout
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Thanh Toán', 'url' => '/admin/thanh-toan'],
        ['label' => 'Chi Tiết Thanh Toán', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Chi tiết thanh toán #<?= $paymentId > 0 ? $paymentId : '-' ?></h3>
                <a href="/admin/thanh-toan" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            <?php if ($paymentId <= 0): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> Không tìm thấy dữ liệu thanh toán hợp lệ.
                </div>
            <?php endif; ?>

            <?php if (!empty($success) && isset($successMessages[$success])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= e($successMessages[$success]) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error) && isset($errorMessages[$error])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= e($errorMessages[$error]) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title text-uppercase text-muted mb-0">Thông tin thanh toán</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Mã đơn hàng:</div>
                                <div class="col-sm-8 fw-bold text-primary"><?= e($donHang['ma_don_hang'] ?? '-') ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Phương thức:</div>
                                <div class="col-sm-8 fw-bold"><?= e($thanhToan['phuong_thuc'] ?? '-') ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Số tiền:</div>
                                <div class="col-sm-8 text-danger fw-bold fs-5"><?= number_format((float)($thanhToan['so_tien'] ?? 0), 0, ',', '.') ?> đ</div>
                            </div>
                            <?php if (!empty($thanhToan['gateway_transaction_id'])): ?>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Mã giao dịch:</div>
                                    <div class="col-sm-8"><code><?= e($thanhToan['gateway_transaction_id']) ?></code></div>
                                </div>
                            <?php endif; ?>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Ngày tạo:</div>
                                <div class="col-sm-8"><?= e($thanhToan['created_at'] ?? $thanhToan['ngay_thanh_toan'] ?? '-') ?></div>
                            </div>
                            <?php if (!empty($thanhToan['expiration_time'])): ?>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Hết hạn:</div>
                                    <div class="col-sm-8"><?= e($thanhToan['expiration_time']) ?></div>
                                </div>
                            <?php endif; ?>
                            <hr>
                            <div class="row mb-2 align-items-center">
                                <div class="col-sm-4 text-muted">Trạng thái duyệt:</div>
                                <div class="col-sm-8">
                                    <?php if ($trangThaiDuyet === 'CHO_DUYET'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> CHO_DUYET</span>
                                    <?php elseif ($trangThaiDuyet === 'THANH_CONG'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> THANH_CONG</span>
                                    <?php elseif ($trangThaiDuyet === 'THAT_BAI'): ?>
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> THAT_BAI</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= e($trangThaiDuyet) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($thanhToan['ngay_duyet'])): ?>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Ngày duyệt:</div>
                                    <div class="col-sm-8"><?= e($thanhToan['ngay_duyet']) ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($thanhToan['ghi_chu_duyet'])): ?>
                                <div class="row mb-0">
                                    <div class="col-sm-4 text-muted">Ghi chú duyệt:</div>
                                    <div class="col-sm-8 fst-italic text-secondary"><?= e($thanhToan['ghi_chu_duyet']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title text-uppercase text-muted mb-0">Thông tin khách hàng & Đơn hàng</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Khách hàng:</div>
                                <div class="col-sm-8 fw-bold"><?= e($donHang['ho_ten'] ?? 'Khách vãng lai') ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Email:</div>
                                <div class="col-sm-8"><?= e($donHang['email'] ?? '-') ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Số điện thoại:</div>
                                <div class="col-sm-8"><?= e($donHang['sdt'] ?? '-') ?></div>
                            </div>
                            <hr>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Tổng thanh toán:</div>
                                <div class="col-sm-8 fw-bold text-primary"><?= number_format((float)($donHang['tong_thanh_toan'] ?? 0), 0, ',', '.') ?> đ</div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-sm-4 text-muted">Trạng thái đơn:</div>
                                <div class="col-sm-8">
                                    <span class="badge bg-secondary px-2 py-1"><?= e($donHang['trang_thai'] ?? '-') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title text-uppercase text-muted mb-0">Biên lai thanh toán</h6>
                        </div>
                        <div class="card-body text-center">
                            <?php if (!empty($thanhToan['anh_bien_lai'])): ?>
                                <img src="<?= e($thanhToan['anh_bien_lai']) ?>" alt="Biên lai thanh toán" class="img-fluid rounded border shadow-sm" style="max-height: 400px; object-fit: contain;">
                            <?php else: ?>
                                <div class="p-5 bg-light rounded border border-secondary" style="border-style: dashed !important;">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Chưa có biên lai thanh toán</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title text-uppercase text-muted mb-0">Hành động xử lý</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($trangThaiDuyet === 'CHO_DUYET' && ($thanhToan['phuong_thuc'] ?? '') === 'COD'): ?>
                                <h5 class="mb-3">Xác nhận thanh toán COD</h5>
                                <p class="text-muted mb-4">Đánh dấu thanh toán COD này là đã hoàn thành sau khi khách hàng đã thanh toán cho shipper.</p>
                                <form method="POST" action="/admin/thanh-toan/xac-nhan-cod?id=<?= $paymentId ?>" onsubmit="return confirm('Bạn có chắc chắn muốn xác nhận thanh toán COD này đã hoàn thành?');">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Xác nhận đã thu tiền COD
                                    </button>
                                </form>

                            <?php elseif ($trangThaiDuyet === 'CHO_DUYET'): ?>
                                <h5 class="mb-3">Duyệt thanh toán</h5>
                                <form id="approvalForm">
                                    <div class="mb-3">
                                        <label for="ghi_chu" class="form-label fw-bold text-muted">Ghi chú (tùy chọn)</label>
                                        <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3" placeholder="Nhập ghi chú về quyết định duyệt..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="button" class="btn btn-success" onclick="submitApproval('approve')">
                                            <i class="bi bi-check-circle me-1"></i> Duyệt thanh toán
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="submitApproval('reject')">
                                            <i class="bi bi-x-circle me-1"></i> Từ chối thanh toán
                                        </button>
                                    </div>
                                </form>

                            <?php else: ?>
                                <div class="alert alert-secondary border-0 mb-0 d-flex align-items-center">
                                    <i class="bi bi-info-circle fs-4 me-3 text-secondary"></i>
                                    <div>
                                        <h6 class="mb-1">Trạng thái đã đóng</h6>
                                        <p class="mb-0 text-muted">Thanh toán đã được xử lý (<?= e($trangThaiDuyet) ?>), không thể thay đổi trạng thái.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($transactionLogs)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title text-uppercase text-muted mb-0">Lịch sử giao dịch (Transaction Logs)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Thời gian</th>
                                        <th>Gateway</th>
                                        <th>Trạng thái</th>
                                        <th class="pe-3">Chi tiết (JSON)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactionLogs as $log): ?>
                                        <tr>
                                            <td class="ps-3 text-muted"><?= e($log['created_at'] ?? '-') ?></td>
                                            <td class="fw-medium"><?= e($log['gateway_name'] ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $statusClass = 'bg-secondary';
                                                if (($log['status'] ?? '') === 'SUCCESS') $statusClass = 'bg-success';
                                                elseif (($log['status'] ?? '') === 'FAILED') $statusClass = 'bg-danger';
                                                elseif (($log['status'] ?? '') === 'PENDING') $statusClass = 'bg-warning text-dark';
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= e($log['status'] ?? '-') ?></span>
                                            </td>
                                            <td class="pe-3">
                                                <?php if (!empty($log['callback_data'])): ?>
                                                    <details>
                                                        <summary class="text-primary text-decoration-none" style="cursor: pointer;">Xem callback data</summary>
                                                        <pre class="bg-light p-2 rounded mt-2 border" style="font-size: 12px; max-height: 200px; overflow-y: auto;"><code><?= e($log['callback_data']) ?></code></pre>
                                                    </details>
                                                <?php elseif (!empty($log['response_data'])): ?>
                                                    <details>
                                                        <summary class="text-primary text-decoration-none" style="cursor: pointer;">Xem response data</summary>
                                                        <pre class="bg-light p-2 rounded mt-2 border" style="font-size: 12px; max-height: 200px; overflow-y: auto;"><code><?= e($log['response_data']) ?></code></pre>
                                                    </details>
                                                <?php elseif (!empty($log['request_data'])): ?>
                                                    <details>
                                                        <summary class="text-primary text-decoration-none" style="cursor: pointer;">Xem request data</summary>
                                                        <pre class="bg-light p-2 rounded mt-2 border" style="font-size: 12px; max-height: 200px; overflow-y: auto;"><code><?= e($log['request_data']) ?></code></pre>
                                                    </details>
                                                <?php else: ?>
                                                    <span class="text-muted fst-italic">Không có dữ liệu</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div> </div> </main>

<script>
    function submitApproval(action) {
        const ghiChu = document.getElementById('ghi_chu').value;
        const paymentId = <?= $paymentId ?>;
        
        if (action === 'approve') {
            if (confirm('Bạn có chắc chắn muốn duyệt thanh toán này?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/thanh-toan/duyet?id=' + paymentId;
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ghi_chu';
                input.value = ghiChu;
                form.appendChild(input);
                
                document.body.appendChild(form);
                form.submit();
            }
        } else if (action === 'reject') {
            if (confirm('Bạn có chắc chắn muốn từ chối thanh toán này?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/thanh-toan/tu-choi?id=' + paymentId;
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ghi_chu';
                input.value = ghiChu;
                form.appendChild(input);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>