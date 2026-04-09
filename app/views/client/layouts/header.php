<?php
// Load danh mục cho header
require_once __DIR__ . '/../../../core/HeaderHelper.php';
use App\Core\HeaderHelper;

// SỬ DỤNG HÀM LẤY DANH MỤC PHÂN CẤP CHA-CON
$danhMucTree = HeaderHelper::layDanhMucNavigation();
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
    height: 10px;          /* bằng với khoảng cách top của panel */
    background: transparent;
    pointer-events: auto;  /* quan trọng: nhận di chuột */
}

    /* --- MEGA MENU PANEL KHỔNG LỒ --- */
    .mega-menu-panel {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        width: 1200px;
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
        grid-template-columns: 1fr 1px 1fr;
        gap: 10px;
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
</style>

<header class="sticky-header">

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

                <div class="col d-flex justify-content-end">
                    <a href="/gio-hang" class="text-white text-decoration-none position-relative">
                        <i class="fa fa-shopping-cart fs-3"></i>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"
                            style="font-size: 0.6rem;">0</span>
                    </a>
                </div>

                <div class="col-12 mt-2">
                    <form class="search-form position-relative" action="/tim-kiem" method="GET">
                        <input class="form-control rounded-pill ps-3" type="search" name="q"
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
                        <div class="left-brand-grid">
                            <a href="#" class="left-brand-item"><i class="fa-brands fa-apple fs-5"></i> Apple</a>
                            <a href="#" class="left-brand-item text-primary"><i class="fa-brands fa-windows fs-5"></i>
                                Samsung</a>
                            <a href="#" class="left-brand-item text-danger"><i class="fa-brands fa-lg fs-5"></i> LG</a>
                            <a href="#" class="left-brand-item" style="color: #ff6700;"><i
                                    class="fa-solid fa-mobile-screen"></i> Xiaomi</a>
                            <a href="#" class="left-brand-item"><i class="fa-solid fa-stopwatch"></i> Garmin</a>
                        </div>

                        <div class="menu-group-title mt-2">Sản phẩm chính</div>

                        <?php foreach ($danhMucTree as $index => $category):
                            $iconClass = HeaderHelper::layIconClass($category['ten']);
                            $categoryUrl = '/danh-muc/' . $category['slug'];
                            $activeClass = ($index === 0) ? 'active' : '';
                            ?>
                            <a href="<?= htmlspecialchars($categoryUrl) ?>" class="left-nav-item <?= $activeClass ?>">
                                <i class="<?= htmlspecialchars($iconClass) ?>"></i>
                                <?= htmlspecialchars($category['ten']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <div class="mega-col-center">
                        <div class="center-title">🔥 Gợi ý cho bạn</div>

                        <div class="brand-pills">
                            <a href="#" class="brand-pill"><i class="fa-brands fa-apple"></i> iPhone</a>
                            <a href="#" class="brand-pill text-primary fw-bold">SAMSUNG</a>
                            <a href="#" class="brand-pill" style="color:#ff6700;">xiaomi</a>
                            <a href="#" class="brand-pill text-success fw-bold">OPPO</a>
                            <a href="#" class="brand-pill fw-bold">HONOR</a>
                            <a href="#" class="brand-pill text-info fw-bold">TECNO</a>
                        </div>

                        <div class="suggest-grid">
                            <a href="#" class="suggest-item">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/150x0/filters:quality(100)/5G_49cfb591b6.png"
                                    alt="5G">
                                <span>Điện thoại 5G</span>
                            </a>
                            <a href="#" class="suggest-item">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/150x0/filters:quality(100)/Ai_15c9284227.png"
                                    alt="AI">
                                <span>Điện thoại AI</span>
                            </a>
                            <a href="#" class="suggest-item">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/150x0/filters:quality(100)/Gap_a8cf92fb6f.png"
                                    alt="Gập">
                                <span>Điện thoại gập</span>
                            </a>
                            <a href="#" class="suggest-item">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/150x0/filters:quality(100)/Gaming_phone_a1e53cd251.png"
                                    alt="Gaming">
                                <span>Gaming phone</span>
                            </a>
                            <a href="#" class="suggest-item">
                                <img src="https://cdn2.fptshop.com.vn/unsafe/150x0/filters:quality(100)/Icon_dien_thoai_Pho_thong_08d1fb5f09.png"
                                    alt="Phổ thông">
                                <span>Phổ thông 4G</span>
                            </a>
                        </div>

                        <div class="sub-cat-grid mt-4">
                            <div class="sub-cat-col">
                                <h6>Apple (iPhone) <i class="fa fa-angle-right ms-1"></i></h6>
                                <a href="#">iPhone 15 Series</a>
                                <a href="#">iPhone 14 Series</a>
                                <a href="#">iPhone 13 Series</a>

                                <h6 class="mt-4">Xiaomi <i class="fa fa-angle-right ms-1"></i></h6>
                                <a href="#">Poco Series</a>
                                <a href="#">Redmi Note Series</a>
                            </div>
                            <div class="sub-cat-col">
                                <h6>Samsung <i class="fa fa-angle-right ms-1"></i></h6>
                                <a href="#">Galaxy S Series</a>
                                <a href="#">Galaxy Z Series</a>
                                <a href="#">Galaxy A Series</a>

                                <h6 class="mt-4">HONOR <i class="fa fa-angle-right ms-1"></i></h6>
                                <a href="#">HONOR 90 Series</a>
                            </div>
                            <div class="sub-cat-col">
                                <h6>OPPO <i class="fa fa-angle-right ms-1"></i></h6>
                                <a href="#">OPPO Reno Series</a>
                                <a href="#">OPPO Find Series</a>

                                <h6 class="mt-4">Thương hiệu khác <i class="fa fa-angle-right ms-1"></i></h6>
                                <a href="#">Realme</a>
                                <a href="#">Vivo</a>
                            </div>
                        </div>
                    </div>

                    <div class="mega-col-right">
                        <a href="/may-cu" class="right-btn"><i class="fa-solid fa-mobile-screen fs-3 text-info"></i> Máy
                            cũ</a>
                        <a href="#" class="right-btn"><i class="fa-solid fa-newspaper fs-3 text-danger"></i> Thông tin
                            hay</a>
                        <a href="#" class="right-btn"><i class="fa-solid fa-money-check-dollar fs-3 text-warning"></i>
                            Sim thẻ Thanh toán</a>
                        <a href="#" class="right-btn"><i class="fa-solid fa-shield-halved fs-3 text-dark"></i> Đặc quyền
                            đối tác</a>
                        <a href="#" class="right-btn"><i class="fa-solid fa-handshake fs-3 text-primary"></i> Chiết khấu
                            DN</a>
                        <a href="#" class="right-btn"><i class="fa-solid fa-headset fs-3 text-success"></i> KTV hỗ
                            trợ</a>
                    </div>
                </div>
            </div>

            <div class="flex-grow-1 me-4">
                <form class="search-form d-flex w-100 position-relative" action="/tim-kiem" method="GET">
                    <input class="form-control rounded-pill ps-4" type="search" name="q"
                        placeholder="Nhập tên điện thoại, laptop, phụ kiện... cần tìm" style="height: 42px;">
                    <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-danger"
                        type="submit" style="text-decoration: none;">
                        <i class="fa fa-magnifying-glass fs-5"></i>
                    </button>
                </form>
            </div>

            <?php
            $isLoggedIn = \App\Core\Session::isLoggedIn();
            $userRole = \App\Core\Session::getUserRole();
            $accountUrl = ($isLoggedIn && $userRole === 'MEMBER') ? '/client/profile' : '/client/auth/login';
            ?>
            <div class="d-flex gap-3 align-items-center">
                <a href="<?= $accountUrl ?>" class="btn btn-profile rounded-circle p-2"
                    style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-user fs-5"></i>
                </a>
                <a href="/gio-hang" class="btn btn-dark rounded-pill px-4"
                    style="height: 42px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-shopping-cart text-white fs-5"></i>
                    <span class="text-white fw-bold">Giỏ hàng</span>
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
