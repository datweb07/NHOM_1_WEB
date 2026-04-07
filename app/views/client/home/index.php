<?php
$pageTitle = 'Fptshop.com.vn | Điện thoại, Laptop, Điện máy, Gia dụng, Phụ kiện chính hãng giá tốt nhất';
$additionalCSS = [
    ASSET_URL . '/assets/client/css/slider.css',
    ASSET_URL . '/assets/client/css/slider-card.css',
];
$additionalJS = [
    ASSET_URL . '/assets/client/js/slider.js',
    ASSET_URL . '/assets/client/js/slider-card.js',
];

ob_start();
?>

<div class="full-width-hero" style="position: relative; width: 100%; overflow: hidden;">

    <div class="wrapper-slider hero-carousel" style="width: 100%; border: none; box-shadow: none;">

        <div class="row no-warp main-slider" style="margin: 0;">
            <?php if (!empty($bannerHero)): ?>
                <?php foreach ($bannerHero as $banner): ?>
                    <div class="col l-12 m-12 c-12 wrapper-item-slider" style="padding: 0; flex: 0 0 100%; max-width: 100%;">
                        <div class="item-slider" style="border: none;">
                            <a href="<?php echo htmlspecialchars($banner['link_dich']); ?>" style="display: block;">
                                <img src="<?php echo htmlspecialchars($banner['hinh_anh_desktop']); ?>"
                                    alt="<?php echo htmlspecialchars($banner['tieu_de']); ?>"
                                    style="width: 100%; min-height: 450px; max-height: 800px; object-fit: cover; display: block; border-radius: 0;">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button class="back-slider-card" type="button"
            style="background-color: rgba(255,255,255,0.7); left: 15px; z-index: 20;"><i
                class="fa fa-chevron-left"></i></button>
        <button class="next-slider-card" type="button"
            style="background-color: rgba(255,255,255,0.7); right: 15px; z-index: 20;"><i
                class="fa fa-chevron-right"></i></button>
    </div>

    <div class="hero-fade-overlay"
        style="position: absolute; bottom: 0; left: 0; width: 100%; height: 280px; background: linear-gradient(to bottom, rgba(245,245,245,0) 0%, #f5f5f5 100%); pointer-events: none; z-index: 5;">
    </div>
</div>

