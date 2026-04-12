<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/HinhAnhSanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/ThongSoKyThuat.php';
require_once dirname(__DIR__, 2) . '/models/entities/DanhGia.php';

use SanPham;
use PhienBanSanPham;
use HinhAnhSanPham;
use ThongSoKyThuat;
use DanhGia;

class SanPhamController
{
    private SanPham $sanPhamModel;
    private PhienBanSanPham $phienBanModel;
    private HinhAnhSanPham $hinhAnhModel;
    private ThongSoKyThuat $thongSoModel;
    private DanhGia $danhGiaModel;

    public function __construct()
    {
        $this->sanPhamModel = new SanPham();
        $this->phienBanModel = new PhienBanSanPham();
        $this->hinhAnhModel = new HinhAnhSanPham();
        $this->thongSoModel = new ThongSoKyThuat();
        $this->danhGiaModel = new DanhGia();
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function chiTiet(string $slug): void
    {
        // Lấy thông tin sản phẩm
        $sanPham = $this->sanPhamModel->layChiTietTheoSlug($slug);
        
        if (!$sanPham) {
            header('Location: /');
            exit;
        }

        // Lấy hình ảnh sản phẩm
        $hinhAnhList = $this->hinhAnhModel->layHinhAnhTheoSanPham($sanPham['id']);
        
        // Lấy phiên bản sản phẩm
        $phienBanList = $this->phienBanModel->layPhienBanTheoSanPham($sanPham['id']);
        
        // Lấy thông số kỹ thuật
        $thongSoList = $this->thongSoModel->layThongSoTheoSanPham($sanPham['id']);
        
        // Lấy đánh giá
        $danhGiaList = $this->danhGiaModel->layDanhGiaTheoSanPham($sanPham['id'], 5);
        $tongDanhGia = $this->danhGiaModel->demDanhGiaTheoSanPham($sanPham['id']);
        
        // Lấy sản phẩm tương tự (cùng danh mục)
        $sanPhamTuongTu = $this->sanPhamModel->laySanPhamTheoDanhMuc(
            $sanPham['slug_danh_muc'], 
            4
        );

        // Load view
        require_once dirname(__DIR__, 2) . '/views/client/san_pham/detail.php';
    }

    /**
     * Danh sách sản phẩm với filter
     */
    public function danhSach(): void
    {
        $keyword = $_GET['keyword'] ?? null;
        $danhMucId = isset($_GET['danh_muc']) ? (int)$_GET['danh_muc'] : 0;
        $giaMin = isset($_GET['gia_min']) ? (float)$_GET['gia_min'] : null;
        $giaMax = isset($_GET['gia_max']) ? (float)$_GET['gia_max'] : null;
        $sortBy = $_GET['sort_by'] ?? 'ngay_tao';
        $sortOrder = $_GET['sort_order'] ?? 'DESC';
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Đếm tổng số sản phẩm
        $tongSanPham = $this->sanPhamModel->demSanPham($keyword, $danhMucId, $giaMin, $giaMax);
        
        // Lấy danh sách sản phẩm
        $sanPhamList = $this->sanPhamModel->layDanhSachPhanTrang(
            $keyword, 
            $danhMucId, 
            $giaMin, 
            $giaMax, 
            $limit, 
            $offset, 
            $sortBy, 
            $sortOrder
        );
        
        // Tính tổng số trang
        $tongTrang = ceil($tongSanPham / $limit);
        
        // Lấy danh sách danh mục
        $danhMucList = $this->sanPhamModel->layDanhSachDanhMucHoatDong();

        // Load view
        require_once dirname(__DIR__, 2) . '/views/client/san_pham/list.php';
    }

    /**
     * Danh sách sản phẩm theo slug danh mục
     */
    public function danhSachTheoSlug(string $slugDanhMuc): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/DanhMuc.php';
        $danhMucModel = new \DanhMuc();
        
        // Lookup category by slug
        $danhMuc = $danhMucModel->findBySlug($slugDanhMuc);
        
        if (!$danhMuc) {
            header('Location: /');
            exit;
        }
        
        // Get filter parameters
        $keyword = $_GET['keyword'] ?? null;
        $danhMucId = $danhMuc['id']; // Use the ID from slug lookup
        $giaMin = isset($_GET['gia_min']) ? (float)$_GET['gia_min'] : null;
        $giaMax = isset($_GET['gia_max']) ? (float)$_GET['gia_max'] : null;
        $sortBy = $_GET['sort_by'] ?? 'ngay_tao';
        $sortOrder = $_GET['sort_order'] ?? 'DESC';
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Count total products
        $tongSanPham = $this->sanPhamModel->demSanPham($keyword, $danhMucId, $giaMin, $giaMax);
        
        // Get product list
        $sanPhamList = $this->sanPhamModel->layDanhSachPhanTrang(
            $keyword, 
            $danhMucId, 
            $giaMin, 
            $giaMax, 
            $limit, 
            $offset, 
            $sortBy, 
            $sortOrder
        );
        
        // Calculate total pages
        $tongTrang = ceil($tongSanPham / $limit);
        
        // Get category list
        $danhMucList = $this->sanPhamModel->layDanhSachDanhMucHoatDong();

        // Load view
        require_once dirname(__DIR__, 2) . '/views/client/san_pham/list.php';
    }

    /**
     * API: Lấy dữ liệu cho Mega Menu khi hover (Hãng, Sản phẩm, Danh mục con)
     */
    public function apiMegaMenu(): void
    {
        // Xóa bộ nhớ đệm đề phòng có khoảng trắng dư thừa làm hỏng JSON
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Bây giờ mới set Header JSON một cách an toàn
        header('Content-Type: application/json; charset=utf-8');
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            exit;
        }

        try {
            // 1. Lấy 5 sản phẩm mới nhất (Sử dụng Subquery để lấy url_anh từ bảng hinh_anh_san_pham)
            $sqlProducts = "SELECT sp.ten_san_pham, sp.slug, 
                                   (SELECT url_anh FROM hinh_anh_san_pham ha WHERE ha.san_pham_id = sp.id AND ha.la_anh_chinh = 1 LIMIT 1) AS anh_chinh
                            FROM san_pham sp 
                            WHERE sp.danh_muc_id = $id AND sp.trang_thai = 'CON_BAN' 
                            ORDER BY sp.ngay_tao DESC LIMIT 5";
            $products = $this->sanPhamModel->query($sqlProducts);

            // 2. Lấy danh sách Hãng (Thương hiệu) có trong danh mục này
            $sqlBrands = "SELECT DISTINCT hang_san_xuat 
                          FROM san_pham 
                          WHERE danh_muc_id = $id AND hang_san_xuat != '' AND hang_san_xuat IS NOT NULL 
                          LIMIT 6";
            $brands = $this->sanPhamModel->query($sqlBrands);

            // 3. Lấy danh mục con (Cấp 2)
            require_once dirname(__DIR__, 2) . '/models/entities/DanhMuc.php';
            $dmModel = new \DanhMuc();
            $subCategories = $dmModel->layDanhMucCon($id);

            echo json_encode([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'brands' => $brands,
                    'subCategories' => $subCategories
                ]
            ]);
            exit;

        } catch (\Throwable $th) {
            // Bắt lỗi và trả về JSON thay vì in ra HTML làm vỡ giao diện
            echo json_encode([
                'success' => false, 
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            exit;
        }
    }
}
