<?php

require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../enums/LoaiTaiKhoan.php';

use App\Core\Session;

class AuthMiddleware
{
    public static function checkMember(): void
    {
        Session::start();
        
        $userRole = Session::getUserRole();
        
        if (!Session::isLoggedIn() || $userRole === null) {
            header('Location: /client/auth/login');
            exit;
        }
        
        if ($userRole === LoaiTaiKhoan::ADMIN) {
            header('Location: /client/auth/login');
            exit;
        }
        
        if ($userRole === LoaiTaiKhoan::MEMBER) {
            return;
        }
        
        header('Location: /client/auth/login');
        exit;
    }
}
