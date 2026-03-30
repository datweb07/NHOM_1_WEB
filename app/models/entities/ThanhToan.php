<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class ThanhToan extends BaseModel
{
    protected ?int $id = null;
    protected ?int $donHangId = null;
    protected ?int $nguoiDuyetId = null;
    protected ?string $phuongThuc = null;
    protected ?float $soTien = null;
    protected ?string $anhBienLai = null;
    protected string $trangThaiDuyet = 'CHO_DUYET';
    protected ?string $ghiChuDuyet = null;
    protected ?string $ngayThanhToan = null;
    protected ?string $ngayDuyet = null;

    public function __construct()
    {
        parent::__construct('thanh_toan');
    }

    public function layTheoDonHang(int $donHangId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE don_hang_id = " . (int)$donHangId . ' LIMIT 1';
        $rows = $this->query($sql);
        return $rows[0] ?? null;
    }

    public function capNhatTrangThaiDuyet(int $id, string $trangThai, ?string $ghiChu = null): int
    {
        $data = [
            'trang_thai_duyet' => addslashes($trangThai),
            'ghi_chu_duyet' => $ghiChu !== null ? addslashes($ghiChu) : null,
            'ngay_duyet' => date('Y-m-d H:i:s'),
        ];

        return $this->update($id, $data);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'don_hang_id' => $this->donHangId,
            'nguoi_duyet_id' => $this->nguoiDuyetId,
            'phuong_thuc' => $this->phuongThuc,
            'so_tien' => $this->soTien,
            'anh_bien_lai' => $this->anhBienLai,
            'trang_thai_duyet' => $this->trangThaiDuyet,
            'ghi_chu_duyet' => $this->ghiChuDuyet,
            'ngay_thanh_toan' => $this->ngayThanhToan,
            'ngay_duyet' => $this->ngayDuyet,
        ];
    }
}
