<?php

class SanPham
{
    private $id;
    private $tenSanPham;
    private $gia;
    private $moTa;
    private $soLuong;
    private $danhMuc;

    public function __construct($id = null, $tenSanPham = "", $gia = 0, $moTa = "", $soLuong = 0, DanhMuc $danhMuc = null)
    {
        $this->id = $id;
        $this->tenSanPham = $tenSanPham;
        $this->gia = $gia;
        $this->moTa = $moTa;
        $this->soLuong = $soLuong;
        $this->danhMuc = $danhMuc;
    }

    // Getter
    public function getId()
    {
        return $this->id;
    }

    public function getTenSanPham()
    {
        return $this->tenSanPham;
    }

    public function getGia()
    {
        return $this->gia;
    }

    public function getMoTa()
    {
        return $this->moTa;
    }

    public function getSoLuong()
    {
        return $this->soLuong;
    }

    public function getDanhMuc()
    {
        return $this->danhMuc;
    }

    // Setter
    public function setTenSanPham($tenSanPham)
    {
        $this->tenSanPham = $tenSanPham;
    }

    public function setGia($gia)
    {
        $this->gia = $gia;
    }

    public function setMoTa($moTa)
    {
        $this->moTa = $moTa;
    }

    public function setSoLuong($soLuong)
    {
        $this->soLuong = $soLuong;
    }

    public function setDanhMuc(DanhMuc $danhMuc)
    {
        $this->danhMuc = $danhMuc;
    }

    // Method hiển thị thông tin
    public function hienThiThongTin()
    {
        return "Sản phẩm: " . $this->tenSanPham . " - Giá: " . number_format($this->gia) . " VNĐ";
    }
}