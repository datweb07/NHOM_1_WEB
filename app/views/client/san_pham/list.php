<?php
$pageTitle = 'Danh sách sản phẩm - FPT Shop';
ob_start();

$selectedSlugsForCompare = $_GET['slug'] ?? [];
if (!is_array($selectedSlugsForCompare)) {
    $selectedSlugsForCompare = [];
}
for ($i = 0; $i < 4; $i++) {
    if (!isset($selectedSlugsForCompare[$i])) {
        $selectedSlugsForCompare[$i] = '';
    }
}

$danhSachHang = $danhSachHang ?? [];
$hangFilters = $hangFilters ?? [];
$giaKhoangFilters = $giaKhoangFilters ?? [];
$giaSliderMin = isset($giaSliderMin) ? (float)$giaSliderMin : 0;
$giaSliderMax = isset($giaSliderMax) ? (float)$giaSliderMax : 0;
$giaMin = isset($giaMin) ? (float)$giaMin : $giaSliderMin;
$giaMax = isset($giaMax) ? (float)$giaMax : $giaSliderMax;
$sortBy = isset($sortBy) ? (string)$sortBy : 'ngay_tao';
$sortOrder = isset($sortOrder) ? strtoupper((string)$sortOrder) : 'DESC';
$selectedSortPreset = $sortBy . ':' . $sortOrder;

if ($giaSliderMax < $giaSliderMin) {
    [$giaSliderMin, $giaSliderMax] = [$giaSliderMax, $giaSliderMin];
}

if ($giaMin < $giaSliderMin) {
    $giaMin = $giaSliderMin;
}
if ($giaMin > $giaSliderMax) {
    $giaMin = $giaSliderMax;
}
if ($giaMax < $giaSliderMin) {
    $giaMax = $giaSliderMin;
}
if ($giaMax > $giaSliderMax) {
    $giaMax = $giaSliderMax;
}

$mucGiaPreset = [
    ['label' => 'Dưới 5 triệu', 'value' => '0-5000000'],
    ['label' => '5 - 10 triệu', 'value' => '5000000-10000000'],
    ['label' => '10 - 20 triệu', 'value' => '10000000-20000000'],
    ['label' => '20 - 40 triệu', 'value' => '20000000-40000000'],
    ['label' => 'Trên 40 triệu', 'value' => '40000000-999999999'],
];
?>

