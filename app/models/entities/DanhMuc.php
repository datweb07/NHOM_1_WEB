<?php

class DanhMuc
{
    private $id;
    private $tenDanhMuc;
    private $slug;
    private $moTa;
    private $trangThai;

    public function __construct($id = null, $tenDanhMuc = "", $slug = "", $moTa = "", $trangThai = 1)
    {
        $this->id = $id;
        $this->tenDanhMuc = $tenDanhMuc;
        $this->slug = $slug;
        $this->moTa = $moTa;
        $this->trangThai = $trangThai;
    }

    // Getter
    public function getId()
    {
        return $this->id;
    }

    public function getTenDanhMuc()
    {
        return $this->tenDanhMuc;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getMoTa()
    {
        return $this->moTa;
    }

    public function getTrangThai()
    {
        return $this->trangThai;
    }

    // Setter
    public function setTenDanhMuc($tenDanhMuc)
    {
        $this->tenDanhMuc = $tenDanhMuc;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setMoTa($moTa)
    {
        $this->moTa = $moTa;
    }

    public function setTrangThai($trangThai)
    {
        $this->trangThai = $trangThai;
    }

    // Method hiển thị thông tin
    public function hienThiThongTin()
    {
        return "Danh mục: " . $this->tenDanhMuc;
    }
}