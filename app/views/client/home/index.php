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
    <?php if (!empty($bannerHero)): ?>
        <div class="grid wide">
            <img src="<?php echo htmlspecialchars($bannerHero[0]['hinh_anh_desktop']); ?>" 
                 alt="<?php echo htmlspecialchars($bannerHero[0]['tieu_de']); ?>" 
                 style="width: 100%">
        </div>
    <?php endif; ?>

    <!-- Slider chính + banner nhỏ bên phải -->
    <div class="grid wide">
        <div class="row">

            <!-- Slider ảnh -->
            <div class="col l-9 m-12 c-12">
                <div class="grid">
                    <div class="wapper-slider">
                        <div class="row no-warp main-slider">
                            <?php if (!empty($bannerHero)): ?>
                                <?php foreach ($bannerHero as $banner): ?>
                                    <div class="col l-12 m-12 c-12 wrapper-item-slider">
                                        <div class="item-slider">
                                            <a href="<?php echo htmlspecialchars($banner['link_dich']); ?>">
                                                <img src="<?php echo htmlspecialchars($banner['hinh_anh_desktop']); ?>" 
                                                     alt="<?php echo htmlspecialchars($banner['tieu_de']); ?>">
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="back-slider-card" type="button"><i class="fa fa-chevron-left"></i></button>
                        <button class="next-slider-card" type="button"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
                <ul class="slider-content">
                    <?php if (!empty($danhMucList)): ?>
                        <?php foreach (array_slice($danhMucList, 0, 6) as $dm): ?>
                            <li><a href="/danh-muc/<?php echo htmlspecialchars($dm['slug']); ?>"><?php echo htmlspecialchars($dm['ten']); ?></a></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Banner nhỏ bên phải -->
            <div class="col l-3 c-12 m-12">
                <div class="banner-top">
                    <?php if (!empty($bannerSide)): ?>
                        <?php foreach (array_slice($bannerSide, 0, 4) as $banner): ?>
                            <div class="banner-img-item">
                                <a href="<?php echo htmlspecialchars($banner['link_dich']); ?>">
                                    <img src="<?php echo htmlspecialchars($banner['hinh_anh_desktop']); ?>" 
                                         alt="<?php echo htmlspecialchars($banner['tieu_de']); ?>">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Danh mục nhanh -->
