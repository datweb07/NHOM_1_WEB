<?php
$pageTitle = 'Thanh toán thành công - FPT Shop';
ob_start();

$donHang = $donHang ?? null;
$thanhToan = $thanhToan ?? null;
$diaChi = $diaChi ?? null;
$sanPhamList = $sanPhamList ?? [];
?>

<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="fa fa-check-circle text-success" style="font-size:5rem;"></i>
                </div>
                <h2 class="h3 fw-bold mb-2">Thanh toán thành công!</h2>
                <p class="text-muted">Cảm ơn bạn đã mua hàng tại FPT Shop.</p>
            </div>

            <?php if ($donHang): ?>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 pb-3 border-bottom">
                        <i class="fa fa-file-invoice text-danger me-2"></i>Thông tin đơn hàng
                    </h5>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Mã đơn hàng:</div>
                        <div class="col-sm-8 fw-bold text-danger"><?= htmlspecialchars($donHang['ma_don_hang'] ?? '') ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Tổng tiền hàng:</div>
                        <div class="col-sm-8"><?= number_format($donHang['tong_tien'] ?? 0, 0, ',', '.') ?>đ</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Phí vận chuyển:</div>
                        <div class="col-sm-8"><?= number_format($donHang['phi_van_chuyen'] ?? 0, 0, ',', '.') ?>đ</div>
                    </div>

                    <?php if (!empty($donHang['tien_giam_gia']) && $donHang['tien_giam_gia'] > 0): ?>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Giảm giá:</div>
                        <div class="col-sm-8 text-success">
                            -<?= number_format($donHang['tien_giam_gia'], 0, ',', '.') ?>đ</div>
                    </div>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Tổng thanh toán:</div>
                        <div class="col-sm-8 fw-bold text-danger fs-5">
                            <?= number_format($donHang['tong_thanh_toan'] ?? 0, 0, ',', '.') ?>đ</div>
                    </div>

                    <?php if ($thanhToan): ?>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Phương thức thanh toán:</div>
                        <div class="col-sm-8">
                            <?php
                                $phuongThucMap = [
                                    'COD' => 'Thanh toán khi nhận hàng',
                                    'CHUYEN_KHOAN' => 'Chuyển khoản VNPay'
                                ];
                                echo $phuongThucMap[$thanhToan['phuong_thuc']] ?? $thanhToan['phuong_thuc'];
                                ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-sm-4 text-muted">Ngày đặt hàng:</div>
                        <div class="col-sm-8"><?= date('d/m/Y H:i', strtotime($donHang['ngay_tao'] ?? 'now')) ?></div>
                    </div>
                </div>
            </div>


            <?php if ($diaChi || !empty($donHang['thong_tin_guest'])): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 pb-3 border-bottom">
                        <i class="fa fa-map-marker-alt text-danger me-2"></i>Thông tin giao hàng
                    </h5>

                    <?php if ($diaChi): ?>
                    <div class="mb-2"><strong><?= htmlspecialchars($diaChi['ten_nguoi_nhan'] ?? '') ?></strong></div>
                    <div class="mb-2">Số điện thoại: <?= htmlspecialchars($diaChi['sdt_nhan'] ?? '') ?></div>
                    <div class="text-muted">
                        <?php
                                $diaChiParts = array_filter([
                                    $diaChi['so_nha_duong'] ?? '',
                                    $diaChi['phuong_xa'] ?? '',
                                    $diaChi['quan_huyen'] ?? '',
                                    $diaChi['tinh_thanh'] ?? ''
                                ]);
                                echo htmlspecialchars(implode(', ', $diaChiParts));
                                ?>
                    </div>
                    <?php elseif (!empty($donHang['thong_tin_guest'])): ?>
                    <?php
                            $guestInfo = json_decode($donHang['thong_tin_guest'], true);
                            if ($guestInfo):
                            ?>
                    <div class="mb-2"><strong><?= htmlspecialchars($guestInfo['ten'] ?? '') ?></strong></div>
                    <div class="mb-2">Số điện thoại: <?= htmlspecialchars($guestInfo['sdt'] ?? '') ?></div>
                    <div class="text-muted"><?= htmlspecialchars($guestInfo['dia_chi'] ?? '') ?></div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>


            <?php if (!empty($sanPhamList)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 pb-3 border-bottom">
                        <i class="fa fa-box text-danger me-2"></i>Chi tiết đơn hàng
                    </h5>

                    <?php foreach ($sanPhamList as $item): ?>
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div style="width:60px;height:60px;border:1px solid #eee;border-radius:4px;overflow:hidden;">
                            <img src="<?= ASSET_URL ?>/assets/client/images/products/default.png" alt=""
                                style="width:100%;height:100%;object-fit:contain;">
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium"><?= htmlspecialchars($item['ten_san_pham'] ?? '') ?></div>
                            <?php if (!empty($item['ten_phien_ban'])): ?>
                            <div class="text-muted small"><?= htmlspecialchars($item['ten_phien_ban']) ?></div>
                            <?php endif; ?>
                            <div class="text-muted small">Số lượng: <?= $item['so_luong'] ?? 0 ?></div>
                        </div>
                        <div class="text-danger fw-bold">
                            <?= number_format(($item['gia_ban'] ?? 0) * ($item['so_luong'] ?? 0), 0, ',', '.') ?>đ
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>


            <div class="d-flex gap-3 justify-content-center">
                <a href="/don-hang/<?= $donHang['id'] ?>" class="btn btn-danger px-4">
                    <i class="fa fa-eye me-2"></i>Xem chi tiết đơn hàng
                </a>
                <a href="/" class="btn btn-outline-secondary px-4">
                    <i class="fa fa-home me-2"></i>Về trang chủ
                </a>
            </div>
            <?php else: ?>

            <div class="alert alert-info text-center">
                <i class="fa fa-info-circle me-2"></i>
                Không tìm thấy thông tin đơn hàng. Vui lòng kiểm tra lại trong mục "Đơn hàng của tôi".
            </div>
            <div class="text-center">
                <a href="/don-hang" class="btn btn-danger">
                    <i class="fa fa-file-invoice me-2"></i>Xem đơn hàng của tôi
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>