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
