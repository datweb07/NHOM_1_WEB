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

<!-- Banner top -->
<div class="slider">
    <div class="grid wide">
        <img src="/public/assets/client/images/header/2.png" style="width: 100%">
    </div>

    <!-- Slider chính + banner nhỏ bên phải -->
    <div class="grid wide">
        <div class="row">

            <!-- Slider ảnh -->
            <div class="col l-9 m-12 c-12">
                <div class="grid">
                    <div class="wapper-slider">
                        <div class="row no-warp main-slider">
                            <div class="col l-12 m-12 c-12 wrapper-item-slider">
                                <div class="item-slider"><img src="/public/assets/client/images/others/1.png"></div>
                            </div>
                            <div class="col l-12 m-12 c-12 wrapper-item-slider">
                                <div class="item-slider"><img src="/public/assets/client/images/others/2.png"></div>
                            </div>
                            <div class="col l-12 m-12 c-12 wrapper-item-slider">
                                <div class="item-slider"><img src="/public/assets/client/images/others/3.png"></div>
                            </div>
                            <div class="col l-12 m-12 c-12 wrapper-item-slider">
                                <div class="item-slider"><img src="/public/assets/client/images/others/1.png"></div>
                            </div>
                            <div class="col l-12 m-12 c-12 wrapper-item-slider">
                                <div class="item-slider"><img src="/public/assets/client/images/others/2.png"></div>
                            </div>
                            <div class="col l-12 m-12 c-12 wrapper-item-slider">
                                <div class="item-slider"><img src="/public/assets/client/images/others/3.png"></div>
                            </div>
                        </div>
                        <button class="back-slider-card" type="button"><i class="fa fa-chevron-left"></i></button>
                        <button class="next-slider-card" type="button"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
                <ul class="slider-content">
                    <li>Iphone 14</li>
                    <li>Iphone 13</li>
                    <li>Samsung</li>
                    <li>Zeno</li>
                    <li>Apple</li>
                    <li>Redmi</li>
                </ul>
            </div>

            <!-- Banner nhỏ bên phải -->
            <div class="col l-3 c-12 m-12">
                <div class="banner-top">
                    <div class="banner-img-item"><img src="/public/assets/client/images/others/4.png"></div>
                    <div class="banner-img-item"><img src="/public/assets/client/images/others/5.png"></div>
                    <div class="banner-img-item"><img src="/public/assets/client/images/others/6.png"></div>
                    <div class="banner-img-item"><img src="/public/assets/client/images/others/7.png"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Danh mục nhanh -->
<div class="category-wapper">
    <div class="grid wide category">
        <div class="row no-gutters">
            <?php
            $categories = [
                ['img' => 'phone.png',   'title' => 'Điện thoại'],
                ['img' => 'lap.png',     'title' => 'Laptop'],
                ['img' => 'apple.png',   'title' => 'Apple'],
                ['img' => 'samsung.png', 'title' => 'Samsung'],
                ['img' => 'tablet.png',  'title' => 'Máy tính bảng'],
                ['img' => 'xiaomi.png',  'title' => 'Xiaomi'],
                ['img' => 'phone.png',   'title' => 'Điện thoại'],
                ['img' => 'lap.png',     'title' => 'Laptop'],
                ['img' => 'apple.png',   'title' => 'Apple'],
                ['img' => 'samsung.png', 'title' => 'Samsung'],
                ['img' => 'tablet.png',  'title' => 'Máy tính bảng'],
                ['img' => 'xiaomi.png',  'title' => 'Xiaomi'],
            ];
            foreach ($categories as $cat): ?>
                <div class="col l-2 m-3 c-3">
                    <div class="category-item">
                        <a href="#">
                            <div class="img-category">
                                <img src="/public/assets/client/images/icon/<?php echo $cat['img']; ?>">
                            </div>
                            <p class="title-category"><?php echo $cat['title']; ?></p>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Slider card khuyến mãi -->
