<?php
$pageTitle = 'Thanh toán - FPT Shop';
ob_start();
$isLoggedIn = \App\Core\Session::isLoggedIn();
?>

<div class="container-xl py-4">
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

            <!-- Thông tin giao hàng -->
            <div class="col-lg-7">

                <?php if ($isLoggedIn && !empty($diaChiList)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fa fa-map-marker-alt text-danger me-2"></i>Địa chỉ giao hàng <span class="text-danger">*</span></h6>
                            <?php foreach ($diaChiList as $dc): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="dia_chi_id"
                                        id="dc_<?= $dc['id'] ?>" value="<?= $dc['id'] ?>"
                                        <?= ($diaChiMacDinh && $dc['id'] == $diaChiMacDinh['id']) ? 'checked' : '' ?>>
                                    <label class="form-check-label w-100" for="dc_<?= $dc['id'] ?>">
                                        <div class="p-2 border rounded" style="background: <?= ($diaChiMacDinh && $dc['id'] == $diaChiMacDinh['id']) ? '#fff0f0' : '#f9f9f9' ?>;">
                                            <span class="fw-medium"><?= htmlspecialchars($dc['ten_nguoi_nhan']) ?></span>
                                            <span class="text-muted"> | <?= htmlspecialchars($dc['sdt']) ?></span>
                                            <?php if ($dc['la_mac_dinh']): ?>
                                                <span class="badge bg-danger ms-1" style="font-size:0.65rem;">Mặc định</span>
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($dc['dia_chi_chi_tiet'] . ', ' . $dc['xa_phuong'] . ', ' . $dc['quan_huyen'] . ', ' . $dc['tinh_thanh']) ?></small>
                                        </div>
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
                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <select name="tinh_thanh" id="tinh-thanh" class="form-select" required>
                                        <option value="">Chọn Tỉnh/Thành</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Quận/Huyện <span class="text-danger">*</span></label>
                                    <select name="quan_huyen" id="quan-huyen" class="form-select" required disabled>
                                        <option value="">Chọn Quận/Huyện</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Xã/Phường <span class="text-danger">*</span></label>
                                    <select name="xa_phuong" id="xa-phuong" class="form-select" required disabled>
                                        <option value="">Chọn Xã/Phường</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Số nhà, ngõ, đường <span class="text-danger">*</span></label>
                                <input type="text" name="dia_chi_chi_tiet" class="form-control" placeholder="Ví dụ: Số 12 ngõ 8 Nguyễn Trãi" required>
                                <input type="hidden" name="dia_chi" id="dia-chi-day-du" value="">
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
                                <img src="<?= htmlspecialchars($item['anh_chinh'] ?? '/public/assets/client/images/products/14.png') ?>"
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
    const isLoggedIn = <?= json_encode($isLoggedIn) ?>;
    const hasDiaChiList = <?= json_encode(!empty($diaChiList)) ?>;

    const tinhThanhSelect = document.getElementById('tinh-thanh');
    const quanHuyenSelect = document.getElementById('quan-huyen');
    const xaPhuongSelect = document.getElementById('xa-phuong');

    async function taiTinhThanh() {
        if (!tinhThanhSelect) return;
        try {
            const response = await fetch('https://provinces.open-api.vn/api/p/');
            const data = await response.json();
            const options = ['<option value="">Chọn Tỉnh/Thành</option>']
                .concat(data.map(item => '<option value="' + item.name + '" data-code="' + item.code + '">' + item.name + '</option>'));
            tinhThanhSelect.innerHTML = options.join('');
        } catch (error) {
            console.error('Không tải được danh sách tỉnh/thành', error);
        }
    }

    async function taiQuanHuyen(maTinh) {
        if (!quanHuyenSelect || !xaPhuongSelect) return;
        quanHuyenSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        xaPhuongSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';
        xaPhuongSelect.disabled = true;

        if (!maTinh) {
            quanHuyenSelect.disabled = true;
            return;
        }

        try {
            const response = await fetch('https://provinces.open-api.vn/api/p/' + maTinh + '?depth=2');
            const data = await response.json();
            const options = ['<option value="">Chọn Quận/Huyện</option>']
                .concat((data.districts || []).map(item => '<option value="' + item.name + '" data-code="' + item.code + '">' + item.name + '</option>'));
            quanHuyenSelect.innerHTML = options.join('');
            quanHuyenSelect.disabled = false;
        } catch (error) {
            console.error('Không tải được danh sách quận/huyện', error);
        }
    }

    async function taiXaPhuong(maQuan) {
        if (!xaPhuongSelect) return;
        xaPhuongSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';

        if (!maQuan) {
            xaPhuongSelect.disabled = true;
            return;
        }

        try {
            const response = await fetch('https://provinces.open-api.vn/api/d/' + maQuan + '?depth=2');
            const data = await response.json();
            const options = ['<option value="">Chọn Xã/Phường</option>']
                .concat((data.wards || []).map(item => '<option value="' + item.name + '">' + item.name + '</option>'));
            xaPhuongSelect.innerHTML = options.join('');
            xaPhuongSelect.disabled = false;
        } catch (error) {
            console.error('Không tải được danh sách xã/phường', error);
        }
    }

    tinhThanhSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const maTinh = selected?.getAttribute('data-code');
        taiQuanHuyen(maTinh);
    });

    quanHuyenSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const maQuan = selected?.getAttribute('data-code');
        taiXaPhuong(maQuan);
    });

    if (!isLoggedIn || !hasDiaChiList) {
        taiTinhThanh();
    }

    // Validar form trước submit
    document.getElementById('order-form')?.addEventListener('submit', function(e) {
        if (isLoggedIn && hasDiaChiList) {
            // Kiểm tra nếu user chọn địa chỉ từ danh sách
            const diaChiId = document.querySelector('input[name="dia_chi_id"]:checked');
            if (!diaChiId) {
                e.preventDefault();
                alert('Vui lòng chọn địa chỉ giao hàng');
                return false;
            }
        } else if (!isLoggedIn || !hasDiaChiList) {
            // Kiểm tra form nhập cho khách vãng lai hoặc user chưa có địa chỉ
            const tenNguoiNhan = document.querySelector('input[name="ten_nguoi_nhan"]')?.value.trim();
            const sdtNhan = document.querySelector('input[name="sdt_nhan"]')?.value.trim();
            const tinhThanh = document.querySelector('select[name="tinh_thanh"]')?.value.trim();
            const quanHuyen = document.querySelector('select[name="quan_huyen"]')?.value.trim();
            const xaPhuong = document.querySelector('select[name="xa_phuong"]')?.value.trim();
            const diaChiChiTiet = document.querySelector('input[name="dia_chi_chi_tiet"]')?.value.trim();
            const diaChiDayDuInput = document.getElementById('dia-chi-day-du');

            if (!tenNguoiNhan) {
                e.preventDefault();
                alert('Vui lòng nhập họ và tên');
                return false;
            }
            if (!sdtNhan) {
                e.preventDefault();
                alert('Vui lòng nhập số điện thoại');
                return false;
            }
            if (!tinhThanh || !quanHuyen || !xaPhuong) {
                e.preventDefault();
                alert('Vui lòng chọn đầy đủ Tỉnh/Thành, Quận/Huyện, Xã/Phường');
                return false;
            }
            if (!diaChiChiTiet) {
                e.preventDefault();
                alert('Vui lòng nhập số nhà, ngõ, đường');
                return false;
            }

            if (diaChiDayDuInput) {
                diaChiDayDuInput.value = diaChiChiTiet + ', ' + xaPhuong + ', ' + quanHuyen + ', ' + tinhThanh;
            }
        }
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