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

    public function dang_ky(string $email, string $matKhau, string $hoTen): ?array
    {
        $existingUser = $this->query("SELECT id FROM nguoi_dung WHERE email = '$email' LIMIT 1");
        if (!empty($existingUser)) {
            return null; //mail đã tồn tại
        }

        $matKhauHash = sha1(trim($matKhau));
        $now = date('Y-m-d H:i:s');
        $token = bin2hex(random_bytes(32)); // 64 ký tự hex ngẫu nhiên

        $newUserId = $this->create([
            'email' => $email,
            'mat_khau' => $matKhauHash,
            'ho_ten' => $hoTen,
            'loai_tai_khoan' => 'MEMBER',
            'trang_thai' => 'UNVERIFIED',
            'verification_token' => $token,
            'ngay_tao' => $now,
            'ngay_cap_nhat' => $now
        ]);

        if ($newUserId) {
            $this->id = $newUserId;
            $this->email = $email;
            $this->hoTen = $hoTen;
            $this->loaiTaiKhoan = 'MEMBER';
            $this->trangThai = 'UNVERIFIED';
        }

        return $newUserId ? ['id' => $newUserId, 'token' => $token] : null;
    }

    public function xac_thuc_email(string $token): bool
    {
        $token = mysqli_real_escape_string($this->link, $token);
        $result = $this->query("SELECT * FROM nguoi_dung WHERE verification_token = '$token' AND trang_thai = 'UNVERIFIED' LIMIT 1");

        if (empty($result)) {
            return false;
        }

        $user = $result[0];
        $now = date('Y-m-d H:i:s');
        $updated = $this->update($user['id'], [
            'trang_thai' => 'ACTIVE',
            'verification_token' => '',
            'ngay_cap_nhat' => $now
        ]);

        if ($updated) {
            $this->id = $user['id'];
            $this->email = $user['email'];
            $this->hoTen = $user['ho_ten'];
            $this->loaiTaiKhoan = $user['loai_tai_khoan'];
            $this->trangThai = 'ACTIVE';
            $this->avatarUrl = $user['avatar_url'];
        }

        return (bool)$updated;
    }

    
    //update thông tin cá nhân
    public function quan_ly_ho_so(array $dataCapNhat)
    {
        if (!$this->id) {
            return false;
        }
        
        $result = $this->update($this->id, $dataCapNhat);
        
        if ($result) {
            foreach ($dataCapNhat as $key => $value) {
                $camelKey = str_replace('_', '', lcfirst(ucwords($key, '_')));
                if (property_exists($this, $camelKey)) {
                    $this->$camelKey = $value;
                }
            }
        }
        
        return $result;
    }

    public function doi_mat_khau(string $matKhauCu, string $matKhauMoi): bool
    {
        if (!$this->id) {
            return false;
        }

        //xác nhận pass cũ
        $matKhauCuHash = sha1(trim($matKhauCu));
        if ($this->matKhau !== $matKhauCuHash) {
            return false;
        }

        //update pass mới
        $matKhauMoiHash = sha1(trim($matKhauMoi));
        $result = $this->update($this->id, ['mat_khau' => $matKhauMoiHash]);
        
        if ($result) {
            $this->matKhau = $matKhauMoiHash;
        }
        
        return $result;
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

    /**
     * Tạo reset token và lưu vào database
     * 
     * @param string $email Email người dùng
     * @return string|null Reset token hoặc null nếu email không tồn tại
     */
    public function tao_reset_token(string $email): ?string
    {
        // Escape email để ngăn SQL injection
        $email = mysqli_real_escape_string($this->link, $email);
        
        // Kiểm tra email có tồn tại trong database
        $result = $this->query("SELECT id FROM nguoi_dung WHERE email = '$email' LIMIT 1");
        
        // Nếu email không tồn tại, return null
        if (empty($result)) {
            return null;
        }
        
        // Tạo reset token 64 ký tự hex
        $token = bin2hex(random_bytes(32));
        
        // Lưu token vào database
        $userId = $result[0]['id'];
        
        $updated = $this->update($userId, [
            'forget_token' => $token
        ]);
        
        // Return token nếu update thành công, null nếu không
        return $updated ? $token : null;
    }

    /**
     * Xác thực reset token
     * 
     * @param string $token Reset token
     * @return array|false User data nếu token hợp lệ, false nếu không
     */
    public function xac_thuc_reset_token(string $token)
    {
        // Escape token để ngăn SQL injection
        $token = mysqli_real_escape_string($this->link, $token);
        
        // Query database để tìm token
        $result = $this->query("SELECT * FROM nguoi_dung WHERE forget_token = '$token' LIMIT 1");
        
        // Kiểm tra token có tồn tại
        if (empty($result)) {
            return false;
        }
        
        // Return user data nếu token hợp lệ
        return $result[0];
    }

    /**
     * Đặt lại mật khẩu và vô hiệu hóa token
     * 
     * @param string $token Reset token
     * @param string $matKhauMoi Mật khẩu mới
     * @return bool True nếu thành công
     */
    public function dat_lai_mat_khau(string $token, string $matKhauMoi): bool
    {
        // Validate password không rỗng
        if (empty(trim($matKhauMoi))) {
            return false;
        }
        
        // Validate password >= 6 ký tự
        if (strlen(trim($matKhauMoi)) < 6) {
            return false;
        }
        
        // Verify token is valid before resetting password
        $userData = $this->xac_thuc_reset_token($token);
        if ($userData === false) {
            return false;
        }
        
        // Escape token để ngăn SQL injection
        $token = mysqli_real_escape_string($this->link, $token);
        
        // Hash password bằng SHA-1
        $matKhauHash = sha1(trim($matKhauMoi));
        
        // Update mat_khau và xóa forget_token
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE nguoi_dung SET 
                mat_khau = '$matKhauHash',
                forget_token = NULL,
                ngay_cap_nhat = '$now'
                WHERE forget_token = '$token'";
        
        $result = chayTruyVanKhongTraVeDL($this->link, $sql);
        
        // Return true nếu update thành công
        return $result && mysqli_affected_rows($this->link) > 0;
    }
}