<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class DanhGia extends BaseModel
{
    protected ?int $id = null;
    protected ?int $nguoiDungId = null;
    protected ?int $sanPhamId = null;
    protected ?int $soSao = null;
    protected ?string $noiDung = null;
    protected ?string $ngayViet = null;

    public function __construct()
    {
        parent::__construct('danh_gia');
    }

    public function layTheoSanPham(int $sanPhamId): array
    {
        $sql = "SELECT dg.*, nd.ho_ten
				FROM {$this->table} dg
				LEFT JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
				WHERE dg.san_pham_id = " . (int)$sanPhamId . '
				ORDER BY dg.ngay_viet DESC';
        return $this->query($sql);
    }

    /**
     * Get reviews list with filtering and pagination
     * @param int|null $soSao Filter by rating (1-5)
     * @param int|null $sanPhamId Filter by product ID
     * @param int $limit Records per page
     * @param int $offset Starting record
     * @return array List of reviews with user and product info
     */
    public function layDanhSach(?int $soSao = null, ?int $sanPhamId = null, int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT dg.*, nd.ho_ten, nd.email, sp.ten_san_pham
                FROM {$this->table} dg
                LEFT JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                LEFT JOIN san_pham sp ON dg.san_pham_id = sp.id
                WHERE 1=1";
        
        if ($soSao !== null) {
            $sql .= " AND dg.so_sao = " . (int)$soSao;
        }
        
        if ($sanPhamId !== null) {
            $sql .= " AND dg.san_pham_id = " . (int)$sanPhamId;
        }
        
        $sql .= " ORDER BY dg.ngay_viet DESC
                  LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        return $this->query($sql);
    }

    /**
     * Search reviews by content or user name
     * @param string $keyword Search keyword
     * @param int $limit Records per page
     * @param int $offset Starting record
     * @return array Matching reviews
     */
    public function timKiem(string $keyword, int $limit = 20, int $offset = 0): array
    {
        $keyword = $this->escape($keyword);
        
        $sql = "SELECT dg.*, nd.ho_ten, nd.email, sp.ten_san_pham
                FROM {$this->table} dg
                LEFT JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                LEFT JOIN san_pham sp ON dg.san_pham_id = sp.id
                WHERE dg.noi_dung LIKE '%{$keyword}%'
                   OR nd.ho_ten LIKE '%{$keyword}%'
                ORDER BY dg.ngay_viet DESC
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        return $this->query($sql);
    }

    /**
     * Count reviews for pagination
     * @param int|null $soSao Filter by rating
     * @param int|null $sanPhamId Filter by product ID
     * @param string|null $keyword Search keyword
     * @return int Total count
     */
    public function demDanhGia(?int $soSao = null, ?int $sanPhamId = null, ?string $keyword = null): int
    {
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table} dg
                LEFT JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                WHERE 1=1";
        
        if ($soSao !== null) {
            $sql .= " AND dg.so_sao = " . (int)$soSao;
        }
        
        if ($sanPhamId !== null) {
            $sql .= " AND dg.san_pham_id = " . (int)$sanPhamId;
        }
        
        if ($keyword !== null && $keyword !== '') {
            $keyword = $this->escape($keyword);
            $sql .= " AND (dg.noi_dung LIKE '%{$keyword}%' OR nd.ho_ten LIKE '%{$keyword}%')";
        }
        
        $result = $this->query($sql);
        return (int)($result[0]['total'] ?? 0);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nguoi_dung_id' => $this->nguoiDungId,
            'san_pham_id' => $this->sanPhamId,
            'so_sao' => $this->soSao,
            'noi_dung' => $this->noiDung,
            'ngay_viet' => $this->ngayViet,
        ];
    }
}
