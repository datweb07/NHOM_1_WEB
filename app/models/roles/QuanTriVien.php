<?php

require_once __DIR__ . '/../abstract/NguoiDung.php';

class QuanTriVien extends NguoiDung
{
    public function __construct()
    {
        parent::__construct();
        $this->loaiTaiKhoan = 'ADMIN';
    }

    /**
     * Đăng nhập admin - check email và password
     */
    public function dang_nhap(string $email, string $matKhau): bool
    {
        $matKhauHash = sha1(trim($matKhau));
        $sql = "SELECT * FROM nguoi_dung WHERE email = '$email' AND mat_khau = '$matKhauHash' AND loai_tai_khoan = 'ADMIN' AND trang_thai = 'ACTIVE' LIMIT 1";
        
        $result = $this->query($sql);
        if ($result && count($result) > 0) {
            $data = $result[0];
            $this->id = $data['id'];
            $this->email = $data['email'];
            $this->matKhau = $data['mat_khau'];
            $this->hoTen = $data['ho_ten'];
            $this->sdt = $data['sdt'];
            $this->avatarUrl = $data['avatar_url'];
            $this->ngaySinh = $data['ngay_sinh'];
            $this->gioiTinh = $data['gioi_tinh'];
            $this->loaiTaiKhoan = $data['loai_tai_khoan'];
            $this->trangThai = $data['trang_thai'];
            $this->ngayTao = $data['ngay_tao'];
            $this->ngayCapNhat = $data['ngay_cap_nhat'];
            return true;
        }
        return false;
    }
}
