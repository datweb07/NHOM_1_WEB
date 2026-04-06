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
        
        return $this->create($data);
    }

    /**
     * Xóa hình ảnh
     */
    public function xoaHinhAnh(int $id): int
    {
        return $this->delete($id);
    }

    /**
     * Đặt ảnh chính cho sản phẩm
     */
    public function datAnhChinh(int $sanPhamId, int $anhId): bool
    {
        $sanPhamId = (int)$sanPhamId;
        $anhId = (int)$anhId;
        
        // Bỏ đánh dấu ảnh chính cũ
        $sql1 = "UPDATE {$this->table} 
                 SET la_anh_chinh = 0 
                 WHERE san_pham_id = $sanPhamId";
        $this->execute($sql1);
        
        // Đánh dấu ảnh mới là ảnh chính
        $sql2 = "UPDATE {$this->table} 
                 SET la_anh_chinh = 1 
                 WHERE id = $anhId AND san_pham_id = $sanPhamId";
        $this->execute($sql2);
        
        return mysqli_affected_rows($this->link) > 0;
    }

    /**
     * Xóa ảnh và xóa file
     */
    public function xoaVaXoaFile(int $id): bool
    {
        // Lấy thông tin ảnh trước khi xóa
        $anh = $this->getById($id);
        if (!$anh) {
            return false;
        }
        
        // Xóa record trong database
        $deleted = $this->delete($id) > 0;
        
        // Xóa file nếu là local file (không phải URL từ CDN)
        if ($deleted && !empty($anh['url_anh'])) {
            $urlAnh = $anh['url_anh'];
            // Chỉ xóa nếu là file local (bắt đầu bằng /uploads/)
            if (strpos($urlAnh, '/uploads/') === 0) {
                $filePath = dirname(__DIR__, 3) . $urlAnh;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }
        
        return $deleted;
    }
}
