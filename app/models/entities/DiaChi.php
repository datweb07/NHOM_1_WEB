<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class DiaChi extends BaseModel
{
    protected ?int $id = null;
    protected ?int $nguoiDungId = null;
    protected ?string $tenNguoiNhan = null;
    protected ?string $sdtNhan = null;
    protected ?string $soNhaDuong = null;
    protected ?string $phuongXa = null;
    protected ?string $quanHuyen = null;
    protected ?string $tinhThanh = null;
    protected int $macDinh = 0;

    public function __construct()
    {
        parent::__construct('dia_chi');
    }

    public function layTheoNguoiDung(int $nguoiDungId): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE nguoi_dung_id = " . (int)$nguoiDungId . '
                ORDER BY mac_dinh DESC, id DESC';
        return $this->query($sql);
    }

    public function getFullAddress(array $diaChi): string
    {
        $parts = [
            $diaChi['so_nha_duong'] ?? '',
            $diaChi['phuong_xa'] ?? '',
            $diaChi['quan_huyen'] ?? '',
            $diaChi['tinh_thanh'] ?? '',
        ];

        return implode(', ', array_filter($parts, static fn($item) => trim((string)$item) !== ''));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nguoi_dung_id' => $this->nguoiDungId,
            'ten_nguoi_nhan' => $this->tenNguoiNhan,
            'sdt_nhan' => $this->sdtNhan,
            'so_nha_duong' => $this->soNhaDuong,
            'phuong_xa' => $this->phuongXa,
            'quan_huyen' => $this->quanHuyen,
            'tinh_thanh' => $this->tinhThanh,
            'mac_dinh' => $this->macDinh,
        ];
    }
}
