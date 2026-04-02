<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class ChiTietGio extends BaseModel
{
    public function __construct()
    {
        parent::__construct('chi_tiet_gio');
    }

    /**
     * Lấy chi tiết giỏ hàng
     */
    public function layChiTietGioHang(int $gioHangId): array
    {
        $gioHangId = (int)$gioHangId;
        
        $sql = "SELECT ctg.*, 
                       pbsp.ten_phien_ban, pbsp.mau_sac, pbsp.dung_luong, pbsp.ram,
                       pbsp.gia_ban, pbsp.so_luong_ton,
                       sp.ten_san_pham, sp.slug,
                       (SELECT url_anh FROM hinh_anh_san_pham 
                        WHERE san_pham_id = sp.id AND la_anh_chinh = 1 
                        LIMIT 1) as anh_chinh
                FROM {$this->table} ctg
                INNER JOIN phien_ban_san_pham pbsp ON ctg.phien_ban_id = pbsp.id
                INNER JOIN san_pham sp ON pbsp.san_pham_id = sp.id
                WHERE ctg.gio_hang_id = $gioHangId";
        
        return $this->query($sql);
    }

    /**
     * Thêm sản phẩm vào giỏ
     */
    public function themVaoGio(int $gioHangId, int $phienBanId, int $soLuong = 1): bool
    {
        $gioHangId = (int)$gioHangId;
        $phienBanId = (int)$phienBanId;
        $soLuong = max(1, (int)$soLuong);
        
        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $sql = "SELECT * FROM {$this->table} 
                WHERE gio_hang_id = $gioHangId AND phien_ban_id = $phienBanId
                LIMIT 1";
        $result = $this->query($sql);
        
        if (!empty($result)) {
            // Cập nhật số lượng
            $soLuongMoi = $result[0]['so_luong'] + $soLuong;
            return $this->update($result[0]['id'], ['so_luong' => $soLuongMoi]) > 0;
        }
        
        // Thêm mới
        return $this->create([
            'gio_hang_id' => $gioHangId,
            'phien_ban_id' => $phienBanId,
            'so_luong' => $soLuong
        ]) > 0;
    }

    /**
     * Cập nhật số lượng
     */
    public function capNhatSoLuong(int $id, int $soLuong): int
    {
        $soLuong = max(1, (int)$soLuong);
        return $this->update($id, ['so_luong' => $soLuong]);
    }

    /**
     * Xóa sản phẩm khỏi giỏ
     */
    public function xoaKhoiGio(int $id): int
    {
        return $this->delete($id);
    }

    /**
     * Tính tổng tiền giỏ hàng
     */
    public function tinhTongTien(int $gioHangId): float
    {
        $gioHangId = (int)$gioHangId;
        
        $sql = "SELECT SUM(ctg.so_luong * pbsp.gia_ban) as tong_tien
                FROM {$this->table} ctg
                INNER JOIN phien_ban_san_pham pbsp ON ctg.phien_ban_id = pbsp.id
                WHERE ctg.gio_hang_id = $gioHangId";
        
        $result = $this->query($sql);
        return !empty($result) && $result[0]['tong_tien'] !== null 
            ? (float)$result[0]['tong_tien'] 
            : 0;
    }

    /**
     * Đếm số sản phẩm trong giỏ
     */
    public function demSanPham(int $gioHangId): int
    {
        $gioHangId = (int)$gioHangId;
        
        $sql = "SELECT SUM(so_luong) as tong_sp FROM {$this->table}
                WHERE gio_hang_id = $gioHangId";
        
        $result = $this->query($sql);
        return !empty($result) && $result[0]['tong_sp'] !== null 
            ? (int)$result[0]['tong_sp'] 
            : 0;
    }

    /**
     * Xóa tất cả sản phẩm trong giỏ
     */
    public function xoaTatCa(int $gioHangId): bool
    {
        $gioHangId = (int)$gioHangId;
        $sql = "DELETE FROM {$this->table} WHERE gio_hang_id = $gioHangId";
        $this->query($sql);
        return true;
    }
}
