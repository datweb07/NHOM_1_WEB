<?php
$pageTitle = 'Thanh toán - FPT Shop';
ob_start();
$isLoggedIn = \App\Core\Session::isLoggedIn();
$hasSavedAddresses = !empty($diaChiList);
?>

<div class="container-xl py-4">
    <style>
        #xac_nhan_don:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .payment-method-option {
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .payment-method-option.is-selected {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.15);
        }
    </style>

    <h1 class="h4 mb-4 fw-bold"><i class="fa fa-credit-card text-danger me-2"></i>Thanh toán</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="/thanh-toan/dat-hang" method="POST" id="order-form">
        <div class="row g-4">
            <div class="col-lg-7">
                <?php if ($isLoggedIn && $hasSavedAddresses): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="fa fa-map-marker-alt text-danger me-2"></i>Địa chỉ giao hàng</h6>
                                    <div class="small text-muted">Chọn nhanh từ sổ địa chỉ hoặc giao tới địa chỉ khác.</div>
                                </div>

                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-danger active" id="btn-use-saved-address">Sổ địa chỉ</button>
                                    <button type="button" class="btn btn-outline-danger" id="btn-use-new-address">Địa chỉ khác</button>
                                </div>
                            </div>

                            <div id="saved-address-block">
                                <?php foreach ($diaChiList as $dc): ?>
                                    <?php
                                    $idDiaChi = (int)($dc['id'] ?? 0);
                                    $tenNguoiNhan = htmlspecialchars($dc['ten_nguoi_nhan'] ?? $dc['ho_ten'] ?? 'Chưa cập nhật');
                                    $sdtNhan = htmlspecialchars($dc['sdt_nhan'] ?? $dc['sdt'] ?? 'Chưa cập nhật');
                                    $diaChiCuThe = $dc['so_nha_duong'] ?? '';
                                    $phuongXa = $dc['phuong_xa'] ?? '';
                                    $quanHuyen = $dc['quan_huyen'] ?? '';
                                    $tinhThanh = $dc['tinh_thanh'] ?? '';
                                    $fullAddressRaw = implode(', ', array_filter([$diaChiCuThe, $phuongXa, $quanHuyen, $tinhThanh]));
                                    $fullAddress = htmlspecialchars($fullAddressRaw);
                                    $isMacDinh = (int)($dc['mac_dinh'] ?? 0) === 1;
                                    $isChecked = (!empty($diaChiMacDinh) && $idDiaChi === (int)$diaChiMacDinh['id']) ? 'checked' : '';
                                    ?>
                                    <div class="form-check mb-2 border rounded p-3 position-relative <?= $isChecked ? 'bg-light' : '' ?>">
                                        <input
                                            class="form-check-input ms-0 me-2 saved-address-radio"
                                            type="radio"
                                            name="dia_chi_id"
                                            id="dc_<?= $idDiaChi ?>"
                                            value="<?= $idDiaChi ?>"
                                            data-recipient="<?= htmlspecialchars($dc['ten_nguoi_nhan'] ?? $dc['ho_ten'] ?? '') ?>"
                                            data-phone="<?= htmlspecialchars($dc['sdt_nhan'] ?? $dc['sdt'] ?? '') ?>"
                                            data-address="<?= htmlspecialchars($fullAddressRaw) ?>"
                                            <?= $isChecked ?>
                                            style="cursor: pointer; margin-top: 5px;">
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

                            <div id="new-address-block" class="d-none mt-3">
                                <input type="hidden" name="su_dung_dia_chi_khac" id="suDungDiaChiKhac" value="0">
                                <div class="alert alert-info small">
                                    <i class="fa fa-info-circle me-1"></i>
                                    Bạn đang giao tới địa chỉ khác. Vui lòng nhập thông tin nhận hàng bên dưới.
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium">Họ và tên <span class="text-danger">*</span></label>
                                        <input type="text" name="ten_nguoi_nhan" class="form-control" placeholder="Nhập họ và tên" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" name="sdt_nhan" class="form-control" placeholder="Nhập số điện thoại" disabled>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-medium">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                        <select class="form-select" id="tinhThanh" name="tinh_thanh" disabled>
                                            <option value="" selected disabled>Chọn Tỉnh/Thành</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-medium">Quận/Huyện <span class="text-danger">*</span></label>
                                        <select class="form-select" id="quanHuyen" name="quan_huyen" disabled>
                                            <option value="" selected disabled>Chọn Quận/Huyện</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-medium">Phường/Xã <span class="text-danger">*</span></label>
                                        <select class="form-select" id="phuongXa" name="phuong_xa" disabled>
                                            <option value="" selected disabled>Chọn Phường/Xã</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-medium">Số nhà, tên đường <span class="text-danger">*</span></label>
                                    <input type="text" name="dia_chi_duong" class="form-control" placeholder="Ví dụ: 123 Lê Lợi" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fa fa-map-marker-alt text-danger me-2"></i>Địa chỉ nhận hàng</h6>
                            <?php if ($isLoggedIn && !$hasSavedAddresses): ?>
                                <div class="alert alert-info small mb-3">
                                    Bạn chưa có địa chỉ trong sổ địa chỉ. Hãy nhập địa chỉ giao hàng mới bên dưới.
                                </div>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                    <div class="small text-muted">Bạn chưa đăng nhập nên không thể chọn địa chỉ có sẵn.</div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="guest-login-address-btn">Đăng nhập để chọn địa chỉ có sẵn</button>
                                </div>
                            <?php endif; ?>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_nguoi_nhan" class="form-control" placeholder="Nhập họ và tên" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" name="sdt_nhan" class="form-control" placeholder="Nhập số điện thoại" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tinhThanh" name="tinh_thanh" required>
                                        <option value="" selected disabled>Chọn Tỉnh/Thành</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Quận/Huyện <span class="text-danger">*</span></label>
                                    <select class="form-select" id="quanHuyen" name="quan_huyen" required disabled>
                                        <option value="" selected disabled>Chọn Quận/Huyện</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Phường/Xã <span class="text-danger">*</span></label>
                                    <select class="form-select" id="phuongXa" name="phuong_xa" required disabled>
                                        <option value="" selected disabled>Chọn Phường/Xã</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-medium">Số nhà, tên đường <span class="text-danger">*</span></label>
                                <input type="text" name="dia_chi_duong" class="form-control" placeholder="Ví dụ: 123 Lê Lợi" required>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fa fa-envelope text-danger me-2"></i>Email nhận xác nhận đơn hàng</h6>
                        <label class="form-label small fw-medium">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email_nhan" class="form-control" placeholder="Nhập email để nhận xác nhận đơn hàng" required>
                        <small class="text-muted">Email sẽ được sử dụng để gửi thông tin xác nhận đơn hàng và trạng thái xử lý.</small>
                    </div>
                </div>

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

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fa fa-pen text-danger me-2"></i>Ghi chú đơn hàng</h6>
                        <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Ghi chú về đơn hàng (không bắt buộc)..."></textarea>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fa fa-circle-check text-danger me-2"></i>Xác nhận đơn hàng</h6>
                        <div class="small text-muted mb-3">Kiểm tra lại thông tin nhận hàng, phương thức thanh toán và tổng tiền trước khi xác nhận.</div>
                        <div class="border rounded p-3 bg-light mb-3">
                            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Người nhận</span><strong id="summary-recipient">Chưa cập nhật</strong></div>
                            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Số điện thoại</span><strong id="summary-phone">Chưa cập nhật</strong></div>
                            <div class="small text-muted mb-1">Địa chỉ nhận</div>
                            <div class="fw-medium" id="summary-address">Vui lòng chọn hoặc nhập địa chỉ nhận hàng</div>
                        </div>
                        <div class="form-check d-flex align-items-center gap-2">
                            <input class="form-check-input border-danger flex-shrink-0" type="checkbox" value="1" id="xac_nhan_don" name="xac_nhan_don" required>
                            <label class="form-check-label small text-danger mb-0" for="xac_nhan_don">Tôi xác nhận thông tin nhận hàng và đồng ý đặt đơn theo thông tin đã nhập.</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Đơn hàng (<?= count($chiTietGioList) ?> sản phẩm)</h6>
                        <?php foreach ($chiTietGioList as $item): ?>
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <img src="<?= htmlspecialchars($item['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>" alt="" style="width:50px;height:50px;object-fit:contain;border:1px solid #eee;border-radius:4px;">
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
                        <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Tổng tiền hàng</span><span><?= number_format($tongTien, 0, ',', '.') ?>đ</span></div>
                        <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Phí vận chuyển</span><span><?= number_format($phiVanChuyen, 0, ',', '.') ?>đ</span></div>
                        <div class="d-flex justify-content-between small mb-2 d-none" id="discount-row"><span class="text-muted">Giảm giá</span><span class="text-danger" id="discount-amount">0đ</span></div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold mb-4">
                            <span>Tổng thanh toán</span>
                            <span class="text-danger fs-5" id="total-final"><?= number_format($tongTien + $phiVanChuyen, 0, ',', '.') ?>đ</span>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 fw-medium"><i class="fa fa-check-circle me-2"></i>Xác nhận đặt hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const tongTien = <?= (float)$tongTien ?>;
    const phiVanChuyen = <?= (float)$phiVanChuyen ?>;
    let tienGiam = 0;
    let vnData = [];

    const orderForm = document.getElementById('order-form');
    const summaryRecipient = document.getElementById('summary-recipient');
    const summaryPhone = document.getElementById('summary-phone');
    const summaryAddress = document.getElementById('summary-address');
    const tinhThanhSelect = document.getElementById('tinhThanh');
    const quanHuyenSelect = document.getElementById('quanHuyen');
    const phuongXaSelect = document.getElementById('phuongXa');
    const savedAddressBlock = document.getElementById('saved-address-block');
    const newAddressBlock = document.getElementById('new-address-block');
    const suDungDiaChiKhac = document.getElementById('suDungDiaChiKhac');
    const btnUseSavedAddress = document.getElementById('btn-use-saved-address');
    const btnUseNewAddress = document.getElementById('btn-use-new-address');
    const guestLoginAddressBtn = document.getElementById('guest-login-address-btn');

    function syncPaymentMethodSelection() {
        const paymentOptions = document.querySelectorAll('.payment-method-option');
        paymentOptions.forEach((option) => {
            const radio = option.querySelector('input[name="phuong_thuc_thanh_toan"]');
            option.classList.toggle('is-selected', Boolean(radio && radio.checked));
        });
    }

    function formatSummaryText(value, fallback) {
        const text = (value || '').trim();
        return text !== '' ? text : fallback;
    }

    function setNewAddressInputsDisabled(isDisabled) {
        [tinhThanhSelect, quanHuyenSelect, phuongXaSelect].forEach((el) => {
            if (el) el.disabled = isDisabled;
        });

        document.querySelectorAll('#new-address-block input, #new-address-block select').forEach((el) => {
            el.disabled = isDisabled;
            if (isDisabled) el.removeAttribute('required');
        });

        if (!isDisabled) {
            document.querySelectorAll('#new-address-block input, #new-address-block select').forEach((el) => {
                if (
                    el.name === 'ten_nguoi_nhan' ||
                    el.name === 'sdt_nhan' ||
                    el.name === 'tinh_thanh' ||
                    el.name === 'quan_huyen' ||
                    el.name === 'phuong_xa' ||
                    el.name === 'dia_chi_duong'
                ) {
                    el.setAttribute('required', 'required');
                }
            });
        }
    }

    function useSavedAddressMode() {
        if (!savedAddressBlock || !newAddressBlock) return;
        savedAddressBlock.classList.remove('d-none');
        newAddressBlock.classList.add('d-none');
        if (suDungDiaChiKhac) suDungDiaChiKhac.value = '0';
        setNewAddressInputsDisabled(true);
        document.querySelectorAll('.saved-address-radio').forEach((radio) => {
            radio.required = true;
        });
        syncSummary();
    }

    function useNewAddressMode() {
        if (!savedAddressBlock || !newAddressBlock) return;
        savedAddressBlock.classList.add('d-none');
        newAddressBlock.classList.remove('d-none');
        if (suDungDiaChiKhac) suDungDiaChiKhac.value = '1';
        setNewAddressInputsDisabled(false);
        document.querySelectorAll('.saved-address-radio').forEach((radio) => {
            radio.required = false;
            radio.checked = false;
            radio.closest('.form-check')?.classList.remove('bg-light');
        });
        syncSummary();
    }

    function syncSummary() {
        const savedChecked = document.querySelector('.saved-address-radio:checked');
        if (savedChecked) {
            summaryRecipient.textContent = formatSummaryText(savedChecked.dataset.recipient, 'Chưa cập nhật');
            summaryPhone.textContent = formatSummaryText(savedChecked.dataset.phone, 'Chưa cập nhật');
            summaryAddress.textContent = formatSummaryText(savedChecked.dataset.address, 'Chưa cập nhật');
            return;
        }

        const recipientInput = document.querySelector('input[name="ten_nguoi_nhan"]');
        const phoneInput = document.querySelector('input[name="sdt_nhan"]');
        const streetInput = document.querySelector('input[name="dia_chi_duong"]');
        const parts = [streetInput?.value, phuongXaSelect?.value, quanHuyenSelect?.value, tinhThanhSelect?.value]
            .map((item) => (item || '').trim())
            .filter((item) => item !== '');

        summaryRecipient.textContent = formatSummaryText(recipientInput?.value, 'Chưa cập nhật');
        summaryPhone.textContent = formatSummaryText(phoneInput?.value, 'Chưa cập nhật');
        summaryAddress.textContent = parts.length > 0 ? parts.join(', ') : 'Vui lòng chọn hoặc nhập địa chỉ nhận hàng';
    }

    function populateTinhThanh() {
        if (!tinhThanhSelect) return;

        fetch('https://raw.githubusercontent.com/daohoangson/dvhcvn/master/data/dvhcvn.json')
            .then((response) => response.json())
            .then((data) => {
                vnData = data.data || [];
                vnData.forEach((tinh) => {
                    const option = document.createElement('option');
                    option.value = tinh.name;
                    option.dataset.id = tinh.level1_id;
                    option.textContent = tinh.name;
                    tinhThanhSelect.appendChild(option);
                });
            })
            .catch((error) => {
                console.error('Lỗi khi tải dữ liệu hành chính:', error);
                tinhThanhSelect.innerHTML = '<option disabled>Không thể tải dữ liệu</option>';
            });
    }

    if (tinhThanhSelect && quanHuyenSelect && phuongXaSelect) {
        populateTinhThanh();

        tinhThanhSelect.addEventListener('change', function() {
            const selectedId = this.options[this.selectedIndex]?.dataset?.id;
            quanHuyenSelect.innerHTML = '<option value="" selected disabled>Chọn Quận/Huyện</option>';
            phuongXaSelect.innerHTML = '<option value="" selected disabled>Chọn Phường/Xã</option>';
            phuongXaSelect.disabled = true;

            if (selectedId) {
                const tinhData = vnData.find((t) => t.level1_id === selectedId);
                if (tinhData && tinhData.level2s) {
                    tinhData.level2s.forEach((quan) => {
                        const option = document.createElement('option');
                        option.value = quan.name;
                        option.dataset.id = quan.level2_id;
                        option.textContent = quan.name;
                        quanHuyenSelect.appendChild(option);
                    });
                    quanHuyenSelect.disabled = false;
                }
            }
            syncSummary();
        });

        quanHuyenSelect.addEventListener('change', function() {
            const selectedTinhId = tinhThanhSelect.options[tinhThanhSelect.selectedIndex]?.dataset?.id;
            const selectedQuanId = this.options[this.selectedIndex]?.dataset?.id;
            phuongXaSelect.innerHTML = '<option value="" selected disabled>Chọn Phường/Xã</option>';

            if (selectedTinhId && selectedQuanId) {
                const tinhData = vnData.find((t) => t.level1_id === selectedTinhId);
                const quanData = tinhData?.level2s?.find((q) => q.level2_id === selectedQuanId);
                if (quanData && quanData.level3s) {
                    quanData.level3s.forEach((phuong) => {
                        const option = document.createElement('option');
                        option.value = phuong.name;
                        option.dataset.id = phuong.level3_id;
                        option.textContent = phuong.name;
                        phuongXaSelect.appendChild(option);
                    });
                    phuongXaSelect.disabled = false;
                }
            }
            syncSummary();
        });

        phuongXaSelect.addEventListener('change', syncSummary);
    }

    btnUseSavedAddress?.addEventListener('click', useSavedAddressMode);
    btnUseNewAddress?.addEventListener('click', useNewAddressMode);
    guestLoginAddressBtn?.addEventListener('click', () => {
        alert('Vui lòng đăng nhập để chọn địa chỉ có sẵn.');
        window.location.href = '/client/auth/login';
    });

    document.getElementById('btn-apply-coupon')?.addEventListener('click', function() {
        const ma = document.getElementById('ma-giam-gia-input').value.trim();
        if (!ma) return;

        fetch('/thanh-toan/kiem-tra-ma-giam-gia', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'ma_code=' + encodeURIComponent(ma) + '&tong_tien=' + tongTien
            })
            .then((response) => response.json())
            .then((data) => {
                const msg = document.getElementById('coupon-msg');
                if (data.success) {
                    tienGiam = data.tien_giam;
                    msg.innerHTML = '<span class="text-success"><i class="fa fa-check"></i> ' + data.message + '</span>';
                    document.getElementById('discount-row').classList.remove('d-none');
                    document.getElementById('discount-amount').textContent = '-' + tienGiam.toLocaleString('vi-VN') + 'đ';
                    document.getElementById('total-final').textContent = (tongTien + phiVanChuyen - tienGiam).toLocaleString('vi-VN') + 'đ';
                } else {
                    tienGiam = 0;
                    msg.innerHTML = '<span class="text-danger"><i class="fa fa-times"></i> ' + data.message + '</span>';
                    document.getElementById('discount-row').classList.add('d-none');
                    document.getElementById('total-final').textContent = (tongTien + phiVanChuyen).toLocaleString('vi-VN') + 'đ';
                }
            });
    });

    document.querySelectorAll('.saved-address-radio').forEach((radio) => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.saved-address-radio').forEach((item) => {
                item.closest('.form-check')?.classList.remove('bg-light');
            });
            if (this.checked) {
                this.closest('.form-check')?.classList.add('bg-light');
            }
            syncSummary();
        });
    });

    document.querySelectorAll('input[name="phuong_thuc_thanh_toan"]').forEach((radio) => {
        radio.addEventListener('change', syncPaymentMethodSelection);
    });

    orderForm?.addEventListener('input', syncSummary);
    orderForm?.addEventListener('change', syncSummary);

    if (savedAddressBlock && newAddressBlock) {
        useSavedAddressMode();
    }

    syncPaymentMethodSelection();
    syncSummary();
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>