<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class BannerQuangCao extends BaseModel
{
    protected ?int $id = null;
    protected ?string $tieuDe = null;
    protected ?string $hinhAnhDesktop = null;
    protected ?string $hinhAnhMobile = null;
    protected ?string $linkDich = null;
    protected string $viTri = 'HOME_HERO';
    protected int $thuTu = 0;
    protected ?string $ngayBatDau = null;
    protected ?string $ngayKetThuc = null;
    protected int $trangThai = 1;

    public function __construct()
    {
        parent::__construct('banner_quang_cao');
    }

    // Lấy danh sách banner với lọc và phân trang
    public function layDanhSach(string $viTri = '', int $trangThai = -1, int $limit = 20, int $offset = 0): array
    {
        $where = [];
        
        if ($viTri !== '') {
            $where[] = "vi_tri = '" . addslashes($viTri) . "'";
        }
        
        if ($trangThai !== -1) {
            $where[] = "trang_thai = " . (int)$trangThai;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT * FROM {$this->table} 
                $whereClause
                ORDER BY thu_tu ASC, id DESC
                LIMIT $limit OFFSET $offset";
        return $this->query($sql);
    }

    // Đếm số lượng banner
    public function demBanner(string $viTri = '', int $trangThai = -1): int
    {
        $where = [];
        
        if ($viTri !== '') {
            $where[] = "vi_tri = '" . addslashes($viTri) . "'";
        }
        
        if ($trangThai !== -1) {
            $where[] = "trang_thai = " . (int)$trangThai;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} $whereClause";
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    // Lấy banner đang hoạt động theo vị trí
    public function layBannerTheoViTri(string $viTri): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE vi_tri = '" . addslashes($viTri) . "'
                AND trang_thai = 1
                AND (ngay_bat_dau IS NULL OR ngay_bat_dau <= NOW())
                AND (ngay_ket_thuc IS NULL OR ngay_ket_thuc >= NOW())
                ORDER BY thu_tu ASC, id DESC";
        return $this->query($sql);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tieu_de' => $this->tieuDe,
            'hinh_anh_desktop' => $this->hinhAnhDesktop,
            'hinh_anh_mobile' => $this->hinhAnhMobile,
            'link_dich' => $this->linkDich,
            'vi_tri' => $this->viTri,
            'thu_tu' => $this->thuTu,
            'ngay_bat_dau' => $this->ngayBatDau,
            'ngay_ket_thuc' => $this->ngayKetThuc,
            'trang_thai' => $this->trangThai,
        ];
    }
}
