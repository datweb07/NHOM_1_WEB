<?php
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$successMessages = [
    'status_updated' => 'Đã cập nhật trạng thái đơn hàng.',
    'refund_initiated' => 'Đã khởi tạo yêu cầu hoàn tiền thành công.',
    'refund_completed' => 'Hoàn tiền thành công.',
];

$errorMessages = [
    'invalid_transition' => 'Không thể chuyển trạng thái theo luồng yêu cầu.',
    'refund_failed' => 'Không thể hoàn tiền. Vui lòng thử lại.',
    'no_payment' => 'Đơn hàng chưa có thông tin thanh toán.',
    'already_refunded' => 'Đơn hàng đã được hoàn tiền.',
];

$donHang = (isset($donHang) && is_array($donHang)) ? $donHang : [];
$chiTietDon = (isset($chiTietDon) && is_array($chiTietDon)) ? $chiTietDon : [];
$trangThaiKeTiep = (isset($trangThaiKeTiep) && is_array($trangThaiKeTiep)) ? $trangThaiKeTiep : [];
$thanhToan = (isset($thanhToan) && is_array($thanhToan)) ? $thanhToan : null;
$refunds = (isset($refunds) && is_array($refunds)) ? $refunds : [];
$orderId = (int)($donHang['id'] ?? 0);

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Đơn Hàng', 'url' => '/admin/don-hang'],
        ['label' => 'Chi Tiết Đơn Hàng', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Chi tiết đơn hàng #<?= $orderId > 0 ? $orderId : '-' ?></h3>
                <a href="/admin/don-hang" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            <?php if ($orderId <= 0): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> Không tìm thấy dữ liệu đơn hàng hợp lệ.
                </div>
            <?php endif; ?>

            <?php if (!empty($success) && isset($successMessages[$success])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= e($successMessages[$success]) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error) && isset($errorMessages[$error])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= e($errorMessages[$error]) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-lg-7 col-md-12 mb-3 mb-lg-0">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title text-uppercase text-muted mb-0">Thông tin đơn hàng</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Mã đơn:</div>
                                <div class="col-sm-8 fw-bold"><?= e($donHang['ma_don_hang'] ?? '-') ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Trạng thái hiện tại:</div>
                                <div class="col-sm-8">
                                    <span class="badge bg-primary px-2 py-1 fs-6">
                                        <?= e($donHang['trang_thai'] ?? '-') ?>
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Tổng tiền hàng:</div>
                                <div class="col-sm-8"><?= number_format((float)($donHang['tong_tien'] ?? 0), 0, ',', '.') ?> đ</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Phí vận chuyển:</div>
                                <div class="col-sm-8"><?= number_format((float)($donHang['phi_van_chuyen'] ?? 0), 0, ',', '.') ?> đ</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Tiền giảm giá:</div>
                                <div class="col-sm-8 text-danger">- <?= number_format((float)($donHang['tien_giam_gia'] ?? 0), 0, ',', '.') ?> đ</div>
                            </div>
                            <hr>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted fw-bold">Tổng thanh toán:</div>
                                <div class="col-sm-8 fw-bold text-primary fs-5"><?= number_format((float)($donHang['tong_thanh_toan'] ?? 0), 0, ',', '.') ?> đ</div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-sm-4 text-muted">Ngày tạo:</div>
                                <div class="col-sm-8"><?= e($donHang['ngay_tao'] ?? '-') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title text-uppercase text-muted mb-0">Khách hàng</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Họ tên:</div>
                                <div class="col-sm-8 fw-bold"><?= e($donHang['ho_ten'] ?? 'Khách vãng lai') ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Email:</div>
                                <div class="col-sm-8"><?= e($donHang['email'] ?? '-') ?></div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-sm-4 text-muted">Số điện thoại:</div>
                                <div class="col-sm-8"><?= e($donHang['sdt'] ?? '-') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title text-uppercase text-muted mb-0">Cập nhật trạng thái</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($trangThaiKeTiep)): ?>
                        <div class="text-muted fst-italic">Đơn hàng đã ở trạng thái cuối, không thể cập nhật tiếp.</div>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($trangThaiKeTiep as $next): ?>
                                <form method="POST" action="/admin/don-hang/cap-nhat-trang-thai?id=<?= $orderId ?>" class="m-0">
                                    <input type="hidden" name="trang_thai" value="<?= e($next) ?>">
                                    <?php if ($next === 'DA_HUY'): ?>
                                        <button class="btn btn-outline-danger" type="submit">
                                            <i class="bi bi-x-circle"></i> Chuyển sang DA_HUY
                                        </button>
                                    <?php elseif ($next === 'DANG_GIAO'): ?>
                                        <button class="btn btn-outline-info" type="submit">
                                            <i class="bi bi-truck"></i> Chuyển sang DANG_GIAO
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-primary" type="submit">
                                            <i class="bi bi-check-circle"></i> Chuyển sang <?= e($next) ?>
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($thanhToan !== null): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title text-uppercase text-muted mb-0">Thông tin thanh toán & Hoàn tiền</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Phương thức:</div>
                                <div class="col-sm-8 fw-bold"><?= e($thanhToan['phuong_thuc'] ?? '-') ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Số tiền:</div>
                                <div class="col-sm-8 text-primary"><?= number_format((float)($thanhToan['so_tien'] ?? 0), 0, ',', '.') ?> đ</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Trạng thái:</div>
                                <div class="col-sm-8">
                                    <?php
                                        $ttDuyet = $thanhToan['trang_thai_duyet'] ?? '';
                                        $badgeBg = $ttDuyet === 'THANH_CONG' ? 'bg-success' : ($ttDuyet === 'THAT_BAI' ? 'bg-danger' : 'bg-warning text-dark');
                                    ?>
                                    <span class="badge <?= $badgeBg ?>"><?= e($ttDuyet) ?></span>
                                </div>
                            </div>
                            <?php if (!empty($thanhToan['gateway_transaction_id'])): ?>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Mã giao dịch:</div>
                                    <div class="col-sm-8"><code><?= e($thanhToan['gateway_transaction_id']) ?></code></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 border-start">
                            <?php
                            $canRefund = false;
                            $refundMessage = '';
                            $phuongThuc = $thanhToan['phuong_thuc'] ?? '';
                            $trangThaiDuyet = $thanhToan['trang_thai_duyet'] ?? '';
                            $hasRefund = !empty($refunds);

                            if ($phuongThuc === 'COD') {
                                $refundMessage = 'Đơn hàng COD không cần hoàn tiền qua cổng thanh toán.';
                            } elseif ($trangThaiDuyet !== 'THANH_CONG') {
                                $refundMessage = 'Chỉ có thể hoàn tiền cho đơn hàng đã thanh toán thành công.';
                            } elseif ($hasRefund) {
                                $refundMessage = 'Đơn hàng đã có yêu cầu hoàn tiền.';
                            } else {
                                $canRefund = true;
                            }
                            ?>

                            <?php if (!empty($refunds)): ?>
                                <h6 class="mb-3">Lịch sử hoàn tiền:</h6>
                                <?php foreach ($refunds as $refund): ?>
                                    <div class="bg-light p-3 rounded mb-2 border">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong><?= number_format((float)($refund['amount'] ?? 0), 0, ',', '.') ?> đ</strong>
                                            <?php
                                            $refundStatus = strtolower($refund['status'] ?? 'pending');
                                            $badgeClass = $refundStatus === 'completed' ? 'bg-success' : ($refundStatus === 'failed' ? 'bg-danger' : 'bg-warning text-dark');
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= e($refund['status'] ?? '-') ?></span>
                                        </div>
                                        <?php if (!empty($refund['gateway_refund_id'])): ?>
                                            <div class="small text-muted mb-1">Mã: <code><?= e($refund['gateway_refund_id']) ?></code></div>
                                        <?php endif; ?>
                                        <div class="small text-muted mb-1">Lý do: <?= e($refund['reason'] ?? '-') ?></div>
                                        <div class="small text-muted">Ngày: <?= e($refund['created_at'] ?? '-') ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <div class="mt-3">
                                <?php if ($canRefund): ?>
                                    <form method="POST" action="/admin/don-hang/hoan-tien?id=<?= $orderId ?>" onsubmit="return confirm('Bạn có chắc chắn muốn hoàn tiền cho đơn hàng này?');">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="bi bi-arrow-counterclockwise"></i> Khởi tạo hoàn tiền
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-secondary py-2 mb-0 border-0 fs-6">
                                        <i class="bi bi-info-circle me-1"></i> <?= e($refundMessage) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title text-uppercase text-muted mb-0">Danh sách sản phẩm</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Sản phẩm</th>
                                    <th>Phiên bản</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Giá mua</th>
                                    <th class="text-end pe-3">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($chiTietDon)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Không có chi tiết đơn hàng.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($chiTietDon as $index => $item): ?>
                                        <?php $thanhTien = (float)($item['gia_tai_thoi_diem_mua'] ?? 0) * (int)($item['so_luong'] ?? 0); ?>
                                        <tr>
                                            <td class="ps-3"><?= $index + 1 ?></td>
                                            <td class="fw-medium"><?= e($item['ten_san_pham'] ?? '-') ?></td>
                                            <td class="text-muted fs-6">
                                                <?= e($item['ten_phien_ban'] ?? '-') ?> / 
                                                <?= e($item['mau_sac'] ?? '-') ?> / 
                                                <?= e($item['dung_luong'] ?? '-') ?>
                                            </td>
                                            <td class="text-center fw-bold"><?= (int)($item['so_luong'] ?? 0) ?></td>
                                            <td class="text-end"><?= number_format((float)($item['gia_tai_thoi_diem_mua'] ?? 0), 0, ',', '.') ?> đ</td>
                                            <td class="text-end pe-3 text-primary fw-bold"><?= number_format($thanhTien, 0, ',', '.') ?> đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div> </div> </main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>