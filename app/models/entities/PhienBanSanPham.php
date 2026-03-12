<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class PhienBanSanPham extends BaseModel
{
    protected ?int $id = null;
    protected ?int $sanPhamId = null;
    protected ?string $sku = null;
    protected ?string $tenPhienBan = null;
    protected ?string $mauSac = null;
    protected ?string $dungLuong = null;
    protected ?string $ram = null;
    protected ?string $cauHinh = null;
    protected ?float $giaBan = null;
    protected ?float $giaGoc = null;
    protected ?int $soLuongTon = 0;
    protected ?string $trangThai = 'CON_HANG';

    public function __construct()
    {
        parent::__construct('phien_ban_san_pham');
    }

    // Lấy tất cả phiên bản của một sản phẩm cụ thể
    public function layTheoSanPhamId(int $sanPhamId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE san_pham_id = $sanPhamId ORDER BY gia_ban ASC";
        return $this->query($sql);
    }

    // Kiểm tra xem phiên bản này còn số lượng tồn kho không
    public function kiemTraKhaDung(): bool
    {
        return $this->soLuongTon > 0 && $this->trangThai === 'CON_HANG';
    }

    // Tính phần trăm giảm giá
    public function getPhanTramGiam(): int
    {
        if ($this->giaGoc && $this->giaGoc > $this->giaBan) {
            return round((($this->giaGoc - $this->giaBan) / $this->giaGoc) * 100);
        }
        return 0;
    }
    //Cập nhật số lượng tồn kho
    public function capNhatTonKho(int $id, int $soLuongMoi)
    {
        $trangThaiMoi = ($soLuongMoi > 0) ? 'CON_HANG' : 'HET_HANG';
        return $this->update($id, ['so_luong_ton' => $soLuongMoi, 'trang_thai' => $trangThaiMoi]);
    }


    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getSanPhamId(): ?int { return $this->sanPhamId; }
    public function setSanPhamId(?int $sanPhamId): void { $this->sanPhamId = $sanPhamId; }

    public function getSku(): ?string { return $this->sku; }
    public function setSku(?string $sku): void { $this->sku = $sku; }

    public function getTenPhienBan(): ?string { return $this->tenPhienBan; }
    public function setTenPhienBan(?string $tenPhienBan): void { $this->tenPhienBan = $tenPhienBan; }

    public function getMauSac(): ?string { return $this->mauSac; }
    public function setMauSac(?string $mauSac): void { $this->mauSac = $mauSac; }

    public function getDungLuong(): ?string { return $this->dungLuong; }
    public function setDungLuong(?string $dungLuong): void { $this->dungLuong = $dungLuong; }

    public function getRam(): ?string { return $this->ram; }
    public function setRam(?string $ram): void { $this->ram = $ram; }

    public function getCauHinh(): ?string { return $this->cauHinh; }
    public function setCauHinh(?string $cauHinh): void { $this->cauHinh = $cauHinh; }

    public function getGiaBan(): ?float { return $this->giaBan; }
    public function setGiaBan(?float $giaBan): void { $this->giaBan = $giaBan; }

    public function getGiaGoc(): ?float { return $this->giaGoc; }
    public function setGiaGoc(?float $giaGoc): void { $this->giaGoc = $giaGoc; }

    public function getSoLuongTon(): ?int { return $this->soLuongTon; }
    public function setSoLuongTon(?int $soLuongTon): void { $this->soLuongTon = $soLuongTon; }

    public function getTrangThai(): ?string { return $this->trangThai; }
    public function setTrangThai(?string $trangThai): void { $this->trangThai = $trangThai; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'san_pham_id' => $this->sanPhamId,
            'sku' => $this->sku,
            'ten_phien_ban' => $this->tenPhienBan,
            'mau_sac' => $this->mauSac,
            'dung_luong' => $this->dungLuong,
            'ram' => $this->ram,
            'cau_hinh' => $this->cauHinh,
            'gia_ban' => $this->giaBan,
            'gia_goc' => $this->giaGoc,
            'so_luong_ton' => $this->soLuongTon,
            'trang_thai' => $this->trangThai
        ];
    }
}
?>