<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class ChiTietGio extends BaseModel
{
    protected ?int $id = null;
    protected ?int $gioHangId = null;
    protected ?int $phienBanId = null;
    protected int $soLuong = 1;

    public function __construct()
    {
        parent::__construct('chi_tiet_gio');
    }

    public function layTheoGioHang(int $gioHangId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE gio_hang_id = " . (int)$gioHangId . ' ORDER BY id ASC';
        return $this->query($sql);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'gio_hang_id' => $this->gioHangId,
            'phien_ban_id' => $this->phienBanId,
            'so_luong' => $this->soLuong,
        ];
    }
}
