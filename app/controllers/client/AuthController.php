<?php

namespace App\Controllers\Client;

require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../models/roles/KhachHang.php';

use App\Core\Session;

class AuthController
{
    public static function login(string $email, string $password): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /client/auth/login?error=invalid_email');
            exit;
        }

        if (empty(trim($password))) {
            header('Location: /client/auth/login?error=empty_password');
            exit;
        }

        //check database
        $khachHang = new \KhachHang();
        if ($khachHang->dang_nhap($email, $password)) {
            Session::start();
            
            $userData = [
                'id' => $khachHang->getId(),
                'email' => $khachHang->getEmail(),
                'ho_ten' => $khachHang->getHoTen(),
                'loai_tai_khoan' => 'MEMBER',
                'avatar_url' => $khachHang->getAvatarUrl()
            ];

            Session::login($userData);

            header('Location: /client/profile');
            exit;
        }

        //lỗi
        header('Location: /client/auth/login?error=invalid_credentials');
        exit;
    }

    public static function register(string $email, string $password, string $name): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /client/auth/register?error=invalid_email');
            exit;
        }

        if (empty(trim($password))) {
            header('Location: /client/auth/register?error=empty_password');
            exit;
        }

        if (empty(trim($name))) {
            header('Location: /client/auth/register?error=empty_name');
            exit;
        }

        //thực hiện register
        $khachHang = new \KhachHang();
        $newUserId = $khachHang->dang_ky($email, $password, $name);
        
        if ($newUserId === null) {
            header('Location: /client/auth/register?error=email_exists');
            exit;
        }

        if ($newUserId) {
            Session::start();
            
            $userData = [
                'id' => $khachHang->getId(),
                'email' => $khachHang->getEmail(),
                'ho_ten' => $khachHang->getHoTen(),
                'loai_tai_khoan' => 'MEMBER',
                'avatar_url' => $khachHang->getAvatarUrl()
            ];

            Session::login($userData);

            header('Location: /client/profile');
            exit;
        }

        //lỗi
        header('Location: /client/auth/register?error=registration_failed');
        exit;
    }

    public static function logout(): void
    {
        Session::start();
        Session::logout();
        
        header('Location: /client/auth/login');
        exit;
    }
}
