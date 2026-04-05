<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class DanhMuc extends BaseModel
{
    protected ?int $id = null;
    protected ?string $ten = null;
    protected ?string $slug = null;
    protected ?string $iconUrl = null;
    protected ?int $danhMucChaId = null;
    protected ?int $thuTu = 0;
    protected int $trangThai = 1;
    
    // Khai báo thêm 2 thuộc tính mới
    protected int $isNoiBat = 0;
    protected int $isGoiY = 0;

    public function __construct()
    {
        parent::__construct('danh_muc');
    }

    private function escapeLikeKeyword(string $keyword): string
    {
        return addslashes(trim($keyword));
    }

    public function buildFilter(int $trangThaiFilter): ?int
    {
        if ($trangThaiFilter === 0 || $trangThaiFilter === 1) {
            return $trangThaiFilter;
        }
        return null;
    }

    public function layDanhSach(?string $keyword = null, ?int $trangThai = null): array
    {
        $where = [];

        if ($keyword !== null && trim($keyword) !== '') {
            $dbKeyword = $this->escapeLikeKeyword($keyword);
            $where[] = "(dm.ten LIKE '%$dbKeyword%' OR dm.slug LIKE '%$dbKeyword%')";
        }

        if ($trangThai !== null) {
            $where[] = 'dm.trang_thai = ' . (int)$trangThai;
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        // Hierarchical ordering: parent categories first, then children
        $sql = "SELECT dm.*, cha.ten AS ten_danh_muc_cha,
                       (SELECT COUNT(*) FROM san_pham sp WHERE sp.danh_muc_id = dm.id) AS tong_san_pham,
                       CASE 
                           WHEN dm.danh_muc_cha_id IS NULL THEN dm.thu_tu
                           ELSE (SELECT cha2.thu_tu FROM {$this->table} cha2 WHERE cha2.id = dm.danh_muc_cha_id) + 0.001 * dm.thu_tu
                       END AS sort_order
                FROM {$this->table} dm
                LEFT JOIN {$this->table} cha ON dm.danh_muc_cha_id = cha.id
                $whereSql
                ORDER BY sort_order ASC, dm.danh_muc_cha_id IS NULL DESC, dm.thu_tu ASC, dm.id ASC";

        return $this->query($sql);
    }

    public function layDuongDanDayDu(int $id): string
    {
        $path = [];
        $currentId = $id;
        $visited = [];

        while ($currentId !== null && !in_array($currentId, $visited, true)) {
            $visited[] = $currentId;
            $category = $this->getById($currentId);
            
            if (!$category) {
                break;
            }

            array_unshift($path, $category['ten']);
            $currentId = $category['danh_muc_cha_id'];
        }

        return implode(' > ', $path);
    }

    public function layDanhMucCha(int $excludeId = 0): array
    {
        $excludeSql = $excludeId > 0 ? 'AND id <> ' . (int)$excludeId : '';
        $sql = "SELECT id, ten FROM {$this->table} WHERE trang_thai = 1 $excludeSql ORDER BY thu_tu ASC, ten ASC";
        return $this->query($sql);
    }

    public function tonTaiSlug(string $slug, int $excludeId = 0): bool
    {
        $safeSlug = addslashes($slug);
        $excludeSql = $excludeId > 0 ? "AND id <> $excludeId" : '';
        $sql = "SELECT id FROM {$this->table} WHERE slug = '$safeSlug' $excludeSql LIMIT 1";
        $result = $this->query($sql);
        return !empty($result);
    }

    public function tonTaiDanhMuc(int $id): bool
    {
        $sql = 'SELECT id FROM ' . $this->table . ' WHERE id = ' . (int)$id . ' LIMIT 1';
        $result = $this->query($sql);
        return !empty($result);
    }

    public function anDanhMuc(int $id): int
    {
        return $this->update($id, ['trang_thai' => 0]);
    }

    public function hienDanhMuc(int $id): int
    {
        return $this->update($id, ['trang_thai' => 1]);
    }

    public function kiemTraCoSanPham(int $id): bool
    {
        $sql = "SELECT COUNT(*) as total FROM san_pham WHERE danh_muc_id = " . (int)$id;
        $result = $this->query($sql);
        return !empty($result) && (int)$result[0]['total'] > 0;
    }

    /**
     * Lấy danh mục hiển thị cho client (trang_thai = 1)
     */
    public function layDanhMucHienThi(int $limit = 12): array
    {
        $limit = max(1, (int)$limit);
        $sql = "SELECT id, ten, slug, icon_url, danh_muc_cha_id
                FROM {$this->table}
                WHERE trang_thai = 1
                ORDER BY thu_tu ASC, ten ASC
                LIMIT $limit";
        
        return $this->query($sql);
    }

    /**
     * Lấy danh mục Nổi Bật (is_noi_bat = 1)
     */
    public function layDanhMucNoiBat(int $limit = 16): array
    {
        $limit = max(1, (int)$limit);
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_noi_bat = 1 AND trang_thai = 1 
                ORDER BY thu_tu ASC LIMIT $limit";
        return $this->query($sql);
    }

    /**
     * Lấy danh mục Gợi Ý (is_goi_y = 1) và Loại trừ Nổi Bật (is_noi_bat = 0)
     */
    public function layDanhMucGoiY(int $limit = 30): array
    {
        $limit = max(1, (int)$limit);
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_goi_y = 1 AND is_noi_bat = 0 AND trang_thai = 1 
                ORDER BY thu_tu ASC LIMIT $limit";
        return $this->query($sql);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ten' => $this->ten,
            'slug' => $this->slug,
            'icon_url' => $this->iconUrl,
            'danh_muc_cha_id' => $this->danhMucChaId,
            'thu_tu' => $this->thuTu,
            'trang_thai' => $this->trangThai,
            // Thêm 2 trường mới vào mảng trả về
            'is_noi_bat' => $this->isNoiBat,
            'is_goi_y' => $this->isGoiY,
        ];
    }
}