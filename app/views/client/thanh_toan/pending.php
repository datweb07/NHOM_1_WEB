<?php
$pageTitle = 'Đang xử lý thanh toán - FPT Shop';
ob_start();

$donHang = $donHang ?? null;
$transactionId = $transactionId ?? null;
?>

<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="fa fa-clock text-warning" style="font-size:5rem;"></i>
                </div>
                <h2 class="h3 fw-bold mb-2">Đang xử lý thanh toán</h2>
                <p class="text-muted">Giao dịch của bạn đang được xử lý. Vui lòng đợi trong giây lát.</p>
            </div>


            <div class="card border-warning shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-warning">
                        <i class="fa fa-info-circle me-2"></i>Trạng thái giao dịch
                    </h5>
                    
                    <div class="alert alert-warning mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Đang xử lý...</span>
                            </div>
                            <div>
                                <div class="fw-medium mb-1">Giao dịch đang được xử lý</div>
                                <small class="text-muted">Thời gian xử lý ước tính: <strong>15 phút</strong></small>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded">
                        <p class="mb-2"><strong>Lưu ý quan trọng:</strong></p>
                        <ul class="mb-0 small">
                            <li class="mb-1">Không đóng trình duyệt hoặc tải lại trang trong quá trình xử lý</li>
                            <li class="mb-1">Hệ thống sẽ tự động cập nhật kết quả thanh toán</li>
                            <li class="mb-1">Nếu sau 15 phút vẫn chưa có kết quả, vui lòng kiểm tra lại trạng thái đơn hàng</li>
                        </ul>
                    </div>
                </div>
            </div>


            <?php if ($donHang): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 pb-3 border-bottom">
                        <i class="fa fa-file-invoice text-danger me-2"></i>Thông tin đơn hàng
                    </h5>
                    
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Mã đơn hàng:</div>
                        <div class="col-sm-7 fw-bold"><?= htmlspecialchars($donHang['ma_don_hang'] ?? '') ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Tổng thanh toán:</div>
                        <div class="col-sm-7 fw-bold text-danger"><?= number_format($donHang['tong_thanh_toan'] ?? 0, 0, ',', '.') ?>đ</div>
                    </div>
                    
                    <?php if ($transactionId): ?>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Mã giao dịch:</div>
                        <div class="col-sm-7"><code><?= htmlspecialchars($transactionId) ?></code></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-sm-5 text-muted">Trạng thái:</div>
                        <div class="col-sm-7">
                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>


            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fa fa-question-circle text-info me-2"></i>Điều gì sẽ xảy ra tiếp theo?
                    </h5>
                    
                    <div class="timeline">
                        <div class="d-flex gap-3 mb-3">
                            <div class="text-success" style="width:30px;">
                                <i class="fa fa-check-circle fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-medium">Đơn hàng đã được tạo</div>
                                <small class="text-muted">Thông tin đơn hàng đã được lưu vào hệ thống</small>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3 mb-3">
                            <div class="text-warning" style="width:30px;">
                                <i class="fa fa-spinner fa-spin fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-medium">Đang xác nhận thanh toán</div>
                                <small class="text-muted">Hệ thống đang xác minh giao dịch với ngân hàng</small>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <div class="text-muted" style="width:30px;">
                                <i class="fa fa-circle fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-medium text-muted">Xác nhận và giao hàng</div>
                                <small class="text-muted">Sau khi thanh toán thành công, đơn hàng sẽ được xử lý</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="d-flex flex-column gap-3">
                <?php if ($donHang): ?>
                <button type="button" class="btn btn-danger btn-lg" onclick="checkPaymentStatus()">
                    <i class="fa fa-sync-alt me-2"></i>Kiểm tra trạng thái thanh toán
                </button>
                
                <a href="/don-hang/<?= $donHang['id'] ?>" class="btn btn-outline-primary btn-lg">
                    <i class="fa fa-eye me-2"></i>Xem chi tiết đơn hàng
                </a>
                <?php endif; ?>
                
                <a href="/" class="btn btn-outline-secondary btn-lg">
                    <i class="fa fa-home me-2"></i>Về trang chủ
                </a>
                
                <div class="text-center mt-2">
                    <small class="text-muted">
                        Cần hỗ trợ? Liên hệ hotline: <strong class="text-danger">1800 6601</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkPaymentStatus() {
    <?php if ($donHang): ?>
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Đang kiểm tra...';
    
    setTimeout(() => {
        window.location.reload();
    }, 1000);
    <?php endif; ?>
}

setTimeout(() => {
    console.log('Auto-refreshing to check payment status...');
    window.location.reload();
}, 30000);
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
