<?php
require_once dirname(__DIR__, 2) . '/core/Session.php';
require_once dirname(__DIR__, 2) . '/models/BaseModel.php';

class KhachHangController
{
    private $donHangModel;
    private $khachHangModel;

    public function __construct()
    {
        $this->donHangModel = new BaseModel('don_hang');
        $this->khachHangModel = new BaseModel('nguoi_dung');
    }

    public function danhSachDonHangCuaToi()
    {
        \App\Core\Session::start();
       
        $userId = $_SESSION['user_id'] ?? null; 

        if (!$userId) {
            header("Location: /login.php");
            exit();
        }

        $safeUserId = (int)$userId;
        $sql = "SELECT id, ma_don_hang, tong_thanh_toan, trang_thai, ngay_tao 
                FROM don_hang 
                WHERE nguoi_dung_id = $safeUserId 
                ORDER BY id DESC";

        $danhSachDonHang = $this->donHangModel->query($sql);

        $data = [
            'danhSachDonHang' => $danhSachDonHang
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/client/don_hang/list.php';
    }

    public function profile()
    {
        \App\Core\Session::start();
       
        $userId = $_SESSION['user_id'] ?? null; 

        if (!$userId) {
            header("Location: /login.php");
            exit();
        }

        $safeUserId = (int)$userId;
        $sql = "SELECT * FROM nguoi_dung WHERE id = $safeUserId";
        $userList = $this->khachHangModel->query($sql);
        
        $user = !empty($userList) ? $userList[0] : null;

        if (!$user) {
            header("Location: /login.php");
            exit();
        }

        $data = [
            'user' => $user
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/client/khach_hang/profile.php';
    }

    public function capNhatHoSo()
    {
        \App\Core\Session::start();
        $userId = $_SESSION['user_id'] ?? null; 
        
        if ($userId && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $hoTen = addslashes(trim($_POST['ho_ten'] ?? ''));
            $sdt = addslashes(trim($_POST['sdt'] ?? ''));
            $ngaySinh = addslashes(trim($_POST['ngay_sinh'] ?? ''));
            $gioiTinh = addslashes(trim($_POST['gioi_tinh'] ?? ''));

            $safeUserId = (int)$userId;
            $sql = "UPDATE nguoi_dung SET ho_ten = '$hoTen', sdt = '$sdt', ngay_sinh = '$ngaySinh', gioi_tinh = '$gioiTinh' WHERE id = $safeUserId";
            $this->khachHangModel->query($sql);

            $_SESSION['success'] = "Cập nhật hồ sơ thành công!";
        }
        header("Location: /profile.php"); 
        exit();
    }

    public function doiMatKhau()
    {
        \App\Core\Session::start();
        $userId = $_SESSION['user_id'] ?? null; 
        
        if ($userId && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $matKhauCu = $_POST['mat_khau_cu'] ?? '';
            $matKhauMoi = $_POST['mat_khau_moi'] ?? '';
            $xacNhanMatKhau = $_POST['xac_nhan_mat_khau'] ?? '';

            if ($matKhauMoi !== $xacNhanMatKhau) {
                $_SESSION['error'] = "Mật khẩu xác nhận không khớp!";
                header("Location: /profile.php");
                exit();
            }

            $safeUserId = (int)$userId;
            $sql = "SELECT mat_khau FROM nguoi_dung WHERE id = $safeUserId";
            $userList = $this->khachHangModel->query($sql);
            $user = !empty($userList) ? $userList[0] : null;

            if ($user) {
                if ($matKhauCu === $user['mat_khau']) {
                    $safeMatKhauMoi = addslashes($matKhauMoi); 
                    $sqlUpdate = "UPDATE nguoi_dung SET mat_khau = '$safeMatKhauMoi' WHERE id = $safeUserId";
                    $this->khachHangModel->query($sqlUpdate);
                    $_SESSION['success'] = "Đổi mật khẩu thành công!";
                } else {
                    $_SESSION['error'] = "Mật khẩu hiện tại không đúng!";
                }
            }
        }
        header("Location: /profile.php"); 
        exit();
    }
}
