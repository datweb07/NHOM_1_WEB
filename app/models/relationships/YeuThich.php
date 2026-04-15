<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class YeuThich extends BaseModel
{
    public function __construct()
    {
        parent::__construct('yeu_thich');
    }

    /**
     * Thêm sản phẩm vào danh sách yêu thích
     */
    public function them(int $nguoiDungId, int $sanPhamId): bool
    {
        // Kiểm tra đã tồn tại chưa
        if ($this->kiemTraDaTonTai($nguoiDungId, $sanPhamId)) {
            return false;
        }

        $nguoiDungId = (int)$nguoiDungId;
        $sanPhamId = (int)$sanPhamId;
        
        $sql = "INSERT INTO {$this->table} (nguoi_dung_id, san_pham_id) 
                VALUES ($nguoiDungId, $sanPhamId)";
        
        $this->query($sql);
        return mysqli_affected_rows($this->link) > 0;
    }

    /**
     * Xóa sản phẩm khỏi danh sách yêu thích
     */
    public function xoa(int $nguoiDungId, int $sanPhamId): bool
    {
        $nguoiDungId = (int)$nguoiDungId;
        $sanPhamId = (int)$sanPhamId;
        
        $sql = "DELETE FROM {$this->table} 
                WHERE nguoi_dung_id = $nguoiDungId AND san_pham_id = $sanPhamId";
        
        $this->query($sql);
        return mysqli_affected_rows($this->link) > 0;
    }

    /**
     * Kiểm tra sản phẩm đã được yêu thích chưa
     */
    public function kiemTraDaTonTai(int $nguoiDungId, int $sanPhamId): bool
    {
        $nguoiDungId = (int)$nguoiDungId;
        $sanPhamId = (int)$sanPhamId;
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE nguoi_dung_id = $nguoiDungId AND san_pham_id = $sanPhamId";
        
        $result = $this->query($sql);
        return !empty($result) && (int)$result[0]['total'] > 0;
    }

    /**
     * Lấy danh sách sản phẩm yêu thích của user
     */
    public function layDanhSachTheoUser(int $nguoiDungId, int $limit = 20, int $offset = 0): array
    {
        $nguoiDungId = (int)$nguoiDungId;
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        
        $sql = "SELECT sp.*, yt.ngay_them,
                       (SELECT url_anh FROM hinh_anh_san_pham 
                        WHERE san_pham_id = sp.id AND la_anh_chinh = 1 
                        LIMIT 1) as anh_chinh
                FROM {$this->table} yt
                INNER JOIN san_pham sp ON yt.san_pham_id = sp.id
                WHERE yt.nguoi_dung_id = $nguoiDungId
                ORDER BY yt.ngay_them DESC
                LIMIT $limit OFFSET $offset";
        
        return $this->query($sql);
    }

    /**
     * Đếm số sản phẩm yêu thích của user
     */
    public function demTheoUser(int $nguoiDungId): int
    {
        $nguoiDungId = (int)$nguoiDungId;
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE nguoi_dung_id = $nguoiDungId";
        
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    /**
     * Đếm số lượng yêu thích (static method để gọi nhanh)
     */
    public static function demSoLuongYeuThich(int $nguoiDungId): int
    {
        $instance = new self();
        return $instance->demTheoUser($nguoiDungId);
    }

    /**
     * Xóa tất cả sản phẩm yêu thích của user
     */
    public function xoaTatCa(int $nguoiDungId): bool
    {
        $nguoiDungId = (int)$nguoiDungId;
        
        $sql = "DELETE FROM {$this->table} WHERE nguoi_dung_id = $nguoiDungId";
        
        $this->query($sql);
        return true;
    }
}
