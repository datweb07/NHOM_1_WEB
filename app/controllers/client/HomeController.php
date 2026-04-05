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
        // Lấy banner
        $banners = $this->bannerController->layBannerTrangChu();
        $bannerHero = $banners['bannerHero'];
        $bannerSide = $banners['bannerSide'];
        $bannerMid  = $banners['bannerMid'];
        
        // Lấy sản phẩm nổi bật & khuyến mãi
        $sanPhamNoiBat = $this->sanPhamModel->laySanPhamNoiBat(8);
        $sanPhamKhuyenMai = $this->sanPhamModel->laySanPhamKhuyenMai(8);
        
        // --- PHẦN CẬP NHẬT DANH MỤC ---
        // Lấy danh mục nổi bật (16 cái)
        $danhMucNoiBat = $this->danhMucModel->layDanhMucNoiBat(16);
        
        // Lấy danh mục gợi ý (30 cái)
        $danhMucGoiY = $this->danhMucModel->layDanhMucGoiY(30);
        // ------------------------------
        
        // Lấy sản phẩm theo danh mục
        $sanPhamDienThoai = $this->sanPhamModel->laySanPhamTheoDanhMuc('dien-thoai', 8);
        $sanPhamLaptop    = $this->sanPhamModel->laySanPhamTheoDanhMuc('laptop', 8);
        $sanPhamPhuKien   = $this->sanPhamModel->laySanPhamTheoDanhMuc('phu-kien', 12);

        // Load view
        require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
    }
}