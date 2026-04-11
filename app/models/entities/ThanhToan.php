<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class ThanhToan extends BaseModel
{
    public function __construct()
    {
        parent::__construct('thanh_toan');
    }

    public function layTheoDonHang(int $donHangId): ?array
    {
        $donHangId = (int)$donHangId;
        
        $sql = "SELECT * FROM {$this->table}
                WHERE don_hang_id = $donHangId
                LIMIT 1";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }

    public function taoThanhToan(int $donHangId, string $phuongThuc, float $soTien): int
    {
        return $this->create([
            'don_hang_id' => $donHangId,
            'phuong_thuc' => $phuongThuc,
            'so_tien' => $soTien,
            'trang_thai_duyet' => 'CHO_DUYET',
            'ngay_thanh_toan' => date('Y-m-d H:i:s')
        ]);
    }

    public function capNhatBienLai(int $id, string $anhBienLai): int
    {
        return $this->update($id, ['anh_bien_lai' => $anhBienLai]);
    }

    public function duyetThanhToan(int $id, int $nguoiDuyetId, string $trangThai, ?string $ghiChu = null): int
    {
        return $this->update($id, [
            'nguoi_duyet_id' => $nguoiDuyetId,
            'trang_thai_duyet' => $trangThai,
            'ghi_chu_duyet' => $ghiChu,
            'ngay_duyet' => date('Y-m-d H:i:s')
        ]);
    }

    public function layDanhSachChoDuyet(int $limit, int $offset): array
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT 
                    tt.*,
                    dh.ma_don_hang,
                    dh.tong_thanh_toan,
                    dh.ngay_tao as ngay_tao_don,
                    nd.ho_ten,
                    nd.email,
                    nd.sdt
                FROM {$this->table} tt
                INNER JOIN don_hang dh ON tt.don_hang_id = dh.id
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
                WHERE tt.trang_thai_duyet = 'CHO_DUYET'
                ORDER BY tt.ngay_thanh_toan DESC
                LIMIT $limit OFFSET $offset";
        
        return $this->query($sql);
    }

    public function demChoDuyet(): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE trang_thai_duyet = 'CHO_DUYET'";
        
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    public function tuChoiThanhToan(int $id, int $nguoiDuyetId, ?string $ghiChu = null): int
    {
        return $this->update($id, [
            'nguoi_duyet_id' => $nguoiDuyetId,
            'trang_thai_duyet' => 'THAT_BAI',
            'ghi_chu_duyet' => $ghiChu,
            'ngay_duyet' => date('Y-m-d H:i:s')
        ]);
    }

    public function findById(int $id): ?array
    {
        $id = (int)$id;
        
        $sql = "SELECT * FROM {$this->table}
                WHERE id = $id
                LIMIT 1";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }

    public function layDanhSachVoiFilter(
        string $paymentMethod = '',
        string $status = '',
        string $search = '',
        int $limit = 20,
        int $offset = 0
    ): array {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        
        $where = [];
        

        if ($paymentMethod !== '') {
            $safeMethod = addslashes($paymentMethod);
            $where[] = "tt.phuong_thuc = '$safeMethod'";
        }
        

        if ($status !== '') {
            $safeStatus = addslashes($status);
            $where[] = "tt.trang_thai_duyet = '$safeStatus'";
        }
        

        if ($search !== '') {
            $safeSearch = addslashes($search);
            $where[] = "(dh.ma_don_hang LIKE '%$safeSearch%' 
                        OR tt.gateway_transaction_id LIKE '%$safeSearch%' 
                        OR nd.ho_ten LIKE '%$safeSearch%')";
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT 
                    tt.*,
                    dh.ma_don_hang,
                    dh.tong_thanh_toan,
                    dh.ngay_tao as ngay_tao_don,
                    nd.ho_ten as customer_name,
                    nd.email as customer_email,
                    nd.sdt as customer_phone
                FROM {$this->table} tt
                INNER JOIN don_hang dh ON tt.don_hang_id = dh.id
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
                $whereClause
                ORDER BY tt.ngay_thanh_toan DESC
                LIMIT $limit OFFSET $offset";
        
        return $this->query($sql);
    }

    public function demVoiFilter(
        string $paymentMethod = '',
        string $status = '',
        string $search = ''
    ): int {
        $where = [];
        
        if ($paymentMethod !== '') {
            $safeMethod = addslashes($paymentMethod);
            $where[] = "tt.phuong_thuc = '$safeMethod'";
        }
        
        if ($status !== '') {
            $safeStatus = addslashes($status);
            $where[] = "tt.trang_thai_duyet = '$safeStatus'";
        }
        
        if ($search !== '') {
            $safeSearch = addslashes($search);
            $where[] = "(dh.ma_don_hang LIKE '%$safeSearch%' 
                        OR tt.gateway_transaction_id LIKE '%$safeSearch%' 
                        OR nd.ho_ten LIKE '%$safeSearch%')";
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} tt
                INNER JOIN don_hang dh ON tt.don_hang_id = dh.id
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
                $whereClause";
        
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    public function layTheoKhoangNgay(string $from, string $to): array
    {
        $safeFrom = addslashes($from);
        $safeTo = addslashes($to);
        
        $sql = "SELECT 
                    tt.*,
                    dh.ma_don_hang,
                    nd.ho_ten as customer_name,
                    nd.email as customer_email
                FROM {$this->table} tt
                INNER JOIN don_hang dh ON tt.don_hang_id = dh.id
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
                WHERE DATE(tt.created_at) BETWEEN '$safeFrom' AND '$safeTo'
                   OR DATE(tt.ngay_thanh_toan) BETWEEN '$safeFrom' AND '$safeTo'
                ORDER BY tt.ngay_thanh_toan DESC";
        
        return $this->query($sql);
    }
}
