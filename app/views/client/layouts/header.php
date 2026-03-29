<!-- header -->
<div class="header">
    <div class="header-top">
        <div class="grid wide">
            <div class="row header-top">

                <!-- Logo -->
                <div class="col l-2 m-6 c-6">
                    <div class="logo-top">
                        <i class="fa fa-bars bar-reponsive"></i>
                        <a class="logo f-logo" href="/index.php"></a>
                    </div>
                </div>

                <!-- Giỏ hàng (mobile/tablet) -->
                <div class="col l-0 m-6 c-6">
                    <div class="cart mobile-tablet">
                        <i class="fa fa-cart-shopping"></i>
                    </div>
                </div>

                <!-- Thanh tìm kiếm -->
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

                <!-- Dịch vụ -->
                <div class="col l-5 m-6 c-6">
                    <div class="service">

                        <!-- Thông tin hay -->
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

                        <!-- Thanh toán & Tiện ích -->
                        <div class="service-pee">
                            <a href="#">
                                <i class="fa fa-file-invoice-dollar"></i>
                                <p>Thanh toán &amp; Tiện ích</p>
                            </a>
                        </div>

                        <!-- Tài khoản -->
                        <div class="service-personal-account">
                            <?php
                            $isLoggedIn = \App\Core\Session::isLoggedIn();
                            $userRole   = \App\Core\Session::getUserRole();
                            $accountUrl = ($isLoggedIn && $userRole === 'MEMBER')
                                ? '/client/profile'
                                : '/client/auth/login';
                            ?>
                            <a href="<?php echo $accountUrl; ?>">
                                <i class="fa fa-user"></i>
                                <p>Tài khoản của tôi</p>
                            </a>
                        </div>

                        <!-- Giỏ hàng -->
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
    </div><!-- end header-top -->

    <!-- Menu điều hướng desktop -->
    <div class="header-bottom">
        <div class="grid wide">
            <div class="row">
                <div class="col l-12 m-0 c-0">
                    <ul class="menu-top">

                        <!-- Điện thoại -->
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-mobile"></i> Điện thoại
                            </a>
                            <div class="nav-box">
                                <table class="nav-company">
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
                                </table>
                                <table class="level">
                                    <tr>
                                        <td class="nav-box-bold">Mức giá</td>
                                    </tr>
                                    <tr><td><a href="#">Dưới 2 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 2 - 4 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 4 - 7 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 7 - 13 triệu</a></td></tr>
                                    <tr><td><a href="#">Trên 13 triệu</a></td></tr>
                                </table>
                                <table class="hot-selling">
                                    <tr>
                                        <td class="nav-box-bold" colspan="2">Bán chạy nhất</td>
                                    </tr>
                                    <tr>
                                        <td><a href="#"><img src="/public/assets/client/images/navbar/1.png"></a></td>
                                        <td><a href="#">Samsung Galaxy A53 5G 256GB<p class="hot-selling-price">10.990.000 ₫</p></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#"><img src="/public/assets/client/images/navbar/2.png"></a></td>
                                        <td><a href="#">OPPO A55 4GB-64GB<p class="hot-selling-price">4.990.000 ₫</p></a></td>
                                    </tr>
                                </table>
                                <div class="nav-box-banner">
                                    <a href="#"><img src="/public/assets/client/images/navbar/3.png"></a>
                                </div>
                            </div>
                        </li>

                        <!-- Laptop -->
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-laptop"></i> Laptop
                            </a>
                            <div class="nav-box">
                                <table class="nav-company">
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
                                </table>
                                <table class="level wlp-2">
                                    <tr>
                                        <td class="nav-box-bold">Mức giá</td>
                                    </tr>
                                    <tr><td><a href="#">Dưới 5 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 5 - 10 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 10 - 15 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 15 - 20 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 20 - 25 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 25 - 30 triệu</a></td></tr>
                                    <tr><td><a href="#">Trên 30 triệu</a></td></tr>
                                </table>
                                <div class="nav-box-banner">
                                    <a href="#"><img src="/public/assets/client/images/navbar/4.png"></a>
                                </div>
                            </div>
                        </li>

                        <!-- Máy tính bảng -->
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-tablet"></i> Máy tính bảng
                            </a>
                            <div class="nav-box">
                                <table class="nav-company htablet">
                                    <tr>
                                        <td colspan="3" class="nav-box-bold">Hàng sản xuất</td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Apple (iPad)</a></td>
                                        <td><a href="#">Samsung</a></td>
                                        <td><a href="#">Masstel</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Lenovo</a></td>
                                        <td><a href="#">Xiaomi</a></td>
                                        <td><a href="#">Coolpad</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Nexta</a></td>
                                    </tr>
                                </table>
                                <table class="level">
                                    <tr>
                                        <td class="nav-box-bold">Mức giá</td>
                                    </tr>
                                    <tr><td><a href="#">Dưới 2 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 2 - 5 triệu</a></td></tr>
                                    <tr><td><a href="#">Từ 5 - 8 triệu</a></td></tr>
                                    <tr><td><a href="#">Trên 8 triệu</a></td></tr>
                                </table>
                                <table class="hot-selling">
                                    <tr>
                                        <td class="nav-box-bold" colspan="2">Bán chạy nhất</td>
                                    </tr>
                                    <tr>
                                        <td><a href="#"><img src="/public/assets/client/images/navbar/5.png"></a></td>
                                        <td><a href="#">iPad Pro 11 2021 M1 Wi-Fi 128GB<p class="hot-selling-price">19.999.000 ₫</p></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#"><img src="/public/assets/client/images/navbar/6.png"></a></td>
                                        <td><a href="#">Samsung Galaxy Tab S6 Lite 2022<p class="hot-selling-price">7.990.000 ₫</p></a></td>
                                    </tr>
                                </table>
                                <div class="nav-box-banner">
                                    <a href="#"><img src="/public/assets/client/images/navbar/7.png"></a>
                                </div>
                            </div>
                        </li>

                        <!-- Apple -->
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-apple"></i> Apple
                            </a>
                            <div class="nav-box">
                                <table class="nav-company">
                                    <tr>
                                        <td colspan="3" class="nav-box-bold">Các sản phẩm Apple</td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">iPhone</a></td>
                                        <td><a href="#">iPad</a></td>
                                        <td><a href="#">MacBook</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Apple Watch</a></td>
                                        <td><a href="#">Apple Tai nghe</a></td>
                                        <td><a href="#">iMac</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Mac Mini</a></td>
                                        <td><a href="#">Ốp lưng &amp; Bao da</a></td>
                                        <td><a href="#">Apple TV</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Chuột &amp; Trackpad</a></td>
                                        <td><a href="#">Bàn phím</a></td>
                                        <td><a href="#">AirTag</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Hàng dự án</a></td>
                                    </tr>
                                </table>
                                <table class="hot-selling">
                                    <tr>
                                        <td class="nav-box-bold" colspan="2">Bán chạy nhất</td>
                                    </tr>
                                    <tr>
                                        <td><a href="#"><img src="/public/assets/client/images/navbar/8.png"></a></td>
                                        <td><a href="#">iPhone 13 Pro Max 128GB<p class="hot-selling-price">27.990.000 ₫</p></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#"><img src="/public/assets/client/images/navbar/9.png"></a></td>
                                        <td><a href="#">iPhone 13 128GB<p class="hot-selling-price">19.490.000 ₫</p></a></td>
                                    </tr>
                                </table>
                                <div class="nav-box-banner">
                                    <a href="#"><img src="/public/assets/client/images/navbar/10.png"></a>
                                </div>
                            </div>
                        </li>

                        <!-- PC-Linh kiện -->
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-desktop"></i> PC-Linh kiện
                            </a>
                            <ul class="news">
                                <li><a href="#">PC</a></li>
                                <li><a href="#">Linh kiện</a></li>
                                <li><a href="#">Màn hình</a></li>
                                <li><a href="#">Xây dựng PC</a></li>
                            </ul>
                        </li>

                        <!-- Phụ kiện -->
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-headphones"></i> Phụ kiện
                            </a>
                            <div class="nav-box">
                                <table class="nav-company">
                                    <tr>
                                        <td colspan="3" class="nav-box-bold">Các sản phẩm phụ kiện</td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Bao da ốp lưng</a></td>
                                        <td><a href="#">Sạc dự phòng</a></td>
                                        <td><a href="#">Thẻ nhớ</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Phụ kiện Apple</a></td>
                                        <td><a href="#">Miếng dán màn hình</a></td>
                                        <td><a href="#">Loa</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">USB - Ổ cứng</a></td>
                                        <td><a href="#">Sạc cáp</a></td>
                                        <td><a href="#">Tai nghe</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Chuột</a></td>
                                        <td><a href="#">Bàn ghế gaming</a></td>
                                        <td><a href="#">Balo - Túi xách</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">TV BOX</a></td>
                                        <td><a href="#">Bàn phím</a></td>
                                        <td><a href="#">Phụ kiện khác</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">Phụ kiện khẩu trang lọc khí</a></td>
                                    </tr>
                                </table>
                                <table class="hot-selling">
                                    <tr>
                                        <td class="nav-box-bold" colspan="2">Bán chạy nhất</td>
                                    </tr>
                                    <tr>
                                        <td><a href="#"><img src="/public/assets/client/images/navbar/11.png"></a></td>
                                        <td><a href="#">Combo Loa Bluetooth Karaoke kèm Mic không dây ivalue F12-65N<p class="hot-selling-price">1.953.000 ₫</p></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="#"><img src="/public/assets/client/images/navbar/12.png"></a></td>
                                        <td><a href="#">Pin sạc dự phòng UmeTravel 10000mAh TRIP10000 Quick Charge<p class="hot-selling-price">809.000 ₫</p></a></td>
                                    </tr>
                                </table>
                                <div class="nav-box-banner">
                                    <a href="#"><img src="/public/assets/client/images/navbar/13.png"></a>
                                </div>
                            </div>
                        </li>

                        <!-- Máy cũ giá rẻ -->
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-rotate-right"></i> Máy cũ giá rẻ
                            </a>
                        </li>

                        <!-- Hàng gia dụng -->
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-house-laptop"></i> Hàng gia dụng
                            </a>
                        </li>

                        <!-- Sim & Thẻ cào -->
                        <li class="menu-top-item">
                            <a href="/san-pham">
                                <i class="fa fa-sd-card"></i> Sim&amp;Thẻ cào
                            </a>
                        </li>

                        <!-- Khuyến mãi -->
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
    </div><!-- end header-bottom -->
</div><!-- end header -->

<!-- Menu mobile & tablet -->
<div class="modal-menu"></div>
<div class="menu-mobile-tablet">
    <ul class="menu-mobile-tablet-list">
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-mobile"></i> Điện thoại</a></li>
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-laptop"></i> Laptop</a></li>
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-apple"></i> Apple</a></li>
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-desktop"></i> PC phụ kiện</a></li>
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-headphones"></i> Tai nghe</a></li>
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-arrow-rotate-right"></i> Máy cũ giá rẻ</a></li>
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-house-laptop"></i> Hàng gia dụng</a></li>
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-sim-card"></i> Sim&amp;Thẻ cào</a></li>
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-certificate"></i> Khuyến mãi</a></li>
        <li class="menu-mobile-tablet-list-item"><a href="/san-pham"><i class="fa fa-circle-dollar-to-slot"></i> Trả góp</a></li>
    </ul>
    <div class="close-menu">
        <i class="fa fa-xmark"></i>
    </div>
</div>