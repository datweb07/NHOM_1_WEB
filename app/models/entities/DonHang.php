<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class DonHang extends BaseModel
{
    public function __construct()
    {
        parent::__construct('don_hang');
    }

    public function layDanhSach(?string $trangThai = null): array
    {
        $where = '';
        if ($trangThai !== null && $trangThai !== '') {
            $safeTrangThai = addslashes($trangThai);
            $where = "WHERE dh.trang_thai = '$safeTrangThai'";
        }

        $sql = "SELECT dh.*, nd.ho_ten, nd.email,
					   COUNT(ct.id) AS tong_san_pham
				FROM {$this->table} dh
				LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
				LEFT JOIN chi_tiet_don ct ON ct.don_hang_id = dh.id
				$where
				GROUP BY dh.id
				ORDER BY dh.ngay_tao DESC";

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
        $allowed = [
            'CHO_DUYET' => ['DANG_GIAO', 'DA_HUY'],
            'DANG_GIAO' => ['HOAN_THANH', 'DA_HUY'],
            'HOAN_THANH' => [],
            'DA_HUY' => [],
        ];

        if (!isset($allowed[$from])) {
            return false;
        }

        return in_array($to, $allowed[$from], true);
    }

    public function layTrangThaiKeTiep(string $trangThaiHienTai): array
    {
        $next = [
            'CHO_DUYET' => ['DANG_GIAO', 'DA_HUY'],
            'DANG_GIAO' => ['HOAN_THANH', 'DA_HUY'],
            'HOAN_THANH' => [],
            'DA_HUY' => [],
        ];

        return $next[$trangThaiHienTai] ?? [];
    }

    public function capNhatTrangThai(int $id, string $trangThaiMoi): int
    {
        return $this->update($id, ['trang_thai' => addslashes($trangThaiMoi)]);
    }
}
