<?php
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<!--begin::App Main-->
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Thông báo</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Thông báo</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!--end::App Content Header-->

    <!--begin::App Content-->
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Danh sách thông báo</h5>
                                <button type="button" class="btn btn-sm btn-primary" id="mark-all-read-btn">
                                    <i class="bi bi-check-all me-1"></i>
                                    Đánh dấu tất cả đã đọc
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter-category" class="form-label">Danh mục</label>
                                    <select class="form-select" id="filter-category">
                                        <option value="all">Tất cả</option>
                                        <option value="orders">Đơn hàng & Thanh toán</option>
                                        <option value="inventory">Kho hàng</option>
                                        <option value="customer">Khách hàng</option>
                                        <option value="system">Hệ thống</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter-priority" class="form-label">Độ ưu tiên</label>
                                    <select class="form-select" id="filter-priority">
                                        <option value="all">Tất cả</option>
                                        <option value="high">Cao</option>
                                        <option value="medium">Trung bình</option>
                                        <option value="low">Thấp</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter-status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="filter-status">
                                        <option value="all">Tất cả</option>
                                        <option value="unread">Chưa đọc</option>
                                        <option value="read">Đã đọc</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-sort" class="form-label">Sắp xếp</label>
                                    <select class="form-select" id="filter-sort">
                                        <option value="time-desc">Mới nhất</option>
                                        <option value="time-asc">Cũ nhất</option>
                                        <option value="priority-desc">Ưu tiên cao → thấp</option>
                                        <option value="priority-asc">Ưu tiên thấp → cao</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-secondary w-100" id="reset-filters-btn">
                                        <i class="bi bi-arrow-clockwise me-1"></i>
                                        Đặt lại
                                    </button>
                                </div>
                            </div>

                            <!-- Loading State -->
                            <div id="loading-state" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <p class="mt-2 text-muted">Đang tải thông báo...</p>
                            </div>

                            <!-- Notification List -->
                            <div id="notification-list" style="display: none;">
                                <!-- Notifications will be rendered here by JavaScript -->
                            </div>

                            <!-- Empty State -->
                            <div id="empty-state" class="text-center py-5" style="display: none;">
                                <i class="bi bi-bell-slash" style="font-size: 3rem; color: #6c757d;"></i>
                                <p class="mt-3 text-muted">Không có thông báo</p>
                            </div>

                            <!-- Pagination -->
                            <div id="pagination-container" class="mt-4" style="display: none;">
                                <nav aria-label="Notification pagination">
                                    <ul class="pagination justify-content-center" id="pagination">
                                        <!-- Pagination will be rendered here by JavaScript -->
                                    </ul>
                                </nav>
                                <div class="text-center text-muted" id="pagination-info">
                                    <!-- Pagination info will be rendered here by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

<!-- Notification List Page JavaScript -->
<script src="<?= ASSET_URL ?>/assets/admin/js/read-status-manager.js"></script>
<script src="<?= ASSET_URL ?>/assets/admin/js/notification-list.js"></script>
<script>
    // Initialize notification list page
    document.addEventListener('DOMContentLoaded', function() {
        window.notificationListPage = new NotificationListPage();
        window.notificationListPage.init();
    });
</script>
