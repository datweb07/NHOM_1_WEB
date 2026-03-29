<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/BaseModel.php';

//check quyền
AuthMiddleware::checkMember();

//lấy data user từ db
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

$pageTitle = 'Hồ sơ cá nhân - FPT Shop';

ob_start();
?>

<div class="profile-wrapper">
    <div class="container-xl">
        <div class="row">

            <div class="col-lg-3 col-md-4 mb-4">
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

            <div class="col-lg-9 col-md-8">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="profile-content-box">
                    <div class="profile-content-header">
                        <h2>Hồ sơ của tôi</h2>
                        <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                    </div>

                    <form action="/khach-hang/cap-nhat-ho-so" method="POST">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label">Email (Tên đăng nhập)</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" name="sdt" value="<?= htmlspecialchars($user['sdt'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ngày sinh</label>
                                    <input type="date" class="form-control" name="ngay_sinh" value="<?= htmlspecialchars($user['ngay_sinh'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giới tính</label>
                                    <select class="form-select" name="gioi_tinh">
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

                <div class="profile-content-box">
                    <div class="profile-content-header">
                        <h2>Đổi mật khẩu</h2>
                        <p>Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
                    </div>
                    <form action="/khach-hang/doi-mat-khau" method="POST">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="mat_khau_cu" placeholder="Nhập mật khẩu hiện tại" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="mat_khau_moi" placeholder="Nhập mật khẩu mới" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="xac_nhan_mat_khau" placeholder="Nhập lại mật khẩu mới" required>
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
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
