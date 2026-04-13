<?php
$pageTitle = htmlspecialchars($sanPham['ten_san_pham'] ?? 'Chi tiết sản phẩm') . ' - FPT Shop';
ob_start();

// Ảnh chính
$anhChinh = !empty($hinhAnhList) ? $hinhAnhList[0]['url_anh'] : ($sanPham['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png');

// Tính điểm trung bình
$diemTB = 0;
if (!empty($danhGiaList)) {
    $diemTB = array_sum(array_column($danhGiaList, 'so_sao')) / count($danhGiaList);
}

$isLoggedIn = \App\Core\Session::isLoggedIn();
?>
<style>
    .variant-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        background: #fff;
        position: relative;
        overflow: hidden;
        user-select: none;
        
        /* --- THÊM CÁC DÒNG NÀY ĐỂ FIX THẲNG HÀNG --- */
        height: 100%;            /* Giãn chiều cao card lấp đầy cột */
        display: flex;           /* Sử dụng flexbox */
        flex-direction: column;  /* Xếp nội dung theo chiều dọc */
        justify-content: center; /* Căn giữa nội dung theo chiều dọc */
        align-items: center;     /* Căn giữa nội dung theo chiều ngang */
    }
    .variant-card:hover:not(.disabled) {
        border-color: #d70018;
        box-shadow: 0 0 5px rgba(215, 0, 24, 0.15);
    }
    .variant-card.active {
        border-color: #d70018;
        background-color: #fef2f2;
    }
    .variant-card.active::before {
        content: '\f00c'; /* Icon dấu tick của FontAwesome */
        font-family: 'Font Awesome 6 Free', 'FontAwesome';
        font-weight: 900;
        position: absolute;
        top: 0;
        right: 0;
        background: #d70018;
        color: #fff;
        font-size: 10px;
        padding: 2px 6px;
        border-bottom-left-radius: 8px;
    }
    .variant-card.disabled {
        background-color: #f8f9fa;
        color: #adb5bd;
        cursor: not-allowed;
        border-color: #e9ecef;
        opacity: 0.7;
    }
    .variant-price-label {
        font-size: 0.85rem;
        margin-top: 2px;
    }
    
    /* CSS cho bài viết mô tả sản phẩm */
    .product-description {
        line-height: 1.6;
        color: #333;
    }
    .product-description img {
        max-width: 100%;
        height: auto !important;
        border-radius: 8px;
        margin: 10px 0;
    }
    .product-description h2, .product-description h3, .product-description h4 {
        color: #d70018;
        margin-top: 20px;
        margin-bottom: 10px;
        font-weight: bold;
    }
</style>

