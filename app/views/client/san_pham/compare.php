<?php
$pageTitle = 'So sanh san pham - FPT Shop';
ob_start();

$selectedSlugsForForm = $selectedSlugs ?? [];
for ($i = 0; $i < 4; $i++) {
    if (!isset($selectedSlugsForForm[$i])) {
        $selectedSlugsForForm[$i] = '';
    }
}
?>

<div class="container-xl py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <div>
            <h1 class="h4 fw-bold mb-1">So sánh sản phẩm</h1>
            <p class="text-muted mb-0">Chọn tối đa 4 sản phẩm để xem khác biệt về giá, tồn kho và thông số.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="/so-sanh" class="row g-2 align-items-end">
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="col-12 col-md-6 col-lg-3">
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
                <?php endfor; ?>

                <div class="col-12 d-flex gap-2 mt-2">
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
            ần ít nhất 2 sản phẩm để bắt đầu so sánh.
        </div>
    <?php else: ?>
        <div class="row g-3 mb-4">
            <?php foreach ($sanPhamSoSanh as $sp): ?>
                <?php
                $giaHienThi = $sp['phien_ban_mac_dinh']['gia_ban'] ?? $sp['gia_hien_thi'] ?? 0;
                $tonKho = (int)($sp['tong_ton_kho'] ?? 0);
                ?>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                            class="card-img-top p-3"
                            style="height: 180px; object-fit: contain;"
                            alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>">
                        <div class="card-body pt-0">
                            <h2 class="h6 fw-semibold" style="min-height: 44px;"><?= htmlspecialchars($sp['ten_san_pham']) ?></h2>
                            <div class="text-danger fw-bold mb-1"><?= number_format((float)$giaHienThi, 0, ',', '.') ?>d</div>
                            <div class="small text-muted mb-1">Hang: <?= htmlspecialchars($sp['hang_san_xuat'] ?? '-') ?></div>
                            <div class="small <?= $tonKho > 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $tonKho > 0 ? 'Con hang: ' . $tonKho : 'Tam het hang' ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="btn btn-outline-danger btn-sm w-100">
                                Xem chi tiet
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
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
                                <td class="text-danger fw-semibold"><?= number_format((float)$gia, 0, ',', '.') ?>d</td>
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
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>