<?php
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$successMessages = [
    'status_updated' => 'Da cap nhat trang thai don hang.',
];

$errorMessages = [
    'invalid_transition' => 'Khong the chuyen trang thai theo luong yeu cau.',
];

$donHang = (isset($donHang) && is_array($donHang)) ? $donHang : [];
$chiTietDon = (isset($chiTietDon) && is_array($chiTietDon)) ? $chiTietDon : [];
$trangThaiKeTiep = (isset($trangThaiKeTiep) && is_array($trangThaiKeTiep)) ? $trangThaiKeTiep : [];
$orderId = (int)($donHang['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng</title>
    <script src="https://kit.fontawesome.com/1f55434e39.js" crossorigin="anonymous"></script>
    <link rel="icon" href="<?= ASSET_URL ?>/assets/client/images/header/1.png">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/client/css/main.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/client/css/grid.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/client/css/reponsive.css">
    <style>
        .admin-detail-page {
            background: linear-gradient(180deg, #f7f8fb 0%, #eef1f7 100%);
            min-height: 100vh;
            padding: 22px 0 40px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .top-bar h1 {
            margin: 0;
            font-size: 25px;
            color: #111827;
            font-weight: 700;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border: 1px solid #d0d5dd;
            color: #344054;
            border-radius: 10px;
            padding: 9px 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            background: #fff;
        }

        .btn-back:hover {
            color: #cb1c22;
            border-color: #cb1c22;
            background: #fff6f6;
        }

        .fpt-alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 12px;
            font-size: 14px;
            border: 1px solid transparent;
        }

        .fpt-alert.success {
            background: #ecfdf3;
            color: #027a48;
            border-color: #abefc6;
        }

        .fpt-alert.error {
            background: #fff1f3;
            color: #b42318;
            border-color: #fecdca;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .fpt-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eceff4;
            box-shadow: 0 14px 38px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .card-body {
            padding: 18px;
        }

        .card-title {
            margin: 0 0 12px;
            color: #475467;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .info-list {
            display: grid;
            gap: 8px;
            font-size: 14px;
            color: #344054;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #fedf89;
            background: #fffaeb;
            color: #b54708;
        }

        .status-actions {
            margin-bottom: 14px;
        }

        .status-actions .card-body {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .action-list {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-list form {
            margin: 0;
        }

        .btn-status {
            border: 1px solid #d0d5dd;
            background: #fff;
            color: #344054;
            border-radius: 10px;
            padding: 9px 13px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s ease;
        }

        .btn-status:hover {
            border-color: #cb1c22;
            color: #cb1c22;
            background: #fff5f5;
        }

        .btn-status.danger:hover {
            border-color: #b42318;
            color: #b42318;
            background: #fff1f3;
        }

        .empty-text {
            color: #667085;
            font-size: 14px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 940px;
        }

        .detail-table thead th {
            background: #f8fafc;
            color: #667085;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-weight: 700;
            text-align: left;
            padding: 14px;
            border-bottom: 1px solid #edf1f6;
        }

        .detail-table tbody td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #344054;
        }

        .detail-table tbody tr:hover {
            background: #fcfcfd;
        }

        .empty-row {
            text-align: center;
            color: #667085;
            padding: 28px 10px;
        }

        @media (max-width: 992px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .admin-detail-page {
                padding-top: 14px;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-back {
                width: 100%;
                justify-content: center;
            }

            .action-list {
                flex-direction: column;
            }

            .btn-status {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="admin-detail-page">
        <div class="grid wide">
            <div class="top-bar">
                <h1>Chi tiết đơn hàng #<?= $orderId > 0 ? $orderId : '-' ?></h1>
                <a class="btn-back" href="/admin/don-hang"><i class="fa fa-arrow-left"></i> Quay lại danh sách</a>
            </div>

            <?php if ($orderId <= 0): ?>
                <div class="fpt-alert error"><i class="fa fa-triangle-exclamation"></i> Khong tim thay du lieu don hang hop le.</div>
            <?php endif; ?>

            <?php if (!empty($success) && isset($successMessages[$success])): ?>
                <div class="fpt-alert success"><i class="fa fa-circle-check"></i> <?= e($successMessages[$success]) ?></div>
            <?php endif; ?>
            <?php if (!empty($error) && isset($errorMessages[$error])): ?>
                <div class="fpt-alert error"><i class="fa fa-triangle-exclamation"></i> <?= e($errorMessages[$error]) ?></div>
            <?php endif; ?>

            <div class="summary-grid">
                <div class="fpt-card">
                    <div class="card-body">
                        <h2 class="card-title">Thông tin đơn hàng</h2>
                        <div class="info-list">
                            <div><strong>Mã đơn:</strong> <?= e($donHang['ma_don_hang'] ?? '-') ?></div>
                            <div><strong>Trạng thái hiện tại:</strong> <span class="status-badge"><i class="fa fa-circle"></i> <?= e($donHang['trang_thai'] ?? '-') ?></span></div>
                            <div><strong>Tổng tiền hàng:</strong> <?= number_format((float)($donHang['tong_tien'] ?? 0), 0, ',', '.') ?> VND</div>
                            <div><strong>Phí vận chuyển:</strong> <?= number_format((float)($donHang['phi_van_chuyen'] ?? 0), 0, ',', '.') ?> VND</div>
                            <div><strong>Tiền giảm giá:</strong> <?= number_format((float)($donHang['tien_giam_gia'] ?? 0), 0, ',', '.') ?> VND</div>
                            <div><strong>Tổng thanh toán:</strong> <strong><?= number_format((float)($donHang['tong_thanh_toan'] ?? 0), 0, ',', '.') ?> VND</strong></div>
                            <div><strong>Ngày tạo:</strong> <?= e($donHang['ngay_tao'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>

                <div class="fpt-card">
                    <div class="card-body">
                        <h2 class="card-title">Khách hàng</h2>
                        <div class="info-list">
                            <div><strong>Họ tên:</strong> <?= e($donHang['ho_ten'] ?? 'Khách vãng lai') ?></div>
                            <div><strong>Email:</strong> <?= e($donHang['email'] ?? '-') ?></div>
                            <div><strong>Số điện thoại:</strong> <?= e($donHang['sdt'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fpt-card status-actions">
                <div class="card-body">
                    <h2 class="card-title">Cập nhật trạng thái</h2>
                    <?php if (empty($trangThaiKeTiep)): ?>
                        <div class="empty-text">Đơn hàng đã ở trạng thái cuối, không thể cập nhật tiếp.</div>
                    <?php else: ?>
                        <div class="action-list">
                            <?php foreach ($trangThaiKeTiep as $next): ?>
                                <form method="POST" action="/admin/don-hang/cap-nhat-trang-thai?id=<?= $orderId ?>">
                                    <input type="hidden" name="trang_thai" value="<?= e($next) ?>">
                                    <?php if ($next === 'DA_HUY'): ?>
                                        <button class="btn-status danger" type="submit"><i class="fa fa-ban"></i> Chuyển sang DA_HUY</button>
                                    <?php elseif ($next === 'DANG_GIAO'): ?>
                                        <button class="btn-status" type="submit"><i class="fa fa-truck-fast"></i> Chuyển sang DANG_GIAO</button>
                                    <?php else: ?>
                                        <button class="btn-status" type="submit"><i class="fa fa-circle-check"></i> Chuyển sang <?= e($next) ?></button>
                                    <?php endif; ?>
                                </form>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="fpt-card">
                <div class="table-wrap">
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sản phẩm</th>
                                <th>Phiên bản</th>
                                <th>Số lượng</th>
                                <th>Giá mua</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($chiTietDon)): ?>
                                <tr>
                                    <td colspan="6" class="empty-row">Không có chi tiết đơn hàng.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($chiTietDon as $index => $item): ?>
                                    <?php $thanhTien = (float)($item['gia_tai_thoi_diem_mua'] ?? 0) * (int)($item['so_luong'] ?? 0); ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= e($item['ten_san_pham'] ?? '-') ?></td>
                                        <td><?= e($item['ten_phien_ban'] ?? '-') ?> / <?= e($item['mau_sac'] ?? '-') ?> / <?= e($item['dung_luong'] ?? '-') ?></td>
                                        <td><?= (int)($item['so_luong'] ?? 0) ?></td>
                                        <td><?= number_format((float)($item['gia_tai_thoi_diem_mua'] ?? 0), 0, ',', '.') ?> VND</td>
                                        <td><strong><?= number_format($thanhTien, 0, ',', '.') ?> VND</strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>