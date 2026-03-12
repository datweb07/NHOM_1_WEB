<?php

require_once __DIR__ . '/../abstract/NguoiDung.php';

class KhachHang extends NguoiDung
{
    private array $danhSachDiaChi = []; 
    
    private array $yeuThich = [];       
    
    private array $lichSuTimKiem = [];  

    public function __construct()
    {
        parent::__construct();

        $this->loaiTaiKhoan = 'MEMBER'; 
    }

    
    //check email và password
    public function dang_nhap(string $email, string $matKhau)
    {
        $matKhauHash = sha1(trim($matKhau));
        $sql = "SELECT * FROM nguoi_dung WHERE email = '$email' AND mat_khau = '$matKhauHash' AND loai_tai_khoan = 'MEMBER' AND trang_thai = 'ACTIVE' LIMIT 1";
        
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

    
    //update thông tin cá nhân
    public function quan_ly_ho_so(array $dataCapNhat)
    {
        if (!$this->id) {
            return false;
        }
        
        return $this->update($this->id, $dataCapNhat);
    }

    

    //get ds đơn hàng
    public function xem_lich_su_don(int $limit = 10)
    {
        if (!$this->id) return [];

        $sql = "SELECT * FROM don_hang WHERE nguoi_dung_id = {$this->id} ORDER BY ngay_tao DESC LIMIT $limit";
        return $this->query($sql);
    }

    

    //viết đánh giá sp
    public function danh_gia_san_pham(int $sanPhamId, int $soSao, string $noiDung)
    {
        if (!$this->id) return false;

        $ngayViet = date('Y-m-d H:i:s');
        $sql = "INSERT INTO danh_gia (nguoi_dung_id, san_pham_id, so_sao, noi_dung, ngay_viet) 
                VALUES ('{$this->id}', '$sanPhamId', '$soSao', '$noiDung', '$ngayViet')";
        
        chayTruyVanKhongTraVeDL($this->link, $sql);
        return mysqli_insert_id($this->link);
    }

    
    //lấy địa chỉ
    public function getDanhSachDiaChi(): array
    {
        if (!$this->id) return [];
        $sql = "SELECT * FROM dia_chi WHERE nguoi_dung_id = {$this->id} ORDER BY mac_dinh DESC";
        $this->danhSachDiaChi = $this->query($sql);
        return $this->danhSachDiaChi;
    }

    

    //lấy ds sp yêu thích
    public function getDanhSachYeuThich(): array
    {
        if (!$this->id) return [];
        $sql = "SELECT sp.* FROM san_pham sp 
                INNER JOIN yeu_thich yt ON sp.id = yt.san_pham_id 
                WHERE yt.nguoi_dung_id = {$this->id} ORDER BY yt.ngay_them DESC";
        $this->yeuThich = $this->query($sql);
        return $this->yeuThich;
    }

    //get lịch sử tk
    public function getLichSuTimKiem(): array
    {
        if (!$this->id) return [];
        $sql = "SELECT tu_khoa, thoi_gian_tim FROM lich_su_tim_kiem WHERE nguoi_dung_id = {$this->id} ORDER BY thoi_gian_tim DESC";
        $this->lichSuTimKiem = $this->query($sql);
        return $this->lichSuTimKiem;
    }
}