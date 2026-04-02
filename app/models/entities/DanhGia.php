<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class DanhGia extends BaseModel
{
    public function __construct()
    {
        parent::__construct('danh_gia');
    }

    /**
     * Lấy đánh giá theo sản phẩm
     */
    public function layDanhGiaTheoSanPham(int $sanPhamId, int $limit = 10): array
    {
        $sanPhamId = (int)$sanPhamId;
        $limit = max(1, (int)$limit);
        
        $sql = "SELECT dg.*, nd.ho_ten, nd.avatar_url
                FROM {$this->table} dg
                INNER JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                WHERE dg.san_pham_id = $sanPhamId
                ORDER BY dg.ngay_viet DESC
                LIMIT $limit";
        
        return $this->query($sql);
    }

    /**
     * Đếm số đánh giá theo sản phẩm
     */
    public function demDanhGiaTheoSanPham(int $sanPhamId): int
    {
        $sanPhamId = (int)$sanPhamId;
        $sql = "SELECT COUNT(*) as total FROM {$this->table}
                WHERE san_pham_id = $sanPhamId";
        
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    /**
     * Tính điểm trung bình
     */
    public function tinhDiemTrungBinh(int $sanPhamId): float
    {
        $sanPhamId = (int)$sanPhamId;
        $sql = "SELECT AVG(so_sao) as diem_tb FROM {$this->table}
                WHERE san_pham_id = $sanPhamId";
        
        $result = $this->query($sql);
        return !empty($result) && $result[0]['diem_tb'] !== null 
            ? (float)$result[0]['diem_tb'] 
            : 0;
    }

    /**
     * Thêm đánh giá
     */
    public function themDanhGia(int $nguoiDungId, int $sanPhamId, int $soSao, string $noiDung): int
    {
        $data = [
            'nguoi_dung_id' => $nguoiDungId,
            'san_pham_id' => $sanPhamId,
            'so_sao' => $soSao,
            'noi_dung' => $noiDung
        ];
        
        return $this->create($data);
    }

    /**
     * Kiểm tra user đã đánh giá sản phẩm chưa
     */
    public function kiemTraDaDanhGia(int $nguoiDungId, int $sanPhamId): bool
    {
        $nguoiDungId = (int)$nguoiDungId;
        $sanPhamId = (int)$sanPhamId;
        
        $sql = "SELECT id FROM {$this->table}
                WHERE nguoi_dung_id = $nguoiDungId AND san_pham_id = $sanPhamId
                LIMIT 1";
        
        $result = $this->query($sql);
        return !empty($result);
    }

    /**
     * Lấy đánh giá của user cho sản phẩm
     */
    public function layDanhGiaCuaUser(int $nguoiDungId, int $sanPhamId): ?array
    {
        $nguoiDungId = (int)$nguoiDungId;
        $sanPhamId = (int)$sanPhamId;
        
        $sql = "SELECT * FROM {$this->table}
                WHERE nguoi_dung_id = $nguoiDungId AND san_pham_id = $sanPhamId
                LIMIT 1";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Lấy danh sách đánh giá với filter (cho admin)
     */
    public function layDanhSach(?int $soSao, ?int $sanPhamId, int $limit, int $offset): array
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT dg.*, nd.ho_ten, nd.email, sp.ten_san_pham, sp.slug
                FROM {$this->table} dg
                LEFT JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                LEFT JOIN san_pham sp ON dg.san_pham_id = sp.id
                WHERE 1=1";
        
        if ($soSao !== null) {
            $soSao = (int)$soSao;
            $sql .= " AND dg.so_sao = $soSao";
        }
        
        if ($sanPhamId !== null) {
            $sanPhamId = (int)$sanPhamId;
            $sql .= " AND dg.san_pham_id = $sanPhamId";
        }
        
        $sql .= " ORDER BY dg.ngay_viet DESC LIMIT $limit OFFSET $offset";
        
        return $this->query($sql);
    }

    /**
     * Tìm kiếm đánh giá theo từ khóa
     */
    public function timKiem(string $keyword, int $limit, int $offset): array
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $keyword = mysqli_real_escape_string($this->link, $keyword);
        
        $sql = "SELECT dg.*, nd.ho_ten, nd.email, sp.ten_san_pham, sp.slug
                FROM {$this->table} dg
                LEFT JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                LEFT JOIN san_pham sp ON dg.san_pham_id = sp.id
                WHERE dg.noi_dung LIKE '%$keyword%'
                   OR nd.ho_ten LIKE '%$keyword%'
                   OR nd.email LIKE '%$keyword%'
                   OR sp.ten_san_pham LIKE '%$keyword%'
                ORDER BY dg.ngay_viet DESC
                LIMIT $limit OFFSET $offset";
        
        return $this->query($sql);
    }

    /**
     * Đếm tổng số đánh giá với filter
     */
    public function demDanhGia(?int $soSao, ?int $sanPhamId, ?string $keyword): int
    {
        if ($keyword !== null && $keyword !== '') {
            $keyword = mysqli_real_escape_string($this->link, $keyword);
            $sql = "SELECT COUNT(*) as total
                    FROM {$this->table} dg
                    LEFT JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                    LEFT JOIN san_pham sp ON dg.san_pham_id = sp.id
                    WHERE dg.noi_dung LIKE '%$keyword%'
                       OR nd.ho_ten LIKE '%$keyword%'
                       OR nd.email LIKE '%$keyword%'
                       OR sp.ten_san_pham LIKE '%$keyword%'";
        } else {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
            
            if ($soSao !== null) {
                $soSao = (int)$soSao;
                $sql .= " AND so_sao = $soSao";
            }
            
            if ($sanPhamId !== null) {
                $sanPhamId = (int)$sanPhamId;
                $sql .= " AND san_pham_id = $sanPhamId";
            }
        }
        
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    /**
     * Lấy đánh giá theo ID
     */
    public function layTheoId(int $id): ?array
    {
        return $this->getById($id);
    }

    /**
     * Xóa đánh giá
     */
    public function xoa(int $id): bool
    {
        return $this->delete($id) > 0;
    }
}