<div class="container-xl py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-danger text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/san-pham" class="text-danger text-decoration-none">Sản phẩm</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($sanPham['ten_san_pham']) ?></li>
        </ol>
    </nav>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-3">
                <img id="main-img" src="<?= htmlspecialchars($anhChinh) ?>"
                     alt="<?= htmlspecialchars($sanPham['ten_san_pham']) ?>"
                     class="img-fluid mx-auto d-block mb-3"
                     style="max-height:320px;object-fit:contain; transition: opacity 0.2s;">
                     
                <?php if (count($hinhAnhList) > 1): ?>
                    <div class="d-flex gap-2 flex-wrap justify-content-center">
                        <?php foreach ($hinhAnhList as $img): ?>
                            <?php 
                                // Nếu ảnh có phien_ban_id thì in id đó ra, nếu không (Chung) thì in chữ 'all'
                                $variantDataId = !empty($img['phien_ban_id']) ? $img['phien_ban_id'] : 'all'; 
                            ?>
                            <img src="<?= htmlspecialchars($img['url_anh']) ?>"
                                 alt="" class="thumb-img border rounded"
                                 data-variant-id="<?= $variantDataId ?>"
                                 style="width:60px;height:60px;object-fit:contain;cursor:pointer;border:2px solid transparent; transition: all 0.2s;"
                                 onclick="document.getElementById('main-img').src=this.src; document.querySelectorAll('.thumb-img').forEach(t=>t.style.borderColor='transparent'); this.style.borderColor='#d70018';">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-7">
            <h1 class="h4 fw-bold mb-2"><?= htmlspecialchars($sanPham['ten_san_pham']) ?></h1>

            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="text-warning">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa<?= $i <= round($diemTB) ? 's' : 'r' ?> fa-star" style="font-size:0.85rem;"></i>
                    <?php endfor; ?>
                </div>
                <span class="text-muted small"><?= $tongDanhGia ?> đánh giá</span>
            </div>

            <div class="mb-3">
                <?php
                $giaBan = $sanPham['gia_hien_thi'];
                // Nếu có phiên bản chọn thì lấy giá phiên bản
                $phienBanDauTien = $phienBanList[0] ?? null;
                if ($phienBanDauTien) $giaBan = $phienBanDauTien['gia_ban'];
                ?>
                <span class="text-danger fw-bold fs-3" id="current-price">
                    <?= number_format($giaBan, 0, ',', '.') ?>đ
                </span>
                <?php if (!empty($sanPham['gia_goc']) && $sanPham['gia_goc'] > $giaBan): ?>
                    <span class="text-muted text-decoration-line-through ms-2">
                        <?= number_format($sanPham['gia_goc'], 0, ',', '.') ?>đ
                    </span>
                <?php endif; ?>
            </div>

            <?php if (!empty($phienBanList)): ?>
                <div class="mb-4">
                    <p class="fw-medium small mb-2">Chọn phiên bản:</p>
                    <div class="row g-2">
                        <?php foreach ($phienBanList as $idx => $pb): ?>
                            <?php 
                                $isOutOfStock = $pb['so_luong_ton'] <= 0;
                                $isActive = ($idx === 0 && !$isOutOfStock) ? 'active' : ''; 
                            ?>
                            <div class="col-4">
                                <div class="variant-card variant-btn <?= $isActive ?> <?= $isOutOfStock ? 'disabled' : '' ?>"
                                     data-id="<?= $pb['id'] ?>"
                                     data-price="<?= $pb['gia_ban'] ?>"
                                     data-stock="<?= $pb['so_luong_ton'] ?>">
                                    
                                    <div class="fw-bold text-wrap" style="font-size: 0.85rem;">
                                        <?= htmlspecialchars($pb['ten_phien_ban']) ?>
                                    </div>
                                    <div class="variant-price-label <?= $isActive ? 'text-danger fw-medium' : 'text-muted' ?>">
                                        <?= number_format($pb['gia_ban'], 0, ',', '.') ?>đ
                                    </div>
                                    
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <small id="stock-info" class="mt-2 d-block <?= ($phienBanDauTien['so_luong_ton'] ?? 0) > 0 ? 'text-success' : 'text-danger' ?>">
                        <?= ($phienBanDauTien['so_luong_ton'] ?? 0) > 0 ? '<i class="fa fa-check-circle me-1"></i>Còn lại: ' . $phienBanDauTien['so_luong_ton'] . ' sản phẩm' : '<i class="fa fa-times-circle me-1"></i>Đã hết hàng' ?>
                    </small>
                </div>
            <?php endif; ?>

            <form action="/gio-hang/them" method="POST" class="mb-3">
                <input type="hidden" name="phien_ban_id" id="selected-variant"
                       value="<?= $phienBanDauTien['id'] ?? 0 ?>">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <label class="small fw-medium">Số lượng:</label>
                    <div class="input-group" style="width:110px;">
                        <button class="btn btn-outline-secondary btn-sm" type="button"
                                onclick="changeQty(-1)">-</button>
                        <input type="number" name="so_luong" id="qty-input" class="form-control text-center"
                               value="1" min="1" max="99" style="font-size:0.88rem;">
                        <button class="btn btn-outline-secondary btn-sm" type="button"
                                onclick="changeQty(1)">+</button>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger fw-medium flex-grow-1">
                        <i class="fa fa-cart-plus me-1"></i>Thêm vào giỏ hàng
                    </button>
                    <?php if ($isLoggedIn): ?>
                        <button type="button" class="btn btn-outline-danger btn-wishlist"
                                data-id="<?= $sanPham['id'] ?>">
                            <i class="fa fa-heart"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </form>

            <div class="border rounded p-3 mt-2">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-shield text-success"></i>
                            <small>Bảo hành 12 tháng</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-truck-fast text-primary"></i>
                            <small>Giao hàng toàn quốc</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-rotate-right text-warning"></i>
                            <small>Đổi trả 30 ngày</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-credit-card text-danger"></i>
                            <small>Trả góp 0%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <ul class="nav nav-tabs" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark" id="tab-desc" data-bs-toggle="tab"
                        data-bs-target="#pane-desc" type="button">Đặc điểm nổi bật</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="tab-specs" data-bs-toggle="tab"
                        data-bs-target="#pane-specs" type="button">Thông số kỹ thuật</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="tab-reviews" data-bs-toggle="tab"
                        data-bs-target="#pane-reviews" type="button">
                    Đánh giá (<?= $tongDanhGia ?>)
                </button>
            </li>
        </ul>
        
        <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white shadow-sm">
            
            <div class="tab-pane fade show active product-description" id="pane-desc" role="tabpanel">
                <?php if (!empty($sanPham['mo_ta'])): ?>
                    <?= $sanPham['mo_ta'] ?>
                <?php else: ?>
                    <p class="text-muted small mb-0 text-center py-4">Nội dung mô tả sản phẩm đang được cập nhật.</p>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="pane-specs" role="tabpanel">
                <?php if (empty($thongSoList)): ?>
                    <p class="text-muted small mb-0 text-center py-4">Chưa có thông số kỹ thuật.</p>
                <?php else: ?>
                    <table class="table table-sm table-striped mb-0">
                        <tbody>
                            <?php foreach ($thongSoList as $ts): ?>
                                <tr>
                                    <td class="fw-medium small py-2" style="width:40%;"><?= htmlspecialchars($ts['ten_thong_so']) ?></td>
                                    <td class="small py-2"><?= htmlspecialchars($ts['gia_tri']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="pane-reviews" role="tabpanel">
                <?php if (empty($danhGiaList)): ?>
                    <p class="text-muted small text-center py-3">Chưa có đánh giá nào.</p>
                <?php else: ?>
                    <?php foreach ($danhGiaList as $dg): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <strong class="small"><?= htmlspecialchars($dg['ho_ten'] ?? 'Ẩn danh') ?></strong>
                                <div class="text-warning" style="font-size:0.75rem;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa<?= $i <= $dg['so_sao'] ? 's' : 'r' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-muted" style="font-size:0.72rem;"><?= date('d/m/Y', strtotime($dg['ngay_viet'])) ?></span>
                            </div>
                            <p class="small mb-0"><?= htmlspecialchars($dg['noi_dung']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($isLoggedIn): ?>
                    <div class="border rounded p-3 bg-light mt-3">
                        <h6 class="fw-bold mb-3">Gửi đánh giá của bạn</h6>
                        <div id="review-msg"></div>
                        <div class="mb-2">
                            <label class="form-label small fw-medium">Điểm đánh giá</label>
                            <select id="so_sao" class="form-select form-select-sm" style="width:120px;">
                                <option value="5">★★★★★ (5)</option>
                                <option value="4">★★★★☆ (4)</option>
                                <option value="3">★★★☆☆ (3)</option>
                                <option value="2">★★☆☆☆ (2)</option>
                                <option value="1">★☆☆☆☆ (1)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nội dung</label>
                            <textarea id="noi_dung" class="form-control form-control-sm" rows="3"
                                      placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-review" data-id="<?= $sanPham['id'] ?>">Gửi đánh giá</button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info small mt-4 mb-0">
                        <i class="fa fa-info-circle me-1"></i> <a href="/client/auth/login" class="text-danger fw-bold text-decoration-none">Đăng nhập</a> để gửi đánh giá.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($sanPhamTuongTu)): ?>
        <div class="mt-5">
            <h5 class="fw-bold mb-3 border-start border-danger border-3 ps-2">Sản phẩm tương tự</h5>
            <div class="row g-3">
                <?php foreach ($sanPhamTuongTu as $sp): ?>
                    <?php if ($sp['id'] == $sanPham['id']) continue; ?>
                    <div class="col-6 col-md-3">
                        <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 custom-hover-card">
                                <div class="overflow-hidden p-2">
                                    <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                                         class="card-img-top custom-hover-zoom" alt=""
                                         style="height:150px;object-fit:contain;">
                                </div>
                                <div class="card-body pt-0 px-3 pb-3">
                                    <p class="small mb-1 text-dark fw-medium" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                        <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                    </p>
                                    <p class="text-danger fw-bold mb-0 small"><?= number_format($sp['gia_hien_thi'], 0, ',', '.') ?>đ</p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let selectedVariantId = document.getElementById('selected-variant')?.value;

// Hàm lọc hình ảnh thông minh theo phiên bản
function filterImagesByVariant(variantId) {
    const thumbnails = document.querySelectorAll('.thumb-img');
    if (thumbnails.length === 0) return;

    let firstVisibleImageSrc = null;
    let hasSpecificImages = false;

    // Kiểm tra xem phiên bản đang chọn CÓ ảnh riêng nào không
    thumbnails.forEach(thumb => {
        if (thumb.getAttribute('data-variant-id') === variantId.toString()) {
            hasSpecificImages = true;
        }
    });

    // Duyệt qua từng ảnh để quyết định Ẩn hay Hiện
    thumbnails.forEach(thumb => {
        const thumbVariantId = thumb.getAttribute('data-variant-id');
        let shouldShow = false;

        if (hasSpecificImages) {
            shouldShow = (thumbVariantId === variantId.toString());
        } else {
            shouldShow = (thumbVariantId === 'all');
        }

        if (shouldShow) {
            thumb.style.display = 'block';
            if (!firstVisibleImageSrc) {
                firstVisibleImageSrc = thumb.src;
            }
        } else {
            thumb.style.display = 'none'; 
        }
    });

    // Tự động đổi ảnh to ở trên cùng theo ảnh đầu tiên
    const mainImg = document.getElementById('main-img');
    if (firstVisibleImageSrc && mainImg) {
        mainImg.style.opacity = 0.5; 
        setTimeout(() => {
            mainImg.src = firstVisibleImageSrc;
            mainImg.style.opacity = 1;
        }, 150);
        
        document.querySelectorAll('.thumb-img').forEach(t => t.style.borderColor = 'transparent');
        const firstVisibleThumb = Array.from(thumbnails).find(t => t.style.display !== 'none');
        if (firstVisibleThumb) {
            firstVisibleThumb.style.borderColor = '#d70018';
        }
    }
}

// Lọc ảnh ngay khi vừa tải trang
if (selectedVariantId) {
    filterImagesByVariant(selectedVariantId);
}

// Sự kiện khi Click chọn phiên bản
document.querySelectorAll('.variant-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (this.classList.contains('disabled')) return;

        document.querySelectorAll('.variant-btn').forEach(b => {
            b.classList.remove('active');
            const priceLabel = b.querySelector('.variant-price-label');
            if (priceLabel) {
                priceLabel.classList.remove('text-danger', 'fw-medium');
                priceLabel.classList.add('text-muted');
            }
        });

        this.classList.add('active');
        const activePriceLabel = this.querySelector('.variant-price-label');
        if (activePriceLabel) {
            activePriceLabel.classList.remove('text-muted');
            activePriceLabel.classList.add('text-danger', 'fw-medium');
        }
        
        const price = parseInt(this.dataset.price);
        const stock = parseInt(this.dataset.stock);
        selectedVariantId = this.dataset.id;
        
        document.getElementById('selected-variant').value = selectedVariantId;
        document.getElementById('current-price').textContent = price.toLocaleString('vi-VN') + 'đ';
        
        const stockInfo = document.getElementById('stock-info');
        if (stock > 0) {
            stockInfo.className = 'mt-2 d-block text-success';
            stockInfo.innerHTML = '<i class="fa fa-check-circle me-1"></i>Còn lại: ' + stock + ' sản phẩm';
        } else {
            stockInfo.className = 'mt-2 d-block text-danger';
            stockInfo.innerHTML = '<i class="fa fa-times-circle me-1"></i>Đã hết hàng';
        }

        const btnCart = document.querySelector('form[action="/gio-hang/them"] button[type="submit"]');
        if (btnCart) {
            if (stock <= 0) {
                btnCart.disabled = true;
                btnCart.classList.replace('btn-danger', 'btn-secondary');
                btnCart.innerHTML = '<i class="fa fa-ban me-1"></i>Hết hàng';
            } else {
                btnCart.disabled = false;
                btnCart.classList.replace('btn-secondary', 'btn-danger');
                btnCart.innerHTML = '<i class="fa fa-cart-plus me-1"></i>Thêm vào giỏ hàng';
            }
        }

        filterImagesByVariant(selectedVariantId);
    });
});

