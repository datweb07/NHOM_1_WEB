<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class KhuyenMai extends BaseModel
{
    protected ?int $id = null;
    protected ?string $tenChuongTrinh = null;
    protected string $loaiGiam = 'PHAN_TRAM';
    protected ?float $giaTriGiam = null;
    protected ?float $giamToiDa = null;
    protected ?string $ngayBatDau = null;
    protected ?string $ngayKetThuc = null;
    protected string $trangThai = 'HOAT_DONG';

    public function __construct()
    {
        parent::__construct('khuyen_mai');
    }

    public function layDangHoatDong(): array
    {
        $sql = "SELECT * FROM {$this->table}
				WHERE trang_thai = 'HOAT_DONG'
				ORDER BY ngay_bat_dau DESC, id DESC";
        return $this->query($sql);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ten_chuong_trinh' => $this->tenChuongTrinh,
            'loai_giam' => $this->loaiGiam,
            'gia_tri_giam' => $this->giaTriGiam,
            'giam_toi_da' => $this->giamToiDa,
            'ngay_bat_dau' => $this->ngayBatDau,
            'ngay_ket_thuc' => $this->ngayKetThuc,
            'trang_thai' => $this->trangThai,
        ];
    }
}
