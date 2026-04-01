<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class PhienBanSanPham extends BaseModel
{
    public function __construct()
    {
        parent::__construct('phien_ban_san_pham');
    }

    /**
     * Lấy phiên bản theo sản phẩm
     */
    public function layPhienBanTheoSanPham(int $sanPhamId): array
    {
        $sanPhamId = (int)$sanPhamId;
        $sql = "SELECT * FROM {$this->table}
                WHERE san_pham_id = $sanPhamId
                ORDER BY gia_ban ASC";
        
        return $this->query($sql);
    }

    /**
     * Lấy phiên bản theo ID
     */
    public function layPhienBanTheoId(int $id): ?array
    {
        $result = $this->getById($id);
        return !empty($result) ? $result : null;
    }

    /**
     * Kiểm tra tồn kho
     */
    public function kiemTraTonKho(int $phienBanId, int $soLuong): bool
    {
        $phienBan = $this->layPhienBanTheoId($phienBanId);
        
        if (!$phienBan) {
            return false;
        }
        
        return $phienBan['so_luong_ton'] >= $soLuong;
    }

    /**
     * Giảm tồn kho
     */
    public function giamTonKho(int $phienBanId, int $soLuong): bool
    {
        $phienBanId = (int)$phienBanId;
        $soLuong = (int)$soLuong;
        
        $sql = "UPDATE {$this->table}
                SET so_luong_ton = so_luong_ton - $soLuong
                WHERE id = $phienBanId AND so_luong_ton >= $soLuong";
        
        $this->query($sql);
        return mysqli_affected_rows($this->link) > 0;
    }

    /**
     * Tăng tồn kho (khi hủy đơn)
     */
    public function tangTonKho(int $phienBanId, int $soLuong): bool
    {
        $phienBanId = (int)$phienBanId;
        $soLuong = (int)$soLuong;
        
        $sql = "UPDATE {$this->table}
                SET so_luong_ton = so_luong_ton + $soLuong
                WHERE id = $phienBanId";
        
        $this->query($sql);
        return mysqli_affected_rows($this->link) > 0;
    }
}
