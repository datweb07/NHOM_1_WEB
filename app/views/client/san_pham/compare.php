<?php
$pageTitle = 'So sanh san pham - FPT Shop';
ob_start();

$selectedSlugsForForm = $selectedSlugs ?? [];
for ($i = 0; $i < 4; $i++) {
    if (!isset($selectedSlugsForForm[$i])) {
        $selectedSlugsForForm[$i] = '';
    }
}

$selectedSlugSet = array_values(array_filter($selectedSlugsForForm, static fn($slug) => $slug !== ''));

$danhMucDangSoSanh = [];
$hangDangSoSanh = [];
foreach (($sanPhamSoSanh ?? []) as $spDangSoSanh) {
    $dmId = isset($spDangSoSanh['danh_muc_id']) ? (int)$spDangSoSanh['danh_muc_id'] : 0;
    if ($dmId > 0) {
        $danhMucDangSoSanh[$dmId] = true;
    }

    $hang = trim((string)($spDangSoSanh['hang_san_xuat'] ?? ''));
    if ($hang !== '') {
        $hangDangSoSanh[mb_strtolower($hang)] = true;
    }
}

$ungVienTuongTu = [];
foreach (($danhSachSanPham ?? []) as $sp) {
    $slug = $sp['slug'] ?? '';
    if ($slug === '' || in_array($slug, $selectedSlugSet, true)) {
        continue;
    }

    $diemTuongTu = 0;
    $dmIdSp = isset($sp['danh_muc_id']) ? (int)$sp['danh_muc_id'] : 0;
    if ($dmIdSp > 0 && isset($danhMucDangSoSanh[$dmIdSp])) {
        $diemTuongTu += 3;
    }

    $hangSp = trim((string)($sp['hang_san_xuat'] ?? ''));
    if ($hangSp !== '' && isset($hangDangSoSanh[mb_strtolower($hangSp)])) {
        $diemTuongTu += 2;
    }

    if (!empty($sp['ten_danh_muc']) && !empty($sanPhamSoSanh)) {
        foreach ($sanPhamSoSanh as $spDangSoSanh) {
            if (($spDangSoSanh['ten_danh_muc'] ?? '') === ($sp['ten_danh_muc'] ?? '')) {
                $diemTuongTu += 1;
                break;
            }
        }
    }

    $sp['_diem_tuong_tu'] = $diemTuongTu;
    $ungVienTuongTu[] = $sp;
}

usort($ungVienTuongTu, static function (array $a, array $b): int {
    return (int)($b['_diem_tuong_tu'] ?? 0) <=> (int)($a['_diem_tuong_tu'] ?? 0);
});

$sanPhamTuongTuGoiY = [];
foreach ($ungVienTuongTu as $sp) {
    if (($sp['_diem_tuong_tu'] ?? 0) <= 0) {
        continue;
    }
    $sanPhamTuongTuGoiY[] = $sp;
    if (count($sanPhamTuongTuGoiY) >= 8) {
        break;
    }
}

if (empty($sanPhamTuongTuGoiY)) {
    $sanPhamTuongTuGoiY = array_slice($ungVienTuongTu, 0, 8);
}
?>

