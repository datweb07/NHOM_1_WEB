<?php
$pageTitle = 'Thanh toán thất bại - FPT Shop';
ob_start();

$donHang = $donHang ?? null;
$errorMessage = $errorMessage ?? 'Giao dịch thanh toán không thành công';
$errorCode = $errorCode ?? null;
?>

<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="fa fa-times-circle text-danger" style="font-size:5rem;"></i>
                </div>
                <h2 class="h3 fw-bold mb-2">Thanh toán thất bại</h2>
                <p class="text-muted">Rất tiếc, giao dịch của bạn không thể hoàn tất.</p>
            </div>


            <div class="card border-danger shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-danger">
                        <i class="fa fa-exclamation-triangle me-2"></i>Lý do thất bại
                    </h5>
                    
                    <div class="alert alert-danger mb-0">
                        <div class="mb-2"><?= htmlspecialchars($errorMessage) ?></div>
                        <?php if ($errorCode): ?>
                            <small class="text-muted">Mã lỗi: <?= htmlspecialchars($errorCode) ?></small>
                        <?php endif; ?>
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
                    
                    <div class="row">
                        <div class="col-sm-5 text-muted">Trạng thái:</div>
                        <div class="col-sm-7">
                            <span class="badge bg-danger">Thanh toán thất bại</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>


            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fa fa-lightbulb text-warning me-2"></i>Các giải pháp thường gặp
                    </h5>
                    
                    <ul class="mb-0">
                        <li class="mb-2">Kiểm tra lại số dư tài khoản hoặc hạn mức thẻ</li>
                        <li class="mb-2">Đảm bảo thông tin thẻ được nhập chính xác</li>
                        <li class="mb-2">Kiểm tra kết nối internet ổn định</li>
                        <li class="mb-2">Thử lại sau vài phút nếu hệ thống đang bận</li>
                        <li class="mb-0">Liên hệ ngân hàng nếu vấn đề vẫn tiếp diễn</li>
                    </ul>
                </div>
            </div>


            <div class="d-flex flex-column gap-3">
                <?php if ($donHang): ?>
                <a href="/don-hang/<?= $donHang['id'] ?>" class="btn btn-danger btn-lg">
                    <i class="fa fa-redo me-2"></i>Thử thanh toán lại
                </a>
                <?php else: ?>
                <a href="/gio-hang" class="btn btn-danger btn-lg">
                    <i class="fa fa-shopping-cart me-2"></i>Quay lại giỏ hàng
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

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
