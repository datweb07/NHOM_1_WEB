<?php

class SanPhamController
{
    private $sanPhamModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
        $this->sanPhamModel = new SanPham();
    }
    
    public function index(): void
    {
        $keyword = trim((string)($_GET['keyword'] ?? ''));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 15; 
        $offset = ($page - 1) * $limit;

        $whereClause = "";
        if ($keyword !== '') {
            $dbKeyword = addslashes($keyword);
            $whereClause = "WHERE sp.ten_san_pham LIKE '%$dbKeyword%' 
                               OR sp.id = '$dbKeyword' 
                               OR sp.hang_san_xuat LIKE '%$dbKeyword%'";
        }

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

        $data = [
            'danhSachSanPham' => $danhSachSanPham,
            'keyword'         => $keyword,
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