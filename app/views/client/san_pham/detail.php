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

$goiYSoSanh = [];
if (!empty($sanPhamTuongTu)) {
    foreach ($sanPhamTuongTu as $spTuongTu) {
        if ((int)($spTuongTu['id'] ?? 0) === (int)($sanPham['id'] ?? 0)) {
            continue;
        }
        if ((int)($spTuongTu['danh_muc_id'] ?? 0) !== (int)($sanPham['danh_muc_id'] ?? 0)) {
            continue;
        }
        if (empty($spTuongTu['slug'])) {
            continue;
        }
        $goiYSoSanh[] = $spTuongTu;
    }
}

$danhSachSanPhamSoSanh = $danhSachSanPhamSoSanh ?? [];
$optionsSanPhamKhacHtml = '<option value="">-- Chọn ngoài sản phẩm tương tự --</option>';
foreach ($danhSachSanPhamSoSanh as $spSoSanhThem) {
    $slugSpThem = (string)($spSoSanhThem['slug'] ?? '');
    if ($slugSpThem === '' || $slugSpThem === (string)($sanPham['slug'] ?? '')) {
        continue;
    }
    $tenSpThem = (string)($spSoSanhThem['ten_san_pham'] ?? $slugSpThem);
    $optionsSanPhamKhacHtml .= '<option value="' . htmlspecialchars($slugSpThem, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($tenSpThem, ENT_QUOTES, 'UTF-8') . '</option>';
}

$phienBanMacDinh = null;
if (!empty($phienBanList)) {
    foreach ($phienBanList as $pbTmp) {
        if ((int)($pbTmp['so_luong_ton'] ?? 0) > 0) {
            $phienBanMacDinh = $pbTmp;
            break;
        }
    }
    if ($phienBanMacDinh === null) {
        $phienBanMacDinh = $phienBanList[0];
    }
}

$bienTheTam = [];
$thuocTinhUngVien = [];
foreach (($phienBanList ?? []) as $pb) {
    $thuocTinhBienThe = [];

    $mauSac = trim((string)($pb['mau_sac'] ?? ''));
    if ($mauSac !== '') {
        $thuocTinhBienThe['Màu sắc'] = $mauSac;
        $thuocTinhUngVien['Màu sắc'] = true;
    }

    foreach (['thuoc_tinh_bien_the', 'cau_hinh'] as $cotJson) {
        $jsonRaw = $pb[$cotJson] ?? null;
        if (!is_string($jsonRaw) || trim($jsonRaw) === '') {
            continue;
        }

        $jsonData = json_decode($jsonRaw, true);
        if (!is_array($jsonData)) {
            continue;
        }

        foreach ($jsonData as $ten => $giaTri) {
            if (!is_scalar($giaTri) && $giaTri !== null) {
                continue;
            }

            $tenThuocTinh = trim(str_replace('_', ' ', (string)$ten));
            $giaTriThuocTinh = trim((string)$giaTri);
            if ($tenThuocTinh === '' || $giaTriThuocTinh === '') {
                continue;
            }

            $thuocTinhBienThe[$tenThuocTinh] = $giaTriThuocTinh;
            if (!isset($thuocTinhUngVien[$tenThuocTinh])) {
                $thuocTinhUngVien[$tenThuocTinh] = true;
            }
        }
    }

    $bienTheTam[] = [
        'id' => (int)($pb['id'] ?? 0),
        'name' => (string)($pb['ten_phien_ban'] ?? ''),
        'price' => (float)($pb['gia_ban'] ?? 0),
        'stock' => (int)($pb['so_luong_ton'] ?? 0),
        'attributes' => $thuocTinhBienThe,
    ];
}

$thuocTinhHienThi = [];
if (!empty($thuocTinhUngVien['Màu sắc'])) {
    $thuocTinhHienThi[] = 'Màu sắc';
}
foreach (array_keys($thuocTinhUngVien) as $tenThuocTinh) {
    if ($tenThuocTinh === 'Màu sắc') {
        continue;
    }
    if (count($thuocTinhHienThi) >= 3) {
        break;
    }
    $thuocTinhHienThi[] = $tenThuocTinh;
}

$productVariantsForJs = [];
$attributeOptions = [];
foreach ($thuocTinhHienThi as $tenThuocTinh) {
    $attributeOptions[$tenThuocTinh] = [];
}

foreach ($bienTheTam as $bienThe) {
    $thuocTinhRutGon = [];
    foreach ($thuocTinhHienThi as $tenThuocTinh) {
        $giaTriThuocTinh = (string)($bienThe['attributes'][$tenThuocTinh] ?? 'Không xác định');
        $thuocTinhRutGon[$tenThuocTinh] = $giaTriThuocTinh;
        $attributeOptions[$tenThuocTinh][$giaTriThuocTinh] = true;
    }

    $productVariantsForJs[] = [
        'id' => $bienThe['id'],
        'name' => $bienThe['name'],
        'price' => $bienThe['price'],
        'stock' => $bienThe['stock'],
        'attributes' => $thuocTinhRutGon,
    ];
}

foreach ($attributeOptions as $tenThuocTinh => $dsGiaTri) {
    $attributeOptions[$tenThuocTinh] = array_keys($dsGiaTri);
    sort($attributeOptions[$tenThuocTinh]);
}
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
        height: 100%;
        /* Giãn chiều cao card lấp đầy cột */
        display: flex;
        /* Sử dụng flexbox */
        flex-direction: column;
        /* Xếp nội dung theo chiều dọc */
        justify-content: center;
        /* Căn giữa nội dung theo chiều dọc */
        align-items: center;
        /* Căn giữa nội dung theo chiều ngang */
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
        content: '\f00c';
        /* Icon dấu tick của FontAwesome */
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

    .variant-attr-group .attr-option-btn {
        border-radius: 999px;
        min-width: 86px;
    }

    .variant-attr-group .attr-option-btn.active {
        background-color: #d70018;
        border-color: #d70018;
        color: #fff;
    }

    .variant-attr-group .attr-option-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
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

    .product-description h2,
    .product-description h3,
    .product-description h4 {
        color: #d70018;
        margin-top: 20px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .compare-similar-card {
        border: 1px solid #f0d7db;
        border-radius: 12px;
        background: linear-gradient(180deg, #fff 0%, #fff8f9 100%);
    }

    .compare-similar-option {
        border: 1px solid #eceff3;
        border-radius: 10px;
        background: #fff;
        padding: 10px;
        height: 100%;
    }

    .inline-compare-table th,
    .inline-compare-table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .inline-compare-table th:first-child,
    .inline-compare-table td:first-child {
        position: sticky;
        left: 0;
        z-index: 1;
        background: #f8f9fa;
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
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
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
                if ($phienBanMacDinh) $giaBan = $phienBanMacDinh['gia_ban'];
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
                    <?php if (!empty($thuocTinhHienThi) && !empty($productVariantsForJs)): ?>
                        <div id="variant-attribute-groups" class="d-flex flex-column gap-3">
                            <?php foreach ($thuocTinhHienThi as $tenThuocTinh): ?>
                                <div class="variant-attr-group" data-attribute-name="<?= htmlspecialchars($tenThuocTinh, ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="small fw-semibold mb-2"><?= htmlspecialchars($tenThuocTinh) ?>:</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach (($attributeOptions[$tenThuocTinh] ?? []) as $giaTriThuocTinh): ?>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary attr-option-btn"
                                                data-attr-name="<?= htmlspecialchars($tenThuocTinh, ENT_QUOTES, 'UTF-8') ?>"
                                                data-attr-value="<?= htmlspecialchars($giaTriThuocTinh, ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($giaTriThuocTinh) ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php foreach ($phienBanList as $idx => $pb): ?>
                                <?php
                                $isOutOfStock = $pb['so_luong_ton'] <= 0;
                                $isActive = ($phienBanMacDinh && (int)$pb['id'] === (int)$phienBanMacDinh['id']) ? 'active' : '';
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
                    <?php endif; ?>

                    <small id="stock-info" class="mt-2 d-block <?= ($phienBanMacDinh['so_luong_ton'] ?? 0) > 0 ? 'text-success' : 'text-danger' ?>">
                        <?= ($phienBanMacDinh['so_luong_ton'] ?? 0) > 0 ? '<i class="fa fa-check-circle me-1"></i>Còn lại: ' . $phienBanMacDinh['so_luong_ton'] . ' sản phẩm' : '<i class="fa fa-times-circle me-1"></i>Đã hết hàng' ?>
                    </small>
                    <small id="variant-selected-name" class="text-muted d-block mt-1">
                        <?= htmlspecialchars((string)($phienBanMacDinh['ten_phien_ban'] ?? '')) ?>
                    </small>
                </div>
            <?php endif; ?>

            <form action="/gio-hang/them" method="POST" class="mb-3">
                <input type="hidden" name="phien_ban_id" id="selected-variant"
                    value="<?= $phienBanMacDinh['id'] ?? 0 ?>">
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
                    <button type="submit" class="btn btn-danger fw-medium flex-grow-1" id="add-to-cart-btn">
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
                                    <p class="small mb-1 text-dark fw-medium" style="display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
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

    <?php if (!empty($goiYSoSanh)): ?>
        <div class="mt-4">
            <div class="compare-similar-card p-3 p-md-4 shadow-sm">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fa fa-balance-scale text-danger me-2"></i>So sánh sản phẩm tương tự
                    </h5>
                    <span class="small text-muted">Chỉ hiển thị sản phẩm cùng danh mục, có thể chọn nhiều sản phẩm để so sánh</span>
                </div>

                <form method="GET" action="/so-sanh" id="compare-similar-form">
                    <div class="row g-2 g-md-3 mb-3">
                        <?php foreach ($goiYSoSanh as $idx => $sp): ?>
                            <?php if ($idx >= 8) break; ?>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="compare-similar-option d-flex align-items-start gap-2">
                                    <input
                                        class="form-check-input mt-1 compare-similar-checkbox"
                                        type="checkbox"
                                        name="slug[]"
                                        value="<?= htmlspecialchars($sp['slug']) ?>">
                                    <div class="flex-grow-1">
                                        <div class="small fw-semibold text-dark mb-1" style="display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                            <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                        </div>
                                        <div class="small text-danger fw-bold">
                                            <?= number_format((float)($sp['gia_hien_thi'] ?? 0), 0, ',', '.') ?>đ
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="compare-extra-selects" class="row g-2 g-md-3 mb-3">
                        <div class="col-12 col-md-6 compare-extra-item">
                            <label class="form-label small fw-semibold mb-1">Chọn thêm sản phẩm khác</label>
                            <select class="form-select form-select-sm compare-extra-select" name="slug[]">
                                <?= $optionsSanPhamKhacHtml ?>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-danger btn-sm mb-3" id="btn-add-compare-select">
                        <i class="fa fa-plus me-1"></i>Thêm ô chọn sản phẩm khác
                    </button>

                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button type="submit" class="btn btn-danger" id="btn-compare-similar">
                            <i class="fa fa-sliders me-1"></i>So sánh sản phẩm đã chọn
                        </button>
                    </div>
                </form>

                <div id="compare-inline-message" class="mt-3"></div>
                <div id="compare-inline-result" class="mt-3 d-none">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="table-responsive" style="max-height: 70vh;">
                            <table class="table table-bordered align-middle mb-0 inline-compare-table" id="compare-inline-table"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    const productVariants = <?= json_encode($productVariantsForJs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const variantAttributeNames = <?= json_encode(array_values($thuocTinhHienThi), JSON_UNESCAPED_UNICODE) ?>;

    let selectedVariantId = document.getElementById('selected-variant')?.value;
    let currentSelection = {};

    function formatCurrency(value) {
        const num = Number(value || 0);
        return num.toLocaleString('vi-VN') + 'đ';
    }

    function isVariantMatchedBySelection(variant, selection) {
        for (const [attrName, attrValue] of Object.entries(selection)) {
            if (variant.attributes?.[attrName] !== attrValue) {
                return false;
            }
        }
        return true;
    }

    function setAddToCartState(stock) {
        const btnCart = document.getElementById('add-to-cart-btn');
        if (!btnCart) {
            return;
        }

        if (stock <= 0) {
            btnCart.disabled = true;
            btnCart.classList.remove('btn-danger');
            btnCart.classList.add('btn-secondary');
            btnCart.innerHTML = '<i class="fa fa-ban me-1"></i>Hết hàng';
            return;
        }

        btnCart.disabled = false;
        btnCart.classList.remove('btn-secondary');
        btnCart.classList.add('btn-danger');
        btnCart.innerHTML = '<i class="fa fa-cart-plus me-1"></i>Thêm vào giỏ hàng';
    }

    function syncVariantUIByData(variant) {
        if (!variant) {
            return;
        }

        selectedVariantId = String(variant.id);

        const selectedVariantInput = document.getElementById('selected-variant');
        if (selectedVariantInput) {
            selectedVariantInput.value = selectedVariantId;
        }

        const priceEl = document.getElementById('current-price');
        if (priceEl) {
            priceEl.textContent = formatCurrency(variant.price);
        }

        const stockInfo = document.getElementById('stock-info');
        if (stockInfo) {
            if (Number(variant.stock) > 0) {
                stockInfo.className = 'mt-2 d-block text-success';
                stockInfo.innerHTML = '<i class="fa fa-check-circle me-1"></i>Còn lại: ' + Number(variant.stock) + ' sản phẩm';
            } else {
                stockInfo.className = 'mt-2 d-block text-danger';
                stockInfo.innerHTML = '<i class="fa fa-times-circle me-1"></i>Đã hết hàng';
            }
        }

        const variantNameEl = document.getElementById('variant-selected-name');
        if (variantNameEl) {
            variantNameEl.textContent = variant.name || '';
        }

        setAddToCartState(Number(variant.stock || 0));
        filterImagesByVariant(selectedVariantId);
    }

    function syncAttributeButtonState() {
        document.querySelectorAll('.attr-option-btn').forEach((btn) => {
            const attrName = btn.dataset.attrName;
            const attrValue = btn.dataset.attrValue;
            const isActive = currentSelection[attrName] === attrValue;
            btn.classList.toggle('active', isActive);
            btn.classList.toggle('btn-outline-danger', isActive);
            btn.classList.toggle('btn-outline-secondary', !isActive);
        });
    }

    function updateUIButtons() {
        const buttons = document.querySelectorAll('.attr-option-btn');
        if (buttons.length === 0) {
            return;
        }

        buttons.forEach((btn) => {
            const attrName = btn.dataset.attrName;
            const attrValue = btn.dataset.attrValue;
            const nextSelection = {
                ...currentSelection,
                [attrName]: attrValue,
            };

            const isSelectable = productVariants.some((variant) => {
                return Number(variant.stock) > 0 && isVariantMatchedBySelection(variant, nextSelection);
            });

            btn.disabled = !isSelectable;
        });

        syncAttributeButtonState();
    }

    function handleOptionSelect(attributeName, attributeValue) {
        currentSelection[attributeName] = attributeValue;

        const validVariants = productVariants.filter((variant) => {
            return Number(variant.stock) > 0 && isVariantMatchedBySelection(variant, currentSelection);
        });

        updateUIButtons(validVariants);

        const matchedBySelection = productVariants.filter((variant) => {
            return isVariantMatchedBySelection(variant, currentSelection);
        });
        const selectedEnough = variantAttributeNames.length > 0 &&
            variantAttributeNames.every((name) => Boolean(currentSelection[name]));

        let finalSKU = null;
        if (selectedEnough && matchedBySelection.length === 1) {
            finalSKU = matchedBySelection[0];
        } else if (matchedBySelection.length === 1) {
            finalSKU = matchedBySelection[0];
        } else if (validVariants.length === 1) {
            finalSKU = validVariants[0];
        } else if (selectedVariantId) {
            finalSKU = matchedBySelection.find((variant) => String(variant.id) === String(selectedVariantId)) || null;
        }

        if (finalSKU) {
            syncVariantUIByData(finalSKU);
        }
    }

    function initAttributeVariantSelector() {
        const attributeButtons = document.querySelectorAll('.attr-option-btn');
        if (attributeButtons.length === 0 || productVariants.length === 0 || variantAttributeNames.length === 0) {
            return false;
        }

        attributeButtons.forEach((btn) => {
            btn.addEventListener('click', function() {
                if (this.disabled) {
                    return;
                }
                handleOptionSelect(this.dataset.attrName, this.dataset.attrValue);
            });
        });

        let initialVariant = productVariants.find((variant) => {
            return String(variant.id) === String(selectedVariantId);
        });

        if (!initialVariant) {
            initialVariant = productVariants.find((variant) => Number(variant.stock) > 0) || productVariants[0];
        }

        if (!initialVariant) {
            return false;
        }

        currentSelection = {
            ...initialVariant.attributes
        };

        updateUIButtons();
        syncVariantUIByData(initialVariant);
        return true;
    }

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

    const hasAttributeSelector = initAttributeVariantSelector();

    // Lọc ảnh ngay khi vừa tải trang
    if (!hasAttributeSelector && selectedVariantId) {
        filterImagesByVariant(selectedVariantId);
    }

    // Sự kiện khi Click chọn phiên bản
    if (!hasAttributeSelector) {
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

                setAddToCartState(stock);
                filterImagesByVariant(selectedVariantId);
            });
        });
    }

    // Yêu thích
    document.querySelector('.btn-wishlist')?.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('/yeu-thich/them', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
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
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
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

    // So sánh sản phẩm ngay trên trang chi tiết
    const compareSimilarForm = document.getElementById('compare-similar-form');
    if (compareSimilarForm) {
        const compareCheckboxes = Array.from(compareSimilarForm.querySelectorAll('.compare-similar-checkbox'));
        const compareBtn = document.getElementById('btn-compare-similar');
        const compareMsg = document.getElementById('compare-inline-message');
        const compareResultWrap = document.getElementById('compare-inline-result');
        const compareTable = document.getElementById('compare-inline-table');
        const addCompareSelectBtn = document.getElementById('btn-add-compare-select');
        const compareExtraSelectsWrap = document.getElementById('compare-extra-selects');
        const extraSelectOptions = <?= json_encode($optionsSanPhamKhacHtml, JSON_UNESCAPED_UNICODE) ?>;

        const getCompareExtraSelects = () => {
            return Array.from(compareSimilarForm.querySelectorAll('.compare-extra-select'));
        };

        addCompareSelectBtn?.addEventListener('click', () => {
            if (!compareExtraSelectsWrap) return;

            const wrapper = document.createElement('div');
            wrapper.className = 'col-12 col-md-6 compare-extra-item';

            wrapper.innerHTML = `
                <label class="form-label small fw-semibold mb-1">Chọn thêm sản phẩm khác</label>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm compare-extra-select" name="slug[]">
                        ${extraSelectOptions}
                    </select>
                    <button type="button" class="btn btn-outline-secondary btn-sm compare-remove-select">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            `;

            compareExtraSelectsWrap.appendChild(wrapper);

            wrapper.querySelector('.compare-remove-select')?.addEventListener('click', () => {
                wrapper.remove();
            });
        });

        const getSelectedSlugs = () => {
            const slugSet = new Set();

            compareCheckboxes.forEach((cb) => {
                if (cb.checked && cb.value) {
                    slugSet.add(cb.value);
                }
            });

            getCompareExtraSelects().forEach((sel) => {
                if (sel.value) {
                    slugSet.add(sel.value);
                }
            });

            return Array.from(slugSet);
        };

        const escapeHtml = (value) => {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        };

        const formatCurrency = (value) => {
            const num = Number(value || 0);
            if (num <= 0) return 'Liên hệ';
            return num.toLocaleString('vi-VN') + 'đ';
        };

        const renderCompareTable = (payload) => {
            const products = payload.products || [];
            const specNames = payload.specNames || [];

            const headCells = products.map((p) => `<th>${escapeHtml(p.ten_san_pham)}</th>`).join('');

            const rows = [];
            rows.push(`
                <tr>
                    <th class="bg-light">Giá hiển thị</th>
                    ${products.map((p) => `<td class="text-danger fw-semibold">${formatCurrency(p.gia_hien_thi)}</td>`).join('')}
                </tr>
            `);
            rows.push(`
                <tr>
                    <th class="bg-light">Hãng sản xuất</th>
                    ${products.map((p) => `<td>${escapeHtml(p.hang_san_xuat || '-')}</td>`).join('')}
                </tr>
            `);
            rows.push(`
                <tr>
                    <th class="bg-light">Danh mục</th>
                    ${products.map((p) => `<td>${escapeHtml(p.ten_danh_muc || '-')}</td>`).join('')}
                </tr>
            `);
            rows.push(`
                <tr>
                    <th class="bg-light">Tổng tồn kho</th>
                    ${products.map((p) => {
                        const ton = Number(p.tong_ton_kho || 0);
                        return `<td class="${ton > 0 ? 'text-success' : 'text-danger'}">${ton > 0 ? `${ton} sản phẩm` : 'Hết hàng'}</td>`;
                    }).join('')}
                </tr>
            `);

            specNames.forEach((name) => {
                rows.push(`
                    <tr>
                        <th class="bg-light">${escapeHtml(name)}</th>
                        ${products.map((p) => `<td>${escapeHtml((p.thong_so && p.thong_so[name]) || '-')}</td>`).join('')}
                    </tr>
                `);
            });

            compareTable.innerHTML = `
                <thead class="table-light">
                    <tr>
                        <th style="width:220px;">Tiêu chí</th>
                        ${headCells}
                    </tr>
                </thead>
                <tbody>
                    ${rows.join('')}
                </tbody>
            `;
        };

        compareCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                // Cho phép chọn nhiều sản phẩm, không giới hạn số lượng
            });
        });

        compareSimilarForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const selectedSlugs = getSelectedSlugs();
            if (selectedSlugs.length < 2) {
                alert('Vui lòng chọn ít nhất 2 sản phẩm để bắt đầu so sánh.');
                return;
            }

            const queryParams = new URLSearchParams();
            selectedSlugs.forEach((slug) => queryParams.append('slug[]', slug));
            const query = queryParams.toString();

            compareMsg.innerHTML = '<div class="alert alert-info py-2 mb-0 small">Đang tạo bảng so sánh...</div>';
            compareBtn.disabled = true;

            try {
                const response = await fetch('/api/so-sanh-san-pham?' + query);
                const data = await response.json();

                if (!data.success || !data.data || !Array.isArray(data.data.products)) {
                    compareMsg.innerHTML = '<div class="alert alert-warning py-2 mb-0 small">' + escapeHtml(data.message || 'Không thể tải dữ liệu so sánh.') + '</div>';
                    compareResultWrap.classList.add('d-none');
                    return;
                }

                renderCompareTable(data.data);
                compareMsg.innerHTML = '<div class="alert alert-success py-2 mb-0 small">Đã tạo bảng so sánh thành công.</div>';
                compareResultWrap.classList.remove('d-none');
                compareResultWrap.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } catch (error) {
                compareMsg.innerHTML = '<div class="alert alert-danger py-2 mb-0 small">Có lỗi xảy ra khi tải dữ liệu so sánh.</div>';
                compareResultWrap.classList.add('d-none');
            } finally {
                compareBtn.disabled = false;
            }
        });
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>