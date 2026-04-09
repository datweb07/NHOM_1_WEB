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

    // Lấy thông số theo sản phẩm (alias cho task 9.1)
    public function layTheoSanPham(int $sanPhamId): array
    {
        return $this->layThongSoTheoSanPham($sanPhamId);
    }

    // Xóa toàn bộ thông số của 1 sản phẩm khi update lại toàn bộ cấu hình
    public function xoaThongSoCuaSanPham(int $sanPhamId)
    {
        $sql = "DELETE FROM {$this->table} WHERE san_pham_id = $sanPhamId";
        $this->query($sql);
        return true;
    }

    // Cập nhật hoặc tạo mới tất cả thông số của sản phẩm
    public function capNhatHoacTao(int $sanPhamId, array $specifications): bool
    {
        // Xóa tất cả thông số cũ
        $this->xoaThongSoCuaSanPham($sanPhamId);

        // Thêm thông số mới
        if (empty($specifications)) {
            return true;
        }

        foreach ($specifications as $spec) {
            if (empty($spec['ten_thong_so']) || empty($spec['gia_tri'])) {
                continue;
            }

            $payload = [
                'san_pham_id' => $sanPhamId,
                'ten_thong_so' => addslashes(trim($spec['ten_thong_so'])),
                'gia_tri' => addslashes(trim($spec['gia_tri'])),
                'thu_tu' => (int)($spec['thu_tu'] ?? 0),
            ];

            $this->create($payload);
        }

        return true;
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

