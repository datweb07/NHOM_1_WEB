<?php

function adminRoute(string $uri): void
{
    $path = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($path === 'admin' || $path === 'admin/') {
        header('Location: /admin/danh-muc');
        exit;
    }

    require_once dirname(__DIR__, 2) . '/controllers/admin/DanhMucController.php';
    $danhMucController = new DanhMucController();

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

    http_response_code(404);
    require_once dirname(__DIR__, 2) . '/views/errors/404.php';
}
