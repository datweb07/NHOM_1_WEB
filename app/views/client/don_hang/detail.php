<?php
$pageTitle = 'Chi tiết đơn hàng - FPT Shop';
ob_start();

$trangThaiMap = [
    'CHO_DUYET'    => ['label' => 'Chờ duyệt',    'class' => 'bg-warning text-dark'],
    'DA_DUYET'     => ['label' => 'Đã duyệt',     'class' => 'bg-info text-white'],
    'DANG_GIAO'    => ['label' => 'Đang giao',    'class' => 'bg-primary'],
    'DA_GIAO'      => ['label' => 'Đã giao',      'class' => 'bg-success'],
    'DA_HUY'       => ['label' => 'Đã hủy',       'class' => 'bg-danger'],
    'HOAN_TIEN'    => ['label' => 'Hoàn tiền',    'class' => 'bg-secondary'],
];
$ts = $trangThaiMap[$donHang['trang_thai']] ?? ['label' => $donHang['trang_thai'], 'class' => 'bg-secondary'];

$ptMap = [
    'COD'            => 'Thanh toán khi nhận hàng',
    'CHUYEN_KHOAN'   => 'Chuyển khoản ngân hàng',
    'MOMO'           => 'Ví MoMo',
    'TIEN_MAT'       => 'Tiền mặt',
];
?>

<div class="container-xl py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-danger text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/don-hang" class="text-danger text-decoration-none">Đơn hàng</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($donHang['ma_don_hang']) ?></li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold mb-0"><i class="fa fa-file-invoice text-danger me-2"></i>Chi tiết đơn hàng</h1>
        <span class="badge <?= $ts['class'] ?>"><?= $ts['label'] ?></span>
    </div>

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
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Sản phẩm đã đặt</h6>
                    <?php foreach ($chiTietDonList as $item): ?>
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                            <img src="<?= htmlspecialchars($item['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                                 alt="" style="width:60px;height:60px;object-fit:contain;border:1px solid #eee;border-radius:6px;">
                            <div class="flex-grow-1">
                                <div class="small fw-medium"><?= htmlspecialchars($item['ten_san_pham'] ?? '') ?></div>
                                <?php if (!empty($item['ten_phien_ban'])): ?>
                                    <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($item['ten_phien_ban']) ?></div>
                                <?php endif; ?>
                                <div class="text-muted small">x<?= $item['so_luong'] ?> | Đơn giá: <?= number_format($item['gia_tai_thoi_diem_mua'], 0, ',', '.') ?>đ</div>
                            </div>
                            <div class="text-danger fw-bold small"><?= number_format($item['gia_tai_thoi_diem_mua'] * $item['so_luong'], 0, ',', '.') ?>đ</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Thông tin giao hàng</h6>
                    <?php if (!empty($donHang['thong_tin_guest'])): ?>
                        <?php $guest = json_decode($donHang['thong_tin_guest'], true); ?>
                        <p class="mb-1 small"><strong>Người nhận:</strong> <?= htmlspecialchars($guest['ten'] ?? '') ?></p>
                        <p class="mb-1 small"><strong>Điện thoại:</strong> <?= htmlspecialchars($guest['sdt'] ?? '') ?></p>
                        <p class="mb-0 small"><strong>Địa chỉ:</strong> <?= htmlspecialchars($guest['dia_chi'] ?? '') ?></p>
                    
                    <?php elseif (!empty($diaChiGiaoHang)): ?>
                        <p class="mb-1 small"><strong>Người nhận:</strong> <?= htmlspecialchars($diaChiGiaoHang['ten_nguoi_nhan']) ?></p>
                        <p class="mb-1 small"><strong>Điện thoại:</strong> <?= htmlspecialchars($diaChiGiaoHang['sdt_nhan']) ?></p>
                        <p class="mb-0 small"><strong>Địa chỉ:</strong> <?= htmlspecialchars($diaChiGiaoHang['so_nha_duong'] . ', ' . $diaChiGiaoHang['phuong_xa'] . ', ' . $diaChiGiaoHang['quan_huyen'] . ', ' . $diaChiGiaoHang['tinh_thanh']) ?></p>
                    
                    <?php else: ?>
                        <p class="text-muted small mb-0">Không có thông tin địa chỉ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Tóm tắt thanh toán</h6>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Mã đơn hàng</span>
                        <span class="fw-medium"><?= htmlspecialchars($donHang['ma_don_hang']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Ngày đặt</span>
                        <span><?= date('d/m/Y H:i', strtotime($donHang['ngay_tao'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Phương thức TT</span>
                        <span><?= $ptMap[$thanhToan['phuong_thuc'] ?? ''] ?? ($thanhToan['phuong_thuc'] ?? '—') ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Tiền hàng</span>
                        <span><?= number_format($donHang['tong_tien'], 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Phí vận chuyển</span>
                        <span><?= number_format($donHang['phi_van_chuyen'], 0, ',', '.') ?>đ</span>
                    </div>
                    <?php if ($donHang['tien_giam_gia'] > 0): ?>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">Giảm giá</span>
                            <span class="text-danger">-<?= number_format($donHang['tien_giam_gia'], 0, ',', '.') ?>đ</span>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Tổng thanh toán</span>
                        <span class="text-danger"><?= number_format($donHang['tong_thanh_toan'], 0, ',', '.') ?>đ</span>
                    </div>
                </div>
            </div>


            <?php if ($donHang['trang_thai'] === 'CHO_DUYET' && \App\Core\Session::isLoggedIn()): ?>
                <form action="/don-hang/huy" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                    <input type="hidden" name="id" value="<?= $donHang['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fa fa-times me-1"></i>Hủy đơn hàng
                    </button>
                </form>
            <?php endif; ?>

            <div class="mt-3">
                <a href="/don-hang" class="btn btn-outline-secondary w-100">
                    <i class="fa fa-arrow-left me-1"></i>Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
