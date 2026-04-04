<?php
$pageTitle = htmlspecialchars($khuyenMai['ten_khuyen_mai']) . ' - FPT Shop';
ob_start();
?>

<div class="container-xl py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-danger text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/khuyen-mai" class="text-danger text-decoration-none">Khuyến mãi</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($khuyenMai['ten_khuyen_mai']) ?></li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-2">
                <h1 class="h4 fw-bold mb-0"><?= htmlspecialchars($khuyenMai['ten_khuyen_mai']) ?></h1>
                <?php if ($khuyenMai['loai_giam'] === 'PHAN_TRAM'): ?>
                    <span class="badge bg-danger">Giảm <?= $khuyenMai['gia_tri_giam'] ?>%</span>
                <?php else: ?>
                    <span class="badge bg-danger">Giảm <?= number_format($khuyenMai['gia_tri_giam'], 0, ',', '.') ?>đ</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($khuyenMai['mo_ta'])): ?>
                <p class="text-muted mb-2"><?= htmlspecialchars($khuyenMai['mo_ta']) ?></p>
            <?php endif; ?>
            <?php if ($khuyenMai['ngay_bat_dau'] || $khuyenMai['ngay_ket_thuc']): ?>
                <p class="small text-muted mb-0">
                    <i class="fa fa-clock me-1"></i>
                    <?php if ($khuyenMai['ngay_bat_dau']): ?>Từ <?= date('d/m/Y', strtotime($khuyenMai['ngay_bat_dau'])) ?> <?php endif; ?>
                    <?php if ($khuyenMai['ngay_ket_thuc']): ?>- Đến <?= date('d/m/Y', strtotime($khuyenMai['ngay_ket_thuc'])) ?><?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <h5 class="fw-bold mb-3">Sản phẩm áp dụng</h5>

    <?php if (empty($sanPhams)): ?>
        <div class="text-center py-4 text-muted">
            <i class="fa fa-box-open mb-2" style="font-size:2.5rem;"></i>
            <p class="mb-0">Chưa có sản phẩm nào áp dụng khuyến mãi này</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($sanPhams as $sp): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? '/public/assets/client/images/products/14.png') ?>"
                                 class="card-img-top p-2"
                                 alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>"
                                 style="height:150px;object-fit:contain;">
                            <div class="card-body pt-0 px-3 pb-3">
                                <p class="small mb-1 text-dark" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                </p>
                                <p class="text-danger fw-bold mb-0 small"><?= number_format($sp['gia_hien_thi'], 0, ',', '.') ?>đ</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
