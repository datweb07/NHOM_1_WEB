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

            <div class="col-lg-7">

                <?php if ($isLoggedIn && !empty($diaChiList)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fa fa-map-marker-alt text-danger me-2"></i>Địa chỉ giao hàng</h6>
                            <?php foreach ($diaChiList as $dc): ?>
                                <?php

                                $idDiaChi = $dc['id'];
                                $tenNguoiNhan = htmlspecialchars($dc['ten_nguoi_nhan'] ?? $dc['ho_ten'] ?? 'Chưa cập nhật');
                                $sdtNhan = htmlspecialchars($dc['sdt_nhan'] ?? $dc['sdt'] ?? 'Chưa cập nhật');
                                

                                $diaChiCuThe = $dc['so_nha_duong'] ?? $dc['dia_chi_chi_tiet'] ?? $dc['dia_chi_cu_the'] ?? '';
                                $phuongXa = $dc['phuong_xa'] ?? $dc['xa_phuong'] ?? '';
                                $quanHuyen = $dc['quan_huyen'] ?? '';
                                $tinhThanh = $dc['tinh_thanh'] ?? '';
                                
                                $fullAddress = htmlspecialchars(implode(', ', array_filter([$diaChiCuThe, $phuongXa, $quanHuyen, $tinhThanh])));
                                

                                $isMacDinh = ($dc['mac_dinh'] ?? $dc['la_mac_dinh'] ?? 0) == 1;
                                $isChecked = ($diaChiMacDinh && $idDiaChi == $diaChiMacDinh['id']) ? 'checked' : '';
                                ?>
                                <div class="form-check mb-2 border rounded p-3 position-relative <?= $isChecked ? 'bg-light' : '' ?>">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="dia_chi_id"
                                           id="dc_<?= $idDiaChi ?>" value="<?= $idDiaChi ?>"
                                           <?= $isChecked ?> required style="cursor: pointer; margin-top: 5px;">
                                    <label class="form-check-label w-100" for="dc_<?= $idDiaChi ?>" style="cursor: pointer; padding-left: 10px;">
                                        <span class="fw-medium text-dark"><?= $tenNguoiNhan ?></span>
                                        <span class="text-muted"> | <?= $sdtNhan ?></span><br>
                                        <small class="text-muted d-block mt-1"><?= $fullAddress ?></small>
                                        <?php if ($isMacDinh): ?>
                                            <span class="badge bg-danger position-absolute top-0 end-0 m-2" style="font-size:0.65rem;">Mặc định</span>
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

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fa fa-wallet text-danger me-2"></i>Phương thức thanh toán</h6>
                        

                        <?php if (!empty($gatewayWarnings)): ?>
                            <div class="alert alert-warning mb-3" role="alert">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                <strong>Thông báo:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($gatewayWarnings as $warning): ?>
                                        <li><?= htmlspecialchars($warning['message']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <small class="d-block mt-2">
                                    <i class="fa fa-info-circle"></i> Chúng tôi khuyến nghị sử dụng phương thức thanh toán COD hoặc chọn cổng thanh toán khác.
                                </small>
                            </div>
                        <?php endif; ?>
                        

                        <div class="form-check mb-3 border rounded p-3 payment-method-option" data-method="COD">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="tt_cod" value="COD" checked>
                            <label class="form-check-label w-100" for="tt_cod" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa fa-money-bill-wave text-success fs-4"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">Thanh toán khi nhận hàng (COD)</div>
                                        <small class="text-muted">Thanh toán bằng tiền mặt khi nhận hàng</small>
                                        <?php if (!empty($gatewayWarnings)): ?>
                                            <span class="badge bg-success ms-2">Khuyến nghị</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </label>
                        </div>


                        <?php if (isset($vnpayEnabled) && $vnpayEnabled): ?>
                        <div class="form-check mb-3 border rounded p-3 payment-method-option <?= isset($gatewayWarnings['vnpay']) ? 'border-warning' : '' ?>" data-method="CHUYEN_KHOAN">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="tt_vnpay" value="CHUYEN_KHOAN" <?= isset($gatewayWarnings['vnpay']) ? 'disabled' : '' ?>>
                            <label class="form-check-label w-100" for="tt_vnpay" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa fa-university text-primary fs-4"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">Thanh toán qua VNPay</div>
                                        <small class="text-muted">Thanh toán qua cổng VNPay (ATM, Visa, MasterCard)</small>
                                        <?php if (isset($gatewayWarnings['vnpay'])): ?>
                                            <span class="badge bg-warning text-dark ms-2">
                                                <i class="fa fa-exclamation-triangle"></i> Đang gặp sự cố
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <?php endif; ?>


                        <?php if (isset($momoEnabled) && $momoEnabled): ?>
                        <div class="form-check mb-3 border rounded p-3 payment-method-option <?= isset($gatewayWarnings['momo']) ? 'border-warning' : '' ?>" data-method="VI_DIEN_TU">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="tt_momo" value="VI_DIEN_TU" <?= isset($gatewayWarnings['momo']) ? 'disabled' : '' ?>>
                            <label class="form-check-label w-100" for="tt_momo" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa fa-mobile-alt text-danger fs-4"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">Thanh toán qua ví Momo</div>
                                        <small class="text-muted">Thanh toán qua ví điện tử Momo</small>
                                        <?php if (isset($gatewayWarnings['momo'])): ?>
                                            <span class="badge bg-warning text-dark ms-2">
                                                <i class="fa fa-exclamation-triangle"></i> Đang gặp sự cố
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($zalopayEnabled) && $zalopayEnabled): ?>
                        <div class="form-check mb-3 border rounded p-3 payment-method-option <?= isset($gatewayWarnings['zalopay']) ? 'border-warning' : '' ?>" data-method="ZALOPAY">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="tt_zalopay" value="ZALOPAY" <?= isset($gatewayWarnings['zalopay']) ? 'disabled' : '' ?>>
                            <label class="form-check-label w-100" for="tt_zalopay" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa fa-wallet text-info fs-4"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">Thanh toán qua ZaloPay</div>
                                        <small class="text-muted">Thanh toán tiện lợi qua ứng dụng Zalo hoặc ZaloPay</small>
                                        <?php if (isset($gatewayWarnings['zalopay'])): ?>
                                            <span class="badge bg-warning text-dark ms-2">
                                                <i class="fa fa-exclamation-triangle"></i> Đang gặp sự cố
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <?php endif; ?>

                        <?php if ((!isset($vnpayEnabled) || !$vnpayEnabled) && (!isset($momoEnabled) || !$momoEnabled) && (!isset($zalopayEnabled) || !$zalopayEnabled)): ?>
                        <div class="alert alert-info small mb-0 mt-2">
                            <i class="fa fa-info-circle me-1"></i>
                            Hiện tại chỉ hỗ trợ thanh toán COD. Các phương thức thanh toán online đang được cập nhật.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <style>
                .payment-method-option {
                    transition: all 0.2s ease;
                    cursor: pointer;
                }
                .payment-method-option:hover {
                    background-color: #f8f9fa;
                    border-color: #dc3545 !important;
                }
                .payment-method-option:has(input:checked) {
                    background-color: #fff5f5;
                    border-color: #dc3545 !important;
                }
                </style>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fa fa-pen text-danger me-2"></i>Ghi chú đơn hàng</h6>
                        <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Ghi chú về đơn hàng (không bắt buộc)..."></textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Đơn hàng (<?= count($chiTietGioList) ?> sản phẩm)</h6>
                        <?php foreach ($chiTietGioList as $item): ?>
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <img src="<?= htmlspecialchars($item['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
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

document.querySelectorAll('input[name="dia_chi_id"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('input[name="dia_chi_id"]').forEach(r => {
            r.closest('.form-check').classList.remove('bg-light');
        });
        if(this.checked) {
            this.closest('.form-check').classList.add('bg-light');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
