<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class ThanhToan extends BaseModel
{
    public function __construct()
    {
        parent::__construct('thanh_toan');
    }

    /**
     * Lấy thông tin thanh toán theo đơn hàng
     */
    public function layTheoDonHang(int $donHangId): ?array
    {
        $donHangId = (int)$donHangId;
        
        $sql = "SELECT * FROM {$this->table}
                WHERE don_hang_id = $donHangId
                LIMIT 1";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Tạo thông tin thanh toán
     */
    public function taoThanhToan(int $donHangId, string $phuongThuc, float $soTien): int
    {
        return $this->insert([
            'don_hang_id' => $donHangId,
            'phuong_thuc' => $phuongThuc,
            'so_tien' => $soTien,
            'trang_thai_duyet' => 'CHO_DUYET',
            'ngay_thanh_toan' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Cập nhật biên lai
     */
    public function capNhatBienLai(int $id, string $anhBienLai): int
    {
        return $this->update($id, ['anh_bien_lai' => $anhBienLai]);
    }

    /**
     * Duyệt thanh toán
     */
    public function duyetThanhToan(int $id, int $nguoiDuyetId, string $trangThai, ?string $ghiChu = null): int
    {
        return $this->update($id, [
            'nguoi_duyet_id' => $nguoiDuyetId,
            'trang_thai_duyet' => $trangThai,
            'ghi_chu_duyet' => $ghiChu,
            'ngay_duyet' => date('Y-m-d H:i:s')
        ]);
    }
}
