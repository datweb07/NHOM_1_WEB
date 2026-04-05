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
}
