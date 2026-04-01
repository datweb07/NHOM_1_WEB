<?php
require_once dirname(__DIR__) . '/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5>Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="/tim-kiem">
                        <input type="hidden" name="q" value="<?= htmlspecialchars($keyword ?? '') ?>">
                        
                        <!-- Danh mục -->
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <select name="danh_muc" class="form-select">
                                <option value="0">Tất cả</option>
                                <?php foreach ($danhMucs as $dm): ?>
                                    <option value="<?= $dm['id'] ?>" <?= ($danhMucId == $dm['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dm['ten']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Khoảng giá -->
                        <div class="mb-3">
                            <label class="form-label">Khoảng giá</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="gia_min" class="form-control" placeholder="Từ" value="<?= $giaMin ?? '' ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="gia_max" class="form-control" placeholder="Đến" value="<?= $giaMax ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kết quả tìm kiếm -->
        <div class="col-md-9">
            <div class="mb-3">
                <h4>Kết quả tìm kiếm: "<?= htmlspecialchars($keyword ?? '') ?>"</h4>
                <p class="text-muted">Tìm thấy <?= $tongSanPham ?> sản phẩm</p>
            </div>

            <?php if (empty($sanPhams)): ?>
                <div class="alert alert-info">
                    Không tìm thấy sản phẩm nào phù hợp.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($sanPhams as $sp): ?>
                        <div class="col-md-4 mb-4">
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

                <!-- Pagination -->
                <?php if ($tongTrang > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $tongTrang; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?q=<?= urlencode($keyword) ?>&danh_muc=<?= $danhMucId ?>&gia_min=<?= $giaMin ?>&gia_max=<?= $giaMax ?>&page=<?= $i ?>">
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

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
