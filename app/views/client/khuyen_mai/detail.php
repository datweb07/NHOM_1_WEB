<?php
require_once dirname(__DIR__) . '/layouts/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/khuyen-mai">Khuyến mãi</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($khuyenMai['ten_khuyen_mai']) ?></li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-body">
            <h2><?= htmlspecialchars($khuyenMai['ten_khuyen_mai']) ?></h2>
            <p class="lead"><?= htmlspecialchars($khuyenMai['mo_ta'] ?? '') ?></p>
            
            <div class="mb-3">
                <?php if ($khuyenMai['loai_giam'] === 'PHAN_TRAM'): ?>
                    <span class="badge bg-danger fs-5">Giảm <?= $khuyenMai['gia_tri_giam'] ?>%</span>
                    <?php if ($khuyenMai['giam_toi_da']): ?>
                        <span class="badge bg-secondary fs-6">Tối đa <?= number_format($khuyenMai['giam_toi_da']) ?>đ</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="badge bg-danger fs-5">Giảm <?= number_format($khuyenMai['gia_tri_giam']) ?>đ</span>
                <?php endif; ?>
            </div>

            <?php if ($khuyenMai['ngay_bat_dau'] || $khuyenMai['ngay_ket_thuc']): ?>
                <p class="text-muted">
                    <i class="bi bi-calendar"></i>
                    <?php if ($khuyenMai['ngay_bat_dau']): ?>
                        Từ: <?= date('d/m/Y H:i', strtotime($khuyenMai['ngay_bat_dau'])) ?>
                    <?php endif; ?>
                    <?php if ($khuyenMai['ngay_ket_thuc']): ?>
                        - Đến: <?= date('d/m/Y H:i', strtotime($khuyenMai['ngay_ket_thuc'])) ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <h4 class="mb-3">Sản phẩm áp dụng</h4>

    <?php if (empty($sanPhams)): ?>
        <div class="alert alert-info">
            Chưa có sản phẩm nào áp dụng khuyến mãi này.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($sanPhams as $sp): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="<?= $sp['anh_chinh'] ?? '/assets/images/no-image.png' ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($sp['ten_san_pham']) ?></h6>
                            <p class="text-danger fw-bold"><?= number_format($sp['gia_hien_thi']) ?>đ</p>
                            <a href="/san-pham/<?= $sp['slug'] ?>" class="btn btn-primary btn-sm w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
