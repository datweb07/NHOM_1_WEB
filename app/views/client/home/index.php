<?php
$pageTitle = 'FPT Shop - Trang chủ';
$additionalCSS = [
    '/public/assets/client/css/slider.css',
    '/public/assets/client/css/slider-card.css',
];
$additionalJS = [
    '/public/assets/client/js/slider.js',
    '/public/assets/client/js/slider-card.js',
];

ob_start();
?>

<div class="full-width-hero" style="position: relative; width: 100%; overflow: hidden;">

    <div class="wapper-slider hero-carousel" style="width: 100%; border: none; box-shadow: none;">

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
        style="position: absolute; bottom: 0; left: 0; width: 100%; height: 280px; background: linear-gradient(to bottom, transparent 0%, #f8f9fa 100%); pointer-events: none; z-index: 5;">
    </div>
</div>

<div class="category-wapper" style="position: relative; z-index: 10; margin-top: -300px;">
  <div class="container-xl category"
    style="background: #fff; border-radius: 12px; border: none; padding: 15px 0;">

    <div class="px-4 pb-4 pt-2">
      <h3 class="fw-bold mb-0" style="font-size: 1.3rem; color: #333;">Danh mục nổi bật</h3>
    </div>
    <div class="row g-2">
      <?php if (!empty($danhMucList)): ?>
        <?php foreach ($danhMucList as $dm): ?>
          <div class="col-lg-2 col-md-3 col-3">
            <div class="category-item" style="border: none; text-align: center;">
              <a href="/danh-muc/<?php echo htmlspecialchars($dm['slug']); ?>" class="text-decoration-none">
                <div class="img-category" style="background: transparent;">
                  <?php if (!empty($dm['icon_url'])): ?>
                    <img src="<?php echo htmlspecialchars($dm['icon_url']); ?>"
                         alt="<?php echo htmlspecialchars($dm['ten']); ?>"
                         style="width: 60px; height: 60px; object-fit: contain; display: inline-block;">
                  <?php else: ?>
                    <img src="/public/assets/client/images/icon/phone.png"
                         alt="<?php echo htmlspecialchars($dm['ten']); ?>"
                         style="width: 60px; height: 60px; object-fit: contain; display: inline-block;">
                  <?php endif; ?>
                </div>
                <p class="title-category mb-0" style="font-size: 0.85rem; font-weight: bold; color: #333;"><?php echo htmlspecialchars($dm['ten']); ?></p>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
    .continuous-slider-wrapper {
        overflow: hidden;
        width: 100%;
        position: relative;
        padding: 15px 0;
        min-height: 400px;
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

    .custom-hover-card:hover {
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
    }

    .custom-hover-zoom {
        transition: transform 0.5s linear;
    }

    .custom-hover-card:hover .custom-hover-zoom {
        transform: scale(1.05);
    }

  .category-item {
    transition: all 0.2s ease;
  }
  .category-item .img-category img {
    transition: transform 1s cubic-bezier(0.2, 0.9, 0.4, 1.1);
  }
  .category-item:hover .img-category img {
    transform: scale(1);
  }
</style>

<div class="slider-card mt-4">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <p class="fs-4 fw-bold text-danger py-2 mb-0"><i class="fa fa-fire-flame-curved"></i> Khuyến mãi</p>
            </div>
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
                                $sp['gia_hien_thi'],
                                $sp['loai_giam'],
                                $sp['gia_tri_giam'],
                                $sp['giam_toi_da']
                            );
                            $tienGiam = $sp['gia_hien_thi'] - $giaSauGiam;
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
                                                <img src="/public/assets/client/images/products/14.png"
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

