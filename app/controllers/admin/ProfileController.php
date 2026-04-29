<?php

require_once dirname(__DIR__, 2) . '/models/roles/QuanTriVien.php';
require_once dirname(__DIR__, 2) . '/core/View.php';
require_once dirname(__DIR__, 2) . '/core/Session.php';
require_once dirname(__DIR__, 2) . '/core/FileUpload.php';

use App\Core\View;
use App\Core\Session;
use App\Core\FileUpload;

class ProfileController
{
    private QuanTriVien $adminModel;

    public function __construct()
    {
        $this->adminModel = new QuanTriVien();
    }

    public function index(): void
    {
        Session::start();
        $adminId = Session::getUserId();

        if (!$adminId) {
            header('Location: /admin/auth/login');
            exit;
        }

        $admin = $this->adminModel->getById($adminId);

        if (!$admin) {
            Session::set('error_message', 'Không tìm thấy thông tin admin');
            header('Location: /admin');
            exit;
        }

        $data = [
            'admin' => $admin,
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => '/admin'],
                ['name' => 'Hồ sơ cá nhân', 'url' => '/admin/profile']
            ]
        ];

        View::render('admin/profile/index', $data, null);
    }

    public function update(): void
    {
        Session::start();
        $adminId = Session::getUserId();

        if (!$adminId) {
            header('Location: /admin/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/profile');
            exit;
        }

        $hoTen = trim($_POST['ho_ten'] ?? '');
        $sdt = trim($_POST['sdt'] ?? '');
        $ngaySinh = trim($_POST['ngay_sinh'] ?? '');
        $gioiTinh = trim($_POST['gioi_tinh'] ?? '');

        $errors = [];

        if (empty($hoTen)) {
            $errors[] = 'Họ tên không được để trống';
        }

        if (!empty($sdt) && !preg_match('/^[0-9]{10}$/', $sdt)) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }

        if (!empty($errors)) {
            Session::set('error_message', implode(', ', $errors));
            header('Location: /admin/profile');
            exit;
        }

        $updateData = [
            'ho_ten' => $hoTen,
            'sdt' => $sdt,
            'ngay_sinh' => $ngaySinh ?: null,
            'gioi_tinh' => $gioiTinh ?: null
        ];

        $result = $this->adminModel->update($adminId, $updateData);

        if ($result > 0) {
            Session::set('user_name', $hoTen);
            Session::set('success_message', 'Cập nhật thông tin thành công');
        } else {
            Session::set('error_message', 'Cập nhật thông tin thất bại');
        }

        header('Location: /admin/profile');
        exit;
    }

    public function updateAvatar(): void
    {
        Session::start();
        $adminId = Session::getUserId();

        if (!$adminId) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Không có file được upload']);
            exit;
        }

        try {
            $uploadDir = __DIR__ . '/../../../public/uploads/avatars/';
            $avatarPath = FileUpload::uploadImage($_FILES['avatar'], $uploadDir);

            if (!$avatarPath) {
                echo json_encode(['success' => false, 'message' => 'Upload file thất bại']);
                exit;
            }

            $admin = $this->adminModel->getById($adminId);
            $oldAvatar = $admin['avatar_url'] ?? null;

            $result = $this->adminModel->update($adminId, ['avatar_url' => $avatarPath]);

            if ($result > 0) {
                if ($oldAvatar && file_exists(__DIR__ . '/../../../public/uploads/avatars/' . $oldAvatar)) {
                    unlink(__DIR__ . '/../../../public/uploads/avatars/' . $oldAvatar);
                }

                Session::set('user_avatar', $avatarPath);

                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật avatar thành công',
                    'avatar_url' => '/public/uploads/avatars/' . $avatarPath
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cập nhật database thất bại']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function changePassword(): void
    {
        Session::start();
        $adminId = Session::getUserId();

        if (!$adminId) {
            header('Location: /admin/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/profile');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        if (empty($currentPassword)) {
            $errors[] = 'Vui lòng nhập mật khẩu hiện tại';
        }

        if (empty($newPassword)) {
            $errors[] = 'Vui lòng nhập mật khẩu mới';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }

        if (!empty($errors)) {
            Session::set('error_message', implode(', ', $errors));
            header('Location: /admin/profile');
            exit;
        }

        $admin = $this->adminModel->getById($adminId);
        
        if (!$admin || !password_verify($currentPassword, $admin['mat_khau'])) {
            Session::set('error_message', 'Mật khẩu hiện tại không đúng');
            header('Location: /admin/profile');
            exit;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $this->adminModel->update($adminId, ['mat_khau' => $hashedPassword]);

        if ($result > 0) {
            Session::set('success_message', 'Đổi mật khẩu thành công');
        } else {
            Session::set('error_message', 'Đổi mật khẩu thất bại');
        }

        header('Location: /admin/profile');
        exit;
    }
}
