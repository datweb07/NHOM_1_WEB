<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

?>

<main class="app-main">
    <?php
  require_once __DIR__ . '/../layouts/breadcrumb.php';
  ?>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3><?= htmlspecialchars($pendingOrders ?? 0) ?></h3>
                            <p>Đơn hàng chờ duyệt</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path
                                d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z">
                            </path>
                        </svg>
                        <a href="/admin/don-hang"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Xem chi tiết <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3><?= htmlspecialchars($totalUsers ?? 0) ?></h3>
                            <p>Người dùng</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path
                                d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z">
                            </path>
                        </svg>
                        <a href="/admin/nguoi-dung"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Xem chi tiết <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3><?= htmlspecialchars($activeProducts ?? 0) ?></h3>
                            <p>Sản phẩm đang bán</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path
                                d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z">
                            </path>
                        </svg>
                        <a href="/admin/san-pham"
                            class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                            Xem chi tiết <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3><?= htmlspecialchars($pendingPayments ?? 0) ?></h3>
                            <p>Thanh toán chờ duyệt</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z">
                            </path>
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z">
                            </path>
                        </svg>
                        <a href="/admin/thanh-toan"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Xem chi tiết <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Thống kê tháng này</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info">
                                            <i class="bi bi-cart-check"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Đơn hàng trong tháng</span>
                                            <span
                                                class="info-box-number"><?= htmlspecialchars($monthlyOrders ?? 0) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success">
                                            <i class="bi bi-currency-dollar"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Doanh thu tháng này</span>
                                            <span
                                                class="info-box-number"><?= number_format($monthlyRevenue ?? 0, 0, ',', '.') ?>
                                                ₫</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Quản lý nhanh</h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="/admin/don-hang" class="list-group-item list-group-item-action">
                                    <i class="bi bi-cart"></i> Quản lý đơn hàng
                                </a>
                                <a href="/admin/san-pham" class="list-group-item list-group-item-action">
                                    <i class="bi bi-box"></i> Quản lý sản phẩm
                                </a>
                                <a href="/admin/nguoi-dung" class="list-group-item list-group-item-action">
                                    <i class="bi bi-people"></i> Quản lý người dùng
                                </a>
                                <a href="/admin/danh-muc" class="list-group-item list-group-item-action">
                                    <i class="bi bi-folder"></i> Quản lý danh mục
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../layouts/footer.php';

?>