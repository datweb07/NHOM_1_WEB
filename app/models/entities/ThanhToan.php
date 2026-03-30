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

    /**
     * Get list of pending payments with pagination
     * 
     * @param int $limit Number of records per page
     * @param int $offset Starting offset
     * @return array List of pending payments with customer and order info
     */
    public function layDanhSachChoDuyet(int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT 
                    tt.id,
                    tt.don_hang_id,
                    tt.phuong_thuc,
                    tt.so_tien,
                    tt.anh_bien_lai,
                    tt.trang_thai_duyet,
                    tt.ngay_thanh_toan,
                    dh.ma_don_hang,
                    nd.ho_ten AS customer_name,
                    nd.email AS customer_email
                FROM {$this->table} tt
                LEFT JOIN don_hang dh ON tt.don_hang_id = dh.id
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
                WHERE tt.trang_thai_duyet = 'CHO_DUYET'
                ORDER BY tt.ngay_thanh_toan DESC
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        return $this->query($sql);
    }

    /**
     * Approve payment
     * 
     * @param int $id Payment ID
     * @param int $nguoiDuyetId Admin user ID who approves
     * @param string|null $ghiChu Optional approval note
     * @return int Number of affected rows
     */
    public function duyetThanhToan(int $id, int $nguoiDuyetId, ?string $ghiChu = null): int
    {
        $data = [
            'trang_thai_duyet' => 'THANH_CONG',
            'nguoi_duyet_id' => (int)$nguoiDuyetId,
            'ngay_duyet' => date('Y-m-d H:i:s'),
        ];
        
        if ($ghiChu !== null && trim($ghiChu) !== '') {
            $data['ghi_chu_duyet'] = addslashes(trim($ghiChu));
        }
        
        return $this->update($id, $data);
    }

    /**
     * Reject payment
     * 
     * @param int $id Payment ID
     * @param int $nguoiDuyetId Admin user ID who rejects
     * @param string|null $ghiChu Optional rejection note
     * @return int Number of affected rows
     */
    public function tuChoiThanhToan(int $id, int $nguoiDuyetId, ?string $ghiChu = null): int
    {
        $data = [
            'trang_thai_duyet' => 'THAT_BAI',
            'nguoi_duyet_id' => (int)$nguoiDuyetId,
            'ngay_duyet' => date('Y-m-d H:i:s'),
        ];
        
        if ($ghiChu !== null && trim($ghiChu) !== '') {
            $data['ghi_chu_duyet'] = addslashes(trim($ghiChu));
        }
        
        return $this->update($id, $data);
    }

    /**
     * Count pending payments for dashboard
     * 
     * @return int Number of pending payments
     */
    public function demChoDuyet(): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE trang_thai_duyet = 'CHO_DUYET'";
        $result = $this->query($sql);
        return (int)($result[0]['total'] ?? 0);
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
