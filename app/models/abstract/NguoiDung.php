<?php

require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/../../enums/GioiTinh.php';
require_once __DIR__ . '/../../enums/LoaiTaiKhoan.php';

abstract class NguoiDung extends BaseModel
{
    protected ?int $id = null;
    protected ?string $email = null;
    protected ?string $matKhau = null;
    protected ?string $hoTen = null;
    protected ?string $sdt = null;
    protected ?string $avatarUrl = null;
    protected ?string $ngaySinh = null;
    protected ?string $gioiTinh = null;
    protected ?string $loaiTaiKhoan = 'MEMBER'; 
    protected ?string $trangThai = 'ACTIVE';    
    protected ?string $ngayTao = null;
    protected ?string $ngayCapNhat = null;

    public function __construct()
    {
        parent::__construct('nguoi_dung');
    }



    public function tim_kiem_san_pham(string $tuKhoa)
    {
        $sql = "SELECT * FROM san_pham WHERE ten_san_pham LIKE '%$tuKhoa%'";

        return $this->query($sql); 
    }


    public function duyet_danh_muc(int $danhMucId)
    {
        $sql = "SELECT * FROM san_pham WHERE danh_muc_id = $danhMucId";

        return $this->query($sql);
    }

    // User management methods for admin
    public function layDanhSach(?string $loaiTaiKhoan = null, ?string $trangThai = null, int $limit = 20, int $offset = 0): array
    {
        $where = [];
        if ($loaiTaiKhoan !== null && $loaiTaiKhoan !== '') {
            $where[] = "loai_tai_khoan = '" . addslashes($loaiTaiKhoan) . "'";
        }
        if ($trangThai !== null && $trangThai !== '') {
            $where[] = "trang_thai = '" . addslashes($trangThai) . "'";
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT * FROM {$this->table} 
                $whereClause
                ORDER BY ngay_tao DESC, id DESC
                LIMIT $limit OFFSET $offset";
        return $this->query($sql);
    }

    public function timKiem(string $keyword, int $limit = 20, int $offset = 0): array
    {
        $keyword = addslashes($keyword);
        $sql = "SELECT * FROM {$this->table}
                WHERE email LIKE '%$keyword%' 
                   OR ho_ten LIKE '%$keyword%' 
                   OR sdt LIKE '%$keyword%'
                ORDER BY ngay_tao DESC, id DESC
                LIMIT $limit OFFSET $offset";
        return $this->query($sql);
    }

    public function layTheoKhoangNgay(string $from, string $to): array
    {
        $from = addslashes($from);
        $to = addslashes($to);
        $sql = "SELECT * FROM {$this->table}
                WHERE ngay_tao BETWEEN '$from' AND '$to'
                ORDER BY ngay_tao DESC, id DESC";
        return $this->query($sql);
    }

    public function chanNguoiDung(int $id): int
    {
        $sql = "UPDATE {$this->table} 
                SET trang_thai = 'BLOCKED'
                WHERE id = $id";
        $this->query($sql);
        return 1;
    }

    public function moChanNguoiDung(int $id): int
    {
        $sql = "UPDATE {$this->table} 
                SET trang_thai = 'ACTIVE'
                WHERE id = $id";
        $this->query($sql);
        return 1;
    }

    public function demNguoiDung(?string $loaiTaiKhoan = null, ?string $trangThai = null): int
    {
        $where = [];
        if ($loaiTaiKhoan !== null && $loaiTaiKhoan !== '') {
            $where[] = "loai_tai_khoan = '" . addslashes($loaiTaiKhoan) . "'";
        }
        if ($trangThai !== null && $trangThai !== '') {
            $where[] = "trang_thai = '" . addslashes($trangThai) . "'";
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} $whereClause";
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }


    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): void { $this->email = $email; }

    public function getMatKhau(): ?string { return $this->matKhau; }

    //dùng sha1
    public function setMatKhau(?string $matKhau): void { 
        $this->matKhau = sha1(trim($matKhau)); 
    }

    //set pass đã mã hóa từ database
    public function setMatKhauHash(?string $matKhauHash): void { 
        $this->matKhau = $matKhauHash; 
    }

    public function getHoTen(): ?string { return $this->hoTen; }
    public function setHoTen(?string $hoTen): void { $this->hoTen = $hoTen; }

    public function getSdt(): ?string { return $this->sdt; }
    public function setSdt(?string $sdt): void { $this->sdt = $sdt; }

    public function getAvatarUrl(): ?string { return $this->avatarUrl; }
    public function setAvatarUrl(?string $avatarUrl): void { $this->avatarUrl = $avatarUrl; }

    public function getNgaySinh(): ?string { return $this->ngaySinh; }
    public function setNgaySinh(?string $ngaySinh): void { $this->ngaySinh = $ngaySinh; }

    public function getGioiTinh(): ?string { return $this->gioiTinh; }
    public function setGioiTinh(?string $gioiTinh): void { $this->gioiTinh = $gioiTinh; }

    public function getLoaiTaiKhoan(): ?string { return $this->loaiTaiKhoan; }
    public function setLoaiTaiKhoan(string $loaiTaiKhoan): void { $this->loaiTaiKhoan = $loaiTaiKhoan; }

    public function getTrangThai(): ?string { return $this->trangThai; }
    public function setTrangThai(string $trangThai): void { $this->trangThai = $trangThai; }

    public function getNgayTao(): ?string { return $this->ngayTao; }
    public function setNgayTao(?string $ngayTao): void { $this->ngayTao = $ngayTao; }

    public function getNgayCapNhat(): ?string { return $this->ngayCapNhat; }
    public function setNgayCapNhat(?string $ngayCapNhat): void { $this->ngayCapNhat = $ngayCapNhat; }

    
    //chuyển sang mảng để insert vào db
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'mat_khau' => $this->matKhau,
            'ho_ten' => $this->hoTen,
            'sdt' => $this->sdt,
            'avatar_url' => $this->avatarUrl,
            'ngay_sinh' => $this->ngaySinh,
            'gioi_tinh' => $this->gioiTinh,
            'loai_tai_khoan' => $this->loaiTaiKhoan,
            'trang_thai' => $this->trangThai,
            'ngay_tao' => $this->ngayTao,
            'ngay_cap_nhat' => $this->ngayCapNhat
        ];
    }
}