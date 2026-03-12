<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class ThongSoKyThuat extends BaseModel
{
    protected ?int $id = null;
    protected ?int $sanPhamId = null;
    protected ?string $tenThongSo = null;
    protected ?string $giaTri = null;
    protected ?int $thuTu = 0;

    public function __construct()
    {
        parent::__construct('thong_so_ky_thuat');
    }
    // Lấy danh sách thông số của sản phẩm để hiển thị bảng cấu hình và sắp xếp theo thứ tự để đảm bảo các thông số quan trọng hiện trước
    public function layThongSoTheoSanPham(int $sanPhamId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE san_pham_id = $sanPhamId ORDER BY thu_tu ASC";
        return $this->query($sql);
    }

    // Xóa toàn bộ thông số của 1 sản phẩm khi update lại toàn bộ cấu hình
    public function xoaThongSoCuaSanPham(int $sanPhamId)
    {
        $sql = "DELETE FROM {$this->table} WHERE san_pham_id = $sanPhamId";
        chayTruyVanKhongTraVeDL($this->link, $sql);
        return mysqli_affected_rows($this->link);
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getSanPhamId(): ?int { return $this->sanPhamId; }
    public function setSanPhamId(?int $sanPhamId): void { $this->sanPhamId = $sanPhamId; }

    public function getTenThongSo(): ?string { return $this->tenThongSo; }
    public function setTenThongSo(?string $tenThongSo): void { $this->tenThongSo = $tenThongSo; }

    public function getGiaTri(): ?string { return $this->giaTri; }
    public function setGiaTri(?string $giaTri): void { $this->giaTri = $giaTri; }

    public function getThuTu(): ?int { return $this->thuTu; }
    public function setThuTu(?int $thuTu): void { $this->thuTu = $thuTu; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'san_pham_id' => $this->sanPhamId,
            'ten_thong_so' => $this->tenThongSo,
            'gia_tri' => $this->giaTri,
            'thu_tu' => $this->thuTu
        ];
    }
}
?>