<style>
    .compare-picker-card {
        border-radius: 12px;
    }

    .compare-slot {
        border: 1px solid #eceff3;
        border-radius: 10px;
        padding: 10px;
        background: #fafbfc;
    }

    .compare-product-card {
        border-radius: 12px;
    }

    .compare-table-wrap .table th,
    .compare-table-wrap .table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .compare-table-wrap .table th:first-child,
    .compare-table-wrap .table td:first-child {
        position: sticky;
        left: 0;
        z-index: 1;
        background: #f8f9fa;
    }

    .compare-table-wrap .table thead th:first-child {
        z-index: 2;
        background: #f1f3f5;
    }

    @media (max-width: 767.98px) {
        .compare-actions {
            flex-direction: column;
        }

        .compare-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="container-xl py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <div>
            <h1 class="h4 fw-bold mb-1">So sánh sản phẩm</h1>
            <p class="text-muted mb-0">Chọn tối đa 4 sản phẩm để xem khác biệt về giá, tồn kho và thông số.</p>
        </div>
    </div>

    <div class="card compare-picker-card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="/so-sanh" class="row g-3 align-items-end">
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="compare-slot">
                            <label class="form-label small fw-semibold mb-1">Sản phẩm <?= $i + 1 ?></label>
                            <select class="form-select form-select-sm" name="slug[]">
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach ($danhSachSanPham as $sp): ?>
                                    <option value="<?= htmlspecialchars($sp['slug']) ?>"
                                        <?= $selectedSlugsForForm[$i] === $sp['slug'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endfor; ?>

                <div class="col-12 d-flex gap-2 mt-1 compare-actions">
                    <button type="submit" class="btn btn-danger btn-sm px-3">
                        <i class="fa fa-sliders me-1"></i>So sánh ngay
                    </button>
                    <a href="/so-sanh" class="btn btn-outline-secondary btn-sm px-3">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($sanPhamSoSanh)): ?>
        <div class="alert alert-info border-0 shadow-sm">
            Chưa có dữ liệu so sánh. Vui lòng chọn ít nhất 2 sản phẩm.
        </div>
    <?php elseif (count($sanPhamSoSanh) < 2): ?>
        <div class="alert alert-warning border-0 shadow-sm">
            Cần ít nhất 2 sản phẩm để bắt đầu so sánh.
        </div>
    <?php else: ?>
        <div class="row g-3 mb-4">
            <?php foreach ($sanPhamSoSanh as $sp): ?>
                <?php
                $giaHienThi = $sp['phien_ban_mac_dinh']['gia_ban'] ?? $sp['gia_hien_thi'] ?? 0;
                $tonKho = (int)($sp['tong_ton_kho'] ?? 0);
                ?>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card compare-product-card border-0 shadow-sm h-100">
                        <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                            class="card-img-top p-3"
                            style="height: 180px; object-fit: contain;"
                            alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>">
                        <div class="card-body pt-0">
                            <h2 class="h6 fw-semibold" style="min-height: 44px;"><?= htmlspecialchars($sp['ten_san_pham']) ?></h2>
                            <div class="text-danger fw-bold mb-1"><?= number_format((float)$giaHienThi, 0, ',', '.') ?>đ</div>
                            <div class="small text-muted mb-1">Hãng: <?= htmlspecialchars($sp['hang_san_xuat'] ?? '-') ?></div>
                            <div class="small <?= $tonKho > 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $tonKho > 0 ? 'Còn hàng: ' . $tonKho : 'Tạm hết hàng' ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="btn btn-outline-danger btn-sm w-100">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden compare-table-wrap">
            <div class="table-responsive" style="max-height: 70vh;">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 220px;">Tiêu chí</th>
                            <?php foreach ($sanPhamSoSanh as $sp): ?>
                                <th><?= htmlspecialchars($sp['ten_san_pham']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="bg-light">Giá hiển thị</th>
                            <?php foreach ($sanPhamSoSanh as $sp): ?>
                                <?php $gia = $sp['phien_ban_mac_dinh']['gia_ban'] ?? $sp['gia_hien_thi'] ?? 0; ?>
                                <td class="text-danger fw-semibold"><?= number_format((float)$gia, 0, ',', '.') ?>đ</td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="bg-light"> Hãng sản xuất</th>
                            <?php foreach ($sanPhamSoSanh as $sp): ?>
                                <td><?= htmlspecialchars($sp['hang_san_xuat'] ?? '-') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="bg-light">Danh mục</th>
                            <?php foreach ($sanPhamSoSanh as $sp): ?>
                                <td><?= htmlspecialchars($sp['ten_danh_muc'] ?? '-') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="bg-light">Màu sắc phiên bản rẻ nhất</th>
                            <?php foreach ($sanPhamSoSanh as $sp): ?>
                                <td><?= htmlspecialchars($sp['phien_ban_mac_dinh']['mau_sac'] ?? '-') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="bg-light">Dung lượng</th>
                            <?php foreach ($sanPhamSoSanh as $sp): ?>
                                <td><?= htmlspecialchars($sp['phien_ban_mac_dinh']['dung_luong'] ?? '-') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="bg-light">RAM</th>
                            <?php foreach ($sanPhamSoSanh as $sp): ?>
                                <td><?= htmlspecialchars($sp['phien_ban_mac_dinh']['ram'] ?? '-') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="bg-light">Tổng tồn kho</th>
                            <?php foreach ($sanPhamSoSanh as $sp): ?>
                                <?php $ton = (int)($sp['tong_ton_kho'] ?? 0); ?>
                                <td class="<?= $ton > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $ton > 0 ? $ton . ' sản phẩm' : 'hết hàng' ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <?php foreach ($tenThongSo as $tenTs): ?>
                            <tr>
                                <th class="bg-light"><?= htmlspecialchars($tenTs) ?></th>
                                <?php foreach ($sanPhamSoSanh as $sp): ?>
                                    <?php $spId = (int)$sp['id']; ?>
                                    <td><?= htmlspecialchars($thongSoTheoSanPham[$spId][$tenTs] ?? '-') ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (!empty($sanPhamTuongTuGoiY)): ?>
            <div class="mt-4">
                <h5 class="fw-bold mb-3 border-start border-danger border-3 ps-2">Những sản phẩm tương tự</h5>
                <div class="row g-3">
                    <?php foreach ($sanPhamTuongTuGoiY as $sp): ?>
                        <?php $giaGoiY = $sp['gia_hien_thi'] ?? $sp['gia_ban'] ?? 0; ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="/san-pham/<?= htmlspecialchars($sp['slug'] ?? '') ?>" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="p-2 overflow-hidden">
                                        <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                                            class="card-img-top"
                                            style="height: 150px; object-fit: contain;"
                                            alt="<?= htmlspecialchars($sp['ten_san_pham'] ?? 'San pham') ?>">
                                    </div>
                                    <div class="card-body pt-0 px-3 pb-3">
                                        <p class="small mb-1 text-dark fw-medium"
                                            style="display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 36px;">
                                            <?= htmlspecialchars($sp['ten_san_pham'] ?? '-') ?>
                                        </p>
                                        <p class="text-muted mb-1" style="font-size: 12px;">
                                            <?= htmlspecialchars($sp['ten_danh_muc'] ?? $sp['hang_san_xuat'] ?? 'Sản phẩm liên quan') ?>
                                        </p>
                                        <p class="text-danger fw-bold mb-0 small">
                                            <?= number_format((float)$giaGoiY, 0, ',', '.') ?>đ
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>