<div class="category-wapper">
    <div class="grid wide category">
        <div class="row no-gutters">
            <?php if (!empty($danhMucList)): ?>
                <?php foreach ($danhMucList as $dm): ?>
                    <div class="col l-2 m-3 c-3">
                        <div class="category-item">
                            <a href="/danh-muc/<?php echo htmlspecialchars($dm['slug']); ?>">
                                <div class="img-category">
                                    <?php if (!empty($dm['icon_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($dm['icon_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($dm['ten']); ?>">
                                    <?php else: ?>
                                        <img src="/public/assets/client/images/icon/phone.png" 
                                             alt="<?php echo htmlspecialchars($dm['ten']); ?>">
                                    <?php endif; ?>
                                </div>
                                <p class="title-category"><?php echo htmlspecialchars($dm['ten']); ?></p>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
            <div class="row slider-main">
                <?php if (!empty($sanPhamKhuyenMai)): ?>
                    <?php 
                    require_once dirname(__DIR__, 3) . '/models/entities/SanPham.php';
                    $spModel = new SanPham();
                    foreach ($sanPhamKhuyenMai as $sp): 
                        $giaSauGiam = $spModel->tinhGiaSauKhuyenMai(
                            $sp['gia_hien_thi'], 
                            $sp['loai_giam'], 
                            $sp['gia_tri_giam'], 
                            $sp['giam_toi_da']
                        );
                        $tienGiam = $sp['gia_hien_thi'] - $giaSauGiam;
                    ?>
                        <div class="col l-3 m-6 c-6 card-slider">
                            <div class="product-card-item">
                                <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>">
                                    <div class="product-card-item-img">
                                        <?php if (!empty($sp['anh_chinh'])): ?>
                                            <img src="<?php echo htmlspecialchars($sp['anh_chinh']); ?>" 
                                                 alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>">
                                        <?php else: ?>
                                            <img src="/public/assets/client/images/products/14.png" 
                                                 alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>">
                                        <?php endif; ?>
                                        <div class="sticker">
                                            <span class="sticker-sale">Ưu đãi <?php echo number_format($tienGiam, 0, ',', '.'); ?>đ</span><br>
                                            <span class="sticker-event">Trả góp 0%</span>
                                        </div>
                                    </div>
                                </a>
                                <div class="product-card-item-content">
                                    <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>">
                                        <h3 class="title-card"><?php echo htmlspecialchars($sp['ten_san_pham']); ?></h3>
                                        <div class="price">
                                            <span class="new-price"><?php echo number_format($giaSauGiam, 0, ',', '.'); ?>đ</span>
                                            <span class="old-price"><?php echo number_format($sp['gia_hien_thi'], 0, ',', '.'); ?>đ</span>
                                        </div>
                                        <div class="card-item-info">
                                            <span class="text-info-card">Giảm thêm 150.000đ khi thanh toán online 100% qua thẻ Mastercard</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
                <?php if (!empty($sanPhamDienThoai)): ?>
                    <?php foreach ($sanPhamDienThoai as $sp): ?>
                        <div class="col l-3 m-4 c-6 product-card">
                            <div class="product-card-item">
                                <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>">
                                    <div class="product-card-item-img">
                                        <?php if (!empty($sp['anh_chinh'])): ?>
                                            <img src="<?php echo htmlspecialchars($sp['anh_chinh']); ?>" 
                                                 alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>">
                                        <?php else: ?>
                                            <img src="/public/assets/client/images/products/14.png" 
                                                 alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>">
                                        <?php endif; ?>
                                        <div class="sticker">
                                            <span class="sticker-event">Trả góp 0%</span>
                                        </div>
                                    </div>
                                </a>
                                <div class="product-card-item-content">
                                    <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>">
                                        <h3 class="title-card"><?php echo htmlspecialchars($sp['ten_san_pham']); ?></h3>
                                        <div class="price">
                                            <span class="new-price"><?php echo number_format($sp['gia_hien_thi'], 0, ',', '.'); ?>đ</span>
                                        </div>
                                        <div class="card-item-info">
                                            <span class="text-info-card">Giảm thêm 150.000đ khi thanh toán online 100% qua thẻ Mastercard</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
                <?php if (!empty($sanPhamLaptop)): ?>
                    <?php foreach ($sanPhamLaptop as $sp): ?>
                        <div class="col l-3 m-4 c-6 product-card">
                            <div class="product-card-item">
                                <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>">
                                    <div class="product-card-item-img">
                                        <?php if (!empty($sp['anh_chinh'])): ?>
                                            <img src="<?php echo htmlspecialchars($sp['anh_chinh']); ?>" 
                                                 alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>">
                                        <?php else: ?>
                                            <img src="/public/assets/client/images/products/20.jpg" 
                                                 alt="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>">
                                        <?php endif; ?>
                                        <div class="sticker">
                                            <span class="sticker-event">Trả góp 0%</span>
                                        </div>
                                    </div>
                                </a>
                                <div class="product-card-item-content">
                                    <a href="/san-pham/<?php echo htmlspecialchars($sp['slug']); ?>">
                                        <h3 class="title-card"><?php echo htmlspecialchars($sp['ten_san_pham']); ?></h3>
                                        <div class="price">
                                            <span class="new-price"><?php echo number_format($sp['gia_hien_thi'], 0, ',', '.'); ?>đ</span>
                                        </div>
                                        <div class="card-item-info">
                                            <span class="text-info-card">Giảm thêm 150.000đ khi thanh toán online 100% qua thẻ Mastercard</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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