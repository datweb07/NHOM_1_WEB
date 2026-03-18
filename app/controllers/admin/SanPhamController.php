<?php

class SanPhamController 
{
    public function index() 
    {
        //kết quả của admin trả ra dưới dạng bảng để quản lý và có quyền xem cả những sẳn phẩm ngừng bán
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $safeKeyword = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');

        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $limit = 15; 
        $offset = ($page - 1) * $limit;

        require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
        $sanPhamModel = new SanPham(); 
        
        $dbKeyword = addslashes($keyword);
        
        // Điều kiện tìm kiếm admin được quyền xem tất cả, kể cả NGUNG_BAN
        $whereClause = "";
        if ($keyword !== '') {
            $whereClause = "WHERE sp.ten_san_pham LIKE '%$dbKeyword%' 
                               OR sp.id = '$dbKeyword' 
                               OR sp.hang_san_xuat LIKE '%$dbKeyword%'";
        }

        $sqlCount = "SELECT COUNT(*) as total FROM san_pham sp $whereClause";
        $resultCount = $sanPhamModel->query($sqlCount);
        $totalProducts = !empty($resultCount) ? (int)$resultCount[0]['total'] : 0;
        $totalPages = ceil($totalProducts / $limit);

        $sqlSearch = "SELECT sp.*, dm.ten AS ten_danh_muc 
                      FROM san_pham sp
                      LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
                      $whereClause
                      ORDER BY sp.ngay_tao DESC
                      LIMIT $limit OFFSET $offset";
        
        $danhSachSanPham = $sanPhamModel->query($sqlSearch);

        $data = [
            'keyword'         => $safeKeyword,
            'danhSachSanPham' => $danhSachSanPham,
            'totalProducts'   => $totalProducts,
            'currentPage'     => $page,
            'totalPages'      => $totalPages,
            'limit'           => $limit
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/index.php';
    }

    
     //Xử lý xóa/ẩn sản phẩm 
     
    public function xoa($id) 
    {
        if (!$id || !is_numeric($id)) {
            header("Location: /admin/san-pham?error=invalid_id");
            exit;
        }
        
        $id = (int)$id; 
        require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
        $sanPhamModel = new SanPham();

        $sanPhamModel->update($id, ['trang_thai' => 'NGUNG_BAN']);
        $sqlPhienBan = "UPDATE phien_ban_san_pham SET trang_thai = 'NGUNG_BAN' WHERE san_pham_id = $id";
        chayTruyVanKhongTraVeDL($sanPhamModel->link, $sqlPhienBan);
        header("Location: /admin/san-pham?success=deleted");
        exit;
    }
    // Xử lý chức năng mở bán lại
    public function moBan($id) 
    {
        if (!$id || !is_numeric($id)) {
            header("Location: /admin/san-pham?error=invalid_id");
            exit;
        }
        
        $id = (int)$id; 

        require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
        $sanPhamModel = new SanPham();
        $sanPhamModel->update($id, ['trang_thai' => 'CON_BAN']);
        $sqlPhienBan = "UPDATE phien_ban_san_pham 
                        SET trang_thai = CASE WHEN so_luong_ton > 0 THEN 'CON_HANG' ELSE 'HET_HANG' END 
                        WHERE san_pham_id = $id";
        chayTruyVanKhongTraVeDL($sanPhamModel->link, $sqlPhienBan);

        header("Location: /admin/san-pham?success=restored");
        exit;
    }
}
?>