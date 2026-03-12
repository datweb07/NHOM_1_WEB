<?php

class Ma_Giam_Gia 
{
    public int $id;
    public string $ma_code;
    public string $loai_giam;
    public float $gia_tri_giam;
    public float $giam_toi_da;
    public float $don_toi_thieu;
    public int $so_luot_su_dung;
    public int $da_su_dung;
    public string $trang_thai;
    public string $ngay_het_han; // Định dạng Y-m-d H:i:s

    public function __construct(
        int $id,
        string $ma_code,
        string $loai_giam,
        float $gia_tri_giam,
        float $giam_toi_da,
        float $don_toi_thieu,
        int $so_luot_su_dung,
        int $da_su_dung,
        string $trang_thai,
        string $ngay_het_han
    ) {
        $this->id = $id;
        $this->ma_code = $ma_code;
        $this->loai_giam = $loai_giam;
        $this->gia_tri_giam = $gia_tri_giam;
        $this->giam_toi_da = $giam_toi_da;
        $this->don_toi_thieu = $don_toi_thieu;
        $this->so_luot_su_dung = $so_luot_su_dung;
        $this->da_su_dung = $da_su_dung;
        $this->trang_thai = $trang_thai;
        $this->ngay_het_han = $ngay_het_han;
    }

    /**
     * Phương thức kiem_tra_hop_le() 
     * Logic: Kiểm tra trạng thái, thời hạn, số lượng và giá trị đơn hàng tối thiểu.
     */
    public function kiem_tra_hop_le(float $tong_tien_don_hang): bool 
    {
        // 1. Kiểm tra trạng thái (Ví dụ: ACTIVE)
        if ($this->trang_thai !== 'ACTIVE') {
            return false;
        }

        // 2. Kiểm tra số lượt còn lại
        if ($this->da_su_dung >= $this->so_luot_su_dung) {
            return false;
        }

        // 3. Kiểm tra ngày hết hạn
        $current_time = time();
        $expiration_time = strtotime($this->ngay_het_han);
        if ($current_time > $expiration_time) {
            return false;
        }

        // 4. Kiểm tra điều kiện giá trị đơn hàng tối thiểu
        if ($tong_tien_don_hang < $this->don_toi_thieu) {
            return false;
        }

        return true;
    }

    /**
     * Logic bổ trợ: Tính toán số tiền được giảm thực tế
     */
    public function tinh_so_tien_giam(float $tong_tien_don_hang): float 
    {
        if (!$this->kiem_tra_hop_le($tong_tien_don_hang)) {
            return 0;
        }

        $so_tien_giam = 0;
        if ($this->loai_giam === 'PERCENT') {
            $so_tien_giam = $tong_tien_don_hang * ($this->gia_tri_giam / 100);
            // Không được vượt quá mức giảm tối đa
            if ($so_tien_giam > $this->giam_toi_da) {
                $so_tien_giam = $this->giam_toi_da;
            }
        } else {
            // Loại giảm cố định (FIXED)
            $so_tien_giam = $this->gia_tri_giam;
        }

        return $so_tien_giam;
    }
}