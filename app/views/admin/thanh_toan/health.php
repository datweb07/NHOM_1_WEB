<?php

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin'],
        ['label' => 'Quản lý thanh toán', 'url' => '/admin/thanh-toan'],
        ['label' => 'Giám sát Cổng thanh toán', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="/admin/thanh-toan" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle me-1"></i> <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <?php foreach ($gatewayMetrics as $metric): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 <?= $metric['has_alert'] ? 'border-start border-4 border-danger' : 'border-start border-4 border-success' ?>">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                <h5 class="mb-0 fw-bold <?= $metric['has_alert'] ? 'text-danger' : 'text-success' ?>">
                                    <i class="bi <?= $metric['has_alert'] ? 'bi-exclamation-octagon' : 'bi-check-circle' ?> me-2"></i>
                                    <?= htmlspecialchars($metric['name']) ?>
                                </h5>
                                <?php if ($metric['has_alert']): ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Đang cảnh báo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-shield-check"></i> Ổn định
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted fw-medium">Tỷ lệ thành công (24h)</span>
                                        <?php
                                            $rateColor = 'bg-danger';
                                            if ($metric['success_rate'] >= 90) $rateColor = 'bg-success';
                                            elseif ($metric['success_rate'] >= 50) $rateColor = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?= $rateColor ?>">
                                            <?= number_format($metric['success_rate'], 2) ?>%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar <?= str_replace('text-dark', '', $rateColor) ?>" 
                                             role="progressbar" 
                                             style="width: <?= $metric['success_rate'] ?>%"
                                             aria-valuenow="<?= $metric['success_rate'] ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <div class="row text-center mb-4 g-2">
                                    <div class="col-4">
                                        <div class="bg-light rounded p-3 h-100">
                                            <div class="text-success fw-bold fs-4 mb-1"><?= number_format($metric['success_count']) ?></div>
                                            <small class="text-muted text-uppercase" style="font-size: 11px;">Thành công</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="bg-light rounded p-3 h-100">
                                            <div class="text-danger fw-bold fs-4 mb-1"><?= number_format($metric['failure_count']) ?></div>
                                            <small class="text-muted text-uppercase" style="font-size: 11px;">Thất bại</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="bg-light rounded p-3 h-100 border border-secondary border-opacity-25">
                                            <div class="text-primary fw-bold fs-4 mb-1"><?= number_format($metric['total_count']) ?></div>
                                            <small class="text-muted text-uppercase" style="font-size: 11px;">Tổng số</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 text-success rounded p-2 me-3">
                                                <i class="bi bi-clock-history fs-5"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block" style="font-size: 12px;">Thành công gần nhất</small>
                                                <span class="fw-medium" style="font-size: 14px;">
                                                    <?= $metric['last_success_at'] ? date('d/m/Y H:i:s', strtotime($metric['last_success_at'])) : '<span class="fst-italic text-muted">Chưa có</span>' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger bg-opacity-10 text-danger rounded p-2 me-3">
                                                <i class="bi bi-x-octagon fs-5"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block" style="font-size: 12px;">Thất bại gần nhất</small>
                                                <span class="fw-medium" style="font-size: 14px;">
                                                    <?= $metric['last_failure_at'] ? date('d/m/Y H:i:s', strtotime($metric['last_failure_at'])) : '<span class="fst-italic text-muted">Chưa có</span>' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3 text-muted">

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        <i class="bi bi-speedometer2 me-1"></i> Thời gian xử lý TB:
                                    </span>
                                    <span class="fw-bold text-dark">
                                        <?= $metric['avg_processing_time'] > 0 ? number_format($metric['avg_processing_time'], 2) . 's' : '<span class="text-muted fw-normal fst-italic">N/A</span>' ?>
                                    </span>
                                </div>

                                <?php if ($metric['has_alert']): ?>
                                    <div class="alert alert-danger mt-3 mb-0 py-2 d-flex align-items-center" role="alert">
                                        <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
                                        <div style="font-size: 13px;">
                                            <strong>Cảnh báo khẩn:</strong> Tỷ lệ thất bại đang vượt mức an toàn (>50% trong 10 giao dịch gần nhất). Cần kiểm tra kết nối API ngay!
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title text-primary text-uppercase mb-0 fw-bold">
                        <i class="bi bi-info-circle-fill me-2"></i>Tài liệu hệ thống giám sát
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6 class="fw-bold text-dark"><i class="bi bi-gear me-2"></i>Cách hệ thống hoạt động</h6>
                            <ul class="text-muted small" style="line-height: 1.8;">
                                <li>Hệ thống tự động theo dõi và ghi nhận mọi giao dịch (Request/Response) với các cổng thanh toán.</li>
                                <li>Tỷ lệ thành công (Success Rate) được tính toán theo thời gian thực dựa trên tổng số giao dịch.</li>
                                <li>Cảnh báo (Alert) sẽ tự động kích hoạt khi tỷ lệ thất bại vượt ngưỡng <strong>50%</strong> trong chuỗi 10 giao dịch gần nhất.</li>
                                <li>Dữ liệu trên trang này sẽ tự động tải lại sau mỗi 30 giây để đảm bảo tính cập nhật.</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark"><i class="bi bi-wrench-adjustable me-2"></i>Quy trình xử lý sự cố (SOP)</h6>
                            <ul class="text-muted small" style="line-height: 1.8;">
                                <li><strong class="text-danger">B1:</strong> Kiểm tra trạng thái cấu hình Secret Key, Partner Code trong file <code>.env</code>.</li>
                                <li><strong class="text-danger">B2:</strong> Kiểm tra log hệ thống (Storage/Logs) để trích xuất mã lỗi cụ thể từ Gateway.</li>
                                <li><strong class="text-danger">B3:</strong> Tạm thời vô hiệu hóa cổng thanh toán đang lỗi trong cài đặt để tránh rủi ro cho khách hàng.</li>
                                <li><strong class="text-danger">B4:</strong> Liên hệ đội ngũ Support kỹ thuật của đối tác (VNPay).</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div> </div> </main>

<script>
    //reload trang sau 30 giây để lấy dữ liệu 
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>