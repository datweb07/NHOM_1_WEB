<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giám Sát Sức Khỏe Cổng Thanh Toán - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php
                $breadcrumbs = [
                    ['label' => 'Trang chủ', 'url' => '/admin'],
                    ['label' => 'Quản lý thanh toán', 'url' => '/admin/thanh-toan'],
                    ['label' => 'Giám sát sức khỏe', 'url' => '']
                ];
                require_once dirname(__DIR__) . '/layouts/breadcrumb.php';
                ?>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="bi bi-heart-pulse"></i> Giám Sát Sức Khỏe Cổng Thanh Toán
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/admin/thanh-toan" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                
                <div class="row mb-4">
                    <?php foreach ($gatewayMetrics as $metric): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card <?= $metric['has_alert'] ? 'border-danger' : 'border-success' ?>">
                                <div class="card-header <?= $metric['has_alert'] ? 'bg-danger text-white' : 'bg-success text-white' ?>">
                                    <h5 class="mb-0">
                                        <i class="bi bi-credit-card"></i> <?= htmlspecialchars($metric['name']) ?>
                                        <?php if ($metric['has_alert']): ?>
                                            <span class="badge bg-warning text-dark float-end">
                                                <i class="bi bi-exclamation-triangle-fill"></i> Cảnh báo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-success float-end">
                                                <i class="bi bi-check-circle-fill"></i> Hoạt động tốt
                                            </span>
                                        <?php endif; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Success Rate -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-bold">Tỷ lệ thành công (24h)</span>
                                            <span class="badge <?= $metric['success_rate'] >= 90 ? 'bg-success' : ($metric['success_rate'] >= 50 ? 'bg-warning' : 'bg-danger') ?>">
                                                <?= number_format($metric['success_rate'], 2) ?>%
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar <?= $metric['success_rate'] >= 90 ? 'bg-success' : ($metric['success_rate'] >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                                                 role="progressbar" 
                                                 style="width: <?= $metric['success_rate'] ?>%"
                                                 aria-valuenow="<?= $metric['success_rate'] ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?= number_format($metric['success_rate'], 1) ?>%
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <div class="border rounded p-2">
                                                <div class="text-success fw-bold fs-4"><?= number_format($metric['success_count']) ?></div>
                                                <small class="text-muted">Thành công</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border rounded p-2">
                                                <div class="text-danger fw-bold fs-4"><?= number_format($metric['failure_count']) ?></div>
                                                <small class="text-muted">Thất bại</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border rounded p-2">
                                                <div class="text-primary fw-bold fs-4"><?= number_format($metric['total_count']) ?></div>
                                                <small class="text-muted">Tổng số</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-check-circle text-success"></i> Thành công gần nhất:
                                            </small>
                                            <span class="fw-bold">
                                                <?php if ($metric['last_success_at']): ?>
                                                    <?= date('d/m/Y H:i:s', strtotime($metric['last_success_at'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Chưa có</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-x-circle text-danger"></i> Thất bại gần nhất:
                                            </small>
                                            <span class="fw-bold">
                                                <?php if ($metric['last_failure_at']): ?>
                                                    <?= date('d/m/Y H:i:s', strtotime($metric['last_failure_at'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Chưa có</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-top">
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> Thời gian xử lý trung bình:
                                        </small>
                                        <span class="fw-bold ms-2">
                                            <?php if ($metric['avg_processing_time'] > 0): ?>
                                                <?= number_format($metric['avg_processing_time'], 2) ?>s
                                            <?php else: ?>
                                                <span class="text-muted">Chưa có dữ liệu</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <?php if ($metric['has_alert']): ?>
                                        <div class="alert alert-danger mt-3 mb-0" role="alert">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <strong>Cảnh báo:</strong> Tỷ lệ thất bại vượt quá 50% trong 10 giao dịch gần nhất. 
                                            Vui lòng kiểm tra cấu hình cổng thanh toán hoặc liên hệ nhà cung cấp dịch vụ.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin giám sát</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold">Cách hoạt động:</h6>
                        <ul>
                            <li>Hệ thống tự động theo dõi mọi giao dịch với cổng thanh toán (VNPay, Momo)</li>
                            <li>Tỷ lệ thành công được tính dựa trên tổng số giao dịch thành công và thất bại</li>
                            <li>Cảnh báo sẽ xuất hiện khi tỷ lệ thất bại vượt quá 50% trong 10 giao dịch gần nhất</li>
                            <li>Dữ liệu được cập nhật theo thời gian thực sau mỗi giao dịch</li>
                        </ul>

                        <h6 class="fw-bold mt-3">Hành động khi có cảnh báo:</h6>
                        <ul>
                            <li>Kiểm tra cấu hình cổng thanh toán trong file .env</li>
                            <li>Xác minh kết nối mạng đến máy chủ cổng thanh toán</li>
                            <li>Kiểm tra log hệ thống để xem chi tiết lỗi</li>
                            <li>Liên hệ nhà cung cấp dịch vụ thanh toán nếu vấn đề kéo dài</li>
                            <li>Cân nhắc hiển thị thông báo cho khách hàng và đề xuất phương thức thanh toán thay thế</li>
                        </ul>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
