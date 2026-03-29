<!-- header -->
<div class="header">
    <div class="header-top">
        <div class="grid wide">
            <div class="row header-top">
                <div class="col l-2 m-6 c-6">
                    <div class="logo-top">
                        <i class="fa fa-bars bar-reponsive"></i>
                        <a class="logo f-logo" href="/"></a>
                    </div>
                </div>
                <!-- cart for mobile and tablet -->
                <div class="col l-0 m-6 c-6">
                    <div class="cart mobile-tablet">
                        <i class="fa fa-cart-shopping"></i> <br>
                    </div>
                </div>
                <!-- end cart for mobile and tablet -->
                <div class="col l-5 m-6 c-12">
                    <form class="search-top" action="/san-pham" method="GET">
                        <input type="search" name="keyword" placeholder="Nhập tên điện thoại, máy tính, phụ kiện... cần tìm">
                        <button class="button-search" type="submit">
                            <i class="fa fa-magnifying-glass"></i>
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
                                <i class="fa fa-file"></i>
                                <p>Thông tin hay</p>
                            </a>
                            <ul class="news">
                                <li><a href="#">Tin mới</a></li>
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
                                <i class="fa fa-file-invoice-dollar"></i>
                                <p>Thanh toán &amp; Tiện ích</p>
                            </a>
                        </div>
                        <div class="service-personal-account">
                            <?php
                            $isLoggedIn = \App\Core\Session::isLoggedIn();
                            $userRole = \App\Core\Session::getUserRole();
                            $accountUrl = ($isLoggedIn && $userRole === 'MEMBER') ? '/client/profile' : '/client/auth/login';
                            ?>
                            <a href="<?php echo $accountUrl; ?>">
                                <i class="fa fa-user"></i>
                                <p>Tài khoản của tôi</p>
                            </a>
                        </div>
                        <div class="service-cart">
                            <a href="/gio-hang">
                                <i class="fa fa-shopping-cart"></i>
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
                            <a href="/san-pham">
                                <i class="fa fa-mobile"></i> Điện thoại
                            </a>
                        </li>
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-laptop"></i> Laptop
                            </a>
                        </li>
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-tablet"></i> Máy tính bảng
                            </a>
                        </li>
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-apple"></i> Apple
                            </a>
                        </li>
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-desktop"></i> PC-Linh kiện
                            </a>
                        </li>
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-headphones"></i> Phụ kiện
                            </a>
                        </li>
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-rotate-right"></i> Máy cũ giá rẻ
                            </a>
                        </li>
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-house-laptop"></i> Hàng gia dụng
                            </a>
                        </li>
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-sd-card"></i> Sim&amp;Thẻ cào
                            </a>
                        </li>
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-certificate"></i> Khuyến mãi
                            </a>
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
            <a href="/san-pham">
                <i class="fa fa-mobile"></i> Điện thoại
            </a>
        </li>
        <li class="menu-mobile-tablet-list-item">
            <a href="/san-pham">
                <i class="fa fa-laptop"></i> Laptop
            </a>
        </li>
        <li class="menu-mobile-tablet-list-item">
            <a href="/san-pham">
                <i class="fa fa-apple"></i> Apple
            </a>
        </li>
        <li class="menu-mobile-tablet-list-item">
            <a href="/san-pham">
                <i class="fa fa-desktop"></i> PC phụ kiện
            </a>
        </li>
        <li class="menu-mobile-tablet-list-item">
            <a href="/san-pham">
                <i class="fa fa-headphones"></i> Tai nghe
            </a>
        </li>
        <li class="menu-mobile-tablet-list-item">
            <a href="/san-pham">
                <i class="fa fa-arrow-rotate-right"></i> Máy cũ giá rẻ
            </a>
        </li>
        <li class="menu-mobile-tablet-list-item">
            <a href="/san-pham">
                <i class="fa fa-house-laptop"></i> Hàng gia dụng
            </a>
        </li>
        <li class="menu-mobile-tablet-list-item">
            <a href="/san-pham">
                <i class="fa fa-sim-card"></i> Sim&amp;Thẻ cào
            </a>
        </li>
        <li class="menu-mobile-tablet-list-item">
            <a href="/san-pham">
                <i class="fa fa-certificate"></i> Khuyến mãi
            </a>
        </li>
        <li class="menu-mobile-tablet-list-item">
            <a href="/san-pham">
                <i class="fa fa-circle-dollar-to-slot"></i> Trả góp
            </a>
        </li>
    </ul>
    <div class="close-menu">
        <i class="fa fa-xmark"></i>
    </div>
</div>
<!-- end-header -->
""