<?php
require_once dirname(__DIR__) . '/layouts/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Mã giảm giá</h2>

    <?php if (empty($maGiamGias)): ?>
        <div class="alert alert-info">
            Hiện tại chưa có mã giảm giá nào.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($maGiamGias as $mgg): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="card-title"><?= htmlspecialchars($mgg['ten_ma']) ?></h5>
                                    <p class="card-text text-muted"><?= htmlspecialchars($mgg['mo_ta'] ?? '') ?></p>
                                    
                                    <div class="mb-2">
                                        <?php if ($mgg['loai_giam'] === 'PHAN_TRAM'): ?>
                                            <span class="badge bg-danger">Giảm <?= $mgg['gia_tri_giam'] ?>%</span>
                                            <?php if ($mgg['giam_toi_da']): ?>
                                                <span class="badge bg-secondary">Tối đa <?= number_format($mgg['giam_toi_da']) ?>đ</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Giảm <?= number_format($mgg['gia_tri_giam']) ?>đ</span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($mgg['gia_tri_don_toi_thieu']): ?>
                                        <p class="text-muted small mb-1">
                                            Đơn tối thiểu: <?= number_format($mgg['gia_tri_don_toi_thieu']) ?>đ
                                        </p>
                                    <?php endif; ?>

                                    <?php if ($mgg['ngay_het_han']): ?>
                                        <p class="text-muted small mb-0">
                                            HSD: <?= date('d/m/Y', strtotime($mgg['ngay_het_han'])) ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if ($mgg['so_luong_con_lai']): ?>
                                        <p class="text-warning small mb-0">
                                            Còn lại: <?= $mgg['so_luong_con_lai'] ?> mã
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="border border-dashed border-primary p-3 rounded">
                                        <code class="fs-5 fw-bold text-primary"><?= htmlspecialchars($mgg['ma_code']) ?></code>
                                        <button class="btn btn-sm btn-outline-primary mt-2 w-100 btn-copy-code" 
                                                data-code="<?= htmlspecialchars($mgg['ma_code']) ?>">
                                            <i class="bi bi-clipboard"></i> Sao chép
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($tongTrang > 1): ?>
            <nav>
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
document.addEventListener('DOMContentLoaded', function() {
    // Sao chép mã
    document.querySelectorAll('.btn-copy-code').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.dataset.code;
            navigator.clipboard.writeText(code).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check"></i> Đã sao chép';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
        });
    });
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
