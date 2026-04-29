<?php
require_once dirname(__DIR__, 2) . '/core/Session.php';
require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
require_once dirname(__DIR__, 2) . '/models/roles/KhachHang.php';
require_once dirname(__DIR__, 2) . '/services/cloudinary/CloudinaryService.php';

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

        $safeUserId = (int) $userId;
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

        $safeUserId = (int) $userId;
        $sql = "SELECT * FROM nguoi_dung WHERE id = $safeUserId";
        $userList = $this->khachHangModel->query($sql);

        $user = !empty($userList) ? $userList[0] : null;

        if (!$user) {
            header("Location: /login.php");
            exit();
        }

        require_once dirname(__DIR__, 2) . '/models/relationships/YeuThich.php';
        $yeuThichModel = new \YeuThich();
        $limit = 20;
        $sanPhamsYeuThich = $yeuThichModel->layDanhSachTheoUser($safeUserId, $limit, 0);

        require_once dirname(__DIR__, 2) . '/models/entities/DanhGia.php';
        $danhGiaModel = new \DanhGia();
        $danhGiaList = $danhGiaModel->layDanhGiaTheoUser($safeUserId, 20, 0);

        $data = [
            'user' => $user,
            'auth_provider' => $user['auth_provider'] ?? 'LOCAL',
            'sanPhamsYeuThich' => $sanPhamsYeuThich,
            'danhGiaList' => $danhGiaList
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/client/khach_hang/profile.php';
    }

    public function capNhatHoSo()
    {
        \App\Core\Session::start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /client/profile");
            exit();
        }

        $dataCapNhat = [
            'ho_ten' => trim($_POST['ho_ten'] ?? ''),
            'ngay_cap_nhat' => date('Y-m-d H:i:s')
        ];

        $sdt = trim($_POST['sdt'] ?? '');
        if (!empty($sdt)) {
            $dataCapNhat['sdt'] = $sdt;
        }

        $ngaySinh = trim($_POST['ngay_sinh'] ?? '');
        if (!empty($ngaySinh)) {
            $dataCapNhat['ngay_sinh'] = $ngaySinh;
        }

        $gioiTinh = trim($_POST['gioi_tinh'] ?? '');
        if (!empty($gioiTinh)) {
            $dataCapNhat['gioi_tinh'] = $gioiTinh;
        }

        $result = $this->khachHangModel->update($userId, $dataCapNhat);

        if ($result) {
            //cập nhật session vs data mới
            \App\Core\Session::set('user_name', $dataCapNhat['ho_ten']);
            $_SESSION['success'] = "Cập nhật hồ sơ thành công!";
        } else {
            $_SESSION['error'] = "Cập nhật hồ sơ thất bại!";
        }

        header("Location: /client/profile");
        exit();
    }

    public function doiMatKhau()
    {
        \App\Core\Session::start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /client/profile");
            exit();
        }

        $matKhauCu = $_POST['mat_khau_cu'] ?? '';
        $matKhauMoi = $_POST['mat_khau_moi'] ?? '';
        $xacNhanMatKhau = $_POST['xac_nhan_mat_khau'] ?? '';

        if ($matKhauMoi !== $xacNhanMatKhau) {
            $_SESSION['error'] = "Mật khẩu xác nhận không khớp!";
            header("Location: /client/profile");
            exit();
        }

        if (strlen($matKhauMoi) < 6) {
            $_SESSION['error'] = "Mật khẩu mới phải có ít nhất 6 ký tự!";
            header("Location: /client/profile");
            exit();
        }

        //lấy userId hiện tại
        $userData = $this->khachHangModel->getById($userId);

        if (!$userData) {
            $_SESSION['error'] = "Không tìm thấy thông tin người dùng!";
            header("Location: /client/profile");
            exit();
        }

        $matKhauCuHash = sha1(trim($matKhauCu));
        if ($userData['mat_khau'] !== $matKhauCuHash) {
            $_SESSION['error'] = "Mật khẩu hiện tại không đúng!";
            header("Location: /client/profile");
            exit();
        }

        $matKhauMoiHash = sha1(trim($matKhauMoi));
        $result = $this->khachHangModel->update($userId, ['mat_khau' => $matKhauMoiHash]);

        if ($result) {
            $_SESSION['success'] = "Đổi mật khẩu thành công!";
        } else {
            $_SESSION['error'] = "Đổi mật khẩu thất bại!";
        }

        header("Location: /client/profile");
        exit();
    }

    public function capNhatAvatar()
    {
        \App\Core\Session::start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /client/profile");
            exit();
        }

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Vui lòng chọn ảnh để upload!";
            header("Location: /client/profile");
            exit();
        }

        $file = $_FILES['avatar'];

        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = "Kích thước ảnh không được vượt quá 2MB!";
            header("Location: /client/profile");
            exit();
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = "Chỉ chấp nhận file JPG, JPEG hoặc PNG!";
            header("Location: /client/profile");
            exit();
        }

        try {
            $cloudinary = CloudinaryService::getInstance();

            $userData = $this->khachHangModel->getById($userId);

            //ép tên theo định dạng: avatar_user_1, avatar_user_2...
            $publicId = 'avatar_user_' . $userId;

            $uploadResult = $cloudinary->uploadApi()->upload($file['tmp_name'], [
                'folder' => 'avatars',
                'public_id' => $publicId,
                'overwrite' => true,
                'invalidate' => true
            ]);

            $avatarUrl = $uploadResult['secure_url'];

            if (!empty($userData['avatar_url'])) {
                $oldUrl = $userData['avatar_url'];

                if (strpos($oldUrl, 'cloudinary.com') === false) {
                    $oldLocalPath = dirname(__DIR__, 3) . $oldUrl;
                    if (file_exists($oldLocalPath)) {
                        @unlink($oldLocalPath);
                    }
                } else if (strpos($oldUrl, $publicId) === false) {

                    $urlPath = parse_url($oldUrl, PHP_URL_PATH);
                    if (preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-zA-Z0-9]+$/', $urlPath, $matches)) {
                        $oldPublicId = $matches[1];
                        try {
                            $cloudinary->uploadApi()->destroy($oldPublicId, ['invalidate' => true]);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }

            $result = $this->khachHangModel->update($userId, [
                'avatar_url' => $avatarUrl,
                'ngay_cap_nhat' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                $_SESSION['success'] = "Cập nhật ảnh đại diện thành công!";
            } else {
                $_SESSION['error'] = "Cập nhật ảnh đại diện thất bại!";
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Upload ảnh thất bại: " . $e->getMessage();
        }

        header("Location: /client/profile");
        exit();
    }

    public function themDiaChi()
    {
        \App\Core\Session::start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /client/profile");
            exit();
        }

        $requiredFields = [
            'ho_ten_nguoi_nhan',
            'sdt_nguoi_nhan',
            'tinh_thanh',
            'quan_huyen',
            'phuong_xa',
            'dia_chi_cu_the'
        ];

        foreach ($requiredFields as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin!";
                header("Location: /client/profile");
                exit();
            }
        }

        $data = [
            'nguoi_dung_id' => (int) $userId,
            'ten_nguoi_nhan' => trim($_POST['ho_ten_nguoi_nhan']),
            'sdt_nhan' => trim($_POST['sdt_nguoi_nhan']),
            'tinh_thanh' => trim($_POST['tinh_thanh']),
            'quan_huyen' => trim($_POST['quan_huyen']),
            'phuong_xa' => trim($_POST['phuong_xa']),
            'so_nha_duong' => trim($_POST['dia_chi_cu_the']),
            'mac_dinh' => isset($_POST['is_mac_dinh']) ? 1 : 0
        ];

        require_once dirname(__DIR__, 2) . '/models/entities/DiaChi.php';
        $diaChiModel = new DiaChi();

        try {
            $result = $diaChiModel->themDiaChi($data);

            if ($result > 0) {
                $_SESSION['success'] = "Thêm địa chỉ thành công!";
            } else {
                $_SESSION['error'] = "Thêm địa chỉ thất bại!";
            }
        } catch (\Exception $e) {
            error_log("Address creation error: " . $e->getMessage());
            $_SESSION['error'] = "Đã xảy ra lỗi. Vui lòng thử lại!";
        }

        header("Location: /client/profile");
        exit();
    }

    public function capNhatDiaChi()
    {
        \App\Core\Session::start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /client/profile");
            exit();
        }

        $addressId = (int) ($_POST['id'] ?? 0);
        if (!$addressId) {
            $_SESSION['error'] = "Địa chỉ không hợp lệ!";
            header("Location: /client/profile");
            exit();
        }

        require_once dirname(__DIR__, 2) . '/models/entities/DiaChi.php';
        $diaChiModel = new DiaChi();

        $diaChi = $diaChiModel->getById($addressId);
        if (!$diaChi || $diaChi['nguoi_dung_id'] != $userId) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện thao tác này!";
            header("Location: /client/profile");
            exit();
        }

        $requiredFields = [
            'ho_ten_nguoi_nhan',
            'sdt_nguoi_nhan',
            'tinh_thanh',
            'quan_huyen',
            'phuong_xa',
            'dia_chi_cu_the'
        ];

        foreach ($requiredFields as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin!";
                header("Location: /client/profile");
                exit();
            }
        }

        $data = [
            'ho_ten_nguoi_nhan' => trim($_POST['ho_ten_nguoi_nhan']),
            'sdt_nguoi_nhan' => trim($_POST['sdt_nguoi_nhan']),
            'tinh_thanh' => trim($_POST['tinh_thanh']),
            'quan_huyen' => trim($_POST['quan_huyen']),
            'phuong_xa' => trim($_POST['phuong_xa']),
            'dia_chi_cu_the' => trim($_POST['dia_chi_cu_the']),
            'mac_dinh' => isset($_POST['is_mac_dinh']) ? 1 : 0
        ];

        try {
            $result = $diaChiModel->capNhatDiaChi($addressId, $data);

            if ($result) {
                $_SESSION['success'] = "Cập nhật địa chỉ thành công!";
            } else {
                $_SESSION['error'] = "Cập nhật địa chỉ thất bại!";
            }
        } catch (\Exception $e) {
            error_log("Address update error: " . $e->getMessage());
            $_SESSION['error'] = "Đã xảy ra lỗi. Vui lòng thử lại!";
        }

        header("Location: /client/profile");
        exit();
    }

    public function xoaDiaChi()
    {
        \App\Core\Session::start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /client/profile");
            exit();
        }

        $addressId = (int) ($_POST['id'] ?? 0);
        if (!$addressId) {
            $_SESSION['error'] = "Địa chỉ không hợp lệ!";
            header("Location: /client/profile");
            exit();
        }

        require_once dirname(__DIR__, 2) . '/models/entities/DiaChi.php';
        $diaChiModel = new DiaChi();

        $diaChi = $diaChiModel->getById($addressId);
        if (!$diaChi || $diaChi['nguoi_dung_id'] != $userId) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện thao tác này!";
            header("Location: /client/profile");
            exit();
        }

        try {
            $result = $diaChiModel->xoaDiaChi($addressId);

            if ($result) {
                $_SESSION['success'] = "Xóa địa chỉ thành công!";
            } else {
                $_SESSION['error'] = "Xóa địa chỉ thất bại!";
            }
        } catch (\Exception $e) {
            error_log("Address deletion error: " . $e->getMessage());
            $_SESSION['error'] = "Đã xảy ra lỗi. Vui lòng thử lại!";
        }

        header("Location: /client/profile");
        exit();
    }

    public function datMacDinh()
    {
        \App\Core\Session::start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /client/profile");
            exit();
        }

        $addressId = (int) ($_POST['id'] ?? 0);
        if (!$addressId) {
            $_SESSION['error'] = "Địa chỉ không hợp lệ!";
            header("Location: /client/profile");
            exit();
        }

        require_once dirname(__DIR__, 2) . '/models/entities/DiaChi.php';
        $diaChiModel = new DiaChi();

        $diaChi = $diaChiModel->getById($addressId);
        if (!$diaChi || $diaChi['nguoi_dung_id'] != $userId) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện thao tác này!";
            header("Location: /client/profile");
            exit();
        }

        try {
            $result = $diaChiModel->datMacDinh($addressId);

            if ($result) {
                $_SESSION['success'] = "Đặt địa chỉ mặc định thành công!";
            } else {
                $_SESSION['error'] = "Đặt địa chỉ mặc định thất bại!";
            }
        } catch (\Exception $e) {
            error_log("Set default address error: " . $e->getMessage());
            $_SESSION['error'] = "Đã xảy ra lỗi. Vui lòng thử lại!";
        }

        header("Location: /client/profile");
        exit();
    }
}
