<?php
$pageTitle = 'Thanh toán VietQR - FPT Shop';
ob_start();
?>

<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fa fa-qrcode text-info" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 fw-bold">Thanh toán qua VietQR</h4>
                        <p class="text-muted">Quét mã QR bên dưới để chuyển khoản</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                        <div class="text-center mt-4">
                            <a href="/thanh-toan" class="btn btn-danger">
                                <i class="fa fa-arrow-left me-2"></i>Quay lại trang thanh toán
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- QR Code Display -->
                        <div class="text-center mb-4">
                            <div class="border rounded p-3 bg-light d-inline-block">
                                <img src="<?= htmlspecialchars($qrInfo['qr_url']) ?>" 
                                     alt="VietQR Code" 
                                     class="img-fluid"
                                     style="max-width: 300px; width: 100%;">
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="border rounded p-3 mb-4">
                            <h6 class="fw-bold mb-3">Thông tin chuyển khoản</h6>
                            <div class="row g-2">
                                <div class="col-5 text-muted">Ngân hàng:</div>
                                <div class="col-7 fw-medium"><?= htmlspecialchars($qrInfo['bank_id']) ?></div>
                                
                                <div class="col-5 text-muted">Số tài khoản:</div>
                                <div class="col-7 fw-medium"><?= htmlspecialchars($qrInfo['account_no']) ?></div>
                                
                                <div class="col-5 text-muted">Chủ tài khoản:</div>
                                <div class="col-7 fw-medium"><?= htmlspecialchars($qrInfo['account_name']) ?></div>
                                
                                <div class="col-5 text-muted">Số tiền:</div>
                                <div class="col-7 fw-bold text-danger"><?= number_format($qrInfo['amount'], 0, ',', '.') ?>đ</div>
                                
                                <div class="col-5 text-muted">Nội dung:</div>
                                <div class="col-7 fw-medium text-primary"><?= htmlspecialchars($qrInfo['description']) ?></div>
                            </div>
                        </div>

                        <!-- Order Information -->
                        <div class="border rounded p-3 mb-4">
                            <h6 class="fw-bold mb-3">Thông tin đơn hàng</h6>
                            <div class="row g-2">
                                <div class="col-5 text-muted">Mã đơn hàng:</div>
                                <div class="col-7 fw-medium"><?= htmlspecialchars($donHang['ma_don_hang'] ?? 'N/A') ?></div>
                                
                                <div class="col-5 text-muted">Tổng tiền:</div>
                                <div class="col-7 fw-bold text-danger"><?= number_format($donHang['tong_thanh_toan'] ?? 0, 0, ',', '.') ?>đ</div>
                                
                                <div class="col-5 text-muted">Trạng thái:</div>
                                <div class="col-7">
                                    <span class="badge bg-warning">Chờ thanh toán</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <a href="/don-hang/<?= $donHang['id'] ?? '' ?>" class="btn btn-primary">
                                <i class="fa fa-eye me-2"></i>Xem chi tiết đơn hàng
                            </a>
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="fa fa-home me-2"></i>Về trang chủ
                            </a>
                        </div>

                        <!-- Auto-refresh notice -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fa fa-sync-alt me-1"></i>
                                Trang này sẽ tự động kiểm tra trạng thái thanh toán mỗi 30 giây
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!isset($error)): ?>
<script>
    // Auto-check payment status every 30 seconds
    let checkCount = 0;
    const maxChecks = 20; // Check for 10 minutes (20 * 30 seconds)
    
    function checkPaymentStatus() {
        if (checkCount >= maxChecks) {
            console.log('Stopped checking payment status after 10 minutes');
            return;
        }
        
        checkCount++;
        
        fetch('/thanh-toan/kiem-tra-trang-thai/<?= $thanhToan['id'] ?? '' ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.status === 'THANH_CONG') {
                    // Payment confirmed, redirect to success page
                    window.location.href = '/thanh-toan/thanh-cong?don_hang_id=<?= $donHang['id'] ?? '' ?>';
                } else if (data.status === 'THAT_BAI') {
                    // Payment failed
                    alert('Thanh toán thất bại. Vui lòng thử lại.');
                    window.location.href = '/thanh-toan';
                }
            })
            .catch(error => {
                console.error('Error checking payment status:', error);
            });
    }
    
    // Start checking after 30 seconds
    setTimeout(() => {
        checkPaymentStatus();
        setInterval(checkPaymentStatus, 30000); // Check every 30 seconds
    }, 30000);
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
