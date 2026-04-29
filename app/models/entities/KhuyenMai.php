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

    public function layDanhSach(string $trangThai = '', int $limit = 20, int $offset = 0, ?string $tuNgay = null, ?string $denNgay = null): array
    {
        $where = [];
        if ($trangThai !== '') {
            $where[] = "trang_thai = '" . addslashes($trangThai) . "'";
        }
        
        if ($tuNgay !== null && $tuNgay !== '') {
            $where[] = "ngay_bat_dau >= '" . addslashes($tuNgay) . "'";
        }
        
        if ($denNgay !== null && $denNgay !== '') {
            $where[] = "ngay_ket_thuc <= '" . addslashes($denNgay) . "'";
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT * FROM {$this->table} 
                $whereClause
                ORDER BY ngay_bat_dau DESC, id DESC
                LIMIT $limit OFFSET $offset";
        return $this->query($sql);
    }

    public function demKhuyenMai(string $trangThai = '', ?string $tuNgay = null, ?string $denNgay = null): int
    {
        $where = [];
        if ($trangThai !== '') {
            $where[] = "trang_thai = '" . addslashes($trangThai) . "'";
        }
        
        if ($tuNgay !== null && $tuNgay !== '') {
            $where[] = "ngay_bat_dau >= '" . addslashes($tuNgay) . "'";
        }
        
        if ($denNgay !== null && $denNgay !== '') {
            $where[] = "ngay_ket_thuc <= '" . addslashes($denNgay) . "'";
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} $whereClause";
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    public function layDanhSachSanPhamLienKet(int $khuyenMaiId): array
    {
        $sql = "SELECT sp.*, spkm.khuyen_mai_id
                FROM san_pham sp
                INNER JOIN san_pham_khuyen_mai spkm ON sp.id = spkm.san_pham_id
                WHERE spkm.khuyen_mai_id = $khuyenMaiId
                ORDER BY sp.ten_san_pham ASC";
        return $this->query($sql);
    }

    public function xoaLienKetSanPham(int $khuyenMaiId): bool
    {
        $sql = "DELETE FROM san_pham_khuyen_mai WHERE khuyen_mai_id = $khuyenMaiId";
        $this->query($sql);
        return true;
    }

    public function themLienKetSanPham(int $khuyenMaiId, array $sanPhamIds): bool
    {
        if (empty($sanPhamIds)) {
            return true;
        }

        $values = [];
        foreach ($sanPhamIds as $sanPhamId) {
            $values[] = "($khuyenMaiId, " . (int)$sanPhamId . ")";
        }

        $sql = "INSERT INTO san_pham_khuyen_mai (khuyen_mai_id, san_pham_id) VALUES " . implode(', ', $values);
        $this->query($sql);
        return true;
    }

    public function capNhatTrangThaiHetHan(): int
    {
        $sql = "UPDATE {$this->table} 
                SET trang_thai = 'DA_HET_HAN'
                WHERE trang_thai = 'HOAT_DONG' 
                AND ngay_ket_thuc < NOW()";
        $this->query($sql);
        return 0; 
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
