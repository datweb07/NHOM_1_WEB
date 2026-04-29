<?php
$pageTitle = 'Tìm kiếm sản phẩm - FPT Shop';
ob_start();
?>

<div class="container-xl py-4">
    <div class="row g-4">

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fa fa-filter text-danger me-2"></i>Bộ lọc</h6>
                    <form method="GET" action="/san-pham">
                        <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Danh mục</label>
                            <select name="danh_muc" class="form-select form-select-sm">
                                <option value="0">Tất cả</option>
                                <?php foreach ($danhMucs as $dm): ?>
                                <option value="<?= $dm['id'] ?>" <?= ($danhMucId == $dm['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dm['ten']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Khoảng giá</label>
                            <div class="row g-1">
                                <div class="col-6">
                                    <input type="number" name="gia_min" class="form-control form-control-sm"
                                        placeholder="Từ" value="<?= $giaMin ?? '' ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="gia_max" class="form-control form-control-sm"
                                        placeholder="Đến" value="<?= $giaMax ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger btn-sm w-100">Áp dụng</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h5 fw-bold mb-0">
                        Kết quả: "<?= htmlspecialchars($keyword ?? '') ?>"
                    </h1>
                    <p class="text-muted small mb-0">Tìm thấy <?= $tongSanPham ?> sản phẩm</p>
                </div>
            </div>

            <?php if (empty($sanPhams)): ?>
            <div class="text-center py-5">
                <i class="fa fa-search text-muted" style="font-size:3rem;"></i>
                <p class="mt-3 text-muted">Không tìm thấy sản phẩm nào phù hợp</p>
                <a href="/san-pham" class="btn btn-danger btn-sm">Xem tất cả sản phẩm</a>
            </div>
            <?php else: ?>
            <div class="row g-3">
                <?php foreach ($sanPhams as $sp): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                                class="card-img-top p-2" alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>"
                                style="height:150px;object-fit:contain;">
                            <div class="card-body pt-0 px-3 pb-3">
                                <p class="small mb-1 text-dark"
                                    style="display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                </p>
                                <p class="text-danger fw-bold mb-0 small">
                                    <?= number_format($sp['gia_hien_thi'], 0, ',', '.') ?>đ</p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (($tongTrang ?? 1) > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $tongTrang; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link"
                            href="?q=<?= urlencode($keyword) ?>&danh_muc=<?= $danhMucId ?>&gia_min=<?= $giaMin ?>&gia_max=<?= $giaMax ?>&page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>