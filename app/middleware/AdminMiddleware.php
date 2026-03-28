<?php

require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../enums/LoaiTaiKhoan.php';

use App\Core\Session;

class AdminMiddleware
{
    public static function checkAdmin(): void
    {
        Session::start();
        
        $userRole = Session::getUserRole();
        
        if (!Session::isLoggedIn() || $userRole === null) {
            header('Location: /admin/auth/login');
            exit;
        }
        
        if ($userRole === LoaiTaiKhoan::MEMBER) {
            Session::set('error_message', 'Không có quyền truy cập');
            header('Location: /');
            exit;
        }

        if ($userRole === LoaiTaiKhoan::ADMIN) {
            return;
        }
        header('Location: /admin/auth/login');
        exit;
    }
}
