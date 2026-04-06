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
        $keyword = isset($_GET['keyword']) ? trim((string)$_GET['keyword']) : null;
        $keyword = ($keyword === '') ? null : $keyword;

        $danhMucId = isset($_GET['danh_muc']) ? max(0, (int)$_GET['danh_muc']) : 0;

        $giaMin = (isset($_GET['gia_min']) && $_GET['gia_min'] !== '' && is_numeric($_GET['gia_min']))
            ? max(0, (float)$_GET['gia_min'])
            : null;

        $giaMax = (isset($_GET['gia_max']) && $_GET['gia_max'] !== '' && is_numeric($_GET['gia_max']))
            ? max(0, (float)$_GET['gia_max'])
            : null;

        if ($giaMin !== null && $giaMax !== null && $giaMin > $giaMax) {
            [$giaMin, $giaMax] = [$giaMax, $giaMin];
        }

        $sortBy = $_GET['sort_by'] ?? 'ngay_tao';
        $sortOrder = strtoupper($_GET['sort_order'] ?? 'DESC');

        $allowedSortColumns = ['ngay_tao', 'gia_hien_thi', 'ten_san_pham'];
        if (!in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'ngay_tao';
        }

        if (!in_array($sortOrder, ['ASC', 'DESC'], true)) {
            $sortOrder = 'DESC';
        }

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
}
