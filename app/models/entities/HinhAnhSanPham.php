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
