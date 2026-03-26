<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class SanPham extends BaseModel
{
    private $id;
    private $danhMucId;
    private $tenSanPham;
    private $slug;
    private $hangSanXuat;
    private $moTa;
    private $giaHienThi;
    private $diemDanhGia;
    private $trangThai;
    private $noiBat;
    private $ngayTao;
    private $ngayCapNhat;

    public function __construct(
        $id = null,
        $danhMucId = null,
        $tenSanPham = "",
        $slug = "",
        $hangSanXuat = "",
        $moTa = "",
        $giaHienThi = 0,
        $diemDanhGia = 0,
        $trangThai = "CON_BAN",
        $noiBat = 0,
        $ngayTao = null,
        $ngayCapNhat = null
    ) {
        parent::__construct('san_pham');

        $this->id = $id;
        $this->danhMucId = $danhMucId;
        $this->tenSanPham = $tenSanPham;
        $this->slug = $slug;
        $this->hangSanXuat = $hangSanXuat;
        $this->moTa = $moTa;
        $this->giaHienThi = $giaHienThi;
        $this->diemDanhGia = $diemDanhGia;
        $this->trangThai = $trangThai;
        $this->noiBat = $noiBat;
        $this->ngayTao = $ngayTao;
        $this->ngayCapNhat = $ngayCapNhat;
    }

    private function escapeLikeKeyword(string $keyword): string
    {
        return addslashes(trim($keyword));
    }

    private function buildWhereClause(?string $keyword = null, int $danhMucId = 0, ?float $giaMin = null, ?float $giaMax = null): string
    {
        $whereConditions = [];

        if ($keyword !== null && trim($keyword) !== '') {
            $dbKeyword = $this->escapeLikeKeyword($keyword);
            $whereConditions[] = "(sp.ten_san_pham LIKE '%$dbKeyword%' OR sp.id = '$dbKeyword' OR sp.hang_san_xuat LIKE '%$dbKeyword%')";
        }

        if ($danhMucId > 0) {
            $whereConditions[] = 'sp.danh_muc_id = ' . (int)$danhMucId;
        }

        if ($giaMin !== null) {
            $whereConditions[] = 'sp.gia_hien_thi >= ' . (float)$giaMin;
        }

        if ($giaMax !== null) {
            $whereConditions[] = 'sp.gia_hien_thi <= ' . (float)$giaMax;
        }

        if (empty($whereConditions)) {
            return '';
        }

        return 'WHERE ' . implode(' AND ', $whereConditions);
    }

    public function demSanPham(?string $keyword = null, int $danhMucId = 0, ?float $giaMin = null, ?float $giaMax = null): int
    {
        $whereClause = $this->buildWhereClause($keyword, $danhMucId, $giaMin, $giaMax);
        $sql = "SELECT COUNT(*) as total FROM {$this->table} sp $whereClause";
        $result = parent::query($sql);

        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    public function layDanhSachPhanTrang(?string $keyword = null, int $danhMucId = 0, ?float $giaMin = null, ?float $giaMax = null, int $limit = 15, int $offset = 0): array
    {
        $whereClause = $this->buildWhereClause($keyword, $danhMucId, $giaMin, $giaMax);
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT sp.*, dm.ten AS ten_danh_muc
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
                $whereClause
                ORDER BY sp.ngay_tao DESC
                LIMIT $limit OFFSET $offset";

        return parent::query($sql);
    }

    public function layDanhSachDanhMucHoatDong(): array
    {
        $sql = 'SELECT id, ten FROM danh_muc WHERE trang_thai = 1 ORDER BY thu_tu ASC, ten ASC';
        return parent::query($sql);
    }

    public function ngungBan(int $id): int
    {
        return $this->update((int)$id, ['trang_thai' => 'NGUNG_BAN']);
    }

    public function moBanSanPham(int $id): int
    {
        return $this->update((int)$id, ['trang_thai' => 'CON_BAN']);
    }

    public function capNhatTrangThaiPhienBanKhiNgungBan(int $sanPhamId): int
    {
        $sanPhamId = (int)$sanPhamId;
        $sql = "UPDATE phien_ban_san_pham SET trang_thai = 'NGUNG_BAN' WHERE san_pham_id = $sanPhamId";
        $this->query($sql);
        return mysqli_affected_rows($this -> link);
    }

    public function capNhatTrangThaiPhienBanKhiMoBan(int $sanPhamId): int
    {
        $sanPhamId = (int)$sanPhamId;
        $sql = "UPDATE phien_ban_san_pham
                SET trang_thai = CASE WHEN so_luong_ton > 0 THEN 'CON_HANG' ELSE 'HET_HANG' END
                WHERE san_pham_id = $sanPhamId";
              $this->query($sql);
              return mysqli_affected_rows($this -> link);
    }

    // public function query($sql)
    // {
    //     $trimmed = ltrim($sql);
    //     $command = strtoupper(strtok($trimmed, " \t\n\r"));

    //     if (in_array($command, ['UPDATE', 'INSERT', 'DELETE', 'REPLACE'], true)) {
    //         chayTruyVanKhongTraVeDL($this->link, $sql);
    //         return mysqli_affected_rows($this->link);
    //     }

    //     return parent::query($sql);
    // }

    // ===== Getter =====

    public function getId()
    {
        return $this->id;
    }

    public function getDanhMucId()
    {
        return $this->danhMucId;
    }

    public function getTenSanPham()
    {
        return $this->tenSanPham;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getHangSanXuat()
    {
        return $this->hangSanXuat;
    }

    public function getMoTa()
    {
        return $this->moTa;
    }

    public function getGiaHienThi()
    {
        return $this->giaHienThi;
    }

    public function getDiemDanhGia()
    {
        return $this->diemDanhGia;
    }

    public function getTrangThai()
    {
        return $this->trangThai;
    }

    public function getNoiBat()
    {
        return $this->noiBat;
    }

    public function getNgayTao()
    {
        return $this->ngayTao;
    }

    public function getNgayCapNhat()
    {
        return $this->ngayCapNhat;
    }

    // ===== Setter =====

    public function setDanhMucId($danhMucId)
    {
        $this->danhMucId = $danhMucId;
    }

    public function setTenSanPham($tenSanPham)
    {
        $this->tenSanPham = $tenSanPham;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setHangSanXuat($hangSanXuat)
    {
        $this->hangSanXuat = $hangSanXuat;
    }

    public function setMoTa($moTa)
    {
        $this->moTa = $moTa;
    }

    public function setGiaHienThi($giaHienThi)
    {
        $this->giaHienThi = $giaHienThi;
    }

    public function setDiemDanhGia($diemDanhGia)
    {
        $this->diemDanhGia = $diemDanhGia;
    }

    public function setTrangThai($trangThai)
    {
        $this->trangThai = $trangThai;
    }

    public function setNoiBat($noiBat)
    {
        $this->noiBat = $noiBat;
    }

    // ===== Method hiển thị =====

    public function hienThiThongTin()
    {
        return "Sản phẩm: " . $this->tenSanPham .
            " | Hãng: " . $this->hangSanXuat .
            " | Giá: " . $this->giaHienThi .
            " | Trạng thái: " . $this->trangThai;
    }
}
