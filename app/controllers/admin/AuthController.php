<?php

namespace App\Controllers\Admin;

require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../models/roles/QuanTriVien.php';

use App\Core\Session;

class AuthController
{
    public static function login(string $email, string $password): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /admin/auth/login?error=invalid_email');
            exit;
        }

        if (empty(trim($password))) {
            header('Location: /admin/auth/login?error=empty_password');
            exit;
        }

        //check vs database
        $admin = new \QuanTriVien();
        if ($admin->dang_nhap($email, $password)) {
            Session::start();
            
            $userData = [
                'id' => $admin->getId(),
                'email' => $admin->getEmail(),
                'ho_ten' => $admin->getHoTen(),
                'loai_tai_khoan' => 'ADMIN',
                'avatar_url' => $admin->getAvatarUrl()
            ];

            Session::login($userData);

            header('Location: /admin/dashboard');
            exit;
        }

        //lỗi
        header('Location: /admin/auth/login?error=invalid_credentials');
        exit;
    }

    public static function logout(): void
    {
        Session::start();
        Session::logout();
        
        header('Location: /admin/auth/login');
        exit;
    }
}
