<style>
  .sticky-header {
    position: sticky;
    top: 0;
    z-index: 1030; 
    background-color: #03224c;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
  }
 
  .offcanvas {
    z-index: 1050 !important;
  }
</style>
<header class="sticky-header">

    <div class="header-top">
        <div class="container-xl">
            <div class="row align-items-center g-2">

                <div class="col-auto">
                    <a href="/" style="display:inline-block; line-height:0;">
                        <img src="/public/assets/client/images/header/3.png"
                             alt="FPT Shop"
                             style="height:40px; width:auto; display:block;">
                    </a>
                </div>

                <div class="col d-flex d-md-none justify-content-end align-items-center">
                    <a href="/gio-hang" class="service-item d-inline-flex flex-column align-items-center">
                        <div class="cart-wrapper">
                            <i class="fa fa-shopping-cart" style="font-size:1.4rem; color:#fff;"></i>
                            <span class="cart-badge">0</span>
                        </div>
                    </a>
                </div>

                <div class="col-12 col-md">
                    <form class="search-form d-flex" action="/tim-kiem" method="GET">
                        <input class="form-control" type="search" name="q"
                            placeholder="Nhập tên điện thoại, máy tính, phụ kiện... cần tìm">
                        <button class="btn-search" type="submit">
                            <i class="fa fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>

                <div class="col-auto d-none d-md-flex justify-content-end align-items-center gap-1">

                    <div class="service-dropdown">
                        <a href="#" class="service-item">
                            <i class="fa fa-file"></i>
                            Thông tin hay
                        </a>
                        <div class="service-dropdown-menu">
                            <a href="#">Tin mới</a>
                            <a href="#">Khuyến mãi</a>
                            <a href="#">Thủ thuật</a>
                            <a href="#">For games</a>
                            <a href="#">Video hot</a>
                            <a href="#">Đánh giá - tư vấn</a>
                            <a href="#">App &amp; Game</a>
                            <a href="#">Sự kiện</a>
                        </div>
                    </div>

                    <a href="#" class="service-item">
                        <i class="fa fa-file-invoice-dollar"></i>
                        Thanh toán &amp; Tiện ích
                    </a>

                    <?php
                    $isLoggedIn = \App\Core\Session::isLoggedIn();
                    $userRole = \App\Core\Session::getUserRole();
                    $accountUrl = ($isLoggedIn && $userRole === 'MEMBER') ? '/client/profile' : '/client/auth/login';
                    ?>
                    <a href="<?php echo $accountUrl; ?>" class="service-item">
                        <i class="fa fa-user"></i>
                        Tài khoản của tôi
                    </a>

                    <?php if ($isLoggedIn && $userRole === 'MEMBER'): ?>
                    <a href="/yeu-thich" class="service-item">
                        <i class="fa fa-heart"></i>
                        Yêu thích
                    </a>
                    <?php endif; ?>

                    <a href="/gio-hang" class="service-item">
                        <div class="cart-wrapper">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="cart-badge" id="cart-count">0</span>
                        </div>
                        Giỏ hàng
                    </a>

                </div>
            </div>
        </div>
    </div>


    <nav class="navbar-main d-none d-lg-block">
        <div class="container-xl">
            <ul class="nav">

                <li class="nav-item">
                    <a class="nav-link" href="/san-pham"><i class="fa fa-mobile"></i> Điện thoại</a>
                    <div class="mega-menu">
                        <div class="mega-col">
                            <div class="mega-section-title">Hãng sản xuất</div>
                            <a href="#">Apple (iPhone)</a><a href="#">Samsung</a><a href="#">Oppo</a>
                            <a href="#">Xiaomi</a><a href="#">Vivo</a><a href="#">Tecno</a>
                            <a href="#">Nokia</a><a href="#">Asus</a><a href="#">Realme</a>
                            <div class="mega-section-title mt-3">Đồng hồ thông minh</div>
                            <a href="#">Apple Watch</a><a href="#">Samsung</a>
                            <a href="#">Garmin</a><a href="#">Huawei</a>
                        </div>
                        <div class="mega-col-sm">
                            <div class="mega-section-title">Mức giá</div>
                            <a href="#">Dưới 2 triệu</a><a href="#">Từ 2 - 4 triệu</a>
                            <a href="#">Từ 4 - 7 triệu</a><a href="#">Từ 7 - 13 triệu</a>
                            <a href="#">Trên 13 triệu</a>
                        </div>
                        <div class="mega-col">
                            <div class="mega-section-title">Bán chạy nhất</div>
                            <a href="#" class="hot-item">
                                <img src="/public/assets/client/images/navbar/1.png" alt="">
                                <div>
                                    <div class="hot-item-name">Samsung Galaxy A53 5G 256GB</div>
                                    <div class="hot-item-price">10.990.000 ₫</div>
                                </div>
                            </a>
                            <a href="#" class="hot-item">
                                <img src="/public/assets/client/images/navbar/2.png" alt="">
                                <div>
                                    <div class="hot-item-name">OPPO A55 4GB-64GB</div>
                                    <div class="hot-item-price">4.990.000 ₫</div>
                                </div>
                            </a>
                            <div class="mega-banner"><a href="#"><img src="/public/assets/client/images/navbar/3.png"
                                        alt=""></a></div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/san-pham"><i class="fa fa-laptop"></i> Laptop</a>
                    <div class="mega-menu">
                        <div class="mega-col">
                            <div class="mega-section-title">Hãng sản xuất</div>
                            <a href="#">Apple (MacBook)</a><a href="#">Asus</a><a href="#">HP</a>
                            <a href="#">Acer</a><a href="#">MSI</a><a href="#">Lenovo</a>
                            <a href="#">Dell</a><a href="#">Microsoft (Surface)</a>
                            <div class="mega-section-title mt-3">Phần mềm</div>
                            <a href="#">Diệt Virus</a><a href="#">Microsoft Office</a><a href="#">Windows</a>
                            <div class="mega-section-title mt-3">Máy in</div>
                            <a href="#">HP</a><a href="#">Canon</a><a href="#">Brother</a>
                        </div>
                        <div class="mega-col-sm">
                            <div class="mega-section-title">Mức giá</div>
                            <a href="#">Dưới 5 triệu</a><a href="#">Từ 5 - 10 triệu</a>
                            <a href="#">Từ 10 - 15 triệu</a><a href="#">Từ 15 - 20 triệu</a>
                            <a href="#">Từ 20 - 25 triệu</a><a href="#">Trên 30 triệu</a>
                        </div>
                        <div class="mega-col">
                            <div class="mega-banner" style="margin-top:0">
                                <a href="#"><img src="/public/assets/client/images/navbar/4.png" alt=""></a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/san-pham"><i class="fa fa-tablet"></i> Máy tính bảng</a>
                    <div class="mega-menu">
                        <div class="mega-col">
                            <div class="mega-section-title">Hãng sản xuất</div>
                            <a href="#">Apple (iPad)</a><a href="#">Samsung</a><a href="#">Masstel</a>
                            <a href="#">Lenovo</a><a href="#">Xiaomi</a><a href="#">Coolpad</a><a href="#">Nexta</a>
                        </div>
                        <div class="mega-col-sm">
                            <div class="mega-section-title">Mức giá</div>
                            <a href="#">Dưới 2 triệu</a><a href="#">Từ 2 - 5 triệu</a>
                            <a href="#">Từ 5 - 8 triệu</a><a href="#">Trên 8 triệu</a>
                        </div>
                        <div class="mega-col">
                            <div class="mega-section-title">Bán chạy nhất</div>
                            <a href="#" class="hot-item">
                                <img src="/public/assets/client/images/navbar/5.png" alt="">
                                <div>
                                    <div class="hot-item-name">iPad Pro 11 2021 M1 Wi-Fi 128GB</div>
                                    <div class="hot-item-price">19.999.000 ₫</div>
                                </div>
                            </a>
                            <a href="#" class="hot-item">
                                <img src="/public/assets/client/images/navbar/6.png" alt="">
                                <div>
                                    <div class="hot-item-name">Samsung Galaxy Tab S6 Lite 2022</div>
                                    <div class="hot-item-price">7.990.000 ₫</div>
                                </div>
                            </a>
                            <div class="mega-banner"><a href="#"><img src="/public/assets/client/images/navbar/7.png"
                                        alt=""></a></div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/san-pham"><i class="fa-brands fa-apple"></i> Apple</a>
                    <div class="mega-menu">
                        <div class="mega-col">
                            <div class="mega-section-title">Các sản phẩm Apple</div>
                            <a href="#">iPhone</a><a href="#">iPad</a><a href="#">MacBook</a>
                            <a href="#">Apple Watch</a><a href="#">Tai nghe Apple</a><a href="#">iMac</a>
                            <a href="#">Mac Mini</a><a href="#">Ốp lưng &amp; Bao da</a>
                            <a href="#">Apple TV</a><a href="#">Chuột &amp; Trackpad</a>
                            <a href="#">Bàn phím</a><a href="#">AirTag</a>
                        </div>
                        <div class="mega-col">
                            <div class="mega-section-title">Bán chạy nhất</div>
                            <a href="#" class="hot-item">
                                <img src="/public/assets/client/images/navbar/8.png" alt="">
                                <div>
                                    <div class="hot-item-name">iPhone 13 Pro Max 128GB</div>
                                    <div class="hot-item-price">27.990.000 ₫</div>
                                </div>
                            </a>
                            <a href="#" class="hot-item">
                                <img src="/public/assets/client/images/navbar/9.png" alt="">
                                <div>
                                    <div class="hot-item-name">iPhone 13 128GB</div>
                                    <div class="hot-item-price">19.490.000 ₫</div>
                                </div>
                            </a>
                            <div class="mega-banner"><a href="#"><img src="/public/assets/client/images/navbar/10.png"
                                        alt=""></a></div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/san-pham"><i class="fa fa-desktop"></i> PC-Linh kiện</a>
                    <div class="simple-dropdown">
                        <a href="#">PC</a><a href="#">Linh kiện</a>
                        <a href="#">Màn hình</a><a href="#">Xây dựng PC</a>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/san-pham"><i class="fa fa-headphones"></i> Phụ kiện</a>
                    <div class="mega-menu">
                        <div class="mega-col">
                            <div class="mega-section-title">Các sản phẩm phụ kiện</div>
                            <a href="#">Bao da ốp lưng</a><a href="#">Sạc dự phòng</a><a href="#">Thẻ nhớ</a>
                            <a href="#">Phụ kiện Apple</a><a href="#">Miếng dán màn hình</a><a href="#">Loa</a>
                            <a href="#">USB - Ổ cứng</a><a href="#">Sạc cáp</a><a href="#">Tai nghe</a>
                            <a href="#">Chuột</a><a href="#">Bàn ghế gaming</a><a href="#">Balo - Túi xách</a>
                            <a href="#">TV BOX</a><a href="#">Bàn phím</a>
                        </div>
                        <div class="mega-col">
                            <div class="mega-section-title">Bán chạy nhất</div>
                            <a href="#" class="hot-item">
                                <img src="/public/assets/client/images/navbar/11.png" alt="">
                                <div>
                                    <div class="hot-item-name">Combo Loa Bluetooth Karaoke ivalue F12-65N</div>
                                    <div class="hot-item-price">1.953.000 ₫</div>
                                </div>
                            </a>
                            <a href="#" class="hot-item">
                                <img src="/public/assets/client/images/navbar/12.png" alt="">
                                <div>
                                    <div class="hot-item-name">Pin sạc dự phòng UmeTravel 10000mAh</div>
                                    <div class="hot-item-price">809.000 ₫</div>
                                </div>
                            </a>
                            <div class="mega-banner"><a href="#"><img src="/public/assets/client/images/navbar/13.png"
                                        alt=""></a></div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/san-pham"><i class="fa fa-rotate-right"></i> Máy cũ giá rẻ</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/san-pham"><i class="fa fa-house-laptop"></i> Hàng gia dụng</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/san-pham"><i class="fa fa-sd-card"></i> Sim&amp;Thẻ cào</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/khuyen-mai"><i class="fa fa-certificate"></i> Khuyến mãi</a>
                    <div class="simple-dropdown">
                        <a href="/khuyen-mai">Tất cả khuyến mãi</a>
                        <a href="/ma-giam-gia">Mã giảm giá</a>
                    </div>
                </li>

            </ul>
        </div>
    </nav>


    <nav class="d-lg-none navbar-main">
        <div class="container-xl d-flex align-items-center py-1">
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="text-white fw-semibold" style="font-size:0.88rem;">Danh mục sản phẩm</span>
        </div>
    </nav>


    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <a href="/" style="display:inline-block; line-height:0;">
                <img src="/public/assets/client/images/header/3.png"
                     alt="FPT Shop"
                     style="height:36px; width:auto; display:block;">
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="list-unstyled mb-0">
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa fa-mobile"></i> Điện thoại</a></li>
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa fa-laptop"></i> Laptop</a></li>
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa fa-tablet"></i> Máy tính bảng</a></li>
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa-brands fa-apple"></i> Apple</a></li>
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa fa-desktop"></i> PC-Linh kiện</a></li>
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa fa-headphones"></i> Phụ kiện</a></li>
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa fa-rotate-right"></i> Máy cũ giá rẻ</a></li>
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa fa-house-laptop"></i> Hàng gia dụng</a></li>
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa fa-sd-card"></i> Sim &amp; Thẻ cào</a></li>
                <li class="offcanvas-menu-item"><a href="/khuyen-mai"><i class="fa fa-certificate"></i> Khuyến mãi</a></li>
                <li class="offcanvas-menu-item"><a href="/ma-giam-gia"><i class="fa fa-ticket"></i> Mã giảm giá</a></li>
                <li class="offcanvas-menu-item"><a href="/san-pham"><i class="fa fa-circle-dollar-to-slot"></i> Trả góp</a></li>

                <li><div style="height:1px; background:#f0f0f0; margin:4px 0;"></div></li>
                <li style="padding:6px 16px 4px; font-size:0.72rem; color:#999; text-transform:uppercase; font-weight:700;">Tài khoản & Dịch vụ</li>

                <li class="offcanvas-menu-item">
                    <a href="#">
                        <i class="fa fa-file"></i> Thông tin hay
                    </a>
                </li>
                <li class="offcanvas-menu-item">
                    <a href="#">
                        <i class="fa fa-file-invoice-dollar"></i> Thanh toán &amp; Tiện ích
                    </a>
                </li>
                <?php
                $isLoggedIn = \App\Core\Session::isLoggedIn();
                $userRole = \App\Core\Session::getUserRole();
                $accountUrl = ($isLoggedIn && $userRole === 'MEMBER') ? '/client/profile' : '/client/auth/login';
                ?>
                <li class="offcanvas-menu-item">
                    <a href="<?php echo $accountUrl; ?>">
                        <i class="fa fa-user"></i> Tài khoản của tôi
                    </a>
                </li>
                <?php if ($isLoggedIn && $userRole === 'MEMBER'): ?>
                <li class="offcanvas-menu-item">
                    <a href="/yeu-thich">
                        <i class="fa fa-heart"></i> Yêu thích
                    </a>
                </li>
                <?php endif; ?>
                <li class="offcanvas-menu-item">
                    <a href="/gio-hang">
                        <i class="fa fa-shopping-cart"></i> Giỏ hàng
                    </a>
                </li>
            </ul>
        </div>
    </div>

</header>