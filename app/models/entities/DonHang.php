<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class DonHang extends BaseModel
{
    public function __construct()
    {
        parent::__construct('don_hang');
    }

    public function layDanhSach(?string $trangThai = null, string $sortBy = 'ngay_tao', string $sortOrder = 'DESC'): array
    {
        $where = '';
        if ($trangThai !== null && $trangThai !== '') {
            $safeTrangThai = addslashes($trangThai);
            $where = "WHERE dh.trang_thai = '$safeTrangThai'";
        }

        // Validate sort column
        $allowedColumns = ['id', 'ma_don_hang', 'tong_tien', 'ngay_tao', 'trang_thai'];
        if (!in_array($sortBy, $allowedColumns, true)) {
            $sortBy = 'ngay_tao';
        }

        // Validate sort order
        $sortOrder = strtoupper($sortOrder);
        if (!in_array($sortOrder, ['ASC', 'DESC'], true)) {
            $sortOrder = 'DESC';
        }

        $sql = "SELECT dh.*, nd.ho_ten, nd.email,
					   COUNT(ct.id) AS tong_san_pham
				FROM {$this->table} dh
				LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
				LEFT JOIN chi_tiet_don ct ON ct.don_hang_id = dh.id
				$where
				GROUP BY dh.id
				ORDER BY dh.$sortBy $sortOrder";

        return $this->query($sql);
    }

    public function layChiTietDonHang(int $id): ?array
    {
        $sql = "SELECT dh.*, nd.ho_ten, nd.email, nd.sdt
				FROM {$this->table} dh
				LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
				WHERE dh.id = $id
				LIMIT 1";

        $rows = $this->query($sql);
        if (empty($rows)) {
            return null;
        }

        return $rows[0];
    }

    public function laySanPhamTrongDon(int $donHangId): array
    {
        $sql = "SELECT ct.*, pbs.ten_phien_ban, pbs.mau_sac, pbs.dung_luong,
					   sp.ten_san_pham
				FROM chi_tiet_don ct
				LEFT JOIN phien_ban_san_pham pbs ON ct.phien_ban_id = pbs.id
				LEFT JOIN san_pham sp ON pbs.san_pham_id = sp.id
				WHERE ct.don_hang_id = $donHangId
				ORDER BY ct.id ASC";

        return $this->query($sql);
    }

    public function trangThaiHopLe(string $from, string $to): bool
    {
        // Workflow: CHO_DUYET → DA_XAC_NHAN → DANG_GIAO → DA_GIAO → HOAN_THANH
        // Allow DA_HUY from any status
        // Allow TRA_HANG from DA_GIAO
        $allowed = [
            'CHO_DUYET' => ['DA_XAC_NHAN', 'DA_HUY'],
            'DA_XAC_NHAN' => ['DANG_GIAO', 'DA_HUY'],
            'DANG_GIAO' => ['DA_GIAO', 'DA_HUY'],
            'DA_GIAO' => ['HOAN_THANH', 'TRA_HANG', 'DA_HUY'],
            'HOAN_THANH' => [],
            'DA_HUY' => [],
            'TRA_HANG' => [],
        ];

        if (!isset($allowed[$from])) {
            return false;
        }

        return in_array($to, $allowed[$from], true);
    }

    public function layTrangThaiKeTiep(string $trangThaiHienTai): array
    {
        $next = [
            'CHO_DUYET' => ['DA_XAC_NHAN', 'DA_HUY'],
            'DA_XAC_NHAN' => ['DANG_GIAO', 'DA_HUY'],
            'DANG_GIAO' => ['DA_GIAO', 'DA_HUY'],
            'DA_GIAO' => ['HOAN_THANH', 'TRA_HANG', 'DA_HUY'],
            'HOAN_THANH' => [],
            'DA_HUY' => [],
            'TRA_HANG' => [],
        ];

        return $next[$trangThaiHienTai] ?? [];
    }

    public function capNhatTrangThai(int $id, string $trangThaiMoi): int
    {
        return $this->update($id, ['trang_thai' => addslashes($trangThaiMoi)]);
    }

    /**
     * Search orders by ma_don_hang or customer name
     * 
     * @param string $keyword Search keyword
     * @param string|null $trangThai Optional status filter
     * @param int $limit Records per page
     * @param int $offset Starting offset for pagination
     * @return array List of orders matching search criteria
     */
    public function timKiem(string $keyword, ?string $trangThai = null, int $limit = 20, int $offset = 0): array
    {
        $safeKeyword = addslashes($keyword);
        
        $where = "(dh.ma_don_hang LIKE '%$safeKeyword%' OR nd.ho_ten LIKE '%$safeKeyword%')";
        
        if ($trangThai !== null && $trangThai !== '') {
            $safeTrangThai = addslashes($trangThai);
            $where .= " AND dh.trang_thai = '$safeTrangThai'";
        }

        $sql = "SELECT dh.*, nd.ho_ten, nd.email,
                       COUNT(ct.id) AS tong_san_pham
                FROM {$this->table} dh
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
                LEFT JOIN chi_tiet_don ct ON ct.don_hang_id = dh.id
                WHERE $where
                GROUP BY dh.id
                ORDER BY dh.ngay_tao DESC
                LIMIT $limit OFFSET $offset";

        return $this->query($sql);
    }

    /**
     * Filter orders by date range
     * 
     * @param string $from Start date (YYYY-MM-DD format)
     * @param string $to End date (YYYY-MM-DD format)
     * @param string|null $trangThai Optional status filter
     * @return array List of orders within date range
     */
    public function layTheoKhoangNgay(string $from, string $to, ?string $trangThai = null): array
    {
        $safeFrom = addslashes($from);
        $safeTo = addslashes($to);
        
        $where = "DATE(dh.ngay_tao) BETWEEN '$safeFrom' AND '$safeTo'";
        
        if ($trangThai !== null && $trangThai !== '') {
            $safeTrangThai = addslashes($trangThai);
            $where .= " AND dh.trang_thai = '$safeTrangThai'";
        }

        $sql = "SELECT dh.*, nd.ho_ten, nd.email,
                       COUNT(ct.id) AS tong_san_pham
                FROM {$this->table} dh
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
                LEFT JOIN chi_tiet_don ct ON ct.don_hang_id = dh.id
                WHERE $where
                GROUP BY dh.id
                ORDER BY dh.ngay_tao DESC";

        return $this->query($sql);
    }

    /**
     * Filter orders by payment method
     * 
     * @param string $phuongThuc Payment method (COD, CHUYEN_KHOAN, QR, TRA_GOP, VI_DIEN_TU)
     * @return array List of orders with specified payment method
     */
    public function layTheoPhuongThuc(string $phuongThuc): array
    {
        $safePhuongThuc = addslashes($phuongThuc);

        $sql = "SELECT dh.*, nd.ho_ten, nd.email,
                       COUNT(ct.id) AS tong_san_pham,
                       tt.phuong_thuc
                FROM {$this->table} dh
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
                LEFT JOIN chi_tiet_don ct ON ct.don_hang_id = dh.id
                LEFT JOIN thanh_toan tt ON tt.don_hang_id = dh.id
                WHERE tt.phuong_thuc = '$safePhuongThuc'
                GROUP BY dh.id
                ORDER BY dh.ngay_tao DESC";

        return $this->query($sql);
    }

    /**
     * Count orders for pagination
     * 
     * @param string|null $trangThai Optional status filter
     * @return int Total count of orders
     */
    public function demDonHang(?string $trangThai = null): int
    {
        $where = '';
        if ($trangThai !== null && $trangThai !== '') {
            $safeTrangThai = addslashes($trangThai);
            $where = "WHERE trang_thai = '$safeTrangThai'";
        }

        $sql = "SELECT COUNT(*) as total FROM {$this->table} $where";
        
        $result = $this->query($sql);
        return (int)($result[0]['total'] ?? 0);
    }

    /**
     * Lấy đơn hàng theo user
     */
    public function layDonHangTheoUser(int $nguoiDungId, int $limit = 10, int $offset = 0): array
    {
        $nguoiDungId = (int)$nguoiDungId;
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT * FROM {$this->table}
                WHERE nguoi_dung_id = $nguoiDungId
                ORDER BY ngay_tao DESC
                LIMIT $limit OFFSET $offset";

        return $this->query($sql);
    }

    /**
     * Đếm đơn hàng theo user
     */
    public function demDonHangTheoUser(int $nguoiDungId): int
    {
        $nguoiDungId = (int)$nguoiDungId;
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table}
                WHERE nguoi_dung_id = $nguoiDungId";
        
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }
}
