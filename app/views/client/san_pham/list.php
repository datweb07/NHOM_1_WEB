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

    .price-slider-group {
        position: relative;
        height: 36px;
    }

    .price-slider-progress {
        position: absolute;
        left: 0;
        right: 0;
        top: 14px;
        height: 4px;
        background-color: #dee2e6;
    }

    .price-slider-group input[type="range"] {
        position: absolute;
        left: 0;
        top: 8px;
        width: 100%;
        pointer-events: none;
        -webkit-appearance: none;
        background: none;
        margin: 0;
        z-index: 4;
    }

    .price-slider-group input[type="range"]::-webkit-slider-thumb {
        pointer-events: auto;
        -webkit-appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #fff;
        background: #dc3545;
        cursor: pointer;

        /* FIX: căn giữa chính xác */
        margin-top: -2px;
        /* = (thumb_height - track_height) / 2 */
        transform: translateY(1px);
        /* tinh chỉnh nhỏ cho đẹp */
    }

    .price-slider-group input[type="range"]::-moz-range-thumb {
        pointer-events: auto;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #fff;
        background: #dc3545;
        cursor: pointer;

        /* Firefox */
        transform: translateY(6px);
    }

    .price-slider-group input[type="range"]::-webkit-slider-runnable-track {
        height: 4px;
        border-radius: 999px;
        background: transparent;
    }

    .price-slider-group input[type="range"]::-moz-range-track {
        height: 4px;
        border-radius: 999px;
        background: transparent;
    }

    .price-slider-value-label {
        font-size: 0.8rem;
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
                        <?php
                        $sliderMinLimit = 0;
                        $sliderMaxLimit = 100000000;
                        $sliderMinValue = ($giaMin !== null) ? (int)$giaMin : $sliderMinLimit;
                        $sliderMaxValue = ($giaMax !== null) ? (int)$giaMax : $sliderMaxLimit;

                        $sliderMinValue = max($sliderMinLimit, min($sliderMinValue, $sliderMaxLimit));
                        $sliderMaxValue = max($sliderMinLimit, min($sliderMaxValue, $sliderMaxLimit));
                        if ($sliderMinValue > $sliderMaxValue) {
                            [$sliderMinValue, $sliderMaxValue] = [$sliderMaxValue, $sliderMinValue];
                        }

                        $quickPriceRanges = [
                            ['label' => 'Dưới 5 triệu', 'min' => 0, 'max' => 5000000],
                            ['label' => '5 - 10 triệu', 'min' => 5000000, 'max' => 10000000],
                            ['label' => '10 - 20 triệu', 'min' => 10000000, 'max' => 20000000],
                            ['label' => 'Trên 20 triệu', 'min' => 20000000, 'max' => 999999999],
                        ];
                        ?>

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
                            <input
                                type="number"
                                name="gia_min"
                                class="form-control form-control-sm"
                                placeholder="VD: 2000000"
                                min="0"
                                step="1000"
                                value="<?= $sliderMinValue ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Giá đến</label>
                            <input
                                type="number"
                                name="gia_max"
                                class="form-control form-control-sm"
                                placeholder="VD: 10000000"
                                min="0"
                                step="1000"
                                value="<?= $sliderMaxValue ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Thanh kéo giá</label>
                            <div class="price-slider-group">
                                <div class="progress price-slider-progress rounded-pill">
                                    <div class="progress-bar bg-danger" id="gia_active_track" role="progressbar"></div>
                                </div>
                                <input
                                    type="range"
                                    id="gia_min_slider"
                                    min="<?= $sliderMinLimit ?>"
                                    max="<?= $sliderMaxLimit ?>"
                                    step="100000"
                                    value="<?= $sliderMinValue ?>">
                                <input
                                    type="range"
                                    id="gia_max_slider"
                                    min="<?= $sliderMinLimit ?>"
                                    max="<?= $sliderMaxLimit ?>"
                                    step="100000"
                                    value="<?= $sliderMaxValue ?>">
                            </div>
                            <div class="d-flex justify-content-between mt-1 text-muted price-slider-value-label">
                                <span id="gia-min-label" class="fw-medium"></span>
                                <span id="gia-max-label" class="fw-medium"></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Khoảng giá nhanh</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($quickPriceRanges as $range): ?>
                                    <?php
                                    $isActiveRange = ((float)($giaMin ?? -1) === (float)$range['min'])
                                        && ((float)($giaMax ?? -1) === (float)$range['max']);
                                    $rangeQuery = [
                                        'danh_muc' => $danhMucId,
                                        'gia_min' => $range['min'],
                                        'gia_max' => $range['max'],
                                        'sort_by' => $sortBy ?? 'ngay_tao',
                                        'sort_order' => $sortOrder ?? 'DESC',
                                    ];
                                    if (!empty($keyword)) {
                                        $rangeQuery['keyword'] = $keyword;
                                    }
                                    ?>
                                    <a
                                        href="/san-pham?<?= http_build_query($rangeQuery) ?>"
                                        class="btn btn-sm <?= $isActiveRange ? 'btn-danger' : 'btn-outline-secondary' ?>">
                                        <?= $range['label'] ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Sắp xếp theo</label>
                            <select name="sort_by" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="ngay_tao" <?= ($sortBy ?? '') === 'ngay_tao' ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="gia_hien_thi" <?= ($sortBy ?? '') === 'gia_hien_thi' ? 'selected' : '' ?>>Giá</option>
                                <option value="ten_san_pham" <?= ($sortBy ?? '') === 'ten_san_pham' ? 'selected' : '' ?>>Tên</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="sort_order" class="form-select form-select-sm" onchange="this.form.submit()">
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
                                        <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? '/public/assets/client/images/products/14.png') ?>"
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
                                        <h6 class="small mb-1 text-dark fw-medium" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.5em;">
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