<div class="category-wrapper" style="position: relative; z-index: 10; margin-top: -300px; margin-bottom: 30px;">
    <div class="container-xl category shadow-sm"
        style="background: #fff; border-radius: 12px; border: none; padding: 15px 0;">

        <div class="px-4 pb-4 pt-2">
            <h3 class="fw-bold mb-0" style="font-size: 1.3rem; color: #333;">Danh mục nổi bật</h3>
        </div>

        <div class="row g-2 px-2">
            <?php if (!empty($danhMucNoiBat)): ?>
                <?php foreach ($danhMucNoiBat as $dm): ?>
                    <div class="col-6 col-md-3 col-lg-custom-8">
                        <div class="category-item" style="border: none; text-align: center;">
                            <a href="/danh-muc/<?php echo htmlspecialchars($dm['slug']); ?>" class="text-decoration-none">
                                <div class="category-image" style="background: transparent;">
                                    <?php if (!empty($dm['icon_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($dm['icon_url']); ?>"
                                            alt="<?php echo htmlspecialchars($dm['ten']); ?>">
                                    <?php else: ?>
                                        <img src="<?= ASSET_URL ?>/assets/client/images/icon/phone.png"
                                            alt="<?php echo htmlspecialchars($dm['ten']); ?>">
                                    <?php endif; ?>
                                </div>
                                <p class="category-title mb-0"
                                    style="font-size: 0.85rem; font-weight: bold; color: #333; margin-top: 8px;">
                                    <?php echo htmlspecialchars($dm['ten']); ?>
                                </p>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f5f5f5 !important;
    }

    .slider-card,
    .product,
    .category-wrapper,
    .suggestion-wrapper {
        background-color: transparent !important;
    }

    .continuous-slider-wrapper {
        overflow: hidden;
        width: 100%;
        margin: 0 24px;
        position: relative;
        padding: 15px 0;
        min-height: 400px;
    }

    .continuous-slider-wrapper::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 100px;

        background: linear-gradient(to right, #fff 0%, transparent 100%);
        z-index: 10;
        pointer-events: none;
    }

    .continuous-slider-wrapper::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 100px;
        background: linear-gradient(to left, #fff 0%, transparent 100%);
        z-index: 10;
        pointer-events: none;
    }

    .continuous-slider-track {
        display: flex;
        flex-wrap: nowrap;
        width: max-content;
        animation: marquee-scroll 5s linear infinite;
    }

    .continuous-slider-track:hover {
        animation-play-state: paused;
    }

    @keyframes marquee-scroll {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }
    }

    .continuous-slider-item {
        flex: 0 0 280px;
        width: 280px;
        padding: 0 10px;
    }

    @media (max-width: 768px) {
        .continuous-slider-item {
            flex: 0 0 220px;
            width: 220px;
        }
    }

    .custom-hover-card {
        transition: box-shadow 0.3s ease;
    }

    .custom-hover-zoom {
        transition: transform 0.5s linear;
    }

    .custom-hover-card:hover .custom-hover-zoom {
        transform: scale(1.02);
    }

    .category-item,
    .suggestion-item {
        transition: all 0.2s ease;
    }

    .category-item .category-image img,
    .suggestion-item .suggestion-image img {
        transition: transform 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }

    .category-item:hover .category-image img,
    .suggestion-item:hover .suggestion-image img {
        transform: scale(1.15);
    }
</style>

<div class="slider-card mt-4" style="padding: 0;">
    <div class="container-xl shadow-sm" style="background: #fff; border-radius: 12px; padding: 15px 0;">

        <div class="px-4 pb-2 pt-2">
            <p class="fs-4 fw-bold text-danger mb-0"><i class="fa fa-fire-flame-curved"></i> Khuyến mãi</p>
        </div>

        <div class="continuous-slider-wrapper">
            <?php
            require_once dirname(__DIR__, 3) . '/models/entities/SanPham.php';
            $spModel = new SanPham();
            ?>
            <div class="continuous-slider-track">
                <?php if (!empty($sanPhamKhuyenMai)): ?>
                    <?php
                    for ($i = 0; $i < 2; $i++):
                        foreach ($sanPhamKhuyenMai as $sp):
                            $giaSauGiam = $spModel->tinhGiaSauKhuyenMai(
                                (float) ($sp['gia_hien_thi'] ?? 0),
                                (string) ($sp['loai_giam'] ?? 'SO_TIEN'),
                                (float) ($sp['gia_tri_giam'] ?? 0),
                                isset($sp['giam_toi_da']) ? (float) $sp['giam_toi_da'] : null
                            );

                            // Cũng cần ép kiểu ở phép tính này để tránh lỗi toán học với NULL
                            $tienGiam = (float) ($sp['gia_hien_thi'] ?? 0) - $giaSauGiam;
                            ?>
                            <div class="continuous-slider-item">
                                <div class="p-2 border rounded-3 bg-white custom-hover-card h-100 mx-1">
                                    <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>"
                                        class="text-dark text-decoration-none d-block">
                                        <div class="position-relative w-100 d-flex justify-content-center overflow-hidden rounded-3"
                                            style="height: 250px;">
                                            <?php if (!empty($sp['anh_chinh'])): ?>
                                                <img src="<?php echo htmlspecialchars($sp['anh_chinh']); ?>"
                                                    alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                                    class="w-100 h-100 object-fit-cover custom-hover-zoom">
                                            <?php else: ?>
                                                <img src="<?= ASSET_URL ?>/assets/client/images/products/14.png"
                                                    alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                                    class="w-100 h-100 object-fit-cover custom-hover-zoom">
                                            <?php endif; ?>
                                            <div class="position-absolute bottom-0 start-0 p-2">
                                                <span class="text-white px-2 py-1 rounded-pill d-inline-block mb-1"
                                                    style="background-color: #4285f4; font-size: 0.75rem;">Ưu đãi
                                                    <?php echo number_format($tienGiam, 0, ',', '.'); ?>đ</span><br>
                                                <span class="text-white px-2 py-1 rounded-pill d-inline-block"
                                                    style="background-color: #66cd42; font-size: 0.75rem;">Trả góp 0%</span>
                                            </div>
                                        </div>
                                        <div class="mt-3 px-1">
                                            <h3 class="fs-6 fw-semibold mb-3 text-truncate">
                                                <?php echo htmlspecialchars($sp['ten_san_pham']); ?>
                                            </h3>
                                            <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                                <span class="px-2 py-1 rounded-pill text-white fw-bold"
                                                    style="background-color: #eb0501; font-size: 0.9rem;"><?php echo number_format($giaSauGiam, 0, ',', '.'); ?>đ</span>
                                                <span class="text-secondary text-decoration-line-through"
                                                    style="font-size: 0.85rem;"><?php echo number_format($sp['gia_hien_thi'], 0, ',', '.'); ?>đ</span>
                                            </div>
                                            <div class="bg-light p-2 rounded-3 mt-3">
                                                <span class="text-secondary" style="font-size: 0.75rem;">Giảm thêm 150.000đ khi TT
                                                    online 100% qua thẻ Mastercard</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <?php
                        endforeach;
                    endfor;
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="suggestion-wrapper mt-4 mb-4" style="position: relative; z-index: 10;">
    <div class="container-xl suggestion shadow-sm position-relative hover-slider-container"
        style="background: #fff; border-radius: 12px; border: none; padding: 15px 0;">

        <div class="px-4 pb-4 pt-2">
            <h3 class="fw-bold mb-0" style="font-size: 1.3rem; color: #333;">Gợi ý cho bạn</h3>
        </div>

        <div class="suggestion-slider-viewport mx-2" id="suggestionViewport">
            <div class="suggestion-slider-track" id="suggestionTrack">
                <?php if (!empty($danhMucGoiY)): ?>
                    <?php foreach ($danhMucGoiY as $dm): ?>
                        <div class="suggestion-slide-item">
                            <div class="suggestion-item" style="border: none; text-align: center;">
                                <a href="/san-pham/<?php echo htmlspecialchars($dm['slug']); ?>" class="text-decoration-none">
                                    <div class="suggestion-image" style="background: transparent;">
                                        <?php if (!empty($dm['icon_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($dm['icon_url']); ?>"
                                                alt="<?php echo htmlspecialchars($dm['ten']); ?>">
                                        <?php else: ?>
                                            <img src="<?= ASSET_URL ?>/assets/client/images/icon/phone.png"
                                                alt="<?php echo htmlspecialchars($dm['ten']); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <p class="suggestion-title mb-0"
                                        style="font-size: 0.85rem; font-weight: bold; color: #333; margin-top: 8px;">
                                        <?php echo htmlspecialchars($dm['ten']); ?>
                                    </p>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="custom-scrollbar-wrapper" id="scrollbarWrapper">
            <div class="custom-scrollbar-track">
                <div class="custom-scrollbar-thumb" id="sugScrollThumb"></div>
            </div>
        </div>

        <button class="custom-slider-btn prev-btn" id="sugPrevBtn" type="button">
            <i class="fa fa-chevron-left"></i>
        </button>
        <button class="custom-slider-btn next-btn" id="sugNextBtn" type="button">
            <i class="fa fa-chevron-right"></i>
        </button>

    </div>
</div>

<style>
    .suggestion-slider-viewport {
        overflow: hidden;
        width: 100%;
        padding-right: 25px;
    }

    .suggestion-slider-track {
        display: grid;
        grid-template-rows: repeat(2, 1fr);
        grid-auto-flow: column;
        grid-auto-columns: calc(100% / 8);
        transition: transform 0.4s ease-in-out;
    }

    .suggestion-slide-item {
        padding: 6px;
        width: 100%;
        user-select: none;
        /* Không cho phép bôi đen text */
        -webkit-user-drag: none;
        /* Chặn kéo thẻ a/img trên các trình duyệt lõi webkit */
    }

    .custom-slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
        background-color: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 50%;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .custom-slider-btn.prev-btn {
        left: -15px;
    }

    .custom-slider-btn.next-btn {
        right: -15px;
    }

    .hover-slider-container:hover .custom-slider-btn {
        opacity: 0.9;
        visibility: visible;
    }

    .hover-slider-container .custom-slider-btn:hover {
        opacity: 1;
        color: #d70018;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .custom-scrollbar-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-top: 20px;
        margin-bottom: 5px;
    }

    .custom-scrollbar-track {
        width: 80px;
        height: 8px;
        background-color: #dcdcdc;
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }

    .custom-scrollbar-thumb {
        height: 100%;
        background-color: #999;
        border-radius: 10px;
        position: absolute;
        top: 0;
        left: 0;
        /* Hiệu ứng trượt cho Desktop */
        transition: left 0.4s ease-in-out;
    }

    @media (max-width: 992px) {
        .suggestion-slider-viewport {
            overflow-x: auto;
            overflow-y: hidden;
            padding-right: 0;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            scroll-snap-type: x mandatory;
        }

        .suggestion-slider-viewport::-webkit-scrollbar {
            display: none;
        }

        .suggestion-slider-track {
            transform: none !important;
            transition: none !important;
            grid-auto-columns: calc(100% / 4);
        }

        .suggestion-slide-item {
            scroll-snap-align: start;
        }

        .custom-slider-btn {
            display: none !important;
        }

        .custom-scrollbar-thumb {
            transition: none;
        }
    }

    @media (max-width: 768px) {
        .suggestion-slider-track {
            grid-auto-columns: calc(100% / 2.3);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const track = document.getElementById('suggestionTrack');
        const viewport = document.getElementById('suggestionViewport');
        const prevBtn = document.getElementById('sugPrevBtn');
        const nextBtn = document.getElementById('sugNextBtn');

        const scrollbarWrapper = document.getElementById('scrollbarWrapper');
        const scrollThumb = document.getElementById('sugScrollThumb');

        if (!track || !prevBtn || !nextBtn || !viewport) return;

        const items = track.querySelectorAll('.suggestion-slide-item');
        const totalItems = items.length;
        const totalCols = Math.ceil(totalItems / 2);

        let currentCol = 0;

        // Các biến dùng cho tính năng Kéo thả (Drag)
        let isDown = false;
        let startX;
        let scrollLeft;
        let isDragging = false;

        function syncScrollbar() {
            if (!scrollThumb || !scrollbarWrapper) return;

            const sLeft = viewport.scrollLeft;
            const sWidth = viewport.scrollWidth;
            const cWidth = viewport.clientWidth;

            if (sWidth <= cWidth) {
                scrollbarWrapper.style.display = 'none';
                return;
            } else {
                scrollbarWrapper.style.display = 'flex';
            }

            const thumbWidthPercent = (cWidth / sWidth) * 100;
            scrollThumb.style.width = `${thumbWidthPercent}%`;

            const scrollPercent = sLeft / (sWidth - cWidth);
            const maxLeft = 100 - thumbWidthPercent;
            scrollThumb.style.left = `${scrollPercent * maxLeft}%`;

            if (window.innerWidth > 992) {
                const itemWidth = viewport.clientWidth / 8;
                currentCol = Math.round(sLeft / itemWidth);
                updateButtons();
            }
        }

        function updateButtons() {
            if (window.innerWidth <= 992) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
                return;
            }

            const visibleCols = 8;
            const maxCols = Math.max(0, totalCols - visibleCols);

            if (maxCols <= 0) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            } else {
                prevBtn.style.display = currentCol === 0 ? 'none' : 'flex';
                nextBtn.style.display = currentCol >= maxCols ? 'none' : 'flex';
            }
        }

        function updateSliderByButton() {
            if (window.innerWidth <= 992) return;

            const visibleCols = 8;
            const maxCols = Math.max(0, totalCols - visibleCols);

            if (currentCol > maxCols) currentCol = maxCols;
            if (currentCol < 0) currentCol = 0;

            const itemWidth = viewport.clientWidth / visibleCols;

            viewport.scrollTo({
                left: currentCol * itemWidth,
                behavior: 'smooth'
            });

            updateButtons();
        }

        function initSlider() {
            track.style.transform = '';

            if (window.innerWidth > 992) {
                viewport.style.overflowX = 'hidden';
                viewport.style.scrollBehavior = 'smooth';
            } else {
                viewport.style.overflowX = 'auto';
                viewport.style.scrollBehavior = 'auto';
            }

            updateButtons();
            syncScrollbar();
        }

        nextBtn.addEventListener('click', () => {
            currentCol += 4;
            updateSliderByButton();
        });

        prevBtn.addEventListener('click', () => {
            currentCol -= 4;
            updateSliderByButton();
        });

        viewport.addEventListener('scroll', () => {
            syncScrollbar();
        });

        window.addEventListener('resize', initSlider);

        // --- XỬ LÝ KÉO THẢ (DRAG TO SCROLL) ---

        // BƯỚC 1: Ngăn chặn trình duyệt tự kéo bóng mờ của ảnh/link
        const draggables = viewport.querySelectorAll('a, img');
        draggables.forEach(el => {
            el.addEventListener('dragstart', (e) => e.preventDefault());
        });

        viewport.addEventListener('mousedown', (e) => {
            isDown = true;
            isDragging = false; // Đặt lại cờ kéo mỗi lần nhấn chuột xuống
            viewport.style.cursor = 'grabbing';
            viewport.style.scrollBehavior = 'auto';
            startX = e.pageX - viewport.offsetLeft;
            scrollLeft = viewport.scrollLeft;
        });

        viewport.addEventListener('mouseleave', () => {
            isDown = false;
            viewport.style.cursor = 'default';
            viewport.style.scrollBehavior = 'smooth';
            setTimeout(() => { isDragging = false; }, 50);
        });

        viewport.addEventListener('mouseup', () => {
            isDown = false;
            viewport.style.cursor = 'default';
            viewport.style.scrollBehavior = 'smooth';

            // BƯỚC 2: Cố tình delay 50 mili-giây để sự kiện 'click' kịp nhận diện isDragging đang là true
            setTimeout(() => {
                isDragging = false;
            }, 50);
        });

        viewport.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault(); // Ngăn bôi đen văn bản

            const x = e.pageX - viewport.offsetLeft;
            const walk = (x - startX) * 2;

            if (Math.abs(walk) > 5) {
                isDragging = true;
            }

            viewport.scrollLeft = scrollLeft - walk;
        });

        // Ngăn chặn sự kiện click vào link nếu hệ thống xác định là thao tác kéo
        const links = viewport.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                if (isDragging) {
                    e.preventDefault();
                }
            });
        });

        initSlider();
    });
