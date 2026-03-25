<?php
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$successMessages = [
    'status_updated' => 'Cap nhat trang thai thanh cong.',
];

$errorMessages = [
    'invalid_id' => 'ID don hang khong hop le.',
    'not_found' => 'Khong tim thay don hang.',
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng</title>
    <script src="https://kit.fontawesome.com/1f55434e39.js" crossorigin="anonymous"></script>
    <link rel="icon" href="/public/assets/client/images/header/1.png">
    <link rel="stylesheet" href="/public/assets/client/css/main.css">
    <link rel="stylesheet" href="/public/assets/client/css/grid.css">
    <link rel="stylesheet" href="/public/assets/client/css/reponsive.css">
    <style>
        .admin-order-page {
            background: linear-gradient(180deg, #f7f8fb 0%, #eef1f7 100%);
            min-height: 100vh;
            padding: 24px 0 40px;
        }

        .order-page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .order-page-head h1 {
            font-size: 26px;
            margin: 0;
            color: #1f2937;
            font-weight: 700;
        }

        .order-page-head .head-sub {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .fpt-pill {
            background: #cb1c22;
            color: #fff;
            border-radius: 99px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
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

        .fpt-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eceff4;
            box-shadow: 0 14px 38px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .filter-wrap {
            padding: 16px;
            border-bottom: 1px solid #edf1f6;
            background: #fcfdff;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .filter-form select {
            min-width: 230px;
            border-radius: 10px;
            border: 1px solid #d0d5dd;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
        }

        .btn-filter {
            border: 1px solid #cb1c22;
            color: #cb1c22;
            background: #fff;
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .btn-filter:hover {
            background: #cb1c22;
            color: #fff;
        }

        .order-table-wrap {
            overflow-x: auto;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        .order-table thead th {
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

        .order-table tbody td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #344054;
            vertical-align: top;
        }

        .order-table tbody tr:hover {
            background: #fcfcfd;
        }

        .customer-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 3px;
        }

        .customer-email {
            font-size: 12px;
            color: #667085;
            word-break: break-word;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid;
            white-space: nowrap;
        }

        .status-cho-duyet {
            color: #b54708;
            background: #fffaeb;
            border-color: #fedf89;
        }

        .status-dang-giao {
            color: #175cd3;
            background: #eff8ff;
            border-color: #b2ddff;
        }

        .status-hoan-thanh {
            color: #027a48;
            background: #ecfdf3;
            border-color: #abefc6;
        }

        .status-da-huy {
            color: #b42318;
            background: #fff1f3;
            border-color: #fecdca;
        }

        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #d0d5dd;
            color: #344054;
            border-radius: 9px;
            padding: 7px 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: .2s ease;
        }

        .btn-view:hover {
            border-color: #cb1c22;
            color: #cb1c22;
            background: #fff5f5;
        }

        .empty-state {
            text-align: center;
            color: #667085;
            padding: 26px 10px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .admin-order-page {
                padding-top: 14px;
            }

            .order-page-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-form select,
            .btn-filter {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="admin-order-page">
        <div class="grid wide">
            <div class="order-page-head">
                <div>
                    <h1>Danh sách đơn hàng</h1>
                    <p class="head-sub">Quản lý trạng thái và theo dõi thông tin đơn hàng nhanh chóng.</p>
                </div>
                <div class="fpt-pill"><i class="fa fa-truck"></i> Đơn hàng online</div>
            </div>

            <?php if (!empty($success) && isset($successMessages[$success])): ?>
                <div class="fpt-alert success"><i class="fa fa-circle-check"></i> <?= e($successMessages[$success]) ?></div>
            <?php endif; ?>
            <?php if (!empty($error) && isset($errorMessages[$error])): ?>
                <div class="fpt-alert error"><i class="fa fa-triangle-exclamation"></i> <?= e($errorMessages[$error]) ?></div>
            <?php endif; ?>

            <div class="fpt-card">
                <div class="filter-wrap">
                    <form class="filter-form" method="GET" action="/admin/don-hang">
                        <select name="trang_thai">
                            <option value="" <?= ($trangThaiFilter ?? '') === '' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                            <option value="CHO_DUYET" <?= ($trangThaiFilter ?? '') === 'CHO_DUYET' ? 'selected' : '' ?>>Chờ duyệt</option>
                            <option value="DANG_GIAO" <?= ($trangThaiFilter ?? '') === 'DANG_GIAO' ? 'selected' : '' ?>>Đang giao</option>
                            <option value="HOAN_THANH" <?= ($trangThaiFilter ?? '') === 'HOAN_THANH' ? 'selected' : '' ?>>Hoàn thành</option>
                            <option value="DA_HUY" <?= ($trangThaiFilter ?? '') === 'DA_HUY' ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                        <button class="btn-filter" type="submit"><i class="fa fa-filter"></i> Lọc đơn hàng</button>
                    </form>
                </div>

                <div class="order-table-wrap">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Trạng thái</th>
                                <th>Tổng thanh toán</th>
                                <th>Ngày tạo</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($danhSachDonHang)): ?>
                                <tr>
                                    <td colspan="7" class="empty-state">Không có đơn hàng.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($danhSachDonHang as $item): ?>
                                    <?php
                                    $statusClass = 'status-cho-duyet';
                                    if (($item['trang_thai'] ?? '') === 'DANG_GIAO') {
                                        $statusClass = 'status-dang-giao';
                                    } elseif (($item['trang_thai'] ?? '') === 'HOAN_THANH') {
                                        $statusClass = 'status-hoan-thanh';
                                    } elseif (($item['trang_thai'] ?? '') === 'DA_HUY') {
                                        $statusClass = 'status-da-huy';
                                    }
                                    ?>
                                    <tr>
                                        <td>#<?= (int)$item['id'] ?></td>
                                        <td><strong><?= e($item['ma_don_hang'] ?? '-') ?></strong></td>
                                        <td>
                                            <div class="customer-name"><?= e($item['ho_ten'] ?? 'Khach vang lai') ?></div>
                                            <div class="customer-email"><?= e($item['email'] ?? '') ?></div>
                                        </td>
                                        <td><span class="status-badge <?= $statusClass ?>"><i class="fa fa-circle"></i> <?= e($item['trang_thai']) ?></span></td>
                                        <td><strong><?= number_format((float)($item['tong_thanh_toan'] ?? 0), 0, ',', '.') ?> VND</strong></td>
                                        <td><?= e($item['ngay_tao'] ?? '-') ?></td>
                                        <td>
                                            <a class="btn-view" href="/admin/don-hang/chi-tiet?id=<?= (int)$item['id'] ?>"><i class="fa fa-eye"></i> Xem</a>
                                        </td>
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