<?php
require_once dirname(__DIR__) . '/layouts/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Khuyến mãi hot</h2>

    <?php if (empty($khuyenMais)): ?>
        <div class="alert alert-info">
            Hiện tại chưa có chương trình khuyến mãi nào.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($khuyenMais as $km): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($km['ten_khuyen_mai']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($km['mo_ta'] ?? '') ?></p>
                            
                            <div class="mb-2">
                                <?php if ($km['loai_giam'] === 'PHAN_TRAM'): ?>
                                    <span class="badge bg-danger">Giảm <?= $km['gia_tri_giam'] ?>%</span>
                                    <?php if ($km['giam_toi_da']): ?>
                                        <span class="badge bg-secondary">Tối đa <?= number_format($km['giam_toi_da']) ?>đ</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-danger">Giảm <?= number_format($km['gia_tri_giam']) ?>đ</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($km['ngay_bat_dau'] || $km['ngay_ket_thuc']): ?>
                                <p class="text-muted small mb-2">
                                    <?php if ($km['ngay_bat_dau']): ?>
                                        Từ: <?= date('d/m/Y', strtotime($km['ngay_bat_dau'])) ?>
                                    <?php endif; ?>
                                    <?php if ($km['ngay_ket_thuc']): ?>
                                        - Đến: <?= date('d/m/Y', strtotime($km['ngay_ket_thuc'])) ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <a href="/khuyen-mai/chi-tiet?id=<?= $km['id'] ?>" class="btn btn-primary btn-sm">
                                Xem sản phẩm
                            </a>
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

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