<script>
    (function() {
        class PriceRangeFilter {
            constructor(formSelector) {
                this.form = document.querySelector(formSelector);
                if (!this.form) {
                    return;
                }

                this.minInput = this.form.querySelector('input[name="gia_min"]');
                this.maxInput = this.form.querySelector('input[name="gia_max"]');
                this.minSlider = document.getElementById('gia_min_slider');
                this.maxSlider = document.getElementById('gia_max_slider');
                this.activeTrack = document.getElementById('gia_active_track');
                this.minLabel = document.getElementById('gia-min-label');
                this.maxLabel = document.getElementById('gia-max-label');

                if (!this.minInput || !this.maxInput || !this.minSlider || !this.maxSlider || !this.activeTrack || !this.minLabel || !this.maxLabel) {
                    return;
                }

                this.minBound = Number(this.minSlider.min) || 0;
                this.maxBound = Number(this.minSlider.max) || 100000000;
                this.bindEvents();
                this.syncFromInputs();
            }

            bindEvents() {
                this.minSlider.addEventListener('input', () => this.syncFromSliders('min'));
                this.maxSlider.addEventListener('input', () => this.syncFromSliders('max'));
                this.minInput.addEventListener('input', () => this.syncFromInputs());
                this.maxInput.addEventListener('input', () => this.syncFromInputs());
            }

            formatMoney(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
            }

            clamp(value, lower, upper) {
                return Math.min(Math.max(value, lower), upper);
            }

            updateLabels(minVal, maxVal) {
                this.minLabel.textContent = 'Từ: ' + this.formatMoney(minVal);
                this.maxLabel.textContent = 'Đến: ' + this.formatMoney(maxVal);
            }

            updateActiveTrack(minVal, maxVal) {
                const startPercent = ((minVal - this.minBound) / (this.maxBound - this.minBound)) * 100;
                const endPercent = ((maxVal - this.minBound) / (this.maxBound - this.minBound)) * 100;

                this.activeTrack.style.marginLeft = startPercent + '%';
                this.activeTrack.style.width = Math.max(0, endPercent - startPercent) + '%';
                this.activeTrack.setAttribute('aria-valuemin', String(minVal));
                this.activeTrack.setAttribute('aria-valuemax', String(maxVal));
            }

            syncFromSliders(source) {
                let minVal = Number(this.minSlider.value);
                let maxVal = Number(this.maxSlider.value);

                if (source === 'min' && minVal > maxVal) {
                    maxVal = minVal;
                    this.maxSlider.value = String(maxVal);
                }

                if (source === 'max' && maxVal < minVal) {
                    minVal = maxVal;
                    this.minSlider.value = String(minVal);
                }

                this.minInput.value = String(minVal);
                this.maxInput.value = String(maxVal);
                this.updateLabels(minVal, maxVal);
                this.updateActiveTrack(minVal, maxVal);
            }

            syncFromInputs() {
                const parsedMin = Number(this.minInput.value);
                const parsedMax = Number(this.maxInput.value);

                let minVal = Number.isFinite(parsedMin) ? this.clamp(parsedMin, this.minBound, this.maxBound) : this.minBound;
                let maxVal = Number.isFinite(parsedMax) ? this.clamp(parsedMax, this.minBound, this.maxBound) : this.maxBound;

                if (minVal > maxVal) {
                    maxVal = minVal;
                }

                this.minSlider.value = String(minVal);
                this.maxSlider.value = String(maxVal);
                this.minInput.value = String(minVal);
                this.maxInput.value = String(maxVal);
                this.updateLabels(minVal, maxVal);
                this.updateActiveTrack(minVal, maxVal);
            }
        }

        new PriceRangeFilter('#filter-form');
    })();
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>