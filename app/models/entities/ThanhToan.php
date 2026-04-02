<?php

require_once dirname(__DIR__) . '/BaseModel.php';
require_once dirname(__DIR__, 2) . '/enums/TrangThaiDuyetThanhToan.php';

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
    public function duyetThanhToan(int $id, int $nguoiDuyetId, ?string $ghiChu = null): int
    {
        return $this->update($id, [
            'nguoi_duyet_id' => $nguoiDuyetId,
            'trang_thai_duyet' => TrangThaiDuyetThanhToan::DA_DUYET,
            'ghi_chu_duyet' => $ghiChu,
            'ngay_duyet' => date('Y-m-d H:i:s')
        ]);
    }

    public function tuChoiThanhToan(int $id, int $nguoiDuyetId, ?string $ghiChu = null): int
    {
        return $this->update($id, [
            'nguoi_duyet_id' => $nguoiDuyetId,
            'trang_thai_duyet' => TrangThaiDuyetThanhToan::TU_CHOI,
            'ghi_chu_duyet' => $ghiChu,
            'ngay_duyet' => date('Y-m-d H:i:s')
        ]);
    }

    public function layDanhSachChoDuyet(int $limit = 20, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT tt.*, dh.ma_don_hang, dh.tong_tien, nd.ho_ten, nd.email
                FROM {$this->table} tt
                LEFT JOIN don_hang dh ON tt.don_hang_id = dh.id
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
                WHERE tt.trang_thai_duyet = '" . TrangThaiDuyetThanhToan::CHO_DUYET . "'
                ORDER BY tt.ngay_thanh_toan DESC, tt.id DESC
                LIMIT $limit OFFSET $offset";

        return $this->query($sql);
    }

    public function demChoDuyet(): int
    {
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table}
                WHERE trang_thai_duyet = '" . TrangThaiDuyetThanhToan::CHO_DUYET . "'";

        $result = $this->query($sql);
        return (int)($result[0]['total'] ?? 0);
    }
}
