<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/BaseModel.php';

//check quyền
AuthMiddleware::checkMember();

//lấy data user từ db
$userId = \App\Core\Session::getUserId();
if (!$userId) {
    header('Location: /client/auth/login');
    exit;
}

$userModel = new BaseModel('nguoi_dung');
$user = $userModel->getById($userId);

if (!$user) {
    header('Location: /client/auth/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân - FPT Shop</title>
    <script src="https://kit.fontawesome.com/1f55434e39.js" crossorigin="anonymous"></script>
    <link rel="icon" href="/public/assets/client/images/header/1.png">
    <link rel="stylesheet" href="/public/assets/client/css/main.css">
    <link rel="stylesheet" href="/public/assets/client/css/grid.css">
    <link rel="stylesheet" href="/public/assets/client/css/reponsive.css">
    <style>
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
            border: 2px solid #cb1c22;
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
            color: #555;
            text-decoration: none;
            padding: 10px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        .profile-menu li a:hover, .profile-menu li a.active {
            background-color: #fde8e8;
            color: #cb1c22;
            font-weight: bold;
        }
        .profile-menu li a i {
            width: 25px;
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #cb1c22;
            outline: none;
        }
        .form-group input[disabled] {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        .btn-submit {
            background-color: #cb1c22;
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
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- header -->
        <div class="header">
            <div class="header-top">
                <div class="grid wide">
                    <div class="row header-top">
                        <div class="col l-2 m-6 c-6">
                            <div class="logo-top">
                                <i class="fa fa-bars bar-reponsive" aria-hidden="true"></i>
                                <a class="logo f-logo" href="./index.html"></a>
                            </div>
                        </div>
                        <!-- cart for mobile and tablet -->
                        <div class="col l-0 m-6 c-6">
                            <div class="cart mobile-tablet">
                                <i class="fa fa-cart-shopping" aria-hidden="true"></i> <br>
                            </div>
                        </div>
                        <!-- end cart for mobile and tablet -->
                        <div class="col l-5 m-6 c-12">
                            <form class="search-top" action="/san-pham" method="GET">
                                <input type="search" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>"
                                    placeholder="Nhập tên điện thoại, máy tính, phụ kiện... cần tìm">
                                <button class="button-search" type="submit">
                                    <i class="fa fa-magnifying-glass" aria-hidden="true"></i>
                                </button>
                                <ul class="history-search">
                                    <span class="title-history-search">Lịch sử tìm</span>
                                    <li class="history-item"><a href="#">Iphone</a></li>
                                    <li class="history-item"><a href="#">Samsung</a></li>
                                    <li class="history-item"><a href="#">Tai nghe</a></li>
                                </ul>
                            </form>
                        </div>
                        <div class="col l-5 m-6 c-6">
                            <div class="service">
                                <div class="service-inf">
                                    <a href="#">
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                        <p>Thông tin hay</p>
                                    </a>
                                    <ul class="news"><a href="#">
                                        </a>
                                        <li><a href="#"></a><a href="#">Tin mới</a></li>
                                        <li><a href="#">Khuyến mãi</a></li>
                                        <li><a href="#">Thủ thuật</a></li>
                                        <li><a href="#">For games</a></li>
                                        <li><a href="#">Video hot</a></li>
                                        <li><a href="#">Đánh giá - tư vấn</a></li>
                                        <li><a href="#">App &amp; Game</a></li>
                                        <li><a href="#">Sự kiện</a></li>
                                    </ul>

                                </div>
                                <div class="service-pee">
                                    <a href="#">
                                        <i class="fa fa-file-invoice-dollar" aria-hidden="true"></i>
                                        <p>Thanh toán &amp; Tiện ích</p>
                                    </a>
                                </div>
                                <div class="service-personal-account">
                                    <a href="/profile.php">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        <p>Tài khoản của tôi</p>
                                    </a>
                                </div>
                                <div class="service-cart">
                                    <a href="/gio-hang">
                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        <p>Giỏ hàng</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-bottom">
                <div class="grid wide">
                    <div class="row">
                        <div class="col l-12 m-0 c-0">
                            <ul class="menu-top">
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-mobile" aria-hidden="true"></i> Điện thoại </a>
                                    <div class="nav-box">
                                        <table class="nav-company">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="nav-box-bold">Hàng sản xuất</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Apple (iphone)</a></td>
                                                    <td><a href="#">Samsung</a></td>
                                                    <td><a href="#">Oppo</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Xiaomi</a></td>
                                                    <td><a href="#">Vivo</a></td>
                                                    <td><a href="#">Tecno</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Nokia</a></td>
                                                    <td><a href="#">Asus</a></td>
                                                    <td><a href="#">Masstel</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Realme</a></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="nav-box-bold">Đồng hồ thông minh</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Apple Watch</a></td>
                                                    <td><a href="#">Samsung</a></td>
                                                    <td><a href="#">Oppo</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Masstel</a></td>
                                                    <td><a href="#">Xiaomi</a></td>
                                                    <td><a href="#">Garmin</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Huawei</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="level">
                                            <tbody>
                                                <tr>
                                                    <td class="nav-box-bold">Mức giá</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Dưới 2 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Từ 2 - 4 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Từ 4 - 7 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Từ 7 - 13 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Trên 13 triệu</a></td>
                                                </tr>
                                                <tr>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="hot-selling">
                                            <tbody>
                                                <tr>
                                                    <td class="nav-box-bold" colspan="2">bán chạy nhất</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="#">
                                                            <img
                                                                src="/public/assets/client/images/navbar/1.png">
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="#">Samsung Galaxy A53 5G 256GB <p
                                                                class="hot-selling-price">10.990.000 ₫</p>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="#">
                                                            <img
                                                                src="/public/assets/client/images/navbar/2.png">
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="#">OPPO A55 4GB-64GB <p class="hot-selling-price">
                                                                4.990.000 ₫</p>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="nav-box-banner">
                                            <a href="#">
                                                <img
                                                    src="/public/assets/client/images/navbar/3.png">
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-laptop" aria-hidden="true"></i> Laptop </a>
                                    <div class="nav-box">
                                        <table class="nav-company">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="nav-box-bold">Hàng sản xuất</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Apple (MacBook)</a></td>
                                                    <td><a href="#">Asus</a></td>
                                                    <td><a href="#">HP</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Acer</a></td>
                                                    <td><a href="#">MSI</a></td>
                                                    <td><a href="#">Lenovo</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Dell</a></td>
                                                    <td><a href="#">Microsoft (Surface)</a></td>
                                                    <td><a href="#">Gigabyte</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Fujitsu</a></td>
                                                    <td><a href="#">Chuwi</a></td>
                                                    <td><a href="#">Avita</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Masstel</a></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="nav-box-bold">Phần mềm</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Diệt Virus</a></td>
                                                    <td><a href="#">Microsoft Office</a></td>
                                                    <td><a href="#">Windows</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Phần mềm khác</a></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="nav-box-bold">Máy in</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">HP</a></td>
                                                    <td><a href="#">Canon</a></td>
                                                    <td><a href="#">Brother</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="level wlp-2">
                                            <tbody>
                                                <tr>
                                                    <td class="nav-box-bold">Mức giá</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Dưới 5 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Từ 5 - 10 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Từ 10 - 15 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Từ 15 - 20 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Từ 20 - 25 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Từ 25 - 30 triệu</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Trên 30 triệu</a></td>
                                                </tr>
                                                <tr>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="nav-box-banner">
                                            <a href="#">
                                                <img
                                                    src="/public/assets/client/images/navbar/4.png">
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-tablet" aria-hidden="true"></i> Máy tính bảng </a>
                                </li>
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-apple" aria-hidden="true"></i> Apple </a>
                                </li>
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-desktop" aria-hidden="true"></i> PC-Linh kiện </a>
                                </li>
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-headphones" aria-hidden="true"></i> Phụ kiện </a>
                                </li>
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-rotate-right" aria-hidden="true"></i> Máy cũ giá rẻ </a>
                                </li>
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-house-laptop" aria-hidden="true"></i> Hàng gia dụng </a>
                                </li>
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-sd-card" aria-hidden="true"></i> Sim&amp;Thẻ cào </a>
                                </li>
                                <li class="menu-top-item">
                                    <a href="product.html">
                                        <i class="fa fa-certificate" aria-hidden="true"></i> Khuyến mãi </a>
                                    <ul class="news">
                                        <li><a href="#">Thông tin trao thưởng</a></li>
                                        <li><a href="#">Tất cả khuyến mại</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- menu-mobile and tablet -->
        <div class="modal-menu"></div>
        <div class="menu-mobile-tablet">
            <ul class="menu-mobile-tablet-list">
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-mobile" aria-hidden="true"></i> Điện thoại </a>
                </li>
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-laptop" aria-hidden="true"></i> Laptop </a>
                </li>
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-apple" aria-hidden="true"></i> Apple </a>
                </li>
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-desktop" aria-hidden="true"></i> PC phụ kiện </a>
                </li>
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-headphones" aria-hidden="true"></i> Tai nghe </a>
                </li>
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-arrow-rotate-right" aria-hidden="true"></i> Máy cũ giá rẻ </a>
                </li>
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-house-laptop" aria-hidden="true"></i> Hàng gia dụng </a>
                </li>
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-sim-card" aria-hidden="true"></i> Sim&amp;Thẻ cào </a>
                </li>
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-certificate" aria-hidden="true"></i> Khuyến mãi </a>
                </li>
                <li class="menu-mobile-tablet-list-item">
                    <a href="product.html">
                        <i class="fa fa-circle-dollar-to-slot" aria-hidden="true"></i> Trả góp </a>
                </li>
            </ul>
            <div class="close-menu">
                <i class="fa fa-xmark" aria-hidden="true"></i>
            </div>
        </div>
        <!-- end-header -->
        

        <!-- Main Content -->
        <div class="main profile-wrapper">
            <div class="grid wide">
                <div class="row">
                    <!-- Sidebar Trái -->
                    <div class="col l-3 m-4 c-12">
                        <div class="profile-sidebar">
                            <div class="profile-sidebar-header">

                                <img src="<?= !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : '/public/assets/client/images/others/anh-avatar.jpg' ?>" alt="Avatar">
                                <h3><?= htmlspecialchars($user['ho_ten'] ?? 'Tên người dùng') ?></h3>
                            </div>
                            <ul class="profile-menu">
                                <li><a href="/profile.php" class="active"><i class="fa fa-user"></i> Hồ sơ của tôi</a></li>
                                <li><a href="/don-hang.php"><i class="fa fa-file-invoice-dollar"></i> Đơn hàng của tôi</a></li>
                                <li><a href="/dia-chi.php"><i class="fa fa-map-marker-alt"></i> Sổ địa chỉ</a></li>
                                <li><a href="/client/auth/logout"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>


                    <div class="col l-9 m-8 c-12">

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

                        <div class="profile-content-box">
                            <div class="profile-content-header">
                                <h2>Hồ sơ của tôi</h2>
                                <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                            </div>

                            <form action="/khach-hang/cap-nhat-ho-so" method="POST">
                                <div class="row">
                                    <div class="col l-8 m-12 c-12">
                                        <div class="form-group">
                                            <label>Email (Tên đăng nhập)</label>
                                            <input type="text" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Họ và tên</label>
                                            <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Số điện thoại</label>
                                            <input type="tel" name="sdt" value="<?= htmlspecialchars($user['sdt'] ?? '') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Ngày sinh</label>
                                            <input type="date" name="ngay_sinh" value="<?= htmlspecialchars($user['ngay_sinh'] ?? '') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Giới tính</label>
                                            <select name="gioi_tinh">
                                                <option value="NAM" <?= (($user['gioi_tinh'] ?? '') === 'NAM') ? 'selected' : '' ?>>Nam</option>
                                                <option value="NU" <?= (($user['gioi_tinh'] ?? '') === 'NU') ? 'selected' : '' ?>>Nữ</option>
                                                <option value="KHAC" <?= (($user['gioi_tinh'] ?? '') === 'KHAC') ? 'selected' : '' ?>>Khác</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn-submit">Lưu thay đổi</button>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <div class="profile-content-box">
                            <div class="profile-content-header">
                                <h2>Đổi mật khẩu</h2>
                                <p>Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
                            </div>
                            <form action="/khach-hang/doi-mat-khau" method="POST">
                                <div class="row">
                                    <div class="col l-8 m-12 c-12">
                                        <div class="form-group">
                                            <label>Mật khẩu hiện tại</label>
                                            <input type="password" name="mat_khau_cu" placeholder="Nhập mật khẩu hiện tại" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Mật khẩu mới</label>
                                            <input type="password" name="mat_khau_moi" placeholder="Nhập mật khẩu mới" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Xác nhận mật khẩu mới</label>
                                            <input type="password" name="xac_nhan_mat_khau" placeholder="Nhập lại mật khẩu mới" required>
                                        </div>
                                        <button type="submit" class="btn-submit">Cập nhật mật khẩu</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <!-- footer -->
        <div class="footer">
            <div class="grid wide">
                <div class="row">
                    <div class="col l-2 m-4 c-12">
                        <div class="footer-1">
                            <ul class="footer-list">
                                <li class="footer-list-item"><a href="#">Giới thiệu về công ty</a></li>
                                <li class="footer-list-item"><a href="#">Câu hỏi thường gặp mua hàng</a></li>
                                <li class="footer-list-item"><a href="#">Chính sách bảo mật</a></li>
                                <li class="footer-list-item"><a href="#"> Quy chế hoạt động</a></li>
                                <li class="footer-list-item"><a href="#">Kiểm tra hóa đơn điện tử</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col l-2 m-4 c-12">
                        <div class="footer-2">
                            <ul class="footer-list">
                                <li class="footer-list-item"><a href="#">Tin tuyển dụng</a></li>
                                <li class="footer-list-item"><a href="#">Tin khuyến mãi</a></li>
                                <li class="footer-list-item"><a href="#">Hướng dẫn mua online</a></li>
                                <li class="footer-list-item"><a href="#">Hướng dẫn mua trả góp</a></li>
                                <li class="footer-list-item"><a href="#">Chính sách trả góp</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col l-2 m-4 c-12">
                        <div class="footer-3">
                            <ul class="footer-list">
                                <li class="footer-list-item"><a href="#">Hệ thống cửa hàng</a></li>
                                <li class="footer-list-item"><a href="#">Bán hàng doanh nghiệp</a></li>
                                <li class="footer-list-item"><a href="#">Hệ thống bảo hành</a></li>
                                <li class="footer-list-item"><a href="#">Giới thiệu máy đổi trả </a></li>
                                <li class="footer-list-item"><a href="#">Chính sách đổi trả</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col l-3 m-6 c-12">
                        <div class="footer-4">
                            <p class="title-footer">Tư vấn mua hàng (Miễn phí)</p>
                            <p class="footer-phone">1800 6601</p>
                            <p class="title-footer">Hỗ trợ kỹ thuật</p>
                            <p class="footer-phone">1800 6601</p>
                            <p class="title-footer">Hỗ trợ thanh toán</p>
                            <div class="img-footer">
                                <img src="/public/assets/client/images/others/28.png" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col l-3 m-6 c-12">
                        <div class="footer-4">
                            <p class="title-footer">Góp ý, khiếu nại dịch vụ (8h00-22h00)</p>
                            <p class="footer-phone">1800 6616</p>
                            <p class="title-footer">Chứng nhận:</p>
                            <div class="img-footer footer-final">
                                <img src="/public/assets/client/images/others/29.png">
                            </div>
                            <p class="title-footer">Website cùng tập đoàn:</p>
                            <div class="img-footer footer-final">
                                <img src="/public/assets/client/images/others/30.jpg">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <div class="grid wide">
                <div class="row">
                    <div class="col l-12 m-12 c-12">
                        <p>© 2007 - 2022 Công Ty Cổ Phần Bán Lẻ Kỹ Thuật Số FPT / Địa chỉ: 261 - 263 Khánh Hội, P2, Q4,
                            TP. Hồ Chí Minh / GPĐKKD số 0311609355 do Sở KHĐT TP.HCM cấp ngày 08/03/2012. GP số
                            47/GP-TTĐT do sở TTTT TP HCM cấp ngày 02/07/2018. Điện thoại: (028)73023456. Email:
                            fptshop@fpt.com.vn. Chịu trách nhiệm nội dung: Nguyễn Trịnh Nhật Linh.
                        </p>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
    <script src="/public/assets/client/js/main.js"></script>
</body>
</html>