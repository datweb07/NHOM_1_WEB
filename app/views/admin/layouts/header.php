<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
        integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/admin/css/admin.css" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/admin/css/notifications.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous" />
    <style>
    .nav-sidebar .nav-item.menu-open>.nav-treeview {
        display: block !important;
        height: auto !important;
        overflow: visible !important;
    }

    .sidebar-menu .nav-item.menu-open>.nav-treeview {
        display: block !important;
    }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                            <i class="bi bi-search"></i>
                        </a>
                    </li>

                    <li class="nav-item dropdown notification-dropdown">
                        <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button">
                            <i class="bi bi-bell-fill"></i>
                            <span class="navbar-badge badge text-bg-danger notification-badge"
                                style="display: none;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <span class="dropdown-item dropdown-header">Thông báo</span>
                            <div class="dropdown-divider"></div>
                            <div class="notification-dropdown-content">
                                <div class="dropdown-item text-center text-muted py-3">Đang tải...</div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="/admin/notifications" class="dropdown-item dropdown-footer text-center">Xem tất
                                cả</a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                        </a>
                    </li>

                    <li class="nav-item dropdown user-menu">
                        <?php
              use App\Core\Session;
              Session::start();
              $adminName = Session::getUserName() ?? 'Admin';
              $adminEmail = Session::getUserEmail() ?? '';
              $adminAvatar = Session::getUserAvatar();
              
              if ($adminAvatar && file_exists(__DIR__ . '/../../../../public/uploads/avatars/' . $adminAvatar)) {
                  $avatarUrl = '/public/uploads/avatars/' . $adminAvatar;
              } else {
                  $initials = '';
                  $nameParts = explode(' ', $adminName);
                  foreach ($nameParts as $part) {
                      if (!empty($part)) {
                          $initials .= mb_substr($part, 0, 1);
                      }
                  }
                  $initials = mb_strtoupper(mb_substr($initials, 0, 2));
                  $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&background=0d6efd&color=fff&size=160';
              }
              ?>
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="<?= htmlspecialchars($avatarUrl) ?>" class="user-image rounded-circle shadow"
                                alt="User Image" />
                            <span class="d-none d-md-inline"><?= htmlspecialchars($adminName) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="<?= htmlspecialchars($avatarUrl) ?>" class="rounded-circle shadow"
                                    alt="User Image" />
                                <p>
                                    <?= htmlspecialchars($adminName) ?>
                                    <small><?= htmlspecialchars($adminEmail) ?></small>
                                </p>
                            </li>

                            <li class="user-body">
                            </li>

                            <li class="user-footer">
                                <a href="/admin/profile" class="btn btn-default btn-flat">Hồ sơ</a>
                                <a href="/admin/auth/logout" class="btn btn-default btn-flat float-end">Đăng xuất</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>