<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $pageTitle ?? 'FPT Shop'; ?></title>
    <link rel="icon" href="/public/assets/client/images/header/1.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/client/css/main.css">
    <link rel="stylesheet" href="/public/assets/client/css/grid.css">
    <link rel="stylesheet" href="/public/assets/client/css/reponsive.css">
    
    <?php if (isset($additionalCSS) && is_array($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        body { font-family: 'Roboto', sans-serif;}

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
        .fpt-logo-box .f { color: #d70018; }
        .fpt-logo-box .p { color: #ff6600; }
        .fpt-logo-box .t { color: #0070c0; }
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
        .search-form .form-control:focus { box-shadow: none; }
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
        .service-item i { font-size: 1.2rem; }

        .cart-wrapper { position: relative; display: inline-block; }
        .cart-badge {
            position: absolute;
            top: -5px; right: -6px;
            background: #fff;
            color: #d70018;
            font-size: 0.6rem;
            font-weight: 700;
            width: 15px; height: 15px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        .service-dropdown { position: relative; }
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
        .service-dropdown:hover .service-dropdown-menu { display: block; }
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
        .navbar-main .nav-item { position: relative; }
        .navbar-main .nav-link {
            color: #fff !important;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 9px 11px !important;
            white-space: nowrap;
            text-transform: uppercase;
        }
        .navbar-main .nav-link i { margin-right: 4px; font-size: 0.75rem; }

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
        .navbar-main .nav-item:hover .mega-menu { display: flex; gap: 18px; }

        .mega-col { flex: 1; }
        .mega-col-sm { flex: 0 0 120px; }

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
        .hot-item:last-of-type { border-bottom: none; }
        .hot-item img { width: 42px; height: 42px; object-fit: contain; }
        .hot-item-name { font-size: 0.76rem; color: #333; }
        .hot-item-price { font-size: 0.76rem; color: #d70018; font-weight: 700; }

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
        .navbar-main .nav-item:hover .simple-dropdown { display: block; }
        .simple-dropdown a {
            display: block;
            padding: 7px 16px;
            font-size: 0.83rem;
            color: #333;
            text-decoration: none;
        }

        .mega-banner { margin-top: 8px; }
        .mega-banner img { width: 100%; }

        .navbar-toggler { border: none; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .offcanvas-header { background: #d70018; }
        .offcanvas-header .btn-close { filter: invert(1); }
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
        .offcanvas-menu-item a i { color: #d70018; width: 18px; text-align: center; }

        .profile-wrapper {
            background-color: #f4f4f4;
            padding: 30px 0;
        }
        .profile-sidebar {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
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
        .profile-menu li a:hover, .profile-menu li a.active {
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
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
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
    </style>
</head>
<body>

    <?php require_once __DIR__ . '/header.php'; ?>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <?php require_once __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="/public/assets/client/js/main.js"></script>
    
    <script>
    // Cập nhật số lượng giỏ hàng
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

    // Gọi khi trang load
    document.addEventListener('DOMContentLoaded', updateCartCount);
    </script>
    
    <?php if (isset($additionalJS) && is_array($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
