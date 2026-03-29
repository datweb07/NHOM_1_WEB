<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/BaseModel.php';

// Check authentication
AuthMiddleware::checkMember();

// Load full user data from database
$userId = \App\Core\Session::getUserId();
if (!$userId) {
    header('Location: /client/auth/login');
    exit;
}

$userModel = new BaseModel('nguoi_dung');
$user = $userModel->getById($userId);

if (!$user) {
    header('Location: /client/auth/login');
    exit;
}

// Set page title
$pageTitle = 'Hồ sơ cá nhân - FPT Shop';

// Start output buffering for content
ob_start();
?>

<style>
    .profile-wrapper {
        background-color: #f4f4f4;
        padding: 30px 0;
    }
    .profile-sidebar {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .profile-sidebar-header {
        text-align: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .profile-sidebar-header img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #cb1c22;
    }
    .profile-sidebar-header h3 {
        font-size: 16px;
        margin-top: 10px;
        color: #333;
    }
    .profile-menu {
        list-style: none;
        padding: 0;
    }
    .profile-menu li {
        margin-bottom: 10px;
    }
    .profile-menu li a {
        display: block;
        color: #555;
        text-decoration: none;
        padding: 10px;
        border-radius: 4px;
        transition: all 0.3s;
    }
    .profile-menu li a:hover, .profile-menu li a.active {
        background-color: #fde8e8;
        color: #cb1c22;
        font-weight: bold;
    }
    .profile-menu li a i {
        width: 25px;
    }
    .profile-content-box {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .profile-content-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .profile-content-header h2 {
        font-size: 20px;
        color: #333;
        margin: 0 0 5px 0;
    }
    .profile-content-header p {
        color: #777;
        font-size: 14px;
        margin: 0;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: #333;
    }
    .form-group input, .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .form-group input:focus, .form-group select:focus {
        border-color: #cb1c22;
        outline: none;
    }
    .form-group input[disabled] {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }
    .btn-submit {
        background-color: #cb1c22;
        color: #fff;
        border: none;
        padding: 12px 25px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        transition: 0.3s;
        font-weight: 500;
    }
    .btn-submit:hover {
        background-color: #a0151b;
    }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-size: 14px;
    }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="profile-wrapper">
    <div class="grid wide">
        <div class="row">
            <!-- Sidebar -->
            <div class="col l-3 m-4 c-12">
                <div class="profile-sidebar">
                    <div class="profile-sidebar-header">
                        <img src="<?= !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : '/public/assets/client/images/others/anh-avatar.jpg' ?>" alt="Avatar">
                        <h3><?= htmlspecialchars($user['ho_ten'] ?? 'Tên người dùng') ?></h3>
                    </div>
                    <ul class="profile-menu">
                        <li><a href="/client/profile" class="active"><i class="fa fa-user"></i> Hồ sơ của tôi</a></li>
                        <li><a href="/don-hang.php"><i class="fa fa-file-invoice-dollar"></i> Đơn hàng của tôi</a></li>
                        <li><a href="/dia-chi.php"><i class="fa fa-map-marker-alt"></i> Sổ địa chỉ</a></li>
                        <li><a href="/client/auth/logout"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col l-9 m-8 c-12">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <!-- Profile Update Form -->
                <div class="profile-content-box">
                    <div class="profile-content-header">
                        <h2>Hồ sơ của tôi</h2>
                        <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                    </div>

                    <form action="/khach-hang/cap-nhat-ho-so" method="POST">
                        <div class="row">
                            <div class="col l-8 m-12 c-12">
                                <div class="form-group">
                                    <label>Email (Tên đăng nhập)</label>
                                    <input type="text" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Họ và tên</label>
                                    <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required>
                                </input>
                                <div class="form-group">
                                    <label>Số điện thoại</label>
                                    <input type="tel" name="sdt" value="<?= htmlspecialchars($user['sdt'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Ngày sinh</label>
                                    <input type="date" name="ngay_sinh" value="<?= htmlspecialchars($user['ngay_sinh'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Giới tính</label>
                                    <select name="gioi_tinh">
                                        <option value="NAM" <?= (($user['gioi_tinh'] ?? '') === 'NAM') ? 'selected' : '' ?>>Nam</option>
                                        <option value="NU" <?= (($user['gioi_tinh'] ?? '') === 'NU') ? 'selected' : '' ?>>Nữ</option>
                                        <option value="KHAC" <?= (($user['gioi_tinh'] ?? '') === 'KHAC') ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn-submit">Lưu thay đổi</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="profile-content-box">
                    <div class="profile-content-header">
                        <h2>Đổi mật khẩu</h2>
                        <p>Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
                    </div>
                    <form action="/khach-hang/doi-mat-khau" method="POST">
                        <div class="row">
                            <div class="col l-8 m-12 c-12">
                                <div class="form-group">
                                    <label>Mật khẩu hiện tại</label>
                                    <input type="password" name="mat_khau_cu" placeholder="Nhập mật khẩu hiện tại" required>
                                </div>
                                <div class="form-group">
                                    <label>Mật khẩu mới</label>
                                    <input type="password" name="mat_khau_moi" placeholder="Nhập mật khẩu mới" required>
                                </div>
                                <div class="form-group">
                                    <label>Xác nhận mật khẩu mới</label>
                                    <input type="password" name="xac_nhan_mat_khau" placeholder="Nhập lại mật khẩu mới" required>
                                </div>
                                <button type="submit" class="btn-submit">Cập nhật mật khẩu</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get content and include layout
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
