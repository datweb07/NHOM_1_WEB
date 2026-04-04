<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/BannerQuangCao.php';
require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/DanhMuc.php';
require_once dirname(__DIR__, 2) . '/models/entities/KhuyenMai.php';

use BannerQuangCao;
use SanPham;
use DanhMuc;
use KhuyenMai;

class HomeController
{
    private BannerQuangCao $bannerModel;
    private SanPham $sanPhamModel;
    private DanhMuc $danhMucModel;
    private KhuyenMai $khuyenMaiModel;

    public function __construct()
    {
        $this->bannerModel = new BannerQuangCao();
        $this->sanPhamModel = new SanPham();
        $this->danhMucModel = new DanhMuc();
        $this->khuyenMaiModel = new KhuyenMai();
    }

    public function index(): void
    {
        // Lấy banner theo vị trí
        $bannerHero = $this->bannerModel->layBannerTheoViTri('HOME_HERO');
        $bannerSide = $this->bannerModel->layBannerTheoViTri('HOME_SIDE');
        $bannerMid  = $this->bannerModel->layBannerTheoViTri('HOME_MID');
        
        // Lấy sản phẩm nổi bật
        $sanPhamNoiBat = $this->sanPhamModel->laySanPhamNoiBat(8);
        
        // Lấy sản phẩm khuyến mãi
        $sanPhamKhuyenMai = $this->sanPhamModel->laySanPhamKhuyenMai(8);
        
        // Lấy danh mục hoạt động
        $danhMucList = $this->danhMucModel->layDanhMucHienThi(12);
        
        // Lấy sản phẩm theo danh mục (Điện thoại, Laptop, Phụ kiện)
        $sanPhamDienThoai = $this->sanPhamModel->laySanPhamTheoDanhMuc('dien-thoai', 8);
        $sanPhamLaptop    = $this->sanPhamModel->laySanPhamTheoDanhMuc('laptop', 8);
        $sanPhamPhuKien   = $this->sanPhamModel->laySanPhamTheoDanhMuc('phu-kien', 12);

        // Load view
        require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
    }
}
