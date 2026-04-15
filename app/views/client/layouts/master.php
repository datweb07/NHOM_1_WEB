<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $pageTitle ?? 'FPT Shop'; ?></title>
    <link rel="icon" href="<?= ASSET_URL ?>/assets/client/images/header/1.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/client/css/main.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/client/css/grid.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/client/css/reponsive.css">

    <?php if (isset($additionalCSS) && is_array($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        main {
            background-color: #f5f5f5; /* Màu xám nhạt, bạn có thể đổi thành #f0f0f0 hoặc #e9ecef nếu muốn đậm hơn nữa */
            min-height: 50vh; /* Đảm bảo main luôn có độ cao nhất định nếu nội dung quá ít */
            padding-bottom: 20px;
        }

        .header-top {
            background: #d70018;
            padding: 8px 0;
        }

        .fpt-logo {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            gap: 6px;
        }

        .fpt-logo-box {
            background: #fff;
            border-radius: 4px;
            padding: 3px 7px;
            display: flex;
            align-items: center;
            font-weight: 900;
            font-size: 1.1rem;
            line-height: 1;
        }

        .fpt-logo-box .f {
            color: #d70018;
        }

        .fpt-logo-box .p {
            color: #ff6600;
        }

        .fpt-logo-box .t {
            color: #0070c0;
        }

        .fpt-logo-text {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            line-height: 1;
        }

        .fpt-logo-text span {
            display: block;
            font-size: 0.6rem;
            font-weight: 400;
        }

        .search-form .form-control {
            border-radius: 3px 0 0 3px;
            border: none;
            font-size: 0.88rem;
            height: 36px;
        }

        .search-form .form-control:focus {
            box-shadow: none;
        }

        .search-form .btn-search {
            background: #333;
            color: #fff;
            border-radius: 0 3px 3px 0;
            border: none;
            width: 42px;
            height: 36px;
        }

        .service-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff;
            text-decoration: none;
            font-size: 0.7rem;
            padding: 0 8px;
            gap: 3px;
        }

        .service-item i {
            font-size: 1.2rem;
        }

        .cart-wrapper {
            position: relative;
            display: inline-block;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -6px;
            background: #fff;
            color: #d70018;
            font-size: 0.6rem;
            font-weight: 700;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .service-dropdown {
            position: relative;
        }

        .service-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            min-width: 160px;
            z-index: 1000;
            padding: 4px 0;
            margin-top: 4px;
        }

        .service-dropdown:hover .service-dropdown-menu {
            display: block;
        }

        .service-dropdown-menu a {
            display: block;
            padding: 7px 14px;
            font-size: 0.83rem;
            color: #333;
            text-decoration: none;
        }

        .navbar-main {
            background: #222;
            padding: 0;
        }

        .navbar-main .nav-item {
            position: relative;
        }

        .navbar-main .nav-link {
            color: #fff !important;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 9px 11px !important;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .navbar-main .nav-link i {
            margin-right: 4px;
            font-size: 0.75rem;
        }

        .mega-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-top: 3px solid #d70018;
            min-width: 620px;
            padding: 16px;
            z-index: 1000;
        }

        .navbar-main .nav-item:hover .mega-menu {
            display: flex;
            gap: 18px;
        }

        .mega-col {
            flex: 1;
        }

        .mega-col-sm {
            flex: 0 0 120px;
        }

        .mega-section-title {
            font-weight: 700;
            font-size: 0.75rem;
            color: #d70018;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .mega-menu a {
            display: block;
            font-size: 0.82rem;
            color: #333;
            text-decoration: none;
            padding: 2px 0;
        }

        .hot-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #f0f0f0;
            text-decoration: none !important;
        }

        .hot-item:last-of-type {
            border-bottom: none;
        }

        .hot-item img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .hot-item-name {
            font-size: 0.76rem;
            color: #333;
        }

        .hot-item-price {
            font-size: 0.76rem;
            color: #d70018;
            font-weight: 700;
        }

        .simple-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-top: 3px solid #d70018;
            min-width: 180px;
            padding: 4px 0;
            z-index: 1000;
        }

        .navbar-main .nav-item:hover .simple-dropdown {
            display: block;
        }

        .simple-dropdown a {
            display: block;
            padding: 7px 16px;
            font-size: 0.83rem;
            color: #333;
            text-decoration: none;
        }

        .mega-banner {
            margin-top: 8px;
        }

        .mega-banner img {
            width: 100%;
        }

        .navbar-toggler {
            border: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .offcanvas-header {
            background: #d70018;
        }

        .offcanvas-header .btn-close {
            filter: invert(1);
        }

        .offcanvas-menu-item a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #eee;
            font-size: 0.88rem;
        }

        .offcanvas-menu-item a i {
            color: #d70018;
            width: 18px;
            text-align: center;
        }

        .profile-wrapper {
            background-color: #f4f4f4;
            padding: 30px 0;
        }

        .profile-sidebar {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .profile-sidebar-header {
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .profile-sidebar-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #d70018;
        }

        .profile-sidebar-header h3 {
            font-size: 16px;
            margin-top: 10px;
            color: #333;
        }

        .profile-menu {
            list-style: none;
            padding: 0;
        }

        .profile-menu li {
            margin-bottom: 10px;
        }

        .profile-menu li a {
            display: block;
            color: #555 !important;
            text-decoration: none;
            padding: 10px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .profile-menu li a:hover,
        .profile-menu li a.active {
            background-color: #fde8e8 !important;
            color: #d70018 !important;
            font-weight: bold;
        }

        .profile-menu li a i {
            width: 25px;
            margin-right: 8px;
        }

        .profile-content-box {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .profile-content-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .profile-content-header h2 {
            font-size: 20px;
            color: #333;
            margin: 0 0 5px 0;
        }

        .profile-content-header p {
            color: #777;
            font-size: 14px;
            margin: 0;
        }

        .btn-submit {
            background-color: #d70018;
            color: #fff;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 500;
        }

        .btn-submit:hover {
            background-color: #a0151b;
        }

        /* --- FOOTER FPT SHOP --- */
        .footer-fpt {
            background-color: #0f1013;
            color: #a1a1aa;
            font-size: 13px;
            padding-top: 50px;
        }

        .footer-fpt .footer-title {
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* Các link trong danh sách */
        .footer-fpt .footer-links li {
            margin-bottom: 12px;
        }

        .footer-fpt .footer-links a {
            color: #ffffffff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-fpt .footer-links a:hover {
            text-decoration: underline;
        }

        /* Phần tải ứng dụng */
        .footer-fpt .app-download {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .footer-fpt .qr-code {
            width: 75px;
            height: 75px;
            background: #fff;
            padding: 3px;
            border-radius: 4px;
        }

        .footer-fpt .store-buttons img {
            height: 35px;
            margin-bottom: 5px;
            display: block;
        }

        /* Mạng xã hội */
        .footer-fpt .social-icons a {
            display: inline-block;
            font-size: 24px;
            margin-right: 15px;
            transition: transform 0.3s;
        }

        /* Tổng đài */
        .footer-fpt .hotline-item {
            margin-bottom: 18px;
        }

        .footer-fpt .hotline-item p {
            margin: 0;
            line-height: 1.4;
        }

        .footer-fpt .hotline-number {
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            margin-top: 2px !important;
        }

        .footer-fpt .hotline-number span {
            font-size: 13px;
            font-weight: normal;
        }

        /* Phần Website cùng hệ thống (Middle) */
        /* Container bọc ngoài cùng */
.footer-fpt .footer-middle {
    border-top: 1px solid #27272a;
    border-bottom: 1px solid #27272a;
    padding: 20px 0;
    margin-top: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px; /* Khoảng cách giữa thẻ Label và Nhóm logo */
    flex-wrap: wrap;
}

.footer-fpt .website-label {
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
}

/* Nhóm chứa 3 đối tác */
.footer-fpt .partner-group {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

/* Từng khối đối tác nhỏ */
.footer-fpt .partner-item {
    padding: 0 30px;
    border-left: 1px solid #3f3f46; /* Đường kẻ dọc mờ phân cách */
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Xóa đường kẻ dọc ở phần tử đầu tiên (F.Studio) */
.footer-fpt .partner-item:first-child {
    border-left: none;
    padding-left: 0;
}

/* Chỉnh chữ mô tả nhỏ phía trên logo */
.footer-fpt .partner-item p {
    color: #ffffff;
    font-size: 12px;
    margin-top: 0;
    margin-bottom: 6px;
    font-weight: 500;
}

/* Chỉnh kích thước logo */
.footer-fpt .partner-item img {
    height: 26px; /* Bạn có thể tăng giảm tùy theo kích thước ảnh thực tế */
    width: auto;
    object-fit: contain;
}

/* Reponsive cho màn hình mobile (sẽ tự động rớt dòng cho gọn) */
@media (max-width: 768px) {
    .footer-fpt .footer-middle {
        flex-direction: column;
        gap: 15px;
    }
    .footer-fpt .partner-group {
        flex-direction: column;
        gap: 15px;
    }
    .footer-fpt .partner-item {
        border-left: none;
        padding: 0;
        align-items: center;
        text-align: center;
    }
}

        /* Phần Copyright (Bottom) */
        .footer-fpt .footer-bottom {
            padding: 25px 0;
            text-align: center;
            font-size: 12.5px;
            color: #ffffffff;
            line-height: 1.6;
        }

        .footer-fpt .footer-bottom p {
            margin-bottom: 8px;
            color: #a1a1aa
        }

        .footer-fpt .seo-links {
            margin-bottom: 15px !important;
        }

        .footer-fpt .seo-links a {
            color: #a1a1aa;
            text-decoration: none;
            padding: 0 4px;
        }

        .footer-fpt .seo-links a:hover {
            color: #ffffff;
        }
        .payment-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr); 
    gap: 8px; 
}

.payment-grid img {
    width: 100%;
    height: auto;
    background-color: #ffffff; 
    border-radius: 4px;
    object-fit: contain;
}
    </style>
</head>

<body>

    <?php require_once __DIR__ . '/header.php'; ?>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <?php require_once __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="<?= ASSET_URL ?>/assets/client/js/main.js"></script>

    <script>

        function updateCartCount() {
            fetch('/gio-hang/dem-san-pham')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const badges = document.querySelectorAll('.cart-badge, #cart-count');
                        badges.forEach(badge => {
                            badge.textContent = data.count || 0;
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }


        document.addEventListener('DOMContentLoaded', updateCartCount);
    </script>

    <?php if (isset($additionalJS) && is_array($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>


    <?php

    if (!isset($_SESSION)) {
        session_start();
    }
    
    require_once dirname(__DIR__, 3) . '/controllers/client/BannerController.php';
    $bannerController = new \App\Controllers\Client\BannerController();
    $popupBanners = $bannerController->layBannerPopup();
    
    if (!empty($popupBanners)):
        $popup = $popupBanners[0]; 
        $popupId = 'popup-banner-' . $popup['id'];
        $miniPopupId = 'mini-popup-' . $popup['id'];
        

        if (!isset($_SESSION['popup_shown_' . $popup['id']])):
    ?>
    

    <div id="<?= $miniPopupId ?>" class="mini-popup-teaser" style="display: none;">
        <button class="mini-popup-close" onclick="closeMiniPopup('<?= $miniPopupId ?>', <?= $popup['id'] ?>)">
            <i class="fas fa-times"></i>
        </button>
        <div class="mini-popup-image-wrapper" onclick="openFullPopup('<?= $popupId ?>', '<?= $miniPopupId ?>')">
            <picture>
                <source media="(max-width: 768px)" srcset="<?= htmlspecialchars($popup['hinh_anh_mobile'] ?? $popup['hinh_anh_desktop']) ?>">
                <img src="<?= htmlspecialchars($popup['hinh_anh_desktop']) ?>" alt="<?= htmlspecialchars($popup['tieu_de'] ?? 'Popup Banner') ?>" class="mini-popup-image">
            </picture>
            <div class="mini-popup-pulse"></div>
        </div>
    </div>

    <div id="<?= $popupId ?>" class="banner-popup-overlay" style="display: none;">
        <div class="banner-popup-content">
            <button class="banner-popup-close" onclick="closeFullPopup('<?= $popupId ?>', <?= $popup['id'] ?>)">
                <i class="fas fa-times"></i>
            </button>
            <?php if (!empty($popup['link_dich'])): ?>
                <a href="<?= htmlspecialchars($popup['link_dich']) ?>" target="_blank">
                    <picture>
                        <source media="(max-width: 768px)" srcset="<?= htmlspecialchars($popup['hinh_anh_mobile'] ?? $popup['hinh_anh_desktop']) ?>">
                        <img src="<?= htmlspecialchars($popup['hinh_anh_desktop']) ?>" alt="<?= htmlspecialchars($popup['tieu_de'] ?? 'Popup Banner') ?>" class="banner-popup-image">
                    </picture>
                </a>
            <?php else: ?>
                <picture>
                    <source media="(max-width: 768px)" srcset="<?= htmlspecialchars($popup['hinh_anh_mobile'] ?? $popup['hinh_anh_desktop']) ?>">
                    <img src="<?= htmlspecialchars($popup['hinh_anh_desktop']) ?>" alt="<?= htmlspecialchars($popup['tieu_de'] ?? 'Popup Banner') ?>" class="banner-popup-image">
                </picture>
            <?php endif; ?>
            <div class="banner-popup-checkbox">
                <label>
                    <input type="checkbox" id="dont-show-again-<?= $popup['id'] ?>">
                    Không hiển thị lại trong hôm nay
                </label>
            </div>
        </div>
    </div>

    <style>
        .mini-popup-teaser {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 9998;
            cursor: pointer;
        }

        .mini-popup-image-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 12px;
            overflow: hidden;
        }

        .mini-popup-image {
            width: 100%;
            height: 100%;
            object-fit: fill;
            display: block;
        }

        .mini-popup-pulse {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 12px;
            pointer-events: none;
        }

        .mini-popup-close {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #000;
            color: #fff;
            border: 2px solid #fff;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.3s;
        }

        .mini-popup-close:hover {
            background: #d70018;
            transform: rotate(90deg);
        }

        /* Full Popup Overlay Styles */
        .banner-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .banner-popup-content {
            position: relative;
            max-width: 90%;
            max-height: 90vh;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .banner-popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.3s;
        }

        .banner-popup-close:hover {
            background: rgba(215, 0, 24, 0.9);
            transform: rotate(90deg);
        }

        .banner-popup-image {
            display: block;
            max-width: 100%;
            max-height: 50vh;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .banner-popup-checkbox {
            padding: 12px 20px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        .banner-popup-checkbox label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #333;
            cursor: pointer;
            margin: 0;
        }

        .banner-popup-checkbox input[type="checkbox"] {
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        @media (max-width: 768px) {
            .mini-popup-teaser {
                bottom: 15px;
                left: 15px;
            }

            .mini-popup-image-wrapper {
                width: 80px;
                height: 80px;
            }

            .banner-popup-content {
                max-width: 95%;
            }

            .banner-popup-image {
                max-height: 70vh;
            }

            .banner-popup-close {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }
        }
    </style>

    <script>
        //hiển thị mini popup sau 2 giây
        setTimeout(function() {
            const miniPopup = document.getElementById('<?= $miniPopupId ?>');
            if (miniPopup) {
                miniPopup.style.display = 'block';
            }
        }, 2000);

        //mở full popup khi click vào mini popup
        function openFullPopup(fullPopupId, miniPopupId) {
            const fullPopup = document.getElementById(fullPopupId);
            const miniPopup = document.getElementById(miniPopupId);
            
            if (fullPopup) {
                fullPopup.style.display = 'flex';
            }
            
            if (miniPopup) {
                miniPopup.style.display = 'none';
            }
        }

        //đóng mini popup
        function closeMiniPopup(miniPopupId, bannerId) {
            event.stopPropagation(); 
            
            const miniPopup = document.getElementById(miniPopupId);
            if (miniPopup) {
                miniPopup.style.display = 'none';
            }

            //lưu vào localStorage
            const today = new Date().toDateString();
            localStorage.setItem('popup_hidden_' + bannerId, today);
            
            // Gọi API để lưu vào session
            fetch('/banner/hide-popup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ banner_id: bannerId })
            });
        }

        // Đóng full popup
        function closeFullPopup(fullPopupId, bannerId) {
            const fullPopup = document.getElementById(fullPopupId);
            const dontShowAgain = document.getElementById('dont-show-again-' + bannerId);
            
            if (fullPopup) {
                fullPopup.style.display = 'none';
            }

            // Nếu checkbox được chọn, lưu vào localStorage
            if (dontShowAgain && dontShowAgain.checked) {
                const today = new Date().toDateString();
                localStorage.setItem('popup_hidden_' + bannerId, today);
                
                // Gọi API để lưu vào session
                fetch('/banner/hide-popup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ banner_id: bannerId })
                });
            } else {
                // Nếu không check, hiển thị lại mini popup
                const miniPopup = document.getElementById('<?= $miniPopupId ?>');
                if (miniPopup) {
                    miniPopup.style.display = 'block';
                }
            }
        }

        // Đóng full popup khi click vào overlay
        document.getElementById('<?= $popupId ?>')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeFullPopup('<?= $popupId ?>', <?= $popup['id'] ?>);
            }
        });

        // Kiểm tra localStorage xem popup đã bị ẩn hôm nay chưa
        (function() {
            const bannerId = <?= $popup['id'] ?>;
            const today = new Date().toDateString();
            const hiddenDate = localStorage.getItem('popup_hidden_' + bannerId);
            
            if (hiddenDate === today) {
                const miniPopup = document.getElementById('<?= $miniPopupId ?>');
                const fullPopup = document.getElementById('<?= $popupId ?>');
                
                if (miniPopup) miniPopup.remove();
                if (fullPopup) fullPopup.remove();
            }
        })();
    </script>
    <?php
        endif; // Kết thúc kiểm tra session
    endif; // Kết thúc kiểm tra popup banners
    ?>

</body>

</html>