<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/HinhAnhSanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/ThongSoKyThuat.php';
require_once dirname(__DIR__, 2) . '/models/entities/DanhGia.php';
require_once dirname(__DIR__, 2) . '/core/EnvSetup.php';

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

    public function chiTiet(string $slug): void
    {
        $sanPham = $this->sanPhamModel->layChiTietTheoSlug($slug);

        if (!$sanPham) {
            header('Location: /');
            exit;
        }

        $hinhAnhList = $this->hinhAnhModel->layHinhAnhTheoSanPham($sanPham['id']);

        $phienBanList = $this->phienBanModel->layPhienBanTheoSanPham($sanPham['id']);

        $thongSoList = $this->thongSoModel->layThongSoTheoSanPham($sanPham['id']);

        $danhGiaList = $this->danhGiaModel->layDanhGiaTheoSanPham($sanPham['id'], 5);
        $tongDanhGia = $this->danhGiaModel->demDanhGiaTheoSanPham($sanPham['id']);

        $sanPhamTuongTu = $this->sanPhamModel->laySanPhamTheoDanhMuc(
            $sanPham['slug_danh_muc'],
            8
        );

        $danhSachSanPhamSoSanh = $this->sanPhamModel->layDanhSachChoSoSanh();

        $isWishlisted = false;
        if (\App\Core\Session::isLoggedIn() && \App\Core\Session::getUserRole() === 'MEMBER') {
            require_once dirname(__DIR__, 2) . '/models/relationships/YeuThich.php';
            $yeuThichModel = new \YeuThich();
            $userId = \App\Core\Session::getUserId();
            $isWishlisted = $yeuThichModel->kiemTraDaTonTai($userId, $sanPham['id']);
        }

        $sqlKhuyenMai = "
            SELECT km.* FROM khuyen_mai km
            INNER JOIN san_pham_khuyen_mai spkm ON km.id = spkm.khuyen_mai_id
            WHERE spkm.san_pham_id = {$sanPham['id']}
              AND km.trang_thai = 'HOAT_DONG'
              AND (km.ngay_bat_dau IS NULL OR km.ngay_bat_dau <= NOW())
              AND (km.ngay_ket_thuc IS NULL OR km.ngay_ket_thuc >= NOW())
            ORDER BY km.id DESC LIMIT 1
        ";
        $resultKM = $this->sanPhamModel->query($sqlKhuyenMai);
        $khuyenMaiApDung = !empty($resultKM) ? $resultKM[0] : null;

        require_once dirname(__DIR__, 2) . '/views/client/san_pham/detail.php';
    }

    public function danhSach(): void
    {
        $keyword = $_GET['keyword'] ?? ($_GET['q'] ?? null);
        $danhMucId = isset($_GET['danh_muc']) ? (int)$_GET['danh_muc'] : 0;
        $giaMin = isset($_GET['gia_min']) && $_GET['gia_min'] !== '' ? (float)$_GET['gia_min'] : null;
        $giaMax = isset($_GET['gia_max']) && $_GET['gia_max'] !== '' ? (float)$_GET['gia_max'] : null;
        $hangFilters = $_GET['hang'] ?? [];
        $giaKhoangFilters = $_GET['gia_khoang'] ?? [];

        if (!is_array($hangFilters)) {
            $hangFilters = [$hangFilters];
        }
        $hangFilters = array_values(array_unique(array_filter(array_map(static fn($item) => trim((string)$item), $hangFilters), static fn($item) => $item !== '')));

        if (!is_array($giaKhoangFilters)) {
            $giaKhoangFilters = [$giaKhoangFilters];
        }
        $giaKhoangFilters = array_values(array_unique(array_filter(array_map(static fn($item) => trim((string)$item), $giaKhoangFilters), static fn($item) => preg_match('/^(\d+)-(\d+)$/', $item) === 1)));

        if ($giaMin !== null && $giaMax !== null && $giaMin > $giaMax) {
            [$giaMin, $giaMax] = [$giaMax, $giaMin];
        }
        $sortBy = $_GET['sort_by'] ?? 'ngay_tao';
        $sortOrder = $_GET['sort_order'] ?? 'DESC';

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $tongSanPham = $this->sanPhamModel->demSanPham($keyword, $danhMucId, $giaMin, $giaMax, $hangFilters, $giaKhoangFilters);

        $sanPhamList = $this->sanPhamModel->layDanhSachPhanTrang(
            $keyword,
            $danhMucId,
            $giaMin,
            $giaMax,
            $limit,
            $offset,
            $sortBy,
            $sortOrder,
            $hangFilters,
            $giaKhoangFilters
        );

        $tongTrang = ceil($tongSanPham / $limit);

        $danhMucList = $this->sanPhamModel->layDanhSachDanhMucHoatDong();
        $danhSachHang = $this->sanPhamModel->layDanhSachHangSanXuat($keyword, $danhMucId);
        $khoangGia = $this->sanPhamModel->layKhoangGiaSanPham($keyword, $danhMucId, $hangFilters);
        $giaSliderMin = isset($khoangGia['min_gia']) ? (float)$khoangGia['min_gia'] : 0;
        $giaSliderMax = isset($khoangGia['max_gia']) ? (float)$khoangGia['max_gia'] : 0;

        if ($giaMin === null) {
            $giaMin = $giaSliderMin;
        }
        if ($giaMax === null || $giaMax <= 0) {
            $giaMax = $giaSliderMax;
        }

        require_once dirname(__DIR__, 2) . '/views/client/san_pham/list.php';
    }

    public function danhSachTheoSlug(string $slugDanhMuc): void
    {
        $envSetup = new \EnvSetup();
        require_once dirname(__DIR__, 2) . '/models/entities/DanhMuc.php';
        $danhMucModel = new \DanhMuc();

        $danhMuc = $danhMucModel->findBySlug($slugDanhMuc);

        if (!$danhMuc) {
            header('Location: /');
            exit;
        }

        $keyword = $_GET['keyword'] ?? ($_GET['q'] ?? null);
        $danhMucId = $danhMuc['id']; 
        $giaMin = isset($_GET['gia_min']) && $_GET['gia_min'] !== '' ? (float)$_GET['gia_min'] : null;
        $giaMax = isset($_GET['gia_max']) && $_GET['gia_max'] !== '' ? (float)$_GET['gia_max'] : null;
        $hangFilters = $_GET['hang'] ?? [];
        $giaKhoangFilters = $_GET['gia_khoang'] ?? [];

        if (!is_array($hangFilters)) {
            $hangFilters = [$hangFilters];
        }
        $hangFilters = array_values(array_unique(array_filter(array_map(static fn($item) => trim((string)$item), $hangFilters), static fn($item) => $item !== '')));

        if (!is_array($giaKhoangFilters)) {
            $giaKhoangFilters = [$giaKhoangFilters];
        }
        $giaKhoangFilters = array_values(array_unique(array_filter(array_map(static fn($item) => trim((string)$item), $giaKhoangFilters), static fn($item) => preg_match('/^(\d+)-(\d+)$/', $item) === 1)));

        if ($giaMin !== null && $giaMax !== null && $giaMin > $giaMax) {
            [$giaMin, $giaMax] = [$giaMax, $giaMin];
        }
        $sortBy = $_GET['sort_by'] ?? 'ngay_tao';
        $sortOrder = $_GET['sort_order'] ?? 'DESC';

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $tongSanPham = $this->sanPhamModel->demSanPham($keyword, $danhMucId, $giaMin, $giaMax, $hangFilters, $giaKhoangFilters);

        $sanPhamList = $this->sanPhamModel->layDanhSachPhanTrang(
            $keyword,
            $danhMucId,
            $giaMin,
            $giaMax,
            $limit,
            $offset,
            $sortBy,
            $sortOrder,
            $hangFilters,
            $giaKhoangFilters
        );

        $tongTrang = ceil($tongSanPham / $limit);

        $danhMucList = $this->sanPhamModel->layDanhSachDanhMucHoatDong();
        $danhSachHang = $this->sanPhamModel->layDanhSachHangSanXuat($keyword, $danhMucId);
        $khoangGia = $this->sanPhamModel->layKhoangGiaSanPham($keyword, $danhMucId, $hangFilters);
        $giaSliderMin = isset($khoangGia['min_gia']) ? (float)$khoangGia['min_gia'] : 0;
        $giaSliderMax = isset($khoangGia['max_gia']) ? (float)$khoangGia['max_gia'] : 0;

        if ($giaMin === null) {
            $giaMin = $giaSliderMin;
        }
        if ($giaMax === null || $giaMax <= 0) {
            $giaMax = $giaSliderMax;
        }

        require_once dirname(__DIR__, 2) . '/views/client/san_pham/list.php';
    }

    public function soSanh(): void
    {
        $selectedSlugs = $_GET['slug'] ?? [];
        if (!is_array($selectedSlugs)) {
            $selectedSlugs = [$selectedSlugs];
        }

        $selectedSlugs = array_values(array_unique(array_filter(array_map(static function ($item) {
            $slug = trim((string)$item);
            return preg_match('/^[a-z0-9-]+$/', $slug) ? $slug : '';
        }, $selectedSlugs))));

        $danhSachSanPham = $this->sanPhamModel->layDanhSachChoSoSanh();
        $sanPhamSoSanh = [];
        $thongSoTheoSanPham = [];
        $tenThongSoMap = [];
        $compareValidationMessage = '';
        $danhMucSoSanhId = null;

        foreach ($selectedSlugs as $slug) {
            $sanPham = $this->sanPhamModel->layChiTietTheoSlug($slug);
            if (!$sanPham) {
                continue;
            }

            $danhMucId = isset($sanPham['danh_muc_id']) ? (int)$sanPham['danh_muc_id'] : 0;
            if ($danhMucSoSanhId === null && $danhMucId > 0) {
                $danhMucSoSanhId = $danhMucId;
            }

            if ($danhMucSoSanhId !== null && $danhMucId > 0 && $danhMucId !== $danhMucSoSanhId) {
                $compareValidationMessage = 'Chỉ có thể so sánh các sản phẩm cùng danh mục. Một số sản phẩm khác danh mục đã bị bỏ qua.';
                continue;
            }

            $phienBanList = $this->phienBanModel->layPhienBanTheoSanPham((int)$sanPham['id']);
            $phienBanMacDinh = null;
            $tongTonKho = 0;

            if (!empty($phienBanList)) {
                usort($phienBanList, static function (array $a, array $b): int {
                    return ((float)($a['gia_ban'] ?? 0)) <=> ((float)($b['gia_ban'] ?? 0));
                });

                $phienBanMacDinh = $phienBanList[0];
                foreach ($phienBanList as $pb) {
                    $tongTonKho += max(0, (int)($pb['so_luong_ton'] ?? 0));
                }
            }

            $thongSoRows = $this->thongSoModel->layThongSoTheoSanPham((int)$sanPham['id']);
            foreach ($thongSoRows as $row) {
                $tenThongSo = trim((string)($row['ten_thong_so'] ?? ''));
                if ($tenThongSo === '') {
                    continue;
                }

                $thongSoTheoSanPham[(int)$sanPham['id']][$tenThongSo] = (string)($row['gia_tri'] ?? '-');
                $tenThongSoMap[$tenThongSo] = true;
            }

            $sanPham['phien_ban_mac_dinh'] = $phienBanMacDinh;
            $sanPham['tong_ton_kho'] = $tongTonKho;
            $sanPhamSoSanh[] = $sanPham;
        }

        $tenThongSo = array_values(array_keys($tenThongSoMap));

        require_once dirname(__DIR__, 2) . '/views/client/san_pham/compare.php';
    }

    public function apiMegaMenu(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            exit;
        }

        try {
            $sqlProducts = "SELECT sp.ten_san_pham, sp.slug, 
                                   (SELECT url_anh FROM hinh_anh_san_pham ha WHERE ha.san_pham_id = sp.id AND ha.la_anh_chinh = 1 LIMIT 1) AS anh_chinh
                            FROM san_pham sp 
                            WHERE sp.danh_muc_id = $id AND sp.trang_thai = 'CON_BAN' 
                            ORDER BY sp.ngay_tao DESC LIMIT 5";
            $products = $this->sanPhamModel->query($sqlProducts);

            $sqlBrands = "SELECT DISTINCT hang_san_xuat 
                          FROM san_pham 
                          WHERE danh_muc_id = $id AND hang_san_xuat != '' AND hang_san_xuat IS NOT NULL 
                          LIMIT 6";
            $brands = $this->sanPhamModel->query($sqlBrands);

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
            echo json_encode([
                'success' => false,
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            exit;
        }
    }

    public function apiSoSanhTheoSlug(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');

        $slugs = $_GET['slug'] ?? [];
        if (!is_array($slugs)) {
            $slugs = [$slugs];
        }

        $slugs = array_values(array_unique(array_filter(array_map(static function ($item) {
            $slug = trim((string)$item);
            return preg_match('/^[a-z0-9-]+$/', $slug) ? $slug : '';
        }, $slugs))));

        if (count($slugs) < 2) {
            echo json_encode([
                'success' => false,
                'message' => 'Cần ít nhất 2 sản phẩm để so sánh.'
            ]);
            exit;
        }

        $products = [];
        $specNameMap = [];
        $danhMucSoSanhId = null;

        foreach ($slugs as $slug) {
            $sanPham = $this->sanPhamModel->layChiTietTheoSlug($slug);
            if (!$sanPham) {
                continue;
            }

            $danhMucId = isset($sanPham['danh_muc_id']) ? (int)$sanPham['danh_muc_id'] : 0;
            if ($danhMucSoSanhId === null && $danhMucId > 0) {
                $danhMucSoSanhId = $danhMucId;
            }

            if ($danhMucSoSanhId !== null && $danhMucId > 0 && $danhMucId !== $danhMucSoSanhId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Chỉ có thể so sánh các sản phẩm cùng danh mục.'
                ]);
                exit;
            }

            $phienBanList = $this->phienBanModel->layPhienBanTheoSanPham((int)$sanPham['id']);
            $gia = (float)($sanPham['gia_hien_thi'] ?? 0);
            $tongTonKho = 0;

            if (!empty($phienBanList)) {
                $giaMin = null;
                foreach ($phienBanList as $pb) {
                    $giaPb = isset($pb['gia_ban']) ? (float)$pb['gia_ban'] : 0;
                    $tonPb = isset($pb['so_luong_ton']) ? (int)$pb['so_luong_ton'] : 0;
                    $tongTonKho += max(0, $tonPb);

                    if ($giaPb > 0 && ($giaMin === null || $giaPb < $giaMin)) {
                        $giaMin = $giaPb;
                    }
                }

                if ($giaMin !== null) {
                    $gia = $giaMin;
                }
            }

            $thongSoRows = $this->thongSoModel->layThongSoTheoSanPham((int)$sanPham['id']);
            $thongSo = [];
            foreach ($thongSoRows as $row) {
                $ten = trim((string)($row['ten_thong_so'] ?? ''));
                if ($ten === '') {
                    continue;
                }
                $thongSo[$ten] = (string)($row['gia_tri'] ?? '-');
                $specNameMap[$ten] = true;
            }

            $products[] = [
                'id' => (int)$sanPham['id'],
                'slug' => (string)$sanPham['slug'],
                'ten_san_pham' => (string)$sanPham['ten_san_pham'],
                'danh_muc_id' => $danhMucId,
                'hang_san_xuat' => (string)($sanPham['hang_san_xuat'] ?? '-'),
                'ten_danh_muc' => (string)($sanPham['ten_danh_muc'] ?? '-'),
                'gia_hien_thi' => $gia,
                'tong_ton_kho' => $tongTonKho,
                'thong_so' => $thongSo,
            ];
        }

        if (count($products) < 2) {
            echo json_encode([
                'success' => false,
                'message' => 'Không đủ dữ liệu sản phẩm để so sánh.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'products' => $products,
                'specNames' => array_values(array_keys($specNameMap)),
            ]
        ]);
        exit;
    }
}
