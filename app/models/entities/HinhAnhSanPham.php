<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class HinhAnhSanPham extends BaseModel
{
    protected ?int $id = null;
    protected ?int $sanPhamId = null;
    protected ?int $phienBanId = null;
    protected ?string $urlAnh = null;
    protected ?string $altText = null;
    protected int $laAnhChinh = 0;
    protected int $thuTu = 0;

    public function __construct()
    {
        parent::__construct('hinh_anh_san_pham');
    }

    public function layTheoSanPham(int $sanPhamId): array
    {
        $sql = "SELECT * FROM {$this->table}
				WHERE san_pham_id = " . (int)$sanPhamId . '
				ORDER BY la_anh_chinh DESC, thu_tu ASC, id ASC';
        return $this->query($sql);
    }

    // Đặt ảnh chính và bỏ đặt các ảnh khác
    public function datAnhChinh(int $imageId, int $sanPhamId): bool
    {
        // Bỏ đặt tất cả ảnh chính của sản phẩm
        $sql1 = "UPDATE {$this->table} SET la_anh_chinh = 0 WHERE san_pham_id = $sanPhamId";
        $this->query($sql1);
        
        // Đặt ảnh được chọn làm ảnh chính
        return $this->update($imageId, ['la_anh_chinh' => 1]);
    }

    // Xóa ảnh và file vật lý
    public function xoaVaXoaFile(int $imageId): bool
    {
        $image = $this->getById($imageId);
        if (!$image) {
            return false;
        }

        // Xóa file vật lý
        $filePath = dirname(__DIR__, 3) . '/public' . $image['url_anh'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Xóa record trong database
        return $this->delete($imageId);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'san_pham_id' => $this->sanPhamId,
            'phien_ban_id' => $this->phienBanId,
            'url_anh' => $this->urlAnh,
            'alt_text' => $this->altText,
            'la_anh_chinh' => $this->laAnhChinh,
            'thu_tu' => $this->thuTu,
        ];
    }
}
