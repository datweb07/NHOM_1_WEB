<?php
$pageTitle = 'Mã giảm giá - FPT Shop';
ob_start();
?>

<div class="container-xl py-4">
    <h1 class="h4 mb-4 fw-bold"><i class="fa fa-ticket text-danger me-2"></i>Mã giảm giá</h1>

    <?php if (empty($maGiamGias)): ?>
        <div class="text-center py-5">
            <i class="fa fa-ticket text-muted" style="font-size:4rem;"></i>
            <p class="mt-3 text-muted fs-5">Hiện tại chưa có mã giảm giá nào</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($maGiamGias as $mgg): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center g-3">
                                <div class="col-8">
                                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($mgg['ten_ma']) ?></h6>
                                    <p class="text-muted small mb-2"><?= htmlspecialchars($mgg['mo_ta'] ?? '') ?></p>
                                    <div class="mb-1">
                                        <?php if ($mgg['loai_giam'] === 'PHAN_TRAM'): ?>
                                            <span class="badge bg-danger">Giảm <?= $mgg['gia_tri_giam'] ?>%</span>
                                            <?php if ($mgg['giam_toi_da']): ?>
                                                <span class="badge bg-secondary">Tối đa <?= number_format($mgg['giam_toi_da'], 0, ',', '.') ?>đ</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Giảm <?= number_format($mgg['gia_tri_giam'], 0, ',', '.') ?>đ</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($mgg['gia_tri_don_toi_thieu']): ?>
                                        <p class="small text-muted mb-1">Đơn tối thiểu: <?= number_format($mgg['gia_tri_don_toi_thieu'], 0, ',', '.') ?>đ</p>
                                    <?php endif; ?>
                                    <?php if ($mgg['ngay_het_han']): ?>
                                        <p class="small text-muted mb-0"><i class="fa fa-clock me-1"></i>HSD: <?= date('d/m/Y', strtotime($mgg['ngay_het_han'])) ?></p>
                                    <?php endif; ?>
                                    <?php if ($mgg['so_luong_con_lai']): ?>
                                        <p class="small text-warning mb-0">Còn lại: <?= $mgg['so_luong_con_lai'] ?> mã</p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="border border-2 border-danger border-dashed rounded p-2 mb-2">
                                        <code class="fs-6 fw-bold text-danger d-block"><?= htmlspecialchars($mgg['ma_code']) ?></code>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm w-100 btn-copy-code"
                                            data-code="<?= htmlspecialchars($mgg['ma_code']) ?>">
                                        <i class="fa fa-copy me-1"></i>Sao chép
                                    </button>
                                </div>
                            </div>
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

<script>
document.querySelectorAll('.btn-copy-code').forEach(btn => {
    btn.addEventListener('click', function() {
        navigator.clipboard.writeText(this.dataset.code).then(() => {
            const orig = this.innerHTML;
            this.innerHTML = '<i class="fa fa-check me-1"></i>Đã sao chép';
            setTimeout(() => this.innerHTML = orig, 2000);
        });
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
