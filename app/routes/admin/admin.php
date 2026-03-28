<?php

function adminRoute(string $uri): void
{
    $path = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($path === 'admin' || $path === 'admin/') {
        header('Location: /admin/danh-muc');
        exit;
    }

    if ($path === 'admin/auth/login') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once dirname(__DIR__, 2) . '/controllers/admin/AuthController.php';
            \App\Controllers\Admin\AuthController::login($_POST['email'] ?? '', $_POST['password'] ?? '');
            return;
        }
        require_once dirname(__DIR__, 2) . '/views/admin/auth/login.php';
        return;
    }

    if ($path === 'admin/auth/logout') {
        require_once dirname(__DIR__, 2) . '/controllers/admin/AuthController.php';
        \App\Controllers\Admin\AuthController::logout();
        return;
    }

    if ($path === 'admin/dashboard') {
        require_once dirname(__DIR__, 2) . '/views/admin/dashboard/index.php';
        return;
    }

    require_once dirname(__DIR__, 2) . '/controllers/admin/DanhMucController.php';
    $danhMucController = new DanhMucController();
    require_once dirname(__DIR__, 2) . '/controllers/admin/DonHangController.php';
    $donHangController = new DonHangController();

    if ($path === 'admin/danh-muc' && $method === 'GET') {
        $danhMucController->index();
        return;
    }

    if ($path === 'admin/danh-muc/them') {
        if ($method === 'POST') {
            $danhMucController->store();
            return;
        }
        $danhMucController->create();
        return;
    }

    if ($path === 'admin/danh-muc/sua') {
        $id = $_GET['id'] ?? null;
        if ($method === 'POST') {
            $danhMucController->update($id);
            return;
        }
        $danhMucController->edit($id);
        return;
    }

    if ($path === 'admin/danh-muc/xoa') {
        $id = $_GET['id'] ?? null;
        $danhMucController->xoa($id);
        return;
    }

    if ($path === 'admin/danh-muc/hien') {
        $id = $_GET['id'] ?? null;
        $danhMucController->hien($id);
        return;
    }

    if ($path === 'admin/don-hang' && $method === 'GET') {
        $donHangController->index();
        return;
    }

    if ($path === 'admin/don-hang/chi-tiet' && $method === 'GET') {
        $id = $_GET['id'] ?? null;
        $donHangController->detail($id);
        return;
    }

    if ($path === 'admin/don-hang/cap-nhat-trang-thai' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $donHangController->capNhatTrangThai($id);
        return;
    }

    http_response_code(404);
    require_once dirname(__DIR__, 2) . '/views/errors/404.php';
}
