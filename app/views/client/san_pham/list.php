<?php
$pageTitle = 'Danh sách sản phẩm - FPT Shop';
ob_start();
?>

<style>
    /* CSS thêm hiệu ứng scale ảnh giống mục Gợi ý cho bạn */
    .product-img-wrapper {
        overflow: hidden;
        /* Ngăn ảnh tràn ra ngoài khi phóng to */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .card {
        transition: box-shadow 0.3s ease;
    }

    .product-img {
        transition: transform 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);

        transform-origin: center center;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
        transform: translateZ(0);
        will-change: transform;
    }

    .card:hover .product-img {
        transform: scale(1.05) translateZ(0);
    }
</style>

<div class="container-xl py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-danger text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active">Sản phẩm</li>
        </ol>
    </nav>

    <div class="row g-4">

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fa fa-sliders text-danger me-2"></i>Lọc sản phẩm</h6>
                    <form method="GET" action="/san-pham" id="filter-form">
                        <?php if (!empty($keyword)): ?>
                            <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Danh mục</label>
                            <select name="danh_muc" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="0">Tất cả danh mục</option>
                                <?php foreach ($danhMucList as $dm): ?>
                                    <option value="<?= $dm['id'] ?>" <?= ($danhMucId == $dm['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dm['ten']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Giá từ</label>
                            <input type="number" name="gia_min" class="form-control form-control-sm" placeholder="VD: 2000000" value="<?= $giaMin ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Giá đến</label>
                            <input type="number" name="gia_max" class="form-control form-control-sm" placeholder="VD: 10000000" value="<?= $giaMax ?? '' ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Sắp xếp theo</label>
                            <select name="sort_by" class="form-select form-select-sm">
                                <option value="ngay_tao" <?= ($sortBy ?? '') === 'ngay_tao' ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="gia_hien_thi" <?= ($sortBy ?? '') === 'gia_hien_thi' ? 'selected' : '' ?>>Giá</option>
                                <option value="ten_san_pham" <?= ($sortBy ?? '') === 'ten_san_pham' ? 'selected' : '' ?>>Tên</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="sort_order" class="form-select form-select-sm">
                                <option value="DESC" <?= ($sortOrder ?? '') === 'DESC' ? 'selected' : '' ?>>Cao → Thấp / Mới → Cũ</option>
                                <option value="ASC" <?= ($sortOrder ?? '') === 'ASC' ? 'selected' : '' ?>>Thấp → Cao / Cũ → Mới</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fa fa-filter me-1"></i>Áp dụng
                        </button>
                        <a href="/san-pham" class="btn btn-outline-secondary btn-sm w-100 mt-2">Xóa bộ lọc</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h1 class="h5 fw-bold mb-0">
                    <?php if (!empty($keyword)): ?>
                        Kết quả cho "<?= htmlspecialchars($keyword) ?>"
                    <?php else: ?>
                        Tất cả sản phẩm
                    <?php endif; ?>
                </h1>
                <span class="text-muted small">Tìm thấy <strong><?= $tongSanPham ?></strong> sản phẩm</span>
            </div>

            <?php if (empty($sanPhamList)): ?>
                <div class="text-center py-5">
                    <i class="fa fa-box-open text-muted" style="font-size:3rem;"></i>
                    <p class="mt-3 text-muted">Không tìm thấy sản phẩm nào</p>
                    <a href="/san-pham" class="btn btn-danger btn-sm">Xem tất cả sản phẩm</a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($sanPhamList as $sp): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="position-relative product-img-wrapper rounded-top">
                                        <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                                            class="card-img-top p-2 product-img"
                                            alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>"
                                            style="height:180px;object-fit:contain;">
                                        <?php if (!empty($sp['phan_tram_giam']) && $sp['phan_tram_giam'] > 0): ?>
                                            <span class="badge bg-danger position-absolute top-0 start-0 m-2" style="font-size:0.7rem; z-index: 2;">
                                                -<?= $sp['phan_tram_giam'] ?>%
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body pt-0 px-3 pb-3 text-center">
                                        <h6 class="small mb-1 text-dark fw-medium" style="display:-webkit-box;line-clamp:2;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.5em;">
                                            <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                        </h6>
                                        <p class="text-danger fw-bold mb-0 fs-6"><?= number_format($sp['gia_hien_thi'], 0, ',', '.') ?>đ</p>
                                        <?php if (!empty($sp['gia_goc']) && $sp['gia_goc'] > $sp['gia_hien_thi']): ?>
                                            <small class="text-muted text-decoration-line-through" style="font-size: 0.75rem;"><?= number_format($sp['gia_goc'], 0, ',', '.') ?>đ</small>
                                        <?php else: ?>
                                            <small class="text-transparent" style="opacity: 0; font-size: 0.75rem;">0</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($tongTrang > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">‹</a>
                                </li>
                            <?php endif; ?>
                            <?php
                            $start = max(1, $page - 2);
                            $end = min($tongTrang, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $tongTrang): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">›</a>
                                </li>
                            <?php endif; ?>
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