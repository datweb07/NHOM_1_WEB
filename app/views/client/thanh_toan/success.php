<?php
$pageTitle = 'Đặt hàng thành công - FPT Shop';
ob_start();
?>

<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <i class="fa fa-check-circle text-success" style="font-size:5rem;"></i>
            </div>
            <h2 class="h4 fw-bold mb-2">Đặt hàng thành công!</h2>
            <p class="text-muted mb-1">Cảm ơn bạn đã mua hàng tại FPT Shop.</p>
            <p class="text-muted mb-4">Chúng tôi sẽ liên hệ xác nhận đơn hàng trong thời gian sớm nhất.</p>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fa fa-info-circle me-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-3 justify-content-center mt-3">
                <a href="/don-hang" class="btn btn-danger">
                    <i class="fa fa-file-invoice me-2"></i>Xem đơn hàng
                </a>
                <a href="/" class="btn btn-outline-secondary">
                    <i class="fa fa-home me-2"></i>Về trang chủ
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
