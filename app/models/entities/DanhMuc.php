<?php

class DanhMuc
{
    private $id;
    private $ten;
    private $slug;
    private $iconUrl;
    private $danhMucChaId;
    private $thuTu;
    private $trangThai;

    public function __construct(
        $id = null,
        $ten = "",
        $slug = "",
        $iconUrl = "",
        $danhMucChaId = null,
        $thuTu = 0,
        $trangThai = 1
    ) {
        $this->id = $id;
        $this->ten = $ten;
        $this->slug = $slug;
        $this->iconUrl = $iconUrl;
        $this->danhMucChaId = $danhMucChaId;
        $this->thuTu = $thuTu;
        $this->trangThai = $trangThai;
    }

    // ===== Getter =====

    public function getId()
    {
        return $this->id;
    }

    public function getTen()
    {
        return $this->ten;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    public function getDanhMucChaId()
    {
        return $this->danhMucChaId;
    }

    public function getThuTu()
    {
        return $this->thuTu;
    }

    public function getTrangThai()
    {
        return $this->trangThai;
    }

    // ===== Setter =====

    public function setTen($ten)
    {
        $this->ten = $ten;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setIconUrl($iconUrl)
    {
        $this->iconUrl = $iconUrl;
    }

    public function setDanhMucChaId($danhMucChaId)
    {
        $this->danhMucChaId = $danhMucChaId;
    }

    public function setThuTu($thuTu)
    {
        $this->thuTu = $thuTu;
    }

    public function setTrangThai($trangThai)
    {
        $this->trangThai = $trangThai;
    }

    // ===== Method hiển thị =====

    public function hienThiThongTin()
    {
        return "Danh mục: " . $this->ten .
               " | Slug: " . $this->slug .
               " | Thứ tự: " . $this->thuTu .
               " | Trạng thái: " . $this->trangThai ;
    }
}