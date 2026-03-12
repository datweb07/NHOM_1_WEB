<?php

class Dia_Chi 
{
    // Thuộc tính chính xác theo sơ đồ (snake_case)
    public int $id;
    public string $ten_nguoi_nhan;
    public string $sdt_nhan;
    public string $so_nha_duong;
    public string $phuong_xa;
    public string $quan_huyen;
    public string $tinh_thanh;
    public bool $mac_dinh;

    public function __construct(
        int $id,
        string $ten_nguoi_nhan,
        string $sdt_nhan,
        string $so_nha_duong,
        string $phuong_xa,
        string $quan_huyen,
        string $tinh_thanh,
        bool $mac_dinh = false
    ) {
        $this->id = $id;
        $this->ten_nguoi_nhan = $ten_nguoi_nhan;
        $this->sdt_nhan = $sdt_nhan;
        $this->so_nha_duong = $so_nha_duong;
        $this->phuong_xa = $phuong_xa;
        $this->quan_huyen = $quan_huyen;
        $this->tinh_thanh = $tinh_thanh;
        $this->mac_dinh = $mac_dinh;
    }

    // Logic bổ trợ: Xuất chuỗi địa chỉ định dạng chuẩn
    public function get_full_address(): string 
    {
        return "{$this->so_nha_duong}, {$this->phuong_xa}, {$this->quan_huyen}, {$this->tinh_thanh}";
    }
}