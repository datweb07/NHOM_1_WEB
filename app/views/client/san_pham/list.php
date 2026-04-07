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
                        <?php
                        $phienBanList = $sp['phien_ban_list'] ?? [];
                        $phienBanMacDinh = $phienBanList[0] ?? null;
                        ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 d-flex flex-column">
                                <!-- Ảnh sản phẩm -->
                                <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none flex-grow-1" style="display: flex; flex-direction: column;">
                                    <div class="position-relative product-img-wrapper rounded-top flex-grow-1">
                                        <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                                            class="card-img-top p-2 product-img product-variant-img"
                                            alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>"
                                            data-sp-id="<?= $sp['id'] ?>"
                                            style="height:180px;object-fit:contain;">
                                        <?php if (!empty($sp['phan_tram_giam']) && $sp['phan_tram_giam'] > 0): ?>
                                            <span class="badge bg-danger position-absolute top-0 start-0 m-2" style="font-size:0.7rem; z-index: 2;">
                                                -<?= $sp['phan_tram_giam'] ?>%
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </a>

                                <!-- Thông tin sản phẩm -->
                                <div class="card-body pt-2 px-3 pb-2">
                                    <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none">
                                        <h6 class="small mb-1 text-dark fw-medium" style="display:-webkit-box;line-clamp:2;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.5em;">
                                            <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                        </h6>
                                    </a>

                                    <!-- Chọn phiên bản -->
                                    <?php if (!empty($phienBanList)): ?>
                                        <div class="mb-2" style="font-size: 0.75rem;">
                                            <?php
                                            $hasColor = false;
                                            $hasCapacity = false;
                                            $hasRam = false;
                                            foreach ($phienBanList as $pb) {
                                                if (!empty($pb['mau_sac'])) $hasColor = true;
                                                if (!empty($pb['dung_luong'])) $hasCapacity = true;
                                                if (!empty($pb['ram'])) $hasRam = true;
                                            }
                                            ?>

                                            <?php if ($hasColor): ?>
                                                <select class="form-select form-select-sm mb-1 variant-select" data-sp-id="<?= $sp['id'] ?>" data-type="mau_sac" onchange="updateProductVariant(this, <?= $sp['id'] ?>)">
                                                    <option value="">-- Màu --</option>
                                                    <?php
                                                    $uniqueColors = [];
                                                    foreach ($phienBanList as $pb) {
                                                        if (!empty($pb['mau_sac']) && !in_array($pb['mau_sac'], $uniqueColors)) {
                                                            $uniqueColors[] = $pb['mau_sac'];
                                                            echo '<option value="' . htmlspecialchars($pb['mau_sac']) . '">' . htmlspecialchars($pb['mau_sac']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            <?php endif; ?>

                                            <?php if ($hasCapacity): ?>
                                                <select class="form-select form-select-sm mb-1 variant-select" data-sp-id="<?= $sp['id'] ?>" data-type="dung_luong" onchange="updateProductVariant(this, <?= $sp['id'] ?>)">
                                                    <option value="">-- Dung lượng --</option>
                                                    <?php
                                                    $uniqueCapacities = [];
                                                    foreach ($phienBanList as $pb) {
                                                        if (!empty($pb['dung_luong']) && !in_array($pb['dung_luong'], $uniqueCapacities)) {
                                                            $uniqueCapacities[] = $pb['dung_luong'];
                                                            echo '<option value="' . htmlspecialchars($pb['dung_luong']) . '">' . htmlspecialchars($pb['dung_luong']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            <?php endif; ?>

                                            <?php if ($hasRam): ?>
                                                <select class="form-select form-select-sm mb-1 variant-select" data-sp-id="<?= $sp['id'] ?>" data-type="ram" onchange="updateProductVariant(this, <?= $sp['id'] ?>)">
                                                    <option value="">-- RAM --</option>
                                                    <?php
                                                    $uniqueRams = [];
                                                    foreach ($phienBanList as $pb) {
                                                        if (!empty($pb['ram']) && !in_array($pb['ram'], $uniqueRams)) {
                                                            $uniqueRams[] = $pb['ram'];
                                                            echo '<option value="' . htmlspecialchars($pb['ram']) . '">' . htmlspecialchars($pb['ram']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Giá -->
                                    <p class="text-danger fw-bold mb-2 fs-6 product-variant-price" data-sp-id="<?= $sp['id'] ?>"><?= number_format($phienBanMacDinh['gia_ban'] ?? $sp['gia_hien_thi'], 0, ',', '.') ?>đ</p>
                                    <?php if (!empty($sp['gia_goc']) && $sp['gia_goc'] > $sp['gia_hien_thi']): ?>
                                        <small class="text-muted text-decoration-line-through" style="font-size: 0.75rem;"><?= number_format($sp['gia_goc'], 0, ',', '.') ?>đ</small>
                                    <?php else: ?>
                                        <small class="text-transparent" style="opacity: 0; font-size: 0.75rem;">0</small>
                                    <?php endif; ?>

                                    <!-- Thêm vào giỏ hàng -->
                                    <form method="POST" action="/gio-hang/them" class="mt-2 add-to-cart-form" data-sp-id="<?= $sp['id'] ?>">
                                        <input type="hidden" name="phien_ban_id" class="phien-ban-id-input" value="<?= $phienBanMacDinh['id'] ?? 0 ?>">
                                        <input type="hidden" name="so_luong" value="1">
                                        <button type="submit" class="btn btn-sm btn-danger w-100" style="font-size: 0.75rem;">
                                            <i class="fas fa-shopping-cart"></i> Thêm giỏ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Lưu dữ liệu phiên bản để JavaScript sử dụng -->
                <script type="application/json" id="product-variants">
                    <?php
                    $variantsData = [];
                    foreach ($sanPhamList as $sp) {
                        $phienBanList = $sp['phien_ban_list'] ?? [];
                        $variantsData[$sp['id']] = $phienBanList;
                    }
                    echo json_encode($variantsData);
                    ?>
                </script>

                <script>
                    // Lấy dữ liệu phiên bản từ JSON
                    const productVariantsElement = document.getElementById('product-variants');
                    const productVariants = productVariantsElement ? JSON.parse(productVariantsElement.innerText) : {};

                    function updateProductVariant(selectElement, spId) {
                        const selectedColor = document.querySelector(`[data-sp-id="${spId}"][data-type="mau_sac"]`)?.value || '';
                        const selectedCapacity = document.querySelector(`[data-sp-id="${spId}"][data-type="dung_luong"]`)?.value || '';
                        const selectedRam = document.querySelector(`[data-sp-id="${spId}"][data-type="ram"]`)?.value || '';

                        const variants = productVariants[spId] || [];

                        // Lọc phiên bản phù hợp
                        let matchedVariant = variants.find(v => {
                            const matchColor = !selectedColor || v.mau_sac === selectedColor;
                            const matchCapacity = !selectedCapacity || v.dung_luong === selectedCapacity;
                            const matchRam = !selectedRam || v.ram === selectedRam;
                            return matchColor && matchCapacity && matchRam;
                        });

                        if (!matchedVariant && (selectedColor || selectedCapacity || selectedRam)) {
                            // Nếu không tìm thấy match chính xác, lấy phiên bản đầu tiên mà match được
                            matchedVariant = variants.find(v => {
                                if (selectedColor && v.mau_sac !== selectedColor) return false;
                                if (selectedCapacity && v.dung_luong !== selectedCapacity) return false;
                                if (selectedRam && v.ram !== selectedRam) return false;
                                return true;
                            });
                        }

                        // Nếu vẫn không tìm thấy, dùng phiên bản đầu tiên
                        if (!matchedVariant && variants.length > 0) {
                            matchedVariant = variants[0];
                        }

                        if (matchedVariant) {
                            // Cập nhật giá
                            const priceElement = document.querySelector(`[data-sp-id="${spId}"].product-variant-price`);
                            if (priceElement) {
                                priceElement.textContent = new Intl.NumberFormat('vi-VN', {
                                    maximumFractionDigits: 0
                                }).format(matchedVariant.gia_ban) + 'đ';
                            }

                            // Cập nhật phien_ban_id trong form
                            const form = document.querySelector(`.add-to-cart-form[data-sp-id="${spId}"]`);
                            if (form) {
                                form.querySelector('.phien-ban-id-input').value = matchedVariant.id;
                            }

                            if (opt.value && !availableRams.has(opt.value)) {
                                opt.disabled = true;
                                opt.style.color = '#ccc';
                            } else {
                                opt.disabled = false;
                                opt.style.color = '#333';
                            }
                        });
                    }
                    }
                    }

                    // Khởi tạo khi trang tải
                    document.addEventListener('DOMContentLoaded', function() {
                        // Có thể thêm các xử lý khởi tạo khác nếu cần
                    });
                </script>

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