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
        
        $adminId = Session::getUserId();
        if ($adminId) {
            try {
                require_once __DIR__ . '/../../services/redis/RedisService.php';
                require_once __DIR__ . '/../../services/notification/NotificationService.php';
                require_once __DIR__ . '/../../models/entities/DonHang.php';
                require_once __DIR__ . '/../../models/entities/ThanhToan.php';
                require_once __DIR__ . '/../../models/entities/Refund.php';
                require_once __DIR__ . '/../../models/entities/PhienBanSanPham.php';
                require_once __DIR__ . '/../../models/entities/DanhGia.php';
                require_once __DIR__ . '/../../models/entities/TransactionLog.php';
                require_once __DIR__ . '/../../models/entities/GatewayHealth.php';
                require_once __DIR__ . '/../../models/entities/MaGiamGia.php';
                
                $redis = \RedisService::getInstance();
                $donHangModel = new \DonHang();
                $thanhToanModel = new \ThanhToan();
                $refundModel = new \Refund();
                $phienBanModel = new \PhienBanSanPham();
                $danhGiaModel = new \DanhGia();
                $transactionLogModel = new \TransactionLog();
                $gatewayHealthModel = new \GatewayHealth();
                $maGiamGiaModel = new \MaGiamGia();
                
                $notificationService = new \NotificationService(
                    $redis,
                    $donHangModel,
                    $thanhToanModel,
                    $refundModel,
                    $phienBanModel,
                    $danhGiaModel,
                    $transactionLogModel,
                    $gatewayHealthModel,
                    $maGiamGiaModel
                );
                
                $notificationService->clearNotificationState($adminId);
            } catch (\Exception $e) {
                error_log('[AuthController] Failed to clear notification state: ' . $e->getMessage());
            }
        }
        
        Session::logout();
        
        header('Location: /admin/auth/login');
        exit;
    }
}
