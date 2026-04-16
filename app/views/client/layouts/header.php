<?php
// Load danh mục cho header
require_once __DIR__ . '/../../../core/HeaderHelper.php';

use App\Core\HeaderHelper;

// SỬ DỤNG HÀM LẤY DANH MỤC PHÂN CẤP CHA-CON
$danhMucTree = HeaderHelper::layDanhMucNavigation();

// Kiểm tra đăng nhập và lấy số lượng yêu thích
$isLoggedIn = \App\Core\Session::isLoggedIn();
$userRole = \App\Core\Session::getUserRole();
$accountUrl = ($isLoggedIn && $userRole === 'MEMBER') ? '/client/profile' : '/client/auth/login';

// Lấy số lượng sản phẩm yêu thích
$wishlistCount = 0;
if ($isLoggedIn && $userRole === 'MEMBER') {
    require_once __DIR__ . '/../../../models/relationships/YeuThich.php';
    $userId = \App\Core\Session::getUserId();
    $wishlistCount = YeuThich::demSoLuongYeuThich($userId);
}

// Lấy số lượng sản phẩm trong giỏ hàng
$cartCount = 0;
require_once __DIR__ . '/../../../models/entities/GioHang.php';
require_once __DIR__ . '/../../../models/entities/ChiTietGio.php';
$gioHangModel = new GioHang();
$chiTietGioModel = new ChiTietGio();

