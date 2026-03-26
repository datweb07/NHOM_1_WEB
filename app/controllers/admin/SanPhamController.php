<?php

class SanPhamController 
{
    private $baseModel;

    public function __construct() 
    {
        require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
        $this->baseModel = new BaseModel('san_pham');
    }

    public function index() 
    {
       
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $safeKeyword = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');

        
        $danhMucId = isset($_GET['danh_muc_id']) && is_numeric($_GET['danh_muc_id']) ? (int)$_GET['danh_muc_id'] : 0;
        $giaMin = isset($_GET['gia_min']) && is_numeric($_GET['gia_min']) ? (float)$_GET['gia_min'] : null;
        $giaMax = isset($_GET['gia_max']) && is_numeric($_GET['gia_max']) ? (float)$_GET['gia_max'] : null;

        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $limit = 15; 
        $offset = ($page - 1) * $limit;

        $dbKeyword = addslashes($keyword);
        
        
        $whereConditions = [];
        if ($keyword !== '') {
            $whereConditions[] = "(sp.ten_san_pham LIKE '%$dbKeyword%' 
                                   OR sp.id = '$dbKeyword' 
                                   OR sp.hang_san_xuat LIKE '%$dbKeyword%')";
        }
        if ($danhMucId > 0) {
            $whereConditions[] = "sp.danh_muc_id = $danhMucId";
        }
        if ($giaMin !== null) {
            $whereConditions[] = "sp.gia_hien_thi >= $giaMin";
        }
        if ($giaMax !== null) {
            $whereConditions[] = "sp.gia_hien_thi <= $giaMax";
        }

        $whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";

        $sqlCount = "SELECT COUNT(*) as total FROM san_pham sp $whereClause";
        $resultCount = $this->baseModel->query($sqlCount);
        $totalProducts = !empty($resultCount) ? (int)$resultCount[0]['total'] : 0;
        $totalPages = ceil($totalProducts / $limit);

        $sqlSearch = "SELECT sp.*, dm.ten AS ten_danh_muc 
                      FROM san_pham sp
                      LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
                      $whereClause
                      ORDER BY sp.ngay_tao DESC
                      LIMIT $limit OFFSET $offset";
        
        $danhSachSanPham = $this->baseModel->query($sqlSearch);

        
        $sqlDanhMuc = "SELECT id, ten FROM danh_muc WHERE trang_thai = 1 ORDER BY thu_tu ASC, ten ASC";
        $danhSachDanhMuc = $this->baseModel->query($sqlDanhMuc);

        $data = [
            'keyword'         => $safeKeyword,
            'danhMucId'       => $danhMucId,
            'giaMin'          => $giaMin,
            'giaMax'          => $giaMax,
            'danhSachDanhMuc' => $danhSachDanhMuc,
            'danhSachSanPham' => $danhSachSanPham,
            'totalProducts'   => $totalProducts,
            'currentPage'     => $page,
            'totalPages'      => $totalPages,
            'limit'           => $limit
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/index.php';
    }

    
     
    public function xoa($id) 
    {
        $id = (int)$id; 
        if ($id <= 0) {
            header("Location: /admin/san-pham?error=invalid_id");
            exit;
        }
        
        $this->baseModel->update($id, ['trang_thai' => 'NGUNG_BAN']);
        $sqlPhienBan = "UPDATE phien_ban_san_pham SET trang_thai = 'NGUNG_BAN' WHERE san_pham_id = $id";
        $this->baseModel->query($sqlPhienBan);

        header("Location: /admin/san-pham?success=deleted");
        exit;
    }
    public function moBan($id) 
    {
        $id = (int)$id; 
        if ($id <= 0) {
            header("Location: /admin/san-pham?error=invalid_id");
            exit;
        }
        
        $this->baseModel->update($id, ['trang_thai' => 'CON_BAN']);
        $sqlPhienBan = "UPDATE phien_ban_san_pham 
                        SET trang_thai = CASE WHEN so_luong_ton > 0 THEN 'CON_HANG' ELSE 'HET_HANG' END 
                        WHERE san_pham_id = $id";
        $this->baseModel->query($sqlPhienBan);

        header("Location: /admin/san-pham?success=restored");
        exit;
    }
}
?>