// Yêu thích
document.querySelector('.btn-wishlist')?.addEventListener('click', function() {
    const id = this.dataset.id;
    fetch('/yeu-thich/them', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'san_pham_id=' + id
    })
    .then(r => r.json())
    .then(data => {
        const icon = this.querySelector('i');
        if (data.success) {
            icon.className = 'fas fa-heart text-danger';
        } else {
            alert(data.message || 'Đã có trong danh sách yêu thích');
        }
    });
});

// Nút cộng/trừ số lượng
function changeQty(delta) {
    const inp = document.getElementById('qty-input');
    let val = parseInt(inp.value) + delta;
    if (val < 1) val = 1;
    if (val > 99) val = 99;
    inp.value = val;
}

// Đánh giá
document.getElementById('btn-review')?.addEventListener('click', function() {
    const sanPhamId = this.dataset.id;
    const soSao = document.getElementById('so_sao').value;
    const noiDung = document.getElementById('noi_dung').value.trim();
    const msg = document.getElementById('review-msg');

    if (!noiDung) { 
        msg.innerHTML = '<div class="alert alert-warning py-2 small mb-3"><i class="fa fa-exclamation-triangle me-1"></i>Vui lòng nhập nội dung đánh giá</div>'; 
        return; 
    }

    fetch('/danh-gia/them', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'san_pham_id=' + sanPhamId + '&so_sao=' + soSao + '&noi_dung=' + encodeURIComponent(noiDung)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            msg.innerHTML = '<div class="alert alert-success py-2 small mb-3"><i class="fa fa-check-circle me-1"></i>' + data.message + '</div>';
            document.getElementById('noi_dung').value = '';
        } else {
            msg.innerHTML = '<div class="alert alert-danger py-2 small mb-3"><i class="fa fa-times-circle me-1"></i>' + data.message + '</div>';
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>