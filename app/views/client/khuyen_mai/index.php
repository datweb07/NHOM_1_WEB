<?php
$pageTitle = 'Khuyến mãi hot - FPT Shop';
ob_start();
?>

<div class="container-xl py-4">
    <h1 class="h4 mb-4 fw-bold"><i class="fa fa-certificate text-danger me-2"></i>Khuyến mãi hot</h1>

    <?php if (empty($khuyenMais)): ?>
    <div class="text-center py-5">
        <i class="fa fa-certificate text-muted" style="font-size:4rem;"></i>
        <p class="mt-3 text-muted fs-5">Hiện tại chưa có chương trình khuyến mãi nào</p>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($khuyenMais as $km): ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <h6 class="fw-bold mb-0\">
                            <?= htmlspecialchars($km['ten_chuong_trinh'] ?? $km['ten_khuyen_mai'] ?? 'Chương trình khuyến mãi') ?>
                        </h6>
                        <?php if ($km['loai_giam'] === 'PHAN_TRAM'): ?>
                        <span class="badge bg-danger ms-2 text-nowrap">-<?= $km['gia_tri_giam'] ?>%</span>
                        <?php else: ?>
                        <span
                            class="badge bg-danger ms-2 text-nowrap">-<?= number_format($km['gia_tri_giam'], 0, ',', '.') ?>đ</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted small mb-2"><?= htmlspecialchars($km['mo_ta'] ?? '') ?></p>
                    <?php if ($km['giam_toi_da'] && $km['loai_giam'] === 'PHAN_TRAM'): ?>
                    <p class="small text-secondary mb-2">Giảm tối đa:
                        <?= number_format($km['giam_toi_da'], 0, ',', '.') ?>đ</p>
                    <?php endif; ?>
                    <?php if ($km['ngay_bat_dau'] || $km['ngay_ket_thuc']): ?>
                    <p class="small text-muted mb-3">
                        <i class="fa fa-clock me-1"></i>
                        <?php if ($km['ngay_bat_dau']): ?>Từ <?= date('d/m/Y', strtotime($km['ngay_bat_dau'])) ?>
                        <?php endif; ?>
                        <?php if ($km['ngay_ket_thuc']): ?>- Đến
                        <?= date('d/m/Y', strtotime($km['ngay_ket_thuc'])) ?><?php endif; ?>
                    </p>
                    <?php endif; ?>
                    <a href="/khuyen-mai/chi-tiet?id=<?= $km['id'] ?>" class="btn btn-outline-danger btn-sm">
                        Xem sản phẩm <i class="fa fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (($tongTrang ?? 1) > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $tongTrang; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>