<div class="slider-card">
    <div class="grid wide slider">
        <div class="row">
            <div class="col l-12 m-12 c-12">
                <p class="title-product fire"><i class="fa fa-fire-flame-curved"></i> Khuyến mãi</p>
            </div>
        </div>
        <div class="slider-wapper">
            <div class="row slider-main" style="transform: translateX(-1212px);">
                <?php
                $promoProducts = [
                    ['img' => '14.png', 'name' => 'Iphone 13 128GB',  'new' => '20.000.000đ', 'old' => '25.000.000đ'],
                    ['img' => '15.jpg', 'name' => 'Iphone 13 256GB',  'new' => '25.000.000đ', 'old' => '30.000.000đ'],
                    ['img' => '16.jpg', 'name' => 'Samsung A50',       'new' => '8.000.000đ',  'old' => '10.000.000đ'],
                    ['img' => '17.jpg', 'name' => 'Vivo V23e',         'new' => '9.000.000đ',  'old' => '11.000.000đ'],
                    ['img' => '14.png', 'name' => 'Iphone 13 128GB',  'new' => '20.000.000đ', 'old' => '25.000.000đ'],
                    ['img' => '15.jpg', 'name' => 'Iphone 13 256GB',  'new' => '25.000.000đ', 'old' => '30.000.000đ'],
                    ['img' => '16.jpg', 'name' => 'Samsung A50',       'new' => '8.000.000đ',  'old' => '10.000.000đ'],
                    ['img' => '17.jpg', 'name' => 'Vivo V23e',         'new' => '9.000.000đ',  'old' => '11.000.000đ'],
                ];
                foreach ($promoProducts as $p): ?>
                    <div class="col l-3 m-6 c-6 card-slider">
                        <div class="product-card-item">
                            <a href="/san-pham/chi-tiet">
                                <div class="product-card-item-img">
                                    <img src="/public/assets/client/images/products/<?php echo $p['img']; ?>">
                                    <div class="sticker">
                                        <span class="sticker-sale">Ưu đãi 5.000.000đ</span><br>
                                        <span class="sticker-event">Trả góp 0%</span>
                                    </div>
                                </div>
                            </a>
                            <div class="product-card-item-content">
                                <a href="/san-pham/chi-tiet">
                                    <h3 class="title-card"><?php echo $p['name']; ?></h3>
                                    <div class="price">
                                        <span class="new-price"><?php echo $p['new']; ?></span>
                                        <span class="old-price"><?php echo $p['old']; ?></span>
                                    </div>
                                    <div class="card-item-info">
                                        <span class="text-info-card">Giảm thêm 150.000đ khi thanh toán online 100% qua thẻ Mastercard</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="next-slider-card" type="button"><i class="fa fa-chevron-right"></i></button>
        <button class="back-slider-card" type="button"><i class="fa fa-chevron-left"></i></button>
    </div>
</div>

<!-- Banner 1 -->
<div class="banner-1">
    <div class="grid wide">
        <div class="row">
            <div class="col l-12 banner">
                <img src="/public/assets/client/images/others/18.jpg">
            </div>
        </div>
    </div>
</div>

<!-- Sản phẩm Điện thoại -->
<div class="product">
    <div class="grid wide">
        <div class="product-wapper">
            <div class="row">
                <div class="col l-12 m-12 c-12">
                    <p class="title-product">Điện thoại</p>
                </div>
            </div>
            <div class="row">
                <?php
                $phones = [
                    ['img' => '14.png', 'name' => 'Iphone 13 128GB',  'new' => '20.000.000đ', 'old' => '25.000.000đ'],
                    ['img' => '15.jpg', 'name' => 'Iphone 13 256GB',  'new' => '25.000.000đ', 'old' => '30.000.000đ'],
                    ['img' => '16.jpg', 'name' => 'Samsung A50',       'new' => '8.000.000đ',  'old' => '10.000.000đ'],
                    ['img' => '17.jpg', 'name' => 'Vivo V23e',         'new' => '9.000.000đ',  'old' => '11.000.000đ'],
                    ['img' => '14.png', 'name' => 'Iphone 13 128GB',  'new' => '20.000.000đ', 'old' => '25.000.000đ'],
                    ['img' => '15.jpg', 'name' => 'Iphone 13 256GB',  'new' => '25.000.000đ', 'old' => '30.000.000đ'],
                    ['img' => '16.jpg', 'name' => 'Samsung A50',       'new' => '8.000.000đ',  'old' => '10.000.000đ'],
                    ['img' => '17.jpg', 'name' => 'Vivo V23e',         'new' => '9.000.000đ',  'old' => '11.000.000đ'],
                ];
                foreach ($phones as $p): ?>
                    <div class="col l-3 m-4 c-6 product-card">
                        <div class="product-card-item">
                            <a href="/san-pham/chi-tiet">
                                <div class="product-card-item-img">
                                    <img src="/public/assets/client/images/products/<?php echo $p['img']; ?>">
                                    <div class="sticker">
                                        <span class="sticker-sale">Ưu đãi 5.000.000đ</span><br>
                                        <span class="sticker-event">Trả góp 0%</span>
                                    </div>
                                </div>
                            </a>
                            <div class="product-card-item-content">
                                <a href="/san-pham/chi-tiet">
                                    <h3 class="title-card"><?php echo $p['name']; ?></h3>
                                    <div class="price">
                                        <span class="new-price"><?php echo $p['new']; ?></span>
                                        <span class="old-price"><?php echo $p['old']; ?></span>
                                    </div>
                                    <div class="card-item-info">
                                        <span class="text-info-card">Giảm thêm 150.000đ khi thanh toán online 100% qua thẻ Mastercard</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Banner 2 -->
<div class="banner-2">
    <div class="grid wide">
        <div class="row">
            <div class="col l-12 banner">
                <img src="/public/assets/client/images/others/19.jpg">
            </div>
        </div>
    </div>
</div>

