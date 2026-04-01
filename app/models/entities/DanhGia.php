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
        
        return $this->insert($data);
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
}
