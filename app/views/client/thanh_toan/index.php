<?php
$pageTitle = 'Thanh toán - FPT Shop';
ob_start();
$isLoggedIn = \App\Core\Session::isLoggedIn();
?>

<div class="container-xl py-4">
    <h1 class="h4 mb-4 fw-bold"><i class="fa fa-credit-card text-danger me-2"></i>Thanh toán</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="/thanh-toan/dat-hang" method="POST" id="order-form">
        <div class="row g-4">

            <!-- Thông tin giao hàng -->
            <div class="col-lg-7">

                <?php if ($isLoggedIn && !empty($diaChiList)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fa fa-map-marker-alt text-danger me-2"></i>Địa chỉ giao hàng</h6>
                            <?php foreach ($diaChiList as $dc): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="dia_chi_id"
                                           id="dc_<?= $dc['id'] ?>" value="<?= $dc['id'] ?>"
                                           <?= ($diaChiMacDinh && $dc['id'] == $diaChiMacDinh['id']) ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="dc_<?= $dc['id'] ?>">
                                        <span class="fw-medium"><?= htmlspecialchars($dc['ten_nguoi_nhan']) ?></span>
                                        <span class="text-muted"> | <?= htmlspecialchars($dc['sdt']) ?></span><br>
                                        <small class="text-muted"><?= htmlspecialchars($dc['dia_chi_chi_tiet'] . ', ' . $dc['xa_phuong'] . ', ' . $dc['quan_huyen'] . ', ' . $dc['tinh_thanh']) ?></small>
                                        <?php if ($dc['la_mac_dinh']): ?>
                                            <span class="badge bg-danger ms-1" style="font-size:0.65rem;">Mặc định</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fa fa-map-marker-alt text-danger me-2"></i>Thông tin nhận hàng</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ten_nguoi_nhan" class="form-control" placeholder="Nhập họ và tên" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="sdt_nhan" class="form-control" placeholder="Nhập số điện thoại" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                <input type="text" name="dia_chi" class="form-control" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành" required>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Phương thức thanh toán -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fa fa-wallet text-danger me-2"></i>Phương thức thanh toán</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="tt_cod" value="COD" checked>
                            <label class="form-check-label d-flex align-items-center gap-2" for="tt_cod">
                                <i class="fa fa-money-bill-wave text-success"></i>
                                <span>Thanh toán khi nhận hàng (COD)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="tt_bank" value="CHUYEN_KHOAN">
                            <label class="form-check-label d-flex align-items-center gap-2" for="tt_bank">
                                <i class="fa fa-university text-primary"></i>
                                <span>Chuyển khoản ngân hàng</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="tt_momo" value="MOMO">
                            <label class="form-check-label d-flex align-items-center gap-2" for="tt_momo">
                                <i class="fa fa-mobile text-danger"></i>
                                <span>Ví MoMo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Ghi chú -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fa fa-pen text-danger me-2"></i>Ghi chú đơn hàng</h6>
                        <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Ghi chú về đơn hàng (không bắt buộc)..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Đơn hàng (<?= count($chiTietGioList) ?> sản phẩm)</h6>
                        <?php foreach ($chiTietGioList as $item): ?>
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <img src="<?= htmlspecialchars($item['anh_chinh'] ?? BASE_URL . '/assets/client/images/products/14.png') ?>"
                                     alt="" style="width:50px;height:50px;object-fit:contain;border:1px solid #eee;border-radius:4px;">
                                <div class="flex-grow-1">
                                    <div class="small fw-medium"><?= htmlspecialchars($item['ten_san_pham'] ?? '') ?></div>
                                    <?php if (!empty($item['ten_phien_ban'])): ?>
                                        <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($item['ten_phien_ban']) ?></div>
                                    <?php endif; ?>
                                    <div class="text-muted small">x<?= $item['so_luong'] ?></div>
                                </div>
                                <div class="text-danger fw-bold small"><?= number_format($item['gia_ban'] * $item['so_luong'], 0, ',', '.') ?>đ</div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Mã giảm giá -->
                        <div class="mt-3 border-top pt-3">
                            <label class="form-label small fw-medium">Mã giảm giá</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="ma-giam-gia-input" name="ma_giam_gia" class="form-control" placeholder="Nhập mã giảm giá">
                                <button type="button" class="btn btn-outline-danger" id="btn-apply-coupon">Áp dụng</button>
                            </div>
                            <div id="coupon-msg" class="mt-1 small"></div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">Tổng tiền hàng</span>
                            <span><?= number_format($tongTien, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">Phí vận chuyển</span>
                            <span><?= number_format($phiVanChuyen, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2 d-none" id="discount-row">
                            <span class="text-muted">Giảm giá</span>
                            <span class="text-danger" id="discount-amount">0đ</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold mb-4">
                            <span>Tổng thanh toán</span>
                            <span class="text-danger fs-5" id="total-final"><?= number_format($tongTien + $phiVanChuyen, 0, ',', '.') ?>đ</span>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 fw-medium">
                            <i class="fa fa-check-circle me-2"></i>Đặt hàng ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const tongTien = <?= $tongTien ?>;
const phiVanChuyen = <?= $phiVanChuyen ?>;
let tienGiam = 0;

document.getElementById('btn-apply-coupon')?.addEventListener('click', function() {
    const ma = document.getElementById('ma-giam-gia-input').value.trim();
    if (!ma) return;

    fetch('/thanh-toan/kiem-tra-ma-giam-gia', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'ma_code=' + encodeURIComponent(ma) + '&tong_tien=' + tongTien
    })
    .then(r => r.json())
    .then(data => {
        const msg = document.getElementById('coupon-msg');
        if (data.success) {
            tienGiam = data.tien_giam;
            msg.innerHTML = '<span class="text-success"><i class="fa fa-check"></i> ' + data.message + '</span>';
            document.getElementById('discount-row').classList.remove('d-none');
            document.getElementById('discount-amount').textContent = '-' + tienGiam.toLocaleString('vi-VN') + 'đ';
            const total = tongTien + phiVanChuyen - tienGiam;
            document.getElementById('total-final').textContent = total.toLocaleString('vi-VN') + 'đ';
        } else {
            tienGiam = 0;
            msg.innerHTML = '<span class="text-danger"><i class="fa fa-times"></i> ' + data.message + '</span>';
            document.getElementById('discount-row').classList.add('d-none');
            document.getElementById('total-final').textContent = (tongTien + phiVanChuyen).toLocaleString('vi-VN') + 'đ';
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