</script>

<?php if (!empty($bannerMid[1])): ?>
    <div class="banner-2 mt-4">
        <div class="container-xl">
            <div class="row">
                <div class="col-12 banner shadow-sm rounded-3">
                    <a href="<?php echo htmlspecialchars($bannerMid[1]['link_dich'] ?? '#'); ?>">
                        <img src="<?php echo htmlspecialchars($bannerMid[1]['hinh_anh_desktop']); ?>"
                            style="width:100%; border-radius: 8px;">
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="product mt-4" style="padding: 0;">
    <div class="container-xl shadow-sm" style="background: #fff; border-radius: 12px; padding: 15px 0;">
        <div class="px-4 pb-3 pt-2">
            <p class="fs-4 fw-bold mb-0">Điện thoại</p>
        </div>
        <div class="row px-3 mx-0">
            <?php if (!empty($sanPhamDienThoai)): ?>
                <?php foreach ($sanPhamDienThoai as $sp): ?>
                    <div class="col-lg-3 col-md-4 col-6 mb-4">
                        <div class="p-2 border rounded-3 bg-white custom-hover-card h-100">
                            <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>"
                                class="text-dark text-decoration-none d-block">
                                <div class="position-relative w-100 d-flex justify-content-center overflow-hidden rounded-3"
                                    style="height: 250px; background-color: #fff;">
                                    <?php if (!empty($sp['anh_chinh'])): ?>
                                        <img src="<?php echo htmlspecialchars($sp['anh_chinh']); ?>"
                                            alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                            class="w-100 h-100 object-fit-contain custom-hover-zoom">
                                    <?php else: ?>
                                        <img src="<?= ASSET_URL ?>/assets/client/images/products/14.png"
                                            alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                            class="w-100 h-100 object-fit-contain custom-hover-zoom">
                                    <?php endif; ?>
                                    <div class="position-absolute bottom-0 start-0 p-2">
                                        <span class="text-white px-2 py-1 rounded-pill d-inline-block"
                                            style="background-color: #66cd42; font-size: 0.75rem;">Trả góp 0%</span>
                                    </div>
                                </div>
                                <div class="mt-3 px-1">
                                    <h3 class="fs-6 fw-semibold mb-3 text-truncate">
                                        <?php echo htmlspecialchars($sp['ten_san_pham']); ?>
                                    </h3>
                                    <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                        <span class="px-2 py-1 rounded-pill text-white fw-bold"
                                            style="background-color: #eb0501; font-size: 0.9rem;"><?php echo number_format($sp['gia_hien_thi'], 0, ',', '.'); ?>đ</span>
                                    </div>
                                    <div class="bg-light p-2 rounded-3 mt-3">
                                        <span class="text-secondary" style="font-size: 0.75rem;">Giảm thêm 150.000đ khi TT
                                            online 100% qua thẻ Mastercard</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($bannerMid[2])): ?>
    <div class="banner-3 mt-4">
        <div class="container-xl">
            <div class="row">
                <div class="col-12 banner shadow-sm rounded-3">
                    <a href="<?php echo htmlspecialchars($bannerMid[2]['link_dich'] ?? '#'); ?>">
                        <img src="<?php echo htmlspecialchars($bannerMid[2]['hinh_anh_desktop']); ?>"
                            style="width:100%; border-radius: 8px;">
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="product mt-4 mb-4" style="padding: 0;">
    <div class="container-xl shadow-sm" style="background: #fff; border-radius: 12px; padding: 15px 0;">
        <div class="px-4 pb-3 pt-2">
            <p class="fs-4 fw-bold mb-0">Laptop</p>
        </div>
        <div class="row px-3 mx-0">
            <?php if (!empty($sanPhamLaptop)): ?>
                <?php foreach ($sanPhamLaptop as $sp): ?>
                    <div class="col-lg-3 col-md-4 col-6 mb-4">
                        <div class="p-2 border rounded-3 bg-white custom-hover-card h-100">
                            <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>"
                                class="text-dark text-decoration-none d-block">
                                <div class="position-relative w-100 d-flex justify-content-center overflow-hidden rounded-3"
                                    style="height: 250px; background-color: #fff;">
                                    <?php if (!empty($sp['anh_chinh'])): ?>
                                        <img src="<?php echo htmlspecialchars($sp['anh_chinh']); ?>"
                                            alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                            class="w-100 h-100 object-fit-contain custom-hover-zoom">
                                    <?php else: ?>
                                        <img src="<?= ASSET_URL ?>/assets/client/images/products/20.jpg"
                                            alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                            class="w-100 h-100 object-fit-contain custom-hover-zoom">
                                    <?php endif; ?>
                                    <div class="position-absolute bottom-0 start-0 p-2">
                                        <span class="text-white px-2 py-1 rounded-pill d-inline-block"
                                            style="background-color: #66cd42; font-size: 0.75rem;">Trả góp 0%</span>
                                    </div>
                                </div>
                                <div class="mt-3 px-1">
                                    <h3 class="fs-6 fw-semibold mb-3 text-truncate">
                                        <?php echo htmlspecialchars($sp['ten_san_pham']); ?>
                                    </h3>
                                    <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                        <span class="px-2 py-1 rounded-pill text-white fw-bold"
                                            style="background-color: #eb0501; font-size: 0.9rem;"><?php echo number_format($sp['gia_hien_thi'], 0, ',', '.'); ?>đ</span>
                                    </div>
                                    <div class="bg-light p-2 rounded-3 mt-3">
                                        <span class="text-secondary" style="font-size: 0.75rem;">Giảm thêm 150.000đ khi TT
                                            online 100% qua thẻ Mastercard</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="category-wrapper mt-4" style="padding: 0;">
    <div class="container-xl shadow-sm" style="background: #fff; border-radius: 12px; padding: 15px 0;">
        <div class="px-4 pb-3 pt-2">
            <p class="fs-4 fw-bold mb-0">Phụ kiện</p>
        </div>
        <div class="row px-3 mx-0">
            <?php if (!empty($sanPhamPhuKien)): ?>
                <?php foreach (array_slice($sanPhamPhuKien, 0, 12) as $sp): ?>
                    <div class="col-lg-2 col-md-4 col-6 mb-4">
                        <div class="p-2 border rounded-3 bg-white custom-hover-card h-100">
                            <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>"
                                class="text-dark text-decoration-none d-block">
                                <div class="position-relative w-100 d-flex justify-content-center overflow-hidden rounded-3"
                                    style="height: 250px;">
                                    <img src="<?php echo htmlspecialchars($sp['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png'); ?>"
                                        alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                        class="w-100 h-100 object-fit-cover custom-hover-zoom">
                                </div>
                                <div class="mt-3 px-1">
                                    <h3 class="fs-6 fw-semibold mb-3 text-truncate">
                                        <?php echo htmlspecialchars($sp['ten_san_pham']); ?>
                                    </h3>
                                    <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                        <span class="px-2 py-1 rounded-pill text-white fw-bold"
                                            style="background-color: #eb0501; font-size: 0.9rem;">
                                            <?php echo number_format($sp['gia_hien_thi'], 0, ',', '.'); ?>đ
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-muted small p-3">Chưa có sản phẩm phụ kiện.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($bannerMid) && count($bannerMid) >= 3): ?>
    <div class="category-bottom mt-5 mb-5">
        <div class="container-xl">
            <div class="row g-3">
                <?php foreach (array_slice($bannerMid, 3, 3) as $b): ?>
                    <div class="col-lg-4 col-12">
                        <div class="category-bot-item shadow-sm rounded-3">
                            <a href="<?php echo htmlspecialchars($b['link_dich'] ?? '#'); ?>">
                                <img src="<?php echo htmlspecialchars($b['hinh_anh_desktop']); ?>"
                                    style="width:100%; border-radius: 8px;">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<style>
    .zalo-floating-button {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 55px;
        height: 55px;
        background: transparent;
        border: none;
        z-index: 999;
        cursor: pointer;
    }

    .zalo-floating-button img {
        width: 55px;
        transition: 0.3s;
    }

    .zalo-chat-widget {
        position: fixed;
        bottom: 170px;
        right: 30px;
        width: 360px;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 0 128px 0 rgba(0, 0, 0, 0.1), 0 32px 64px -48px rgba(0, 0, 0, 0.5);
        flex-direction: column;
        z-index: 1000;

        opacity: 0;
        pointer-events: none;
        transform: scale(0.2);
        transform-origin: bottom right;
        transition: 0.3s ease;
    }

    .zalo-chat-widget.show {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }

    .zalo-header {
        background: linear-gradient(135deg, #1a73e8, #2a6fe3);
        padding: 18px;
        color: white;
        position: relative;
    }

    .zalo-header-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .zalo-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .zalo-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        position: relative;
    }

    .zalo-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .zalo-online {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        background: #00c853;
        border-radius: 50%;
        border: 2px solid white;
    }

    .zalo-title {
        font-weight: 600;
        font-size: 16px;
    }

    .zalo-header-actions {
        display: flex;
        gap: 10px;
    }

    .zalo-circle-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
    }

    .zalo-circle-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .zalo-hello {
        margin-top: 15px;
    }

    .zalo-hello h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
    }

    .zalo-hello p {
        margin: 5px 0 0;
        font-size: 14px;
        opacity: 0.9;
    }

    .zalo-body {
        padding: 20px;
        text-align: center;
    }

    .zalo-body p {
        color: #777;
        margin-bottom: 20px;
    }

    .zalo-btn {
        display: block;
        padding: 14px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 10px;
        transition: 0.2s;
    }

    .zalo-btn-primary {
        background: #1a73e8;
        color: white;
    }

    .zalo-btn-secondary {
        background: #e5e7eb;
        color: #333;
    }

    .zalo-footer {
        border-top: 1px solid #eee;
        padding: 12px;
        text-align: center;
    }

    .zalo-footer span {
        margin: 0 10px;
        font-size: 13px;
        color: #777;
        cursor: pointer;
    }

    .zalo-footer .active {
        background: #e3efff;
        padding: 5px 10px;
        border-radius: 8px;
        color: #1a73e8;
    }

    @media (max-width: 768px) {
        .zalo-chat-widget {
            right: 10px;
            left: 10px;
            width: auto;
        }
    }

    #backToTopBtn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 99;
        border: none;
        outline: none;
        background-color: #e31837;
        color: white;
        cursor: pointer;
        width: 55px;
        height: 55px;
        border-radius: 50%;
        font-size: 22px;
        transition: all 0.3s ease;
        opacity: 0;
        visibility: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(20px);
    }

    #backToTopBtn.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        animation: gentlePulse 0.8s ease-in-out 2;
    }