<!-- Sản phẩm Laptop -->
<div class="product">
    <div class="grid wide">
        <div class="product-wapper">
            <div class="row">
                <div class="col l-12 m-12 c-12">
                    <p class="title-product">Laptop</p>
                </div>
            </div>
            <div class="row">
                <?php
                $laptops = [
                    ['img' => '20.jpg', 'name' => 'Laptop Dell',   'new' => '20.000.000đ', 'old' => '25.000.000đ'],
                    ['img' => '21.jpg', 'name' => 'Laptop HP',     'new' => '25.000.000đ', 'old' => '30.000.000đ'],
                    ['img' => '22.jpg', 'name' => 'Laptop Asus',   'new' => '8.000.000đ',  'old' => '10.000.000đ'],
                    ['img' => '23.jpg', 'name' => 'Laptop Lenovo', 'new' => '9.000.000đ',  'old' => '11.000.000đ'],
                    ['img' => '20.jpg', 'name' => 'Laptop Dell',   'new' => '20.000.000đ', 'old' => '25.000.000đ'],
                    ['img' => '21.jpg', 'name' => 'Laptop HP',     'new' => '25.000.000đ', 'old' => '30.000.000đ'],
                    ['img' => '22.jpg', 'name' => 'Laptop Asus',   'new' => '8.000.000đ',  'old' => '10.000.000đ'],
                    ['img' => '23.jpg', 'name' => 'Laptop Lenovo', 'new' => '9.000.000đ',  'old' => '11.000.000đ'],
                ];
                foreach ($laptops as $p): ?>
                    <div class="col l-3 m-4 c-6 product-card">
                        <div class="product-card-item">
                            <a href="/san-pham/chi-tiet">
                                <div class="product-card-item-img">
                                    <img src="/public/assets/client/images/products/<?php echo $p['img']; ?>">
                                    <div class="sticker">
                                        <span class="sticker-sale">Ưu đãi 5.000.000đ</span><br>
                                        <span class="sticker-event">Trả góp 0%</span>
                                    </div>
                                </div>
                            </a>
                            <div class="product-card-item-content">
                                <a href="/san-pham/chi-tiet">
                                    <h3 class="title-card"><?php echo $p['name']; ?></h3>
                                    <div class="price">
                                        <span class="new-price"><?php echo $p['new']; ?></span>
                                        <span class="old-price"><?php echo $p['old']; ?></span>
                                    </div>
                                    <div class="card-item-info">
                                        <span class="text-info-card">Giảm thêm 150.000đ khi thanh toán online 100% qua thẻ Mastercard</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Banner 3 -->
<div class="banner-3">
    <div class="grid wide">
        <div class="row">
            <div class="col l-12 banner">
                <img src="/public/assets/client/images/others/24.jpg">
            </div>
        </div>
    </div>
</div>

<!-- Danh mục Phụ kiện -->
<div class="category-wapper">
    <div class="grid wide category">
        <div class="row">
            <div class="col l-12 m-12 c-12">
                <p class="title-product">Phụ kiện</p>
            </div>
        </div>
        <div class="row no-gutters">
            <?php
            $accessories = [
                ['icon' => 'fa-bahai',            'title' => 'Phụ kiện hot'],
                ['icon' => 'fa-apple',            'title' => 'Apple'],
                ['icon' => 'fa-battery-full',     'title' => 'Pin dự phòng'],
                ['icon' => 'fa-headphones-simple','title' => 'Tai nghe'],
                ['icon' => 'fa-keyboard',         'title' => 'Bàn phím'],
                ['icon' => 'fa-computer-mouse',   'title' => 'Chuột'],
                ['icon' => 'fa-bahai',            'title' => 'Phụ kiện hot'],
                ['icon' => 'fa-apple',            'title' => 'Apple'],
                ['icon' => 'fa-battery-full',     'title' => 'Pin dự phòng'],
                ['icon' => 'fa-headphones-simple','title' => 'Tai nghe'],
                ['icon' => 'fa-keyboard',         'title' => 'Bàn phím'],
                ['icon' => 'fa-computer-mouse',   'title' => 'Chuột'],
            ];
            foreach ($accessories as $acc): ?>
                <div class="col l-2 m-4 c-4">
                    <div class="category-item">
                        <a href="#">
                            <div class="category-icon">
                                <i class="fa <?php echo $acc['icon']; ?>"></i>
                            </div>
                            <p class="title-category"><?php echo $acc['title']; ?></p>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Banner bottom -->
<div class="category-bottom">
    <div class="grid wide">
        <div class="row">
            <div class="col l-4 m-12 c-12">
                <div class="category-bot-item"><img src="/public/assets/client/images/others/25.jpg"></div>
            </div>
            <div class="col l-4 m-12 c-12">
                <div class="category-bot-item"><img src="/public/assets/client/images/others/26.jpg"></div>
            </div>
            <div class="col l-4 m-12 c-12">
                <div class="category-bot-item"><img src="/public/assets/client/images/others/27.jpg"></div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>