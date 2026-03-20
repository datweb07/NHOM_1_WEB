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

    public function __construct()
    {
        parent::__construct('danh_muc');
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
            $dbKeyword = addslashes(trim($keyword));
            $where[] = "(dm.ten LIKE '%$dbKeyword%' OR dm.slug LIKE '%$dbKeyword%')";
        }

        if ($trangThai !== null) {
            $where[] = "dm.trang_thai = $trangThai";
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT dm.*, cha.ten AS ten_danh_muc_cha,
                       (SELECT COUNT(*) FROM san_pham sp WHERE sp.danh_muc_id = dm.id) AS tong_san_pham
                FROM {$this->table} dm
                LEFT JOIN {$this->table} cha ON dm.danh_muc_cha_id = cha.id
                $whereSql
                ORDER BY dm.thu_tu ASC, dm.id DESC";

        return $this->query($sql);
    }

    public function layDanhMucCha(int $excludeId = 0): array
    {
        $excludeSql = $excludeId > 0 ? "AND id <> $excludeId" : '';
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
        $sql = "SELECT id FROM {$this->table} WHERE id = $id LIMIT 1";
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
        ];
    }
}