</style>

<button class="zalo-floating-button" id="zaloBtn">
    <img src="https://page.widget.zalo.me/static/images/2.0/Logo.svg" alt="Zalo">
</button>

<div class="zalo-chat-widget" id="zaloWidget">
    <div class="zalo-header">
        <div class="zalo-header-top">
            <div class="zalo-info">
                <div class="zalo-avatar">
                    <img src="https://s160-ava-talk.zadn.vn/0/8/a/d/23/160/7433bce2f1ac6e26cdac43a73037f10d.jpg"
                        alt="Avatar">
                    <div class="zalo-online"></div>
                </div>
                <div class="zalo-title">FPT Shop</div>
            </div>
            <div class="zalo-header-actions">
                <div class="zalo-circle-btn">•••</div>
                <div class="zalo-circle-btn" id="zaloClose"><i class="fas fa-chevron-down text-white"></i></div>
            </div>
        </div>
        <div class="zalo-hello">
            <h2 id="greeting">Xin chào!</h2>
            <p id="greetingText">Rất vui khi được hỗ trợ bạn</p>
        </div>
    </div>
    <div class="zalo-body">
        <p id="chatPrompt">Bắt đầu trò chuyện với FPT Shop</p>
        <a href="https://zalo.me/YOUR_ZALO_OA_ID" target="_blank" class="zalo-btn zalo-btn-primary" id="chatBtn">Chat
            bằng Zalo</a>
        <a href="https://zalo.me/YOUR_ZALO_OA_ID" target="_blank" class="zalo-btn zalo-btn-secondary"
            id="quickChatBtn">Chat nhanh</a>
    </div>
    <div class="zalo-footer">
        <span class="lang-option active" data-lang="vi">Tiếng Việt</span>
        <span class="lang-option" data-lang="en">English</span>
    </div>
