<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class GioHang extends BaseModel
{
    public function __construct()
    {
        parent::__construct('gio_hang');
    }

    public function layHoacTaoGioHangUser(int $nguoiDungId): array
    {
        $nguoiDungId = (int)$nguoiDungId;

        $sql = "SELECT * FROM {$this->table} WHERE nguoi_dung_id = $nguoiDungId LIMIT 1";
        $result = $this->query($sql);
        
        if (!empty($result)) {
            return $result[0];
        }

        $id = $this->create(['nguoi_dung_id' => $nguoiDungId]);
        return $this->getById($id);
    }

    public function layHoacTaoGioHangGuest(string $sessionId): array
    {
        $sessionId = mysqli_real_escape_string($this->link, $sessionId);
        
        $sql = "SELECT * FROM {$this->table} WHERE session_id = '$sessionId' LIMIT 1";
        $result = $this->query($sql);
        
        if (!empty($result)) {
            return $result[0];
        }
        
        $id = $this->create(['session_id' => $sessionId]);
        return $this->getById($id);
    }

    public function chuyenGioHangGuestSangUser(string $sessionId, int $nguoiDungId): bool
    {
        $sessionId = mysqli_real_escape_string($this->link, $sessionId);
        $nguoiDungId = (int)$nguoiDungId;
        
        $gioHangGuest = $this->layHoacTaoGioHangGuest($sessionId);
        
        $gioHangUser = $this->layHoacTaoGioHangUser($nguoiDungId);
        
        $sql = "UPDATE chi_tiet_gio 
                SET gio_hang_id = {$gioHangUser['id']}
                WHERE gio_hang_id = {$gioHangGuest['id']}
                ON DUPLICATE KEY UPDATE so_luong = so_luong + VALUES(so_luong)";
        
        $this->query($sql);
        
        $this->delete($gioHangGuest['id']);
        
        return true;
    }

    public function xoaGioHang(int $id): int
    {
        return $this->delete($id);
    }
}