<style>
    .product-img-wrapper {
        overflow: hidden;
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

    .compare-inline-card {
        border-radius: 12px;
        background: linear-gradient(180deg, #fff 0%, #fff6f7 100%);
    }

    .compare-inline-slot {
        border: 1px solid #f0dfe2;
        border-radius: 10px;
        padding: 10px;
        background: #fff;
    }

    .btn-compare-toggle.active {
        background-color: #d70018;
        border-color: #d70018;
        color: #fff;
    }

    .btn-compare-toggle.is-locked {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .compare-floating-bar {
        position: fixed;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1050;
        width: min(920px, calc(100vw - 24px));
        border-radius: 12px;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.18);
    }

    .brand-toggle-btn {
        border-radius: 999px;
        border: 1px solid #e8e8e8;
        background: #fff;
        color: #2f3542;
        font-size: 0.8rem;
        padding: 6px 10px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .brand-toggle-btn:hover {
        border-color: #d70018;
        color: #d70018;
    }

    .brand-check:checked+.brand-toggle-btn {
        border-color: #d70018;
        background: #fff1f3;
        color: #d70018;
    }

    .brand-logo-badge {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #f2f3f5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.68rem;
        font-weight: 700;
        color: #4b5563;
        flex: 0 0 20px;
    }

    .price-preset-list {
        display: grid;
        gap: 8px;
    }

    .price-preset-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #eceff3;
        border-radius: 10px;
        padding: 8px 10px;
        background: #fff;
        color: #374151;
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .price-preset-item::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free', 'FontAwesome';
        font-weight: 900;
        font-size: 0.68rem;
        color: #d70018;
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.2s ease;
    }

    .price-preset-item:hover {
        border-color: #d70018;
        background: #fff6f7;
        color: #d70018;
    }

    .price-preset-check:checked+.price-preset-item {
        border-color: #d70018;
        background: #fff1f3;
        color: #b10016;
        box-shadow: 0 0 0 1px rgba(215, 0, 24, 0.12);
    }

    .price-preset-check:checked+.price-preset-item::after {
        opacity: 1;
        transform: scale(1);
    }

    .price-preset-check:focus-visible+.price-preset-item {
        outline: 2px solid rgba(215, 0, 24, 0.25);
        outline-offset: 2px;
    }

    .price-range-values {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .price-slider-track {
        display: grid;
        gap: 8px;
    }

    .price-slider-dual {
        position: relative;
        height: 36px;
        padding: 10px 0;
    }

    .price-slider-base,
    .price-slider-active {
        position: absolute;
        left: 0;
        right: 0;
        top: 50%;
        height: 6px;
        border-radius: 999px;
        transform: translateY(-50%);
    }

    .price-slider-base {
        background: #e9ecef;
        z-index: 1;
    }

    .price-slider-active {
        background: #d70018;
        z-index: 2;
    }

    .price-slider-dual .range-input {
        -webkit-appearance: none;
        appearance: none;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 36px;
        margin: 0;
        background: transparent;
        pointer-events: none;
        z-index: 3;
    }

    .price-slider-dual .range-input:focus {
        outline: none;
    }

    .price-slider-dual .range-input::-webkit-slider-runnable-track {
        height: 6px;
        background: transparent;
    }

    .price-slider-dual .range-input::-moz-range-track {
        height: 6px;
        background: transparent;
    }

    .price-slider-dual .range-input::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #d70018;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        pointer-events: auto;
        margin-top: -6px;
        cursor: pointer;
    }

    .price-slider-dual .range-input::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #d70018;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        pointer-events: auto;
        cursor: pointer;
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
                    <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fa fa-sliders text-danger me-2"></i>Bộ lọc tìm kiếm</h6>
                    <form method="GET" action="/san-pham" id="filter-form">
                        <?php if (!empty($keyword)): ?>
                            <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        <?php endif; ?>

                        <?php if (!empty($sortBy)): ?>
                            <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sortBy) ?>">
                        <?php endif; ?>
                        <?php if (!empty($sortOrder)): ?>
                            <input type="hidden" name="sort_order" value="<?= htmlspecialchars($sortOrder) ?>">
                        <?php endif; ?>



                        <div class="mb-3">
                            <label class="form-label small fw-medium">Danh mục</label>
                            <select name="danh_muc" class="form-select form-select-sm" onchange="resetPriceFiltersOnCategoryChange(this.form)">
                                <option value="0">Tất cả danh mục</option>
                                <?php foreach ($danhMucList as $dm): ?>
                                    <option value="<?= $dm['id'] ?>" <?= ($danhMucId == $dm['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dm['ten']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium d-block mb-2">Sắp xếp</label>
                            <select class="form-select form-select-sm" id="sort-preset-select">
                                <option value="ngay_tao:DESC" <?= $selectedSortPreset === 'ngay_tao:DESC' ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="gia_hien_thi:ASC" <?= $selectedSortPreset === 'gia_hien_thi:ASC' ? 'selected' : '' ?>>Giá thấp đến cao</option>
                                <option value="gia_hien_thi:DESC" <?= $selectedSortPreset === 'gia_hien_thi:DESC' ? 'selected' : '' ?>>Giá cao đến thấp</option>
                                <option value="ten_san_pham:ASC" <?= $selectedSortPreset === 'ten_san_pham:ASC' ? 'selected' : '' ?>>A-Z</option>
                                <option value="ten_san_pham:DESC" <?= $selectedSortPreset === 'ten_san_pham:DESC' ? 'selected' : '' ?>>Z-A</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium d-block mb-2">Hãng sản xuất</label>
                            <div id="brand-list" class="d-flex flex-wrap gap-2">
                                <?php foreach ($danhSachHang as $idx => $hangRow): ?>
                                    <?php
                                    $hang = trim((string)($hangRow['hang_san_xuat'] ?? ''));
                                    if ($hang === '') {
                                        continue;
                                    }
                                    $isChecked = in_array($hang, $hangFilters, true);
                                    $isHiddenByDefault = $idx >= 8;
                                    $hangId = 'hang-filter-' . md5($hang);
                                    $logoText = strtoupper(substr($hang, 0, 1));
                                    ?>
                                    <div class="<?= $isHiddenByDefault ? 'brand-extra d-none' : '' ?>">
                                        <input
                                            class="btn-check brand-check"
                                            type="checkbox"
                                            name="hang[]"
                                            value="<?= htmlspecialchars($hang) ?>"
                                            id="<?= $hangId ?>"
                                            <?= $isChecked ? 'checked' : '' ?>>
                                        <label class="brand-toggle-btn" for="<?= $hangId ?>">
                                            <span class="brand-logo-badge"><?= htmlspecialchars($logoText) ?></span>
                                            <span><?= htmlspecialchars($hang) ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($danhSachHang) > 8): ?>
                                <button type="button" class="btn btn-link btn-sm p-0 mt-2 text-decoration-none" id="toggle-brand-list">
                                    Xem thêm
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium d-block mb-2">Mức giá</label>
                            <div class="price-preset-list">
                                <?php foreach ($mucGiaPreset as $muc): ?>
                                    <?php $giaKhoangId = 'gia-khoang-' . md5($muc['value']); ?>
                                    <div>
                                        <input
                                            class="btn-check price-preset-check"
                                            type="checkbox"
                                            name="gia_khoang[]"
                                            value="<?= $muc['value'] ?>"
                                            id="<?= $giaKhoangId ?>"
                                            <?= in_array($muc['value'], $giaKhoangFilters, true) ? 'checked' : '' ?>>
                                        <label class="price-preset-item" for="<?= $giaKhoangId ?>">
                                            <?= htmlspecialchars($muc['label']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium d-block mb-2">Khoảng giá tùy chỉnh</label>
                            <div class="price-range-values mb-2">
                                <span id="price-min-label"><?= number_format($giaMin, 0, ',', '.') ?>đ</span>
                                -
                                <span id="price-max-label"><?= number_format($giaMax, 0, ',', '.') ?>đ</span>
                            </div>

                            <div class="price-slider-track mb-2">
                                <div class="price-slider-dual">
                                    <div class="price-slider-base"></div>
                                    <div class="price-slider-active" id="price-slider-active"></div>
                                    <input
                                        type="range"
                                        class="form-range range-input"
                                        id="price-min-range"
                                        min="<?= (int)$giaSliderMin ?>"
                                        max="<?= (int)$giaSliderMax ?>"
                                        step="100000"
                                        value="<?= (int)$giaMin ?>">
                                    <input
                                        type="range"
                                        class="form-range range-input"
                                        id="price-max-range"
                                        min="<?= (int)$giaSliderMin ?>"
                                        max="<?= (int)$giaSliderMax ?>"
                                        step="100000"
                                        value="<?= (int)$giaMax ?>">
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="gia_min" id="gia-min-input" class="form-control form-control-sm" value="<?= (int)$giaMin ?>" min="<?= (int)$giaSliderMin ?>" max="<?= (int)$giaSliderMax ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="gia_max" id="gia-max-input" class="form-control form-control-sm" value="<?= (int)$giaMax ?>" min="<?= (int)$giaSliderMin ?>" max="<?= (int)$giaSliderMax ?>">
                                </div>
                            </div>
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
                            <div class="card border-0 shadow-sm h-100">
                                <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none">
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
                                    <div class="card-body pt-0 px-3 pb-3">
                                        <h6 class="small mb-1 text-dark fw-medium" style="display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.5em;">
                                            <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                        </h6>
                                        <p class="text-muted mb-1" style="font-size: 12px; min-height: 18px;">
                                            <?= htmlspecialchars($sp['hang_san_xuat'] ?? 'Thương hiệu') ?>
                                        </p>
                                        <?php $giaHienThi = (float)($sp['gia_hien_thi'] ?? 0); ?>
                                        <p class="text-danger fw-bold mb-0 fs-6">
                                            <?= $giaHienThi > 0 ? number_format($giaHienThi, 0, ',', '.') . 'đ' : 'Liên hệ' ?>
                                        </p>
                                        <?php if (!empty($sp['gia_goc']) && (float)$sp['gia_goc'] > $giaHienThi && $giaHienThi > 0): ?>
                                            <small class="text-muted text-decoration-line-through" style="font-size: 0.75rem;"><?= number_format((float)$sp['gia_goc'], 0, ',', '.') ?>đ</small>
                                        <?php else: ?>
                                            <small style="visibility: hidden; font-size: 0.75rem;">0</small>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <div class="card-footer bg-white border-0 pt-0 px-3 pb-3">
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm w-100 btn-compare-toggle"
                                        data-slug="<?= htmlspecialchars($sp['slug']) ?>"
                                        data-category-id="<?= (int)($sp['danh_muc_id'] ?? 0) ?>"
                                        data-name="<?= htmlspecialchars($sp['ten_san_pham']) ?>">
                                        <i class="fa fa-plus me-1"></i>Chọn so sánh
                                    </button>
                                </div>
                            </div>
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

<div id="compare-floating-bar" class="compare-floating-bar card border-0 bg-white d-none">
    <div class="card-body py-2 px-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
            <div class="small text-muted flex-grow-1">
                Đã chọn <strong id="compare-count">0</strong> sản phẩm để so sánh
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="compare-clear-btn">Bỏ chọn tất cả</button>
                <button type="button" class="btn btn-danger btn-sm" id="compare-submit-btn">
                    <i class="fa fa-sliders me-1"></i>So sánh sản phẩm đã chọn
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const COMPARE_STORAGE_KEY = 'fptshop_compare_slugs';
    const COMPARE_CATEGORY_STORAGE_KEY = 'fptshop_compare_category_id';
    const selectedCompareSlugs = new Set();
    let selectedCompareCategoryId = null;
    const compareButtons = Array.from(document.querySelectorAll('.btn-compare-toggle'));
    const compareFloatingBar = document.getElementById('compare-floating-bar');
    const compareCount = document.getElementById('compare-count');
    const compareClearBtn = document.getElementById('compare-clear-btn');
    const compareSubmitBtn = document.getElementById('compare-submit-btn');

    function parseCategoryId(rawValue) {
        const parsed = Number(rawValue);
        return Number.isInteger(parsed) && parsed > 0 ? parsed : null;
    }

    function saveCompareState() {
        try {
            localStorage.setItem(COMPARE_STORAGE_KEY, JSON.stringify(Array.from(selectedCompareSlugs)));
            if (selectedCompareCategoryId === null) {
                localStorage.removeItem(COMPARE_CATEGORY_STORAGE_KEY);
            } else {
                localStorage.setItem(COMPARE_CATEGORY_STORAGE_KEY, String(selectedCompareCategoryId));
            }
        } catch (e) {

        }
    }

    function loadCompareState() {
        try {
            const raw = localStorage.getItem(COMPARE_STORAGE_KEY);
            if (!raw) return;
            const parsed = JSON.parse(raw);
            if (!Array.isArray(parsed)) return;
            parsed.forEach((slug) => {
                if (typeof slug === 'string' && slug.trim() !== '') {
                    selectedCompareSlugs.add(slug);
                }
            });

            selectedCompareCategoryId = parseCategoryId(localStorage.getItem(COMPARE_CATEGORY_STORAGE_KEY));
            if (selectedCompareCategoryId === null && selectedCompareSlugs.size > 0) {
                const firstMatchedBtn = compareButtons.find((btn) => selectedCompareSlugs.has(btn.dataset.slug));
                selectedCompareCategoryId = parseCategoryId(firstMatchedBtn?.dataset.categoryId ?? null);
            }
        } catch (e) {

        }
    }

    function renderCompareState() {
        const count = selectedCompareSlugs.size;
        compareCount.textContent = String(count);

        compareButtons.forEach((btn) => {
            const slug = btn.dataset.slug;
            const isSelected = selectedCompareSlugs.has(slug);
            const buttonCategoryId = parseCategoryId(btn.dataset.categoryId ?? null);
            const isLockedByCategory =
                selectedCompareCategoryId !== null &&
                buttonCategoryId !== null &&
                buttonCategoryId !== selectedCompareCategoryId &&
                !isSelected;

            btn.classList.toggle('active', isSelected);
            btn.classList.toggle('is-locked', isLockedByCategory);
            btn.innerHTML = isSelected ?
                '<i class="fa fa-check me-1"></i>Đã chọn so sánh' :
                '<i class="fa fa-plus me-1"></i>Chọn so sánh';

            if (isLockedByCategory) {
                btn.title = 'Chỉ so sánh những sản phẩm cùng danh mục';
                btn.setAttribute('aria-disabled', 'true');
            } else {
                btn.removeAttribute('title');
                btn.removeAttribute('aria-disabled');
            }
        });

        if (count > 0) {
            compareFloatingBar.classList.remove('d-none');
        } else {
            compareFloatingBar.classList.add('d-none');
        }

        saveCompareState();
    }

    loadCompareState();
    renderCompareState();

    compareButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const slug = btn.dataset.slug;
            if (!slug) return;

            const buttonCategoryId = parseCategoryId(btn.dataset.categoryId ?? null);

            if (selectedCompareSlugs.has(slug)) {
                selectedCompareSlugs.delete(slug);
                if (selectedCompareSlugs.size === 0) {
                    selectedCompareCategoryId = null;
                }
            } else {
                if (selectedCompareCategoryId !== null && buttonCategoryId !== null && buttonCategoryId !== selectedCompareCategoryId) {
                    alert('Bạn chỉ có thể chọn sản phẩm cùng danh mục để so sánh.');
                    return;
                }

                selectedCompareSlugs.add(slug);
                if (selectedCompareCategoryId === null && buttonCategoryId !== null) {
                    selectedCompareCategoryId = buttonCategoryId;
                }
            }

            renderCompareState();
        });
    });

    compareClearBtn?.addEventListener('click', () => {
        selectedCompareSlugs.clear();
        selectedCompareCategoryId = null;
        renderCompareState();
    });

    compareSubmitBtn?.addEventListener('click', () => {
        if (selectedCompareSlugs.size < 2) {
            alert('Vui lòng chọn ít nhất 2 sản phẩm để so sánh.');
            return;
        }

        const params = new URLSearchParams();
        Array.from(selectedCompareSlugs).forEach((slug) => {
            params.append('slug[]', slug);
        });

        window.location.href = '/so-sanh?' + params.toString();
    });

    function resetPriceFiltersOnCategoryChange(form) {
        if (!form) return;

        const minInput = form.querySelector('#gia-min-input');
        const maxInput = form.querySelector('#gia-max-input');
        if (minInput) minInput.value = '';
        if (maxInput) maxInput.value = '';

        form.querySelectorAll('input[name="gia_khoang[]"]').forEach((cb) => {
            cb.checked = false;
        });

        form.submit();
    }

    const toggleBrandBtn = document.getElementById('toggle-brand-list');
    if (toggleBrandBtn) {
        toggleBrandBtn.addEventListener('click', () => {
            const extras = Array.from(document.querySelectorAll('.brand-extra'));
            const isHidden = extras.some((item) => item.classList.contains('d-none'));
            extras.forEach((item) => item.classList.toggle('d-none', !isHidden));
            toggleBrandBtn.textContent = isHidden ? 'Thu gọn' : 'Xem thêm';
        });
    }

    const priceMinRange = document.getElementById('price-min-range');
    const priceMaxRange = document.getElementById('price-max-range');
    const filterForm = document.getElementById('filter-form');
    const giaMinInput = document.getElementById('gia-min-input');
    const giaMaxInput = document.getElementById('gia-max-input');
    const priceMinLabel = document.getElementById('price-min-label');
    const priceMaxLabel = document.getElementById('price-max-label');
    const priceSliderActive = document.getElementById('price-slider-active');
    const sortPresetSelect = document.getElementById('sort-preset-select');

    if (sortPresetSelect && filterForm) {
        sortPresetSelect.addEventListener('change', () => {
            const [sortByValue, sortOrderValue] = sortPresetSelect.value.split(':');
            const sortByInput = filterForm.querySelector('input[name="sort_by"]');
            const sortOrderInput = filterForm.querySelector('input[name="sort_order"]');

            if (sortByInput) sortByInput.value = sortByValue || 'ngay_tao';
            if (sortOrderInput) sortOrderInput.value = sortOrderValue || 'DESC';

            filterForm.submit();
        });
    }

    const formatVnd = (value) => Number(value || 0).toLocaleString('vi-VN') + 'đ';

    const syncPriceLabels = () => {
        const minVal = Number(giaMinInput?.value || 0);
        const maxVal = Number(giaMaxInput?.value || 0);
        if (priceMinLabel) priceMinLabel.textContent = formatVnd(minVal);
        if (priceMaxLabel) priceMaxLabel.textContent = formatVnd(maxVal);
    };

    const syncSliderHighlight = () => {
        if (!priceMinRange || !priceMaxRange || !priceSliderActive) return;

        const absoluteMin = Number(priceMinRange.min || 0);
        const absoluteMax = Number(priceMinRange.max || 1);
        const minVal = Number(priceMinRange.value || absoluteMin);
        const maxVal = Number(priceMaxRange.value || absoluteMax);
        const span = Math.max(1, absoluteMax - absoluteMin);

        const leftPercent = ((minVal - absoluteMin) / span) * 100;
        const rightPercent = ((maxVal - absoluteMin) / span) * 100;

        priceSliderActive.style.left = leftPercent + '%';
        priceSliderActive.style.right = (100 - rightPercent) + '%';
    };

    const syncFromRange = (source = '') => {
        if (!priceMinRange || !priceMaxRange || !giaMinInput || !giaMaxInput) return;

        let minVal = Number(priceMinRange.value || 0);
        let maxVal = Number(priceMaxRange.value || 0);
        if (minVal > maxVal) {
            if (source === 'min') {
                minVal = maxVal;
            } else {
                maxVal = minVal;
            }
        }

        giaMinInput.value = String(minVal);
        giaMaxInput.value = String(maxVal);
        priceMinRange.value = String(minVal);
        priceMaxRange.value = String(maxVal);
        syncPriceLabels();
        syncSliderHighlight();
    };

    const syncFromInput = () => {
        if (!priceMinRange || !priceMaxRange || !giaMinInput || !giaMaxInput) return;

        const absoluteMin = Number(priceMinRange.min || 0);
        const absoluteMax = Number(priceMinRange.max || 0);
        let minVal = Number(giaMinInput.value);
        let maxVal = Number(giaMaxInput.value);

        if (Number.isNaN(minVal)) {
            minVal = absoluteMin;
        }
        if (Number.isNaN(maxVal)) {
            maxVal = absoluteMax;
        }

        minVal = Math.max(absoluteMin, Math.min(minVal, absoluteMax));
        maxVal = Math.max(absoluteMin, Math.min(maxVal, absoluteMax));
        if (minVal > maxVal) {
            [minVal, maxVal] = [maxVal, minVal];
        }

        giaMinInput.value = String(minVal);
        giaMaxInput.value = String(maxVal);
        priceMinRange.value = String(minVal);
        priceMaxRange.value = String(maxVal);
        syncPriceLabels();
        syncSliderHighlight();
    };

    const clampPriceInputs = () => {
        if (!priceMinRange || !giaMinInput || !giaMaxInput) return;

        const absoluteMin = Number(priceMinRange.min || 0);
        const absoluteMax = Number(priceMinRange.max || 0);
        let minVal = Number(giaMinInput.value || absoluteMin);
        let maxVal = Number(giaMaxInput.value || absoluteMax);

        if (minVal < absoluteMin) minVal = absoluteMin;
        if (minVal > absoluteMax) minVal = absoluteMax;
        if (maxVal < absoluteMin) maxVal = absoluteMin;
        if (maxVal > absoluteMax) maxVal = absoluteMax;
        if (minVal > maxVal)[minVal, maxVal] = [maxVal, minVal];

        giaMinInput.value = String(minVal);
        giaMaxInput.value = String(maxVal);
        priceMinRange.value = String(minVal);
        priceMaxRange.value = String(maxVal);
        syncPriceLabels();
        syncSliderHighlight();
    };

    priceMinRange?.addEventListener('input', () => syncFromRange('min'));
    priceMaxRange?.addEventListener('input', () => syncFromRange('max'));
    giaMinInput?.addEventListener('blur', syncFromInput);
    giaMaxInput?.addEventListener('blur', syncFromInput);
    giaMinInput?.addEventListener('change', syncFromInput);
    giaMaxInput?.addEventListener('change', syncFromInput);
    giaMinInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            syncFromInput();
        }
    });
    giaMaxInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            syncFromInput();
        }
    });
    filterForm?.addEventListener('submit', clampPriceInputs);
    syncPriceLabels();
    syncSliderHighlight();
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>