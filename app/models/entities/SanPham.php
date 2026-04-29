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

    private function buildWhereClause(
        ?string $keyword = null,
        int $danhMucId = 0,
        ?float $giaMin = null,
        ?float $giaMax = null,
        ?string $trangThai = 'CON_BAN',
        ?array $hangFilters = null,
        ?array $giaKhoangFilters = null
    ): string {
        $whereConditions = [];

        if ($trangThai !== null) {
            $whereConditions[] = "sp.trang_thai = '" . addslashes($trangThai) . "'";
        }

        if ($keyword !== null && trim($keyword) !== '') {
            $dbKeyword = $this->escapeLikeKeyword($keyword);
            $whereConditions[] = "(sp.ten_san_pham LIKE '%$dbKeyword%' OR sp.id = '$dbKeyword' OR sp.hang_san_xuat LIKE '%$dbKeyword%')";
        }

        if ($danhMucId > 0) {
            $danhMucId = (int)$danhMucId;
            $whereConditions[] = "sp.danh_muc_id IN (
                SELECT dm.id
                FROM danh_muc dm
                WHERE dm.id = $danhMucId OR dm.danh_muc_cha_id = $danhMucId
            )";
        }

        if (!empty($hangFilters)) {
            $hangConditions = [];
            foreach ($hangFilters as $hang) {
                $hang = trim((string)$hang);
                if ($hang === '') {
                    continue;
                }
                $hangConditions[] = "sp.hang_san_xuat = '" . addslashes($hang) . "'";
            }

            if (!empty($hangConditions)) {
                $whereConditions[] = '(' . implode(' OR ', $hangConditions) . ')';
            }
        }

        if (!empty($giaKhoangFilters)) {
            $giaKhoangConditions = [];
            foreach ($giaKhoangFilters as $khoang) {
                $khoang = trim((string)$khoang);
                if (!preg_match('/^(\d+)-(\d+)$/', $khoang, $matches)) {
                    continue;
                }

                $minVal = (float)$matches[1];
                $maxVal = (float)$matches[2];
                if ($minVal < 0 || $maxVal <= 0 || $maxVal < $minVal) {
                    continue;
                }

                $giaKhoangConditions[] = '(sp.gia_hien_thi >= ' . $minVal . ' AND sp.gia_hien_thi <= ' . $maxVal . ')';
            }

            if (!empty($giaKhoangConditions)) {
                $whereConditions[] = '(' . implode(' OR ', $giaKhoangConditions) . ')';
            }
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

    public function demSanPham(
        ?string $keyword = null,
        int $danhMucId = 0,
        ?float $giaMin = null,
        ?float $giaMax = null,
        ?array $hangFilters = null,
        ?array $giaKhoangFilters = null
    ): int {
        $whereClause = $this->buildWhereClause($keyword, $danhMucId, $giaMin, $giaMax, 'CON_BAN', $hangFilters, $giaKhoangFilters);
        $sql = "SELECT COUNT(*) as total FROM {$this->table} sp $whereClause";
        $result = parent::query($sql);

        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    public function layDanhSachPhanTrang(
        ?string $keyword = null,
        int $danhMucId = 0,
        ?float $giaMin = null,
        ?float $giaMax = null,
        int $limit = 15,
        int $offset = 0,
        string $sortBy = 'ngay_tao',
        string $sortOrder = 'DESC',
        ?array $hangFilters = null,
        ?array $giaKhoangFilters = null
    ): array {
        $whereClause = $this->buildWhereClause($keyword, $danhMucId, $giaMin, $giaMax, 'CON_BAN', $hangFilters, $giaKhoangFilters);
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $allowedColumns = ['id', 'ten_san_pham', 'hang_san_xuat', 'gia_hien_thi', 'ngay_tao', 'trang_thai'];
        if (!in_array($sortBy, $allowedColumns, true)) {
            $sortBy = 'ngay_tao';
        }

        $sortOrder = strtoupper($sortOrder);
        if (!in_array($sortOrder, ['ASC', 'DESC'], true)) {
            $sortOrder = 'DESC';
        }

        $sql = "SELECT sp.*, dm.ten AS ten_danh_muc,
                       (SELECT url_anh FROM hinh_anh_san_pham 
                        WHERE san_pham_id = sp.id AND la_anh_chinh = 1 
                        LIMIT 1) as anh_chinh,
                       COALESCE(
                           (SELECT MIN(pb.gia_ban) 
                            FROM phien_ban_san_pham pb 
                            WHERE pb.san_pham_id = sp.id),
                           sp.gia_hien_thi
                       ) AS gia_hien_thi
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
                $whereClause
                ORDER BY sp.$sortBy $sortOrder
                LIMIT $limit OFFSET $offset";

        return parent::query($sql);
    }

    public function layDanhSachHangSanXuat(?string $keyword = null, int $danhMucId = 0): array
    {
        $whereClause = $this->buildWhereClause($keyword, $danhMucId, null, null, 'CON_BAN', null, null);
        $sql = "SELECT DISTINCT sp.hang_san_xuat
                FROM {$this->table} sp
                $whereClause
                AND sp.hang_san_xuat IS NOT NULL
                AND sp.hang_san_xuat != ''
                ORDER BY sp.hang_san_xuat ASC";

        if ($whereClause === '') {
            $sql = "SELECT DISTINCT sp.hang_san_xuat
                    FROM {$this->table} sp
                    WHERE sp.trang_thai = 'CON_BAN'
                    AND sp.hang_san_xuat IS NOT NULL
                    AND sp.hang_san_xuat != ''
                    ORDER BY sp.hang_san_xuat ASC";
        }

        return parent::query($sql);
    }

    public function layKhoangGiaSanPham(?string $keyword = null, int $danhMucId = 0, ?array $hangFilters = null): array
    {
        $whereClause = $this->buildWhereClause($keyword, $danhMucId, null, null, 'CON_BAN', $hangFilters, null);

        if ($whereClause === '') {
            $whereClause = "WHERE sp.trang_thai = 'CON_BAN'";
        }

        $sql = "SELECT MIN(sp.gia_hien_thi) AS min_gia, MAX(sp.gia_hien_thi) AS max_gia
                FROM {$this->table} sp
                $whereClause
                AND sp.gia_hien_thi IS NOT NULL
                AND sp.gia_hien_thi > 0";

        $result = parent::query($sql);
        return $result[0] ?? ['min_gia' => 0, 'max_gia' => 0];
    }

    public function layDanhSachDanhMucHoatDong(): array
    {
        $sql = 'SELECT id, ten FROM danh_muc WHERE trang_thai = 1 ORDER BY thu_tu ASC, ten ASC';
        return parent::query($sql);
    }

    public function layTatCa(): array
    {
        $sql = "SELECT id, ten_san_pham, slug FROM {$this->table} WHERE slug IS NOT NULL AND slug != '' ORDER BY ten_san_pham ASC";
        return parent::query($sql);
    }

    public function layDanhSachChoSoSanh(): array
    {
        $sql = "SELECT sp.id,
                       sp.ten_san_pham,
                       sp.slug,
                       COALESCE(
                           (SELECT MIN(pb.gia_ban) 
                            FROM phien_ban_san_pham pb 
                            WHERE pb.san_pham_id = sp.id),
                           sp.gia_hien_thi
                       ) AS gia_hien_thi,
                       sp.hang_san_xuat,
                       sp.danh_muc_id,
                       dm.ten AS ten_danh_muc,
                       (SELECT ha.url_anh
                        FROM hinh_anh_san_pham ha
                        WHERE ha.san_pham_id = sp.id AND ha.la_anh_chinh = 1
                        LIMIT 1) AS anh_chinh
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON dm.id = sp.danh_muc_id
                WHERE sp.trang_thai = 'CON_BAN'
                  AND sp.slug IS NOT NULL
                  AND sp.slug != ''
                ORDER BY sp.ten_san_pham ASC";

        return parent::query($sql);
    }

    public function kiemTraCoDonHang(int $id): bool
    {
        $sql = "SELECT COUNT(*) as total FROM chi_tiet_don ctd
                INNER JOIN phien_ban_san_pham pbsp ON ctd.phien_ban_id = pbsp.id
                WHERE pbsp.san_pham_id = " . (int)$id;
        $result = parent::query($sql);
        return !empty($result) && (int)$result[0]['total'] > 0;
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
        return mysqli_affected_rows($this->link);
    }

    public function capNhatTrangThaiPhienBanKhiMoBan(int $sanPhamId): int
    {
        $sanPhamId = (int)$sanPhamId;
        $sql = "UPDATE phien_ban_san_pham
                SET trang_thai = CASE WHEN so_luong_ton > 0 THEN 'CON_HANG' ELSE 'HET_HANG' END
                WHERE san_pham_id = $sanPhamId";
        $this->query($sql);
        return mysqli_affected_rows($this->link);
    }

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

    public function laySanPhamNoiBat(int $limit = 8): array
    {
        $limit = max(1, (int)$limit);
        $sql = "SELECT sp.*, 
                       (SELECT url_anh FROM hinh_anh_san_pham 
                        WHERE san_pham_id = sp.id AND la_anh_chinh = 1 
                        LIMIT 1) as anh_chinh,
                       COALESCE(
                           (SELECT MIN(pb.gia_ban) 
                            FROM phien_ban_san_pham pb 
                            WHERE pb.san_pham_id = sp.id),
                           sp.gia_hien_thi
                       ) AS gia_hien_thi
                FROM {$this->table} sp
                WHERE sp.noi_bat = 1 AND sp.trang_thai = 'CON_BAN'
                ORDER BY sp.ngay_tao DESC
                LIMIT $limit";

        return parent::query($sql);
    }

    public function laySanPhamKhuyenMai(int $limit = 8): array
    {
        $limit = max(1, (int)$limit);
        $sql = "SELECT sp.*, 
                       km.loai_giam, 
                       km.gia_tri_giam, 
                       km.giam_toi_da,
                       km.ngay_ket_thuc,
                       (SELECT url_anh FROM hinh_anh_san_pham 
                        WHERE san_pham_id = sp.id AND la_anh_chinh = 1 
                        LIMIT 1) as anh_chinh,
                       COALESCE(
                           (SELECT MIN(pb.gia_ban) 
                            FROM phien_ban_san_pham pb 
                            WHERE pb.san_pham_id = sp.id),
                           sp.gia_hien_thi
                       ) AS gia_hien_thi
                FROM {$this->table} sp
                INNER JOIN san_pham_khuyen_mai spkm ON sp.id = spkm.san_pham_id
                INNER JOIN khuyen_mai km ON spkm.khuyen_mai_id = km.id
                WHERE sp.trang_thai = 'CON_BAN' 
                  AND km.trang_thai = 'HOAT_DONG'
                  AND (km.ngay_bat_dau IS NULL OR km.ngay_bat_dau <= NOW())
                  AND (km.ngay_ket_thuc IS NULL OR km.ngay_ket_thuc >= NOW())
                ORDER BY sp.ngay_tao DESC
                LIMIT $limit";

        return parent::query($sql);
    }

    public function laySanPhamTheoDanhMuc(string $slugDanhMuc, int $limit = 8): array
    {
        $limit = max(1, (int)$limit);
        $slugDanhMuc = mysqli_real_escape_string($this->link, $slugDanhMuc);

        $sql = "SELECT sp.*, 
                       (SELECT url_anh FROM hinh_anh_san_pham 
                        WHERE san_pham_id = sp.id AND la_anh_chinh = 1 
                        LIMIT 1) as anh_chinh,
                       COALESCE(
                           (SELECT MIN(pb.gia_ban) 
                            FROM phien_ban_san_pham pb 
                            WHERE pb.san_pham_id = sp.id),
                           sp.gia_hien_thi
                       ) AS gia_hien_thi
                FROM {$this->table} sp
                INNER JOIN danh_muc dm ON sp.danh_muc_id = dm.id
                WHERE dm.slug = '$slugDanhMuc' 
                  AND sp.trang_thai = 'CON_BAN'
                ORDER BY sp.ngay_tao DESC
                LIMIT $limit";

        return parent::query($sql);
    }

    public function tinhGiaSauKhuyenMai(float $giaGoc, string $loaiGiam, float $giaTriGiam, ?float $giamToiDa = null): float
    {
        if ($loaiGiam === 'PHAN_TRAM') {
            $tienGiam = $giaGoc * ($giaTriGiam / 100);
            if ($giamToiDa !== null && $tienGiam > $giamToiDa) {
                $tienGiam = $giamToiDa;
            }
            return $giaGoc - $tienGiam;
        }

        return max(0, $giaGoc - $giaTriGiam);
    }

    public function layChiTietTheoSlug(string $slug): ?array
    {
        $slug = mysqli_real_escape_string($this->link, $slug);

        $sql = "SELECT sp.*, dm.ten AS ten_danh_muc, dm.slug AS slug_danh_muc,
                       (SELECT url_anh FROM hinh_anh_san_pham 
                        WHERE san_pham_id = sp.id AND la_anh_chinh = 1 
                        LIMIT 1) as anh_chinh
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
                WHERE sp.slug = '$slug' AND sp.trang_thai = 'CON_BAN'
                LIMIT 1";

        $result = parent::query($sql);
        return !empty($result) ? $result[0] : null;
    }

    public function hienThiThongTin()
    {
        return "Sản phẩm: " . $this->tenSanPham .
            " | Hãng: " . $this->hangSanXuat .
            " | Giá: " . $this->giaHienThi .
            " | Trạng thái: " . $this->trangThai;
    }
}
