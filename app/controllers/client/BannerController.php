<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/BannerQuangCao.php';

use BannerQuangCao;

class BannerController
{
    private BannerQuangCao $bannerModel;

    public function __construct()
    {
        $this->bannerModel = new BannerQuangCao();
    }

    /**
     * Lấy banner theo vị trí
     */
    public function layBannerTheoViTri(string $viTri): array
    {
        return $this->bannerModel->layBannerTheoViTri($viTri);
    }

    /**
     * Lấy tất cả banner cho trang chủ
     */
    public function layBannerTrangChu(): array
    {
        return [
            'bannerHero' => $this->bannerModel->layBannerTheoViTri('HOME_HERO'),
            'bannerSide' => $this->bannerModel->layBannerTheoViTri('HOME_SIDE'),
            'bannerMid'  => $this->bannerModel->layBannerTheoViTri('HOME_MID'),
        ];
    }

    /**
     * Lấy banner popup
     */
    public function layBannerPopup(): array
    {
        return $this->bannerModel->layBannerTheoViTri('POPUP');
    }

    /**
     * Ẩn popup banner trong session
     */
    public function hidePopup(): void
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $bannerId = $data['banner_id'] ?? null;

        if (!$bannerId) {
            echo json_encode(['success' => false, 'message' => 'Banner ID is required']);
            exit;
        }

        // Lưu vào session
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['popup_shown_' . $bannerId] = true;

        echo json_encode(['success' => true, 'message' => 'Popup hidden successfully']);
        exit;
    }
}
