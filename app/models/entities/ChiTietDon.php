<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class ChiTietDon extends BaseModel
{
    public function __construct()
    {
        parent::__construct('chi_tiet_don');
    }

    public function layChiTietDonHang(int $donHangId): array
    {
        $donHangId = (int)$donHangId;
        
        $sql = "SELECT ctd.*, 
                       pbsp.ten_phien_ban, pbsp.mau_sac,
                       sp.ten_san_pham, sp.slug,
                       (SELECT url_anh FROM hinh_anh_san_pham 
                        WHERE san_pham_id = sp.id AND la_anh_chinh = 1 
                        LIMIT 1) as anh_chinh
                FROM {$this->table} ctd
                INNER JOIN phien_ban_san_pham pbsp ON ctd.phien_ban_id = pbsp.id
                INNER JOIN san_pham sp ON pbsp.san_pham_id = sp.id
                WHERE ctd.don_hang_id = $donHangId";
        
        return $this->query($sql);
    }

    public function themChiTiet(int $donHangId, int $phienBanId, int $soLuong, float $giaTaiThoiDiemMua): int
    {
        return $this->create([
            'don_hang_id' => $donHangId,
            'phien_ban_id' => $phienBanId,
            'so_luong' => $soLuong,
            'gia_tai_thoi_diem_mua' => $giaTaiThoiDiemMua
        ]);
    }

    public function layTheoDonHang(int $donHangId): array
    {
        return $this->layChiTietDonHang($donHangId);
    }
}
