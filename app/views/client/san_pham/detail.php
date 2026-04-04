<?php
$pageTitle = htmlspecialchars($sanPham['ten_san_pham'] ?? 'Chi tiết sản phẩm') . ' - FPT Shop';
ob_start();

// Ảnh chính
$anhChinh = !empty($hinhAnhList) ? $hinhAnhList[0]['url_anh'] : ($sanPham['anh_chinh'] ?? '/public/assets/client/images/products/14.png');

// Tính điểm trung bình
$diemTB = 0;
if (!empty($danhGiaList)) {
    $diemTB = array_sum(array_column($danhGiaList, 'so_sao')) / count($danhGiaList);
}

$isLoggedIn = \App\Core\Session::isLoggedIn();
?>

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

        <!-- Hình ảnh sản phẩm -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-3">
                <img id="main-img" src="<?= htmlspecialchars($anhChinh) ?>"
                     alt="<?= htmlspecialchars($sanPham['ten_san_pham']) ?>"
                     class="img-fluid mx-auto d-block mb-3"
                     style="max-height:320px;object-fit:contain;">
                <?php if (count($hinhAnhList) > 1): ?>
                    <div class="d-flex gap-2 flex-wrap justify-content-center">
                        <?php foreach ($hinhAnhList as $img): ?>
                            <img src="<?= htmlspecialchars($img['url_anh']) ?>"
                                 alt="" class="thumb-img border rounded"
                                 style="width:60px;height:60px;object-fit:contain;cursor:pointer;border:2px solid transparent;"
                                 onclick="document.getElementById('main-img').src=this.src; document.querySelectorAll('.thumb-img').forEach(t=>t.style.borderColor='transparent'); this.style.borderColor='#d70018';">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-md-7">
            <h1 class="h4 fw-bold mb-2"><?= htmlspecialchars($sanPham['ten_san_pham']) ?></h1>

            <!-- Đánh giá -->
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="text-warning">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa<?= $i <= round($diemTB) ? 's' : 'r' ?> fa-star" style="font-size:0.85rem;"></i>
                    <?php endfor; ?>
                </div>
                <span class="text-muted small"><?= $tongDanhGia ?> đánh giá</span>
            </div>

            <!-- Giá -->
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

            <!-- Phiên bản -->
            <?php if (!empty($phienBanList)): ?>
                <div class="mb-3">
                    <p class="fw-medium small mb-2">Chọn phiên bản:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($phienBanList as $idx => $pb): ?>
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary variant-btn <?= $idx === 0 ? 'active border-danger text-danger' : '' ?>"
                                    data-id="<?= $pb['id'] ?>"
                                    data-price="<?= $pb['gia_ban'] ?>"
                                    data-stock="<?= $pb['ton_kho'] ?>">
                                <?= htmlspecialchars($pb['ten_phien_ban']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <small class="text-muted mt-1 d-block" id="stock-info">
                        Còn lại: <?= $phienBanDauTien['ton_kho'] ?? 0 ?> sản phẩm
                    </small>
                </div>
            <?php endif; ?>

            <!-- Form thêm giỏ -->
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

            <!-- Dịch vụ -->
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

    <!-- Tabs thông số & đánh giá -->
    <div class="mt-4">
        <ul class="nav nav-tabs" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-specs" data-bs-toggle="tab"
                        data-bs-target="#pane-specs" type="button">Thông số kỹ thuật</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-reviews" data-bs-toggle="tab"
                        data-bs-target="#pane-reviews" type="button">
                    Đánh giá (<?= $tongDanhGia ?>)
                </button>
            </li>
        </ul>
        <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white shadow-sm">

            <!-- Thông số -->
            <div class="tab-pane fade show active" id="pane-specs" role="tabpanel">
                <?php if (empty($thongSoList)): ?>
                    <p class="text-muted small mb-0">Chưa có thông số kỹ thuật.</p>
                <?php else: ?>
                    <table class="table table-sm table-striped mb-0">
                        <tbody>
                            <?php foreach ($thongSoList as $ts): ?>
                                <tr>
                                    <td class="fw-medium small" style="width:40%;"><?= htmlspecialchars($ts['ten_thong_so']) ?></td>
                                    <td class="small"><?= htmlspecialchars($ts['gia_tri']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Đánh giá -->
            <div class="tab-pane fade" id="pane-reviews" role="tabpanel">
                <?php if (empty($danhGiaList)): ?>
                    <p class="text-muted small">Chưa có đánh giá nào.</p>
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
                                <span class="text-muted" style="font-size:0.72rem;"><?= date('d/m/Y', strtotime($dg['ngay_tao'])) ?></span>
                            </div>
                            <p class="small mb-0"><?= htmlspecialchars($dg['noi_dung']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Form gửi đánh giá -->
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
                        <div class="mb-2">
                            <label class="form-label small fw-medium">Nội dung</label>
                            <textarea id="noi_dung" class="form-control form-control-sm" rows="3"
                                      placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-review" data-id="<?= $sanPham['id'] ?>">Gửi đánh giá</button>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mt-3">
                        <a href="/client/auth/login" class="text-danger">Đăng nhập</a> để gửi đánh giá.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sản phẩm tương tự -->
    <?php if (!empty($sanPhamTuongTu)): ?>
        <div class="mt-5">
            <h5 class="fw-bold mb-3 border-start border-danger border-3 ps-2">Sản phẩm tương tự</h5>
            <div class="row g-3">
                <?php foreach ($sanPhamTuongTu as $sp): ?>
                    <?php if ($sp['id'] == $sanPham['id']) continue; ?>
                    <div class="col-6 col-md-3">
                        <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100">
                                <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? '/public/assets/client/images/products/14.png') ?>"
                                     class="card-img-top p-2" alt=""
                                     style="height:130px;object-fit:contain;">
                                <div class="card-body pt-0 px-3 pb-3">
                                    <p class="small mb-1 text-dark" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
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

// Chọn phiên bản
document.querySelectorAll('.variant-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.variant-btn').forEach(b => {
            b.classList.remove('active', 'border-danger', 'text-danger');
        });
        this.classList.add('active', 'border-danger', 'text-danger');
        
        const price = parseInt(this.dataset.price);
        const stock = parseInt(this.dataset.stock);
        selectedVariantId = this.dataset.id;
        
        document.getElementById('selected-variant').value = selectedVariantId;
        document.getElementById('current-price').textContent = price.toLocaleString('vi-VN') + 'đ';
        document.getElementById('stock-info').textContent = 'Còn lại: ' + stock + ' sản phẩm';
    });
});

function changeQty(delta) {
    const inp = document.getElementById('qty-input');
    let val = parseInt(inp.value) + delta;
    if (val < 1) val = 1;
    if (val > 99) val = 99;
    inp.value = val;
}

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

// Đánh giá
document.getElementById('btn-review')?.addEventListener('click', function() {
    const sanPhamId = this.dataset.id;
    const soSao = document.getElementById('so_sao').value;
    const noiDung = document.getElementById('noi_dung').value.trim();
    const msg = document.getElementById('review-msg');

    if (!noiDung) { msg.innerHTML = '<div class="alert alert-warning py-1 small">Vui lòng nhập nội dung đánh giá</div>'; return; }

    fetch('/danh-gia/them', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'san_pham_id=' + sanPhamId + '&so_sao=' + soSao + '&noi_dung=' + encodeURIComponent(noiDung)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            msg.innerHTML = '<div class="alert alert-success py-1 small"><i class="fa fa-check me-1"></i>' + data.message + '</div>';
            document.getElementById('noi_dung').value = '';
        } else {
            msg.innerHTML = '<div class="alert alert-danger py-1 small">' + data.message + '</div>';
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>