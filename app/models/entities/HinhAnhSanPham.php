<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class HinhAnhSanPham extends BaseModel
{
    public function __construct()
    {
        parent::__construct('hinh_anh_san_pham');
    }

    /**
     * Lấy hình ảnh theo sản phẩm
     */
    public function layHinhAnhTheoSanPham(int $sanPhamId, ?int $phienBanId = null): array
    {
        $sanPhamId = (int)$sanPhamId;
        $where = "san_pham_id = $sanPhamId";
        
        if ($phienBanId !== null) {
            $phienBanId = (int)$phienBanId;
            $where .= " AND (phien_ban_id = $phienBanId OR phien_ban_id IS NULL)";
        }
        
        $sql = "SELECT * FROM {$this->table}
                WHERE $where
                ORDER BY la_anh_chinh DESC, thu_tu ASC";
        
        return $this->query($sql);
    }

    /**
     * Lấy ảnh chính của sản phẩm
     */
    public function layAnhChinh(int $sanPhamId): ?array
    {
        $sanPhamId = (int)$sanPhamId;
        $sql = "SELECT * FROM {$this->table}
                WHERE san_pham_id = $sanPhamId AND la_anh_chinh = 1
                LIMIT 1";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Thêm hình ảnh sản phẩm
     */
    public function themHinhAnh(int $sanPhamId, string $urlAnh, ?int $phienBanId = null, bool $laAnhChinh = false, int $thuTu = 0): int
    {
        $data = [
            'san_pham_id' => $sanPhamId,
            'url_anh' => $urlAnh,
            'phien_ban_id' => $phienBanId,
            'la_anh_chinh' => $laAnhChinh ? 1 : 0,
            'thu_tu' => $thuTu
        ];
        
        return $this->insert($data);
    }

    /**
     * Xóa hình ảnh
     */
    public function xoaHinhAnh(int $id): int
    {
        return $this->delete($id);
    }
}
