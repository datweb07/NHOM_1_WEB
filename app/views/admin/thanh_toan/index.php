<?php
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$successMessages = [
    'approved' => 'Da duyet thanh toan thanh cong.',
    'rejected' => 'Da tu choi thanh toan.',
];

$errorMessages = [
    'invalid_id' => 'ID thanh toan khong hop le.',
    'not_found' => 'Khong tim thay thanh toan.',
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thanh toán</title>
    <script src="https://kit.fontawesome.com/1f55434e39.js" crossorigin="anonymous"></script>
    <link rel="icon" href="/public/assets/client/images/header/1.png">
    <link rel="stylesheet" href="/public/assets/client/css/main.css">
    <link rel="stylesheet" href="/public/assets/client/css/grid.css">
    <link rel="stylesheet" href="/public/assets/client/css/reponsive.css">
    <style>
        .admin-payment-page {
            background: linear-gradient(180deg, #f7f8fb 0%, #eef1f7 100%);
            min-height: 100vh;
            padding: 24px 0 40px;
        }

        .payment-page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .payment-page-head h1 {
            font-size: 26px;
            margin: 0;
            color: #1f2937;
            font-weight: 700;
        }

        .payment-page-head .head-sub {
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

        .pagination-wrap {
            padding: 16px;
            border-top: 1px solid #edf1f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .pagination-info {
            font-size: 14px;
            color: #667085;
        }

        .pagination-controls {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .page-btn {
            border: 1px solid #d0d5dd;
            color: #344054;
            background: #fff;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .page-btn:hover:not(.disabled):not(.active) {
            border-color: #cb1c22;
            color: #cb1c22;
        }

        .page-btn.active {
            background: #cb1c22;
            color: #fff;
            border-color: #cb1c22;
        }

        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .payment-table-wrap {
            overflow-x: auto;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        .payment-table thead th {
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

        .payment-table tbody td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #344054;
            vertical-align: top;
        }

        .payment-table tbody tr:hover {
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

        .method-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #d0d5dd;
            background: #f9fafb;
            color: #344054;
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
            .admin-payment-page {
                padding-top: 14px;
            }

            .payment-page-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .pagination-wrap {
                flex-direction: column;
                align-items: flex-start;
            }

            .pagination-controls {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>
    <div class="admin-payment-page">
        <div class="grid wide">
            <div class="payment-page-head">
                <div>
                    <h1>Danh sách thanh toán chờ duyệt</h1>
                    <p class="head-sub">Xem xét và duyệt các giao dịch chuyển khoản từ khách hàng.</p>
                </div>
                <div class="fpt-pill"><i class="fa fa-clock"></i> Chờ duyệt</div>
            </div>

            <?php if (!empty($success) && isset($successMessages[$success])): ?>
                <div class="fpt-alert success"><i class="fa fa-circle-check"></i> <?= e($successMessages[$success]) ?></div>
            <?php endif; ?>
            <?php if (!empty($error) && isset($errorMessages[$error])): ?>
                <div class="fpt-alert error"><i class="fa fa-triangle-exclamation"></i> <?= e($errorMessages[$error]) ?></div>
            <?php endif; ?>

            <div class="fpt-card">
                <div class="payment-table-wrap">
                    <table class="payment-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Phương thức</th>
                                <th>Số tiền</th>
                                <th>Ngày thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($danhSachThanhToan)): ?>
                                <tr>
                                    <td colspan="8" class="empty-state">Không có thanh toán chờ duyệt.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($danhSachThanhToan as $item): ?>
                                    <tr>
                                        <td>#<?= (int)$item['id'] ?></td>
                                        <td><strong><?= e($item['ma_don_hang'] ?? '-') ?></strong></td>
                                        <td>
                                            <div class="customer-name"><?= e($item['customer_name'] ?? 'Khach vang lai') ?></div>
                                            <div class="customer-email"><?= e($item['customer_email'] ?? '') ?></div>
                                        </td>
                                        <td><span class="method-badge"><i class="fa fa-credit-card"></i> <?= e($item['phuong_thuc'] ?? '-') ?></span></td>
                                        <td><strong><?= number_format((float)($item['so_tien'] ?? 0), 0, ',', '.') ?> VND</strong></td>
                                        <td><?= e($item['ngay_thanh_toan'] ?? '-') ?></td>
                                        <td><span class="status-badge status-cho-duyet"><i class="fa fa-clock"></i> <?= e($item['trang_thai_duyet'] ?? '-') ?></span></td>
                                        <td>
                                            <a class="btn-view" href="/admin/thanh-toan/chi-tiet?id=<?= (int)$item['id'] ?>"><i class="fa fa-eye"></i> Xem</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (($totalPages ?? 1) > 1): ?>
                    <div class="pagination-wrap">
                        <div class="pagination-info">
                            Hiển thị <?= count($danhSachThanhToan) ?> / <?= $totalRecords ?? 0 ?> thanh toán
                        </div>
                        <div class="pagination-controls">
                            <?php
                            $currentPage = $currentPage ?? 1;
                            $totalPages = $totalPages ?? 1;
                            ?>
                            
                            <?php if ($currentPage > 1): ?>
                                <a class="page-btn" href="/admin/thanh-toan?page=<?= $currentPage - 1 ?>"><i class="fa fa-chevron-left"></i> Trước</a>
                            <?php else: ?>
                                <span class="page-btn disabled"><i class="fa fa-chevron-left"></i> Trước</span>
                            <?php endif; ?>
                            
                            <?php
                            // Show page numbers
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
                            if ($startPage > 1): ?>
                                <a class="page-btn" href="/admin/thanh-toan?page=1">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span class="page-btn disabled">...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i == $currentPage): ?>
                                    <span class="page-btn active"><?= $i ?></span>
                                <?php else: ?>
                                    <a class="page-btn" href="/admin/thanh-toan?page=<?= $i ?>"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="page-btn disabled">...</span>
                                <?php endif; ?>
                                <a class="page-btn" href="/admin/thanh-toan?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                            <?php endif; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a class="page-btn" href="/admin/thanh-toan?page=<?= $currentPage + 1 ?>">Sau <i class="fa fa-chevron-right"></i></a>
                            <?php else: ?>
                                <span class="page-btn disabled">Sau <i class="fa fa-chevron-right"></i></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
