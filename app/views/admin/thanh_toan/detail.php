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

$thanhToan = (isset($thanhToan) && is_array($thanhToan)) ? $thanhToan : [];
$donHang = (isset($donHang) && is_array($donHang)) ? $donHang : [];
$paymentId = (int)($thanhToan['id'] ?? 0);
$trangThaiDuyet = (string)($thanhToan['trang_thai_duyet'] ?? '');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết thanh toán</title>
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
            grid-template-columns: 1fr 1fr;
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

        .status-badge.success {
            border-color: #abefc6;
            background: #ecfdf3;
            color: #027a48;
        }

        .status-badge.error {
            border-color: #fecdca;
            background: #fff1f3;
            color: #b42318;
        }

        .receipt-section {
            margin-bottom: 14px;
        }

        .receipt-image {
            max-width: 100%;
            border-radius: 12px;
            border: 1px solid #edf1f6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .no-receipt {
            padding: 20px;
            text-align: center;
            color: #667085;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px dashed #d0d5dd;
        }

        .action-section {
            margin-bottom: 14px;
        }

        .action-section .card-body {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .action-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #344054;
        }

        .form-group textarea {
            border: 1px solid #d0d5dd;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-approve {
            border: 1px solid #027a48;
            background: #027a48;
            color: #fff;
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s ease;
        }

        .btn-approve:hover {
            background: #05603a;
            border-color: #05603a;
        }

        .btn-reject {
            border: 1px solid #b42318;
            background: #b42318;
            color: #fff;
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s ease;
        }

        .btn-reject:hover {
            background: #912018;
            border-color: #912018;
        }

        .empty-text {
            color: #667085;
            font-size: 14px;
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

            .action-buttons {
                flex-direction: column;
            }

            .btn-approve,
            .btn-reject {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="admin-detail-page">
        <div class="grid wide">
            <div class="top-bar">
                <h1>Chi tiết thanh toán #<?= $paymentId > 0 ? $paymentId : '-' ?></h1>
                <a class="btn-back" href="/admin/thanh-toan"><i class="fa fa-arrow-left"></i> Quay lại danh sách</a>
            </div>

            <?php if ($paymentId <= 0): ?>
                <div class="fpt-alert error"><i class="fa fa-triangle-exclamation"></i> Khong tim thay du lieu thanh toan hop le.</div>
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
                        <h2 class="card-title">Thông tin thanh toán</h2>
                        <div class="info-list">
                            <div><strong>Mã đơn hàng:</strong> <?= e($donHang['ma_don_hang'] ?? '-') ?></div>
                            <div><strong>Phương thức:</strong> <?= e($thanhToan['phuong_thuc'] ?? '-') ?></div>
                            <div><strong>Số tiền:</strong> <strong><?= number_format((float)($thanhToan['so_tien'] ?? 0), 0, ',', '.') ?> VND</strong></div>
                            <div><strong>Ngày thanh toán:</strong> <?= e($thanhToan['ngay_thanh_toan'] ?? '-') ?></div>
                            <div>
                                <strong>Trạng thái duyệt:</strong> 
                                <?php if ($trangThaiDuyet === 'CHO_DUYET'): ?>
                                    <span class="status-badge"><i class="fa fa-clock"></i> CHO_DUYET</span>
                                <?php elseif ($trangThaiDuyet === 'THANH_CONG'): ?>
                                    <span class="status-badge success"><i class="fa fa-circle-check"></i> THANH_CONG</span>
                                <?php elseif ($trangThaiDuyet === 'THAT_BAI'): ?>
                                    <span class="status-badge error"><i class="fa fa-circle-xmark"></i> THAT_BAI</span>
                                <?php else: ?>
                                    <span class="status-badge"><?= e($trangThaiDuyet) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($thanhToan['ngay_duyet'])): ?>
                                <div><strong>Ngày duyệt:</strong> <?= e($thanhToan['ngay_duyet']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($thanhToan['ghi_chu_duyet'])): ?>
                                <div><strong>Ghi chú duyệt:</strong> <?= e($thanhToan['ghi_chu_duyet']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="fpt-card">
                    <div class="card-body">
                        <h2 class="card-title">Thông tin đơn hàng</h2>
                        <div class="info-list">
                            <div><strong>Khách hàng:</strong> <?= e($donHang['ho_ten'] ?? 'Khách vãng lai') ?></div>
                            <div><strong>Email:</strong> <?= e($donHang['email'] ?? '-') ?></div>
                            <div><strong>Số điện thoại:</strong> <?= e($donHang['sdt'] ?? '-') ?></div>
                            <div><strong>Tổng thanh toán:</strong> <strong><?= number_format((float)($donHang['tong_thanh_toan'] ?? 0), 0, ',', '.') ?> VND</strong></div>
                            <div><strong>Trạng thái đơn:</strong> <?= e($donHang['trang_thai'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fpt-card receipt-section">
                <div class="card-body">
                    <h2 class="card-title">Biên lai thanh toán</h2>
                    <?php if (!empty($thanhToan['anh_bien_lai'])): ?>
                        <img src="<?= e($thanhToan['anh_bien_lai']) ?>" alt="Biên lai thanh toán" class="receipt-image">
                    <?php else: ?>
                        <div class="no-receipt">
                            <i class="fa fa-image" style="font-size: 32px; color: #98a2b3; margin-bottom: 8px;"></i>
                            <p>Chưa có biên lai thanh toán</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($trangThaiDuyet === 'CHO_DUYET'): ?>
                <div class="fpt-card action-section">
                    <div class="card-body">
                        <h2 class="card-title">Duyệt thanh toán</h2>
                        <form class="action-form" id="approvalForm">
                            <div class="form-group">
                                <label for="ghi_chu">Ghi chú (tùy chọn)</label>
                                <textarea id="ghi_chu" name="ghi_chu" placeholder="Nhập ghi chú về quyết định duyệt..."></textarea>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn-approve" onclick="submitApproval('approve')">
                                    <i class="fa fa-circle-check"></i> Duyệt thanh toán
                                </button>
                                <button type="button" class="btn-reject" onclick="submitApproval('reject')">
                                    <i class="fa fa-circle-xmark"></i> Từ chối thanh toán
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="fpt-card action-section">
                    <div class="card-body">
                        <h2 class="card-title">Trạng thái</h2>
                        <div class="empty-text">Thanh toán đã được xử lý, không thể thay đổi trạng thái.</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function submitApproval(action) {
            const ghiChu = document.getElementById('ghi_chu').value;
            const paymentId = <?= $paymentId ?>;
            
            if (action === 'approve') {
                if (confirm('Bạn có chắc chắn muốn duyệt thanh toán này?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/thanh-toan/duyet?id=' + paymentId;
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ghi_chu';
                    input.value = ghiChu;
                    form.appendChild(input);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            } else if (action === 'reject') {
                if (confirm('Bạn có chắc chắn muốn từ chối thanh toán này?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/thanh-toan/tu-choi?id=' + paymentId;
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ghi_chu';
                    input.value = ghiChu;
                    form.appendChild(input);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    </script>
</body>

</html>
