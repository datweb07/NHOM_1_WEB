<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class ChiTietDon extends BaseModel
{
    protected ?int $id = null;
    protected ?int $donHangId = null;
    protected ?int $phienBanId = null;
    protected int $soLuong = 1;
    protected ?float $giaTaiThoiDiemMua = null;

    public function __construct()
    {
        parent::__construct('chi_tiet_don');
    }

    public function layTheoDonHang(int $donHangId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE don_hang_id = " . (int)$donHangId . ' ORDER BY id ASC';
        return $this->query($sql);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'don_hang_id' => $this->donHangId,
            'phien_ban_id' => $this->phienBanId,
            'so_luong' => $this->soLuong,
            'gia_tai_thoi_diem_mua' => $this->giaTaiThoiDiemMua,
        ];
    }
}