<?php if (!empty($bannerMid[1])): ?>
    <div class="banner-2 mt-4">
        <div class="container-xl">
            <div class="row">
                <div class="col-12 banner">
                    <a href="<?php echo htmlspecialchars($bannerMid[1]['link_dich'] ?? '#'); ?>">
                        <img src="<?php echo htmlspecialchars($bannerMid[1]['hinh_anh_desktop']); ?>"
                            style="width:100%; border-radius: 8px;">
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="product mt-4">
    <div class="container-xl">
        <div class="product-wapper">
            <div class="row mb-3">
                <div class="col-12">
                    <p class="fs-4 fw-bold py-2 mb-0">Điện thoại</p>
                </div>
            </div>
            <div class="row">
                <?php if (!empty($sanPhamDienThoai)): ?>
                    <?php foreach ($sanPhamDienThoai as $sp): ?>
                        <div class="col-lg-3 col-md-4 col-6 mb-4">
                            <div class="p-2 border rounded-3 bg-white custom-hover-card h-100">
                                <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>"
                                    class="text-dark text-decoration-none d-block">
                                    <div class="position-relative w-100 d-flex justify-content-center overflow-hidden rounded-3"
                                        style="height: 250px;">
                                        <?php if (!empty($sp['anh_chinh'])): ?>
                                            <img src="<?php echo htmlspecialchars($sp['anh_chinh']); ?>"
                                                alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                                class="w-100 h-100 object-fit-cover custom-hover-zoom">
                                        <?php else: ?>
                                            <img src="/public/assets/client/images/products/14.png"
                                                alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                                class="w-100 h-100 object-fit-cover custom-hover-zoom">
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
</div>

<?php if (!empty($bannerMid[2])): ?>
    <div class="banner-3 mt-4">
        <div class="container-xl">
            <div class="row">
                <div class="col-12 banner">
                    <a href="<?php echo htmlspecialchars($bannerMid[2]['link_dich'] ?? '#'); ?>">
                        <img src="<?php echo htmlspecialchars($bannerMid[2]['hinh_anh_desktop']); ?>"
                            style="width:100%; border-radius: 8px;">
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="product mt-4 mb-5">
    <div class="container-xl">
        <div class="product-wapper">
            <div class="row mb-3">
                <div class="col-12">
                    <p class="fs-4 fw-bold py-2 mb-0">Laptop</p>
                </div>
            </div>
            <div class="row">
                <?php if (!empty($sanPhamLaptop)): ?>
                    <?php foreach ($sanPhamLaptop as $sp): ?>
                        <div class="col-lg-3 col-md-4 col-6 mb-4">
                            <div class="p-2 border rounded-3 bg-white custom-hover-card h-100">
                                <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>"
                                    class="text-dark text-decoration-none d-block">
                                    <div class="position-relative w-100 d-flex justify-content-center overflow-hidden rounded-3"
                                        style="height: 250px;">
                                        <?php if (!empty($sp['anh_chinh'])): ?>
                                            <img src="<?php echo htmlspecialchars($sp['anh_chinh']); ?>"
                                                alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                                class="w-100 h-100 object-fit-cover custom-hover-zoom">
                                        <?php else: ?>
                                            <img src="/public/assets/client/images/products/20.jpg"
                                                alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                                class="w-100 h-100 object-fit-cover custom-hover-zoom">
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
</div>

<div class="category-wapper mt-4">
    <div class="container-xl category">
        <div class="row">
            <div class="col-12">
                <p class="fs-4 fw-bold py-2 mb-0">Phụ kiện</p>
            </div>
        </div>
        <div class="row g-0 mt-3">
            <?php if (!empty($sanPhamPhuKien)): ?>
                <?php foreach (array_slice($sanPhamPhuKien, 0, 12) as $sp): ?>
                    <div class="col-lg-2 col-md-4 col-4 mb-4 text-center">
                        <div class="category-item">
                            <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>"
                                class="text-dark text-decoration-none">
                                <div class="img-category d-flex justify-content-center">
                                    <img src="<?php echo htmlspecialchars($sp['anh_chinh'] ?? '/public/assets/client/images/products/14.png'); ?>"
                                        alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>"
                                        style="width:60px;height:60px;object-fit:contain;">
                                </div>
                                <p class="title-category mt-2" style="font-size:0.85rem;">
                                    <?php echo htmlspecialchars($sp['ten_san_pham']); ?>
                                </p>
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
                        <div class="category-bot-item">
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