<?php

class SanPham
{
    private $id;
    private $danhMucId;
    private $tenSanPham;
    private $slug;
    private $hangSanXuat;
    private $moTa;
    private $giaHienThi;
    private $diemDanhGia;
    private $trangThai;
    private $noiBat;
    private $ngayTao;
    private $ngayCapNhat;

    public function __construct(
        $id = null,
        $danhMucId = null,
        $tenSanPham = "",
        $slug = "",
        $hangSanXuat = "",
        $moTa = "",
        $giaHienThi = 0,
        $diemDanhGia = 0,
        $trangThai = "CON_BAN",
        $noiBat = 0,
        $ngayTao = null,
        $ngayCapNhat = null
    ) {
        $this->id = $id;
        $this->danhMucId = $danhMucId;
        $this->tenSanPham = $tenSanPham;
        $this->slug = $slug;
        $this->hangSanXuat = $hangSanXuat;
        $this->moTa = $moTa;
        $this->giaHienThi = $giaHienThi;
        $this->diemDanhGia = $diemDanhGia;
        $this->trangThai = $trangThai;
        $this->noiBat = $noiBat;
        $this->ngayTao = $ngayTao;
        $this->ngayCapNhat = $ngayCapNhat;
    }

    // ===== Getter =====

    public function getId()
    {
        return $this->id;
    }

    public function getDanhMucId()
    {
        return $this->danhMucId;
    }

    public function getTenSanPham()
    {
        return $this->tenSanPham;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getHangSanXuat()
    {
        return $this->hangSanXuat;
    }

    public function getMoTa()
    {
        return $this->moTa;
    }

    public function getGiaHienThi()
    {
        return $this->giaHienThi;
    }

    public function getDiemDanhGia()
    {
        return $this->diemDanhGia;
    }

    public function getTrangThai()
    {
        return $this->trangThai;
    }

    public function getNoiBat()
    {
        return $this->noiBat;
    }

    public function getNgayTao()
    {
        return $this->ngayTao;
    }

    public function getNgayCapNhat()
    {
        return $this->ngayCapNhat;
    }

    // ===== Setter =====

    public function setDanhMucId($danhMucId)
    {
        $this->danhMucId = $danhMucId;
    }

    public function setTenSanPham($tenSanPham)
    {
        $this->tenSanPham = $tenSanPham;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setHangSanXuat($hangSanXuat)
    {
        $this->hangSanXuat = $hangSanXuat;
    }

    public function setMoTa($moTa)
    {
        $this->moTa = $moTa;
    }

    public function setGiaHienThi($giaHienThi)
    {
        $this->giaHienThi = $giaHienThi;
    }

    public function setDiemDanhGia($diemDanhGia)
    {
        $this->diemDanhGia = $diemDanhGia;
    }

    public function setTrangThai($trangThai)
    {
        $this->trangThai = $trangThai;
    }

    public function setNoiBat($noiBat)
    {
        $this->noiBat = $noiBat;
    }

    // ===== Method hiển thị =====

    public function hienThiThongTin()
    {
        return "Sản phẩm: " . $this->tenSanPham .
               " | Hãng: " . $this->hangSanXuat .
               " | Giá: " . $this->giaHienThi .
               " | Trạng thái: " . $this->trangThai;
    }
}