</div>

<button id="backToTopBtn" title="Lên đầu trang"><i class="fa fa-arrow-up"></i></button>

<script>
    (function () {
        var backBtn = document.getElementById('backToTopBtn');
        if (backBtn) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 300) {
                    backBtn.classList.add('show');
                } else {
                    backBtn.classList.remove('show');
                }
            });
            backBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        const translations = {
            vi: {
                greeting: "Xin chào!",
                greetingText: "Rất vui khi được hỗ trợ bạn",
                chatPrompt: "Bắt đầu trò chuyện với FPT Shop",
                chatBtn: "Chat bằng Zalo",
                quickChatBtn: "Chat nhanh"
            },
            en: {
                greeting: "Hello!",
                greetingText: "We're happy to support you",
                chatPrompt: "Start chatting with FPT Shop",
                chatBtn: "Chat on Zalo",
                quickChatBtn: "Quick chat"
            }
        };

        const greetingEl = document.getElementById('greeting');
        const greetingTextEl = document.getElementById('greetingText');
        const chatPromptEl = document.getElementById('chatPrompt');
        const chatBtnEl = document.getElementById('chatBtn');
        const quickChatBtnEl = document.getElementById('quickChatBtn');
        const langOptions = document.querySelectorAll('.lang-option');

        function setLanguage(lang) {
            const t = translations[lang];
            if (!t) return;
            greetingEl.innerText = t.greeting;
            greetingTextEl.innerText = t.greetingText;
            chatPromptEl.innerText = t.chatPrompt;
            chatBtnEl.innerText = t.chatBtn;
            quickChatBtnEl.innerText = t.quickChatBtn;

            langOptions.forEach(opt => {
                if (opt.getAttribute('data-lang') === lang) {
                    opt.classList.add('active');
                } else {
                    opt.classList.remove('active');
                }
            });
        }

        langOptions.forEach(opt => {
            opt.addEventListener('click', function (e) {
                const lang = this.getAttribute('data-lang');
                setLanguage(lang);
            });
        });

        setLanguage('vi');

        var zaloBtn = document.getElementById('zaloBtn');
        var zaloWidget = document.getElementById('zaloWidget');
        var zaloClose = document.getElementById('zaloClose');

        if (zaloBtn && zaloWidget) {
            zaloBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                zaloWidget.classList.toggle('show');
            });

            if (zaloClose) {
                zaloClose.addEventListener('click', function (e) {
                    e.stopPropagation();
                    zaloWidget.classList.remove('show');
                });
            }
        }
    })();
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>