<?php

class SanPhamController
{
    private $sanPhamModel;
    private $danhMucModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
        require_once dirname(__DIR__, 2) . '/models/entities/DanhMuc.php';
        $this->sanPhamModel = new SanPham();
        $this->danhMucModel = new DanhMuc();
    }
    
    public function index(): void
    {
        $keyword = trim((string)($_GET['keyword'] ?? ''));
        $danhMucId = trim((string)($_GET['danh_muc_id'] ?? ''));
        $minPrice = trim((string)($_GET['min_price'] ?? ''));
        $maxPrice = trim((string)($_GET['max_price'] ?? ''));

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 15; 
        $offset = ($page - 1) * $limit;

        $conditions = [];

        if ($keyword !== '') {
            $dbKeyword = addslashes($keyword);
            $conditions[] = "(sp.ten_san_pham LIKE '%$dbKeyword%' OR sp.id = '$dbKeyword' OR sp.hang_san_xuat LIKE '%$dbKeyword%')";
        }

        if ($danhMucId !== '' && is_numeric($danhMucId)) {
            $conditions[] = "sp.danh_muc_id = " . (int)$danhMucId;
        }

        if ($minPrice !== '' && is_numeric($minPrice)) {
            $conditions[] = "sp.gia_hien_thi >= " . (float)$minPrice;
        }

        if ($maxPrice !== '' && is_numeric($maxPrice)) {
            $conditions[] = "sp.gia_hien_thi <= " . (float)$maxPrice;
        }

        $whereClause = empty($conditions) ? "" : "WHERE " . implode(" AND ", $conditions);

        $sqlCount = "SELECT COUNT(*) as total FROM san_pham sp $whereClause";
        $resultCount = $this->sanPhamModel->query($sqlCount);
        $totalProducts = !empty($resultCount) ? (int)$resultCount[0]['total'] : 0;
        $totalPages = max(1, ceil($totalProducts / $limit));

        $sqlSearch = "SELECT sp.*, dm.ten AS ten_danh_muc 
                      FROM san_pham sp
                      LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
                      $whereClause
                      ORDER BY sp.ngay_tao DESC
                      LIMIT $limit OFFSET $offset";
        
        $danhSachSanPham = $this->sanPhamModel->query($sqlSearch);

        $danhSachDanhMuc = $this->danhMucModel->layDanhSach(null, 1);

        $data = [
            'danhSachSanPham' => $danhSachSanPham,
            'danhSachDanhMuc' => $danhSachDanhMuc,
            'keyword'         => $keyword,
            'danhMucId'       => $danhMucId,
            'minPrice'        => $minPrice,
            'maxPrice'        => $maxPrice,
            'totalProducts'   => $totalProducts,
            'currentPage'     => $page,
            'totalPages'      => $totalPages,
            'limit'           => $limit,
            'success'         => $_GET['success'] ?? '',
            'error'           => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/index.php';
    }

    public function xoa($id): void 
    {
        $id = (int)$id;
        if ($id <= 0) {
            header("Location: /admin/san-pham?error=invalid_id");
            exit;
        }
        
        $sanPham = $this->sanPhamModel->getById($id);
        if (!$sanPham) {
            header("Location: /admin/san-pham?error=not_found");
            exit;
        }

        $this->sanPhamModel->update($id, ['trang_thai' => 'NGUNG_BAN']);
        
        $sqlPhienBan = "UPDATE phien_ban_san_pham SET trang_thai = 'NGUNG_BAN' WHERE san_pham_id = $id";
        if (function_exists('chayTruyVanKhongTraVeDL')) {
            chayTruyVanKhongTraVeDL($this->sanPhamModel->link, $sqlPhienBan);
        }
        
        header("Location: /admin/san-pham?success=deleted");
        exit;
    }

    public function moBan($id): void 
    {
        $id = (int)$id;
        if ($id <= 0) {
            header("Location: /admin/san-pham?error=invalid_id");
            exit;
        }
        
        $sanPham = $this->sanPhamModel->getById($id);
        if (!$sanPham) {
            header("Location: /admin/san-pham?error=not_found");
            exit;
        }

        $this->sanPhamModel->update($id, ['trang_thai' => 'CON_BAN']);
        
        $sqlPhienBan = "UPDATE phien_ban_san_pham 
                        SET trang_thai = CASE WHEN so_luong_ton > 0 THEN 'CON_HANG' ELSE 'HET_HANG' END 
                        WHERE san_pham_id = $id";
        if (function_exists('chayTruyVanKhongTraVeDL')) {
            chayTruyVanKhongTraVeDL($this->sanPhamModel->link, $sqlPhienBan);
        }

        header("Location: /admin/san-pham?success=restored");
        exit;
    }
}
?>