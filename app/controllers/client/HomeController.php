<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/controllers/client/BannerController.php';
require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/DanhMuc.php';
require_once dirname(__DIR__, 2) . '/models/entities/KhuyenMai.php';

use App\Controllers\Client\BannerController;
use SanPham;
use DanhMuc;
use KhuyenMai;

class HomeController
{
    private BannerController $bannerController;
    private SanPham $sanPhamModel;
    private DanhMuc $danhMucModel;
    private KhuyenMai $khuyenMaiModel;

    public function __construct()
    {
        $this->bannerController = new BannerController();
        $this->sanPhamModel = new SanPham();
        $this->danhMucModel = new DanhMuc();
        $this->khuyenMaiModel = new KhuyenMai();
    }

    public function index(): void
    {
        $banners = $this->bannerController->layBannerTrangChu();
        $bannerHero = $banners['bannerHero'];
        $bannerSide = $banners['bannerSide'];
        $bannerMid  = $banners['bannerMid'];
        
        $sanPhamNoiBat = $this->sanPhamModel->laySanPhamNoiBat(8);
        $sanPhamKhuyenMai = $this->sanPhamModel->laySanPhamKhuyenMai(8);

        $danhMucNoiBat = $this->danhMucModel->layDanhMucNoiBat(16);
        
        $danhMucGoiY = $this->danhMucModel->layDanhMucGoiY(30);

        $sanPhamDienThoai = $this->sanPhamModel->laySanPhamTheoDanhMuc('dien-thoai', 8);
        $sanPhamLaptop    = $this->sanPhamModel->laySanPhamTheoDanhMuc('may-tinh-xach-tay', 8);
        $sanPhamPhuKien   = $this->sanPhamModel->laySanPhamTheoDanhMuc('phu-kien', 12);

        require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
    }
}