if ($isLoggedIn && $userRole === 'MEMBER') {
    // User đã đăng nhập
    $userId = \App\Core\Session::getUserId();
    $gioHang = $gioHangModel->layHoacTaoGioHangUser($userId);
    $cartCount = $chiTietGioModel->demSanPham($gioHang['id']);
} else {
    // Guest user
    $sessionId = session_id();
    if ($sessionId) {
        $gioHang = $gioHangModel->layHoacTaoGioHangGuest($sessionId);
        $cartCount = $chiTietGioModel->demSanPham($gioHang['id']);
    }
}
?>
<style>
    /* --- BẠN GIỮ NGUYÊN CSS NÀY, TÔI KHÔNG XÓA MỘT CHỮ NÀO --- */
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 1030;
        background-color: #cb1c22;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .navbar-main {
        background-color: #cb1c22;
    }

    /* FPT Shop để cả thanh đỏ */
    .fpt-menu-wrapper {
        position: relative;
        height: 100%;
        display: flex;
        align-items: center;
    }

    /* Nút Danh mục */
    .fpt-btn-menu {
        background-color: rgba(0, 0, 0, 0.15);
        /* Nền hơi tối nhẹ */
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .fpt-btn-menu:hover {
        background-color: rgba(0, 0, 0, 0.25);
    }

    .fpt-menu-wrapper::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        height: 10px;
        /* bằng với khoảng cách top của panel */
        background: transparent;
        pointer-events: auto;
        /* quan trọng: nhận di chuột */
    }

    /* --- MEGA MENU PANEL KHỔNG LỒ --- */
    .mega-menu-panel {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        width: 1200px;
        max-width: 100vw;
        /* Độ rộng fix chuẩn PC */
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        display: none;
        /* Ẩn mặc định */
        flex-direction: row;
        z-index: 1050;
        overflow: hidden;
    }

    /* Hiển thị khi hover vào wrapper */
    .fpt-menu-wrapper:hover .mega-menu-panel {
        display: flex;
    }

    /* --- CỘT 1: BÊN TRÁI (260px) --- */
    .mega-col-left {
        width: 260px;
        background: #fff;
        border-right: 1px solid #f0f0f0;
        padding: 15px 0;
        height: 600px;
        overflow-y: auto;
    }

    /* Scrollbar custom cho cột trái */
    .mega-col-left::-webkit-scrollbar {
        width: 4px;
    }

    .mega-col-left::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 4px;
    }

    .left-brand-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 0 15px 15px;
    }

    .left-brand-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #333;
        text-decoration: none;
        font-weight: 500;
    }

    .left-brand-item:hover,
    .left-brand-item.active {
        color: #cb1c22;
        font-weight: 600;
    }

    .left-brand-item img {
        width: 20px;
        height: 20px;
        object-fit: contain;
    }

    .menu-group-title {
        font-size: 0.75rem;
        color: #999;
        text-transform: uppercase;
        padding: 10px 15px 5px;
        position: relative;
    }

    .menu-group-title::before {
        content: "";
        position: absolute;
        left: 15px;
        right: 15px;
        top: 0;
        border-top: 1px solid #f0f0f0;
    }

    .left-nav-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        font-size: 0.9rem;
        transition: background 0.2s;
    }

    .left-nav-item i {
        width: 24px;
        text-align: center;
        font-size: 1.1rem;
        color: #555;
    }

    .left-nav-item:hover,
    .left-nav-item.active {
        background-color: #f8f9fa;
        color: #cb1c22;
        font-weight: 500;
    }

    .left-nav-item:hover i,
    .left-nav-item.active i {
        color: #cb1c22;
    }

    /* --- CỘT 2: Ở GIỮA (Phần lớn) --- */
    .mega-col-center {
        flex: 1;
        padding: 20px;
        background: #fff;
    }

    .center-title {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .brand-pills {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .brand-pill {
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        padding: 6px 16px;
        color: #333;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
        transition: border 0.2s;
    }

    .brand-pill:hover {
        border-color: #cb1c22;
        color: #cb1c22;
    }

    .suggest-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
        margin-bottom: 25px;
        text-align: center;
    }

    .suggest-item {
        text-decoration: none;
        color: #333;
    }

    .suggest-item img {
        width: 60px;
        height: 60px;
        object-fit: contain;
        margin-bottom: 8px;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 5px;
    }

    .suggest-item span {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .sub-cat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .sub-cat-col h6 {
        font-size: 0.9rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
        cursor: pointer;
    }

    .sub-cat-col h6:hover {
        color: #cb1c22;
    }

    .sub-cat-col a {
        display: block;
        color: #555;
        text-decoration: none;
        font-size: 0.85rem;
        margin-bottom: 8px;
        transition: color 0.2s;
    }

    .sub-cat-col a:hover {
        color: #cb1c22;
    }

    /* --- CỘT 3: BÊN PHẢI (Utilities - 260px) --- */
    .mega-col-right {
        width: 260px;
        background: #f8f9fa;
        /* Nền xám nhẹ */
        padding: 20px;
        border-left: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .right-btn {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #333;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        transition: all 0.2s;
    }

    .right-btn img {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }

    .right-btn:hover {
        border-color: #cb1c22;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        color: #cb1c22;
    }

    .promo-banner {
        margin-top: auto;
        border-radius: 12px;
        overflow: hidden;
    }

    .promo-banner img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Ẩn offcanvas trên PC để tránh đụng độ */
    .offcanvas {
        z-index: 1050 !important;
    }

    .btn-profile {
        background-color: rgba(0, 0, 0, 0.15);
        border: none;
        color: white;
    }

    .btn-profile:hover {
        background-color: rgba(0, 0, 0, 0.25);
        color: white;
    }

    /* Dropdown lịch sử tìm kiếm */
    .search-history-dropdown {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        right: 0;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        z-index: 1060;
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #eee;
    }

    .dropdown-header {
        border-bottom: 1px solid #f0f0f0;
        background: #fafafa;
    }

    .history-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .history-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        border-bottom: 1px solid #f5f5f5;
        cursor: pointer;
        transition: background 0.2s;
    }

    .history-item:hover {
        background: #f8f9fa;
    }

    .history-item .keyword {
        flex: 1;
        color: #333;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .history-item .delete-btn {
        color: #999;
        background: none;
        border: none;
        font-size: 0.8rem;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 20px;
    }

    .history-item .delete-btn:hover {
        background: #eee;
        color: #cb1c22;
    }

    .history-empty {
        padding: 20px;
        text-align: center;
        color: #999;
        font-size: 0.85rem;
    }

    /* Băng rôn chạy */
    .top-banner {
        background-color: #f5a623;
        color: #fff;
        overflow: hidden;
        white-space: nowrap;
        padding: 8px 0;
        font-size: 14px;
        font-weight: 500;
        position: relative;
        z-index: 1031;
        /* cao hơn header một chút nếu cần */
    }

    .marquee {
        display: inline-block;
        animation: marquee 20s linear infinite;
    }

    .marquee span {
        display: inline-block;
        padding-right: 50px;
    }

    @keyframes marquee {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }
    }

    /* Responsive trên mobile */
    @media (max-width: 768px) {
        .top-banner {
            font-size: 12px;
            padding: 6px 0;
        }

        .marquee {
            animation-duration: 15s;
        }
    }
</style>

<header class="sticky-header">

    <!-- Băng rôn chạy phía trên header -->
    <div class="top-banner">
        <div class="marquee">
            <span>🔥 ƯU ĐÃI LỚN – GIẢM ĐẾN 50% CHO ĐIỆN THOẠI, LAPTOP – MIỄN PHÍ VẬN CHUYỂN 🔥</span>
            <span>🔥 ƯU ĐÃI LỚN – GIẢM ĐẾN 50% CHO ĐIỆN THOẠI, LAPTOP – MIỄN PHÍ VẬN CHUYỂN 🔥</span>
        </div>
    </div>

    <div class="d-lg-none d-block">
        <div class="container-xl">
            <div class="row align-items-center py-2">
                <div class="col-auto d-flex align-items-center">
                    <button class="btn btn-link text-white p-0 me-2 text-decoration-none border-0" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                        <i class="fa fa-bars fs-3"></i>
                    </button>
                    <a href="/" style="line-height:0;">
                        <img src="https://cdn2.fptshop.com.vn/unsafe/360x0/filters:format(webp):quality(75)/small/logo_main_c9fbde96f1.png"
                            alt="FPT Shop" style="height:32px;">
                    </a>
                </div>

                <div class="col d-flex justify-content-end gap-3">
                    <a href="/yeu-thich" class="text-white text-decoration-none position-relative">
                        <i class="fa fa-heart fs-4"></i>
                        <?php if ($wishlistCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"
                                style="font-size: 0.6rem;">
                                <?= $wishlistCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="/gio-hang" class="text-white text-decoration-none position-relative">
                        <i class="fa fa-shopping-cart fs-3"></i>
                        <?php if ($cartCount > 0): ?>
                            <span id="cart-count-mobile"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"
                                style="font-size: 0.6rem;">
                                <?= $cartCount ?>
                            </span>
                        <?php else: ?>
                            <span id="cart-count-mobile"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark d-none"
                                style="font-size: 0.6rem;">0</span>
                        <?php endif; ?>
                    </a>
                </div>

                <div class="col-12 mt-2">
                    <form class="search-form position-relative" action="/san-pham" method="GET">
                        <input class="form-control rounded-pill ps-3" type="search" name="keyword"
                            placeholder="Nhập tên sản phẩm..." style="height: 38px;">
                        <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-danger"
                            type="submit" style="text-decoration: none;">
                            <i class="fa fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar-main d-none d-lg-block py-2">
        <div class="container-xl d-flex align-items-center">

            <div class="col-auto me-3">
                <a href="/" style="display:inline-block; line-height:0;">
                    <img src="https://cdn2.fptshop.com.vn/unsafe/360x0/filters:format(webp):quality(75)/small/logo_main_c9fbde96f1.png"
                        alt="FPT Shop" style="height:40px;">
                </a>
            </div>

            <div class="fpt-menu-wrapper me-3">
                <div class="fpt-btn-menu">
                    <i class="fa fa-bars fs-5"></i> Danh mục
                </div>

                <div class="mega-menu-panel">

                    <div class="mega-col-left">
                        <div class="left-brand-grid px-3 pt-2">
                            <a href="/san-pham?keyword=Apple" class="left-brand-item p-1"
                                onmouseenter="fetchBrandMenu('Apple', this)">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/48x0/filters:format(webp):quality(75)/small/apple_icon_menu_b4ebd564eb.png"
                                    alt="Apple"> Apple
                            </a>

                            <a href="/san-pham?keyword=Samsung" class="left-brand-item p-1"
                                onmouseenter="fetchBrandMenu('Samsung', this)">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/48x0/filters:format(webp):quality(75)/small/samsung_icon_menu_80d224e1c9.png"
                                    alt="Samsung"> Samsung
                            </a>

                            <a href="/san-pham?keyword=LG" class="left-brand-item p-1"
                                onmouseenter="fetchBrandMenu('LG', this)">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/48x0/filters:format(webp):quality(75)/small/LG_con_menu_8607e6758e.png"
                                    alt="LG"> LG
                            </a>

                            <a href="/san-pham?keyword=Xiaomi" class="left-brand-item p-1"
                                onmouseenter="fetchBrandMenu('Xiaomi', this)">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/48x0/filters:format(webp):quality(75)/small/xiaomi_icon_menu_c3719b2a43.png"
                                    alt="Xiaomi"> Xiaomi
                            </a>

                            <a href="/san-pham?keyword=Garmin" class="left-brand-item p-1"
                                onmouseenter="fetchBrandMenu('Garmin', this)">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/48x0/filters:format(webp):quality(75)/small/garmin_menu_d9a8802e3b.png"
                                    alt="Garmin"> Garmin
                            </a>
                        </div>

                        <div class="menu-group-title mt-2">Sản phẩm chính</div>

                        <?php foreach ($danhMucTree as $index => $category):
                            $iconClass = HeaderHelper::layIconClass($category['ten']);
                            $categoryUrl = '/danh-muc/' . $category['slug'];
                            $activeClass = ($index === 0) ? 'active' : '';
                        ?>
                            <a href="<?= htmlspecialchars($categoryUrl) ?>" class="left-nav-item <?= $activeClass ?>"
                                onmouseenter="fetchMegaMenu(<?= $category['id'] ?>, this)">
                                <i class="<?= htmlspecialchars($iconClass) ?>"></i>
                                <?= htmlspecialchars($category['ten']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <div class="mega-col-center" id="mega-center-content">
                        <div class="text-center py-5 mt-5">
                            <div class="spinner-border text-danger" role="status"></div>
                            <p class="text-muted mt-2">Đang tải dữ liệu...</p>
                        </div>
                    </div>

                    <div class="mega-col-right">
                        <a href="/danh-muc/may-doi-tra" class="right-btn">
                            <img src="https://cdn2.fptshop.com.vn/unsafe/96x0/filters:format(webp):quality(75)/small/may_cu_ca2257f72f.png"
                                alt="" style="width:28px; height:28px;">
                            Máy cũ
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex-grow-1 me-4 position-relative">
                <form class="search-form d-flex w-100 position-relative" action="/san-pham" method="GET"
                    autocomplete="off">
                    <input class="form-control rounded-pill ps-4" type="search" name="keyword" id="searchInput"
                        placeholder="Nhập tên điện thoại, laptop, phụ kiện... cần tìm" style="height: 42px;">
                    <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-danger"
                        type="submit" style="text-decoration: none;">
                        <i class="fa fa-magnifying-glass fs-5"></i>
                    </button>
                </form>
                <!-- Dropdown lịch sử tìm kiếm -->
                <div id="searchHistoryDropdown" class="search-history-dropdown" style="display: none;">
                    <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="fw-bold">Lịch sử tìm kiếm</span>
                        <div>
                            <a href="#" id="viewAllHistory" class="text-decoration-none me-3"
                                style="font-size: 0.8rem;">Xem tất cả</a>
                            <a href="#" id="clearAllHistory" class="text-decoration-none text-danger"
                                style="font-size: 0.8rem;">Xóa tất cả</a>
                        </div>
                    </div>
                    <div id="historyList" class="history-list">
                        <!-- Danh sách sẽ được load bằng JS -->
                        <div class="text-muted text-center py-2">Đang tải...</div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 align-items-center">
                <a href="<?= $accountUrl ?>" class="btn btn-profile rounded-circle p-2"
                    style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-user fs-5"></i>
                </a>
                <a href="/yeu-thich" class="btn btn-profile rounded-circle p-2 position-relative"
                    style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-heart fs-5"></i>
                    <?php if ($wishlistCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"
                            style="font-size: 0.65rem; padding: 0.25em 0.5em;">
                            <?= $wishlistCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="/gio-hang" class="btn btn-dark rounded-pill px-4 position-relative"
                    style="height: 42px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-shopping-cart text-white fs-5"></i>
                    <span class="text-white fw-bold">Giỏ hàng</span>
                    <?php if ($cartCount > 0): ?>
                        <span id="cart-count-desktop"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"
                            style="font-size: 0.65rem; padding: 0.25em 0.5em;">
                            <?= $cartCount ?>
                        </span>
                    <?php else: ?>
                        <span id="cart-count-desktop"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark d-none"
                            style="font-size: 0.65rem; padding: 0.25em 0.5em;">0</span>
                    <?php endif; ?>
                </a>
            </div>

        </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header bg-light">
            <h5 class="offcanvas-title fw-bold text-danger m-0">FPT SHOP</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush rounded-0">
                <?php foreach ($danhMucTree as $category):
                    $iconClass = HeaderHelper::layIconClass($category['ten']);
                    $categoryUrl = '/danh-muc/' . $category['slug'];
                ?>
                    <a href="<?= htmlspecialchars($categoryUrl) ?>"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="<?= htmlspecialchars($iconClass) ?> fa-fw me-2"></i>
                            <?= htmlspecialchars($category['ten']) ?></span>
                        <?php if (!empty($category['children'])): ?>
                            <i class="fa fa-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>

                <div class="p-3 bg-light text-muted fw-bold mt-2" style="font-size:0.8rem;">TIỆN ÍCH & TÀI KHOẢN</div>
                <a href="/khuyen-mai" class="list-group-item list-group-item-action"><i
                        class="fa fa-certificate fa-fw me-2"></i> Khuyến mãi</a>
                <a href="<?= $accountUrl ?>" class="list-group-item list-group-item-action"><i
                        class="fa fa-user fa-fw me-2"></i> Tài khoản của tôi</a>
            </div>
        </div>
    </div>

</header>

<script>
    const megaMenuCache = {};
    const brandMenuCache = {}; // Cache mới dành riêng cho thương hiệu
    let currentHoverId = null;
    let currentHoverBrand = null; // Theo dõi hãng đang hover

    // Load dữ liệu cho danh mục đầu tiên ngay khi vào trang (giữ nguyên)
    document.addEventListener('DOMContentLoaded', () => {
        const firstNavItem = document.querySelector('.left-nav-item');
        if (firstNavItem) {
            const onclickAttr = firstNavItem.getAttribute('onmouseenter');
            if (onclickAttr) {
                const idMatch = onclickAttr.match(/fetchMegaMenu\((\d+)/);
                if (idMatch) {
                    fetchMegaMenu(idMatch[1], firstNavItem);
                }
            }
        }
    });

    // --- HÀM 1: LẤY DỮ LIỆU THEO DANH MỤC (Đã chỉnh sửa để xóa active của Brand) ---
    async function fetchMegaMenu(id, element) {
        // Tắt active của TẤT CẢ danh mục và thương hiệu
        document.querySelectorAll('.left-nav-item, .left-brand-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        currentHoverBrand = null; // Reset trạng thái brand
        currentHoverId = id;
        const container = document.getElementById('mega-center-content');

        if (megaMenuCache[id]) {
            renderMegaMenu(megaMenuCache[id], container);
            return;
        }

        container.innerHTML = '<div class="text-center py-5 mt-5"><div class="spinner-border text-danger"></div></div>';

        try {
            const response = await fetch(`/api/mega-menu?id=${id}`);
            const result = await response.json();

            if (result.success) {
                megaMenuCache[id] = result.data;
                if (currentHoverId === id) { // Chống nháy
                    renderMegaMenu(result.data, container);
                }
            }
        } catch (error) {
            container.innerHTML = '<div class="text-danger p-3 text-center mt-5">Lỗi tải dữ liệu. Vui lòng thử lại.</div>';
        }
    }

    // --- HÀM 2: HÀM MỚI LẤY DỮ LIỆU THEO THƯƠNG HIỆU ---
    async function fetchBrandMenu(brandName, element) {
        // Tắt active của TẤT CẢ danh mục và thương hiệu
        document.querySelectorAll('.left-nav-item, .left-brand-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        currentHoverId = null; // Reset trạng thái danh mục
        currentHoverBrand = brandName;
        const container = document.getElementById('mega-center-content');

        if (brandMenuCache[brandName]) {
            renderMegaMenu(brandMenuCache[brandName], container);
            return;
        }

        container.innerHTML = '<div class="text-center py-5 mt-5"><div class="spinner-border text-danger"></div></div>';

        try {

            const response = await fetch(`/app/api/MegaMenuBrandApi.php?name=${encodeURIComponent(brandName)}`);

            const rawText = await response.text();

            try {

                const result = JSON.parse(rawText);

                if (result.success) {
                    brandMenuCache[brandName] = result.data;
                    if (currentHoverBrand === brandName) {
                        renderMegaMenu(result.data, container);
                    }
                } else {
                    container.innerHTML = `<div class="text-muted p-3 text-center mt-5">${result.message || 'Chưa có dữ liệu cho thương hiệu này.'}</div>`;
                }
            } catch (parseError) {

                console.error("Raw response:", rawText);
                container.innerHTML = `
                    <div class="text-danger p-3 text-start mt-4" style="max-height: 400px; overflow-y: auto; background: #fff3f3; border-radius: 8px;">
                        <b>Phát hiện lỗi từ Server (Không phải chuẩn JSON):</b><br/>
                        <code style="color: #d63384;">${rawText}</code>
                    </div>`;
            }
        } catch (error) {
            container.innerHTML = '<div class="text-danger p-3 text-center mt-5">Lỗi tải dữ liệu thương hiệu.</div>';
        }
    }

    function renderMegaMenu(data, container) {
        let html = '<div class="center-title">🔥 Gợi ý cho bạn</div>';

        // 1. Thương hiệu (Pills)
        if (data.brands && data.brands.length > 0) {
            html += '<div class="brand-pills">';
            data.brands.forEach(b => {
                html += `<a href="/san-pham?keyword=${encodeURIComponent(b.hang_san_xuat)}" class="brand-pill">${b.hang_san_xuat}</a>`;
            });
            html += '</div>';
        }

        // 2. Gợi ý Sản phẩm (5 Sản phẩm có ảnh chính)
        if (data.products && data.products.length > 0) {
            html += '<div class="suggest-grid">';
            data.products.forEach(p => {
                const img = p.anh_chinh || 'https://via.placeholder.com/60';
                const shortName = p.ten_san_pham.length > 25 ? p.ten_san_pham.substring(0, 25) + '...' : p.ten_san_pham;

                html += `
                <a href="/san-pham/${p.slug}" class="suggest-item">
                    <img src="${img}" alt="${p.ten_san_pham}">
                    <span>${shortName}</span>
                </a>`;
            });
            html += '</div>';
        } else {
            html += '<div class="text-muted mb-4 small">Chưa có sản phẩm nào cho danh mục này.</div>';
        }

        // 3. Danh mục con
        if (data.subCategories && data.subCategories.length > 0) {
            html += '<div class="sub-cat-grid mt-4">';

            let colHtml = '';
            data.subCategories.forEach((sub, index) => {
                if (index % 5 === 0) {
                    if (index > 0) colHtml += '</div>'; // Đóng cột
                    colHtml += `<div class="sub-cat-col">
                                    <h6>Phân loại <i class="fa fa-angle-right ms-1"></i></h6>`;
                }
                colHtml += `<a href="/danh-muc/${sub.slug}">${sub.ten}</a>`;
            });
            colHtml += '</div>';

            html += colHtml + '</div>';
        }

        container.innerHTML = html;
    }
    // ========== LỊCH SỬ TÌM KIẾM ==========
    const searchInput = document.getElementById('searchInput');
    const historyDropdown = document.getElementById('searchHistoryDropdown');
    const historyListDiv = document.getElementById('historyList');
    const viewAllBtn = document.getElementById('viewAllHistory');
    const clearAllBtn = document.getElementById('clearAllHistory');

    let currentHistoryData = [];

    // Kiểm tra đăng nhập qua session (có thể gán biến từ PHP)
    // Giả sử bạn đã có biến isLoggedIn ở đây, nếu không thì gọi API kiểm tra riêng
    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>; // $isLoggedIn đã có ở trên

    async function loadSearchHistory(limit = 5) {
        if (!isLoggedIn) {
            historyListDiv.innerHTML = '<div class="history-empty">Đăng nhập để xem lịch sử tìm kiếm</div>';
            return;
        }
        try {
            const response = await fetch(`/tim-kiem/lich-su?limit=${limit}`);
            const result = await response.json();
            if (result.success && result.data.length > 0) {
                currentHistoryData = result.data;
                renderHistoryList(result.data);
            } else {
                historyListDiv.innerHTML = '<div class="history-empty">Chưa có lịch sử tìm kiếm</div>';
            }
        } catch (error) {
            historyListDiv.innerHTML = '<div class="history-empty">Lỗi tải dữ liệu</div>';
        }
    }

    function renderHistoryList(history) {
        if (!history.length) {
            historyListDiv.innerHTML = '<div class="history-empty">Chưa có lịch sử tìm kiếm</div>';
            return;
        }
        let html = '';
        history.forEach(item => {
            html += `
            <div class="history-item" data-keyword="${escapeHtml(item.tu_khoa)}">
                <a href="/san-pham?keyword=${encodeURIComponent(item.tu_khoa)}" class="keyword">${escapeHtml(item.tu_khoa)}</a>
                <button class="delete-btn" data-keyword="${escapeHtml(item.tu_khoa)}"><i class="fa fa-trash-o"></i></button>
            </div>
        `;
        });
        historyListDiv.innerHTML = html;

        // Gắn sự kiện xóa từng từ khóa
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.stopPropagation();
                const keyword = btn.getAttribute('data-keyword');
                await deleteSingleKeyword(keyword);
            });
        });
    }

    async function deleteSingleKeyword(keyword) {
        if (!confirm(`Xóa từ khóa "${keyword}" khỏi lịch sử?`)) return;
        try {
            const response = await fetch('/tim-kiem/xoa-lich-su', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    keyword: keyword
                }) // cần sửa backend hỗ trợ xóa 1 từ
            });
            const result = await response.json();
            if (result.success) {
                loadSearchHistory(5); // reload lại 5 mục
            } else {
                alert('Xóa thất bại');
            }
        } catch (error) {
            alert('Lỗi kết nối');
        }
    }

    async function clearAllHistory() {
        if (!confirm('Bạn có chắc muốn xóa toàn bộ lịch sử tìm kiếm?')) return;
        try {
            const response = await fetch('/tim-kiem/xoa-lich-su', {
                method: 'POST'
            });
            const result = await response.json();
            if (result.success) {
                loadSearchHistory(5);
            } else {
                alert('Xóa thất bại');
            }
        } catch (error) {
            alert('Lỗi kết nối');
        }
    }

    // Xem tất cả: load 20 từ khóa (hoặc tất cả)
    function viewAllHistory() {
        loadSearchHistory(20); // hoặc 0 để lấy tất cả (cần backend hỗ trợ)
    }

    // Khi focus vào input
    searchInput.addEventListener('focus', () => {
        loadSearchHistory(5);
        historyDropdown.style.display = 'block';
    });

    // Ẩn dropdown khi click ra ngoài
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !historyDropdown.contains(e.target)) {
            historyDropdown.style.display = 'none';
        }
    });

    // Ngăn click vào dropdown làm mất focus input
    historyDropdown.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    // Xử lý nút xem tất cả và xóa tất cả
    if (viewAllBtn) viewAllBtn.addEventListener('click', (e) => {
        e.preventDefault();
        viewAllHistory();
    });
    if (clearAllBtn) clearAllBtn.addEventListener('click', (e) => {
        e.preventDefault();
        clearAllHistory();
    });

    // Helper escape HTML
    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Hàm cập nhật số lượng giỏ hàng
    function updateCartCount() {
        fetch('/gio-hang/dem-san-pham')
            .then(response => response.json())
            .then(data => {
                const count = data.count || 0;
                const cartCountMobile = document.getElementById('cart-count-mobile');
                const cartCountDesktop = document.getElementById('cart-count-desktop');

                if (cartCountMobile) {
                    cartCountMobile.textContent = count;
                    if (count > 0) {
                        cartCountMobile.classList.remove('d-none');
                    } else {
                        cartCountMobile.classList.add('d-none');
                    }
                }

                if (cartCountDesktop) {
                    cartCountDesktop.textContent = count;
                    if (count > 0) {
                        cartCountDesktop.classList.remove('d-none');
                    } else {
                        cartCountDesktop.classList.add('d-none');
                    }
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }

    // Expose hàm updateCartCount ra global scope để các trang khác có thể gọi
    window.updateCartCount = updateCartCount;
</script>