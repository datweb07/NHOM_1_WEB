<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class GioHang extends BaseModel
{
    public function __construct()
    {
        parent::__construct('gio_hang');
    }

    /**
     * Lấy hoặc tạo giỏ hàng cho user đã đăng nhập
     */
    public function layHoacTaoGioHangUser(int $nguoiDungId): array
    {
        $nguoiDungId = (int)$nguoiDungId;
        
        // Kiểm tra giỏ hàng đã tồn tại
        $sql = "SELECT * FROM {$this->table} WHERE nguoi_dung_id = $nguoiDungId LIMIT 1";
        $result = $this->query($sql);
        
        if (!empty($result)) {
            return $result[0];
        }
        
        // Tạo giỏ hàng mới
        $id = $this->insert(['nguoi_dung_id' => $nguoiDungId]);
        return $this->getById($id);
    }

    /**
     * Lấy hoặc tạo giỏ hàng cho khách vãng lai
     */
    public function layHoacTaoGioHangGuest(string $sessionId): array
    {
        $sessionId = mysqli_real_escape_string($this->link, $sessionId);
        
        // Kiểm tra giỏ hàng đã tồn tại
        $sql = "SELECT * FROM {$this->table} WHERE session_id = '$sessionId' LIMIT 1";
        $result = $this->query($sql);
        
        if (!empty($result)) {
            return $result[0];
        }
        
        // Tạo giỏ hàng mới
        $id = $this->insert(['session_id' => $sessionId]);
        return $this->getById($id);
    }

    /**
     * Chuyển giỏ hàng từ guest sang user khi đăng nhập
     */
    public function chuyenGioHangGuestSangUser(string $sessionId, int $nguoiDungId): bool
    {
        $sessionId = mysqli_real_escape_string($this->link, $sessionId);
        $nguoiDungId = (int)$nguoiDungId;
        
        // Lấy giỏ hàng guest
        $gioHangGuest = $this->layHoacTaoGioHangGuest($sessionId);
        
        // Lấy hoặc tạo giỏ hàng user
        $gioHangUser = $this->layHoacTaoGioHangUser($nguoiDungId);
        
        // Chuyển các sản phẩm từ giỏ guest sang giỏ user
        $sql = "UPDATE chi_tiet_gio 
                SET gio_hang_id = {$gioHangUser['id']}
                WHERE gio_hang_id = {$gioHangGuest['id']}
                ON DUPLICATE KEY UPDATE so_luong = so_luong + VALUES(so_luong)";
        
        $this->query($sql);
        
        // Xóa giỏ hàng guest
        $this->delete($gioHangGuest['id']);
        
        return true;
    }

    /**
     * Xóa giỏ hàng
     */
    public function xoaGioHang(int $id): int
    {
        return $this->delete($id);
    }
}
