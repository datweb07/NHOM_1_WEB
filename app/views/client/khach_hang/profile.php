<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/BaseModel.php';

AuthMiddleware::checkMember();

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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
    .btn-submit {
        background-color: #cb1c22;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 500;
        transition: 0.3s;
    }
    .btn-submit:hover {
        background-color: #a8151b;
        color: #fff;
    }
    .profile-content-box {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }
    .profile-content-header {
        border-bottom: 1px solid #eee;
        margin-bottom: 24px;
        padding-bottom: 16px;
    }
    .profile-content-header h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 4px;
        color: #333;
    }
    .profile-content-header p {
        color: #666;
        margin-bottom: 0;
        font-size: 14px;
    }

    .profile-menu .nav-link {
        color: #555 !important;
        padding: 12px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        margin-bottom: 4px;
    }
    .profile-menu .nav-link:hover {
        background-color: #fde8e8 !important;
        color: #d70018 !important;
        font-weight: 600;
    }
    .profile-menu .nav-link.active {
        background-color: #fde8e8 !important;
        color: #d70018 !important;
        font-weight: 600;
    }

    .avatar-upload-section {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px dashed #dee2e6;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .avatar-preview {
        width: 150px;
        height: 150px;
        margin: 0 auto 20px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-note {
        font-size: 12.5px;
        color: #6c757d;
        margin-top: 12px;
        line-height: 1.5;
    }
    .profile-sidebar-header img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 2px solid #cb1c22;
    }
</style>

<div class="profile-wrapper py-4" style="background-color: #f4f4f4;">
    <div class="container-xl">
        <div class="row">

            <div class="col-lg-3 col-md-4 mb-4">
                <div class="profile-content-box" style="padding: 20px;">
                    <div class="profile-sidebar-header text-center border-bottom pb-3 mb-3">
                        <img src="<?= !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : BASE_URL . '/assets/client/images/others/anh-avatar.jpg' ?>" alt="Avatar">
                        <h3 class="fs-6 fw-bold m-0"><?= htmlspecialchars($user['ho_ten'] ?? 'Tên người dùng') ?></h3>
                    </div>
                    <ul class="nav flex-column profile-menu">
                        <li class="nav-item">
                            <a href="/client/profile" class="nav-link active">
                                <i class="bi bi-person me-2"></i> Hồ sơ của tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/don-hang" class="nav-link">
                                <i class="bi bi-receipt me-2"></i> Đơn hàng của tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/dia-chi" class="nav-link">
                                <i class="bi bi-geo-alt me-2"></i> Sổ địa chỉ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" id="logout-link" class="nav-link mt-2 pt-2 border-top">
                                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9 col-md-8">

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Hồ sơ -->
                <div class="profile-content-box">
                    <div class="profile-content-header">
                        <h2>Hồ sơ của tôi</h2>
                        <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                    </div>

                    <div class="row">
                        <div class="col-lg-8 pe-lg-4 border-end">
                            <form action="/khach-hang/cap-nhat-ho-so" method="POST">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Email (Tên đăng nhập)</label>
                                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Số điện thoại</label>
                                    <input type="tel" class="form-control" name="sdt" value="<?= htmlspecialchars($user['sdt'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Ngày sinh</label>
                                    <input type="date" class="form-control" name="ngay_sinh" value="<?= htmlspecialchars($user['ngay_sinh'] ?? '') ?>">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-medium">Giới tính</label>
                                    <select class="form-select" name="gioi_tinh">
                                        <option value="NAM" <?= (($user['gioi_tinh'] ?? '') === 'NAM') ? 'selected' : '' ?>>Nam</option>
                                        <option value="NU"  <?= (($user['gioi_tinh'] ?? '') === 'NU')  ? 'selected' : '' ?>>Nữ</option>
                                        <option value="KHAC" <?= (($user['gioi_tinh'] ?? '') === 'KHAC') ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn-submit">Lưu thay đổi</button>
                            </form>
                        </div>

                        <div class="col-lg-4 mt-4 mt-lg-0">
                            <form action="/khach-hang/cap-nhat-avatar" method="POST" enctype="multipart/form-data" id="avatar-upload-form" class="h-100">
                                <div class="avatar-upload-section">
                                    <div class="avatar-preview">
                                        <img id="avatar-preview-img"
                                             src="<?= !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : BASE_URL . '/assets/client/images/others/anh-avatar.jpg' ?>"
                                             alt="Avatar Preview">
                                    </div>
                                    <div class="w-100 px-3">
                                        <label for="avatar-input" class="btn btn-outline-secondary btn-sm w-100 mb-2" style="cursor: pointer;">
                                            <i class="bi bi-camera me-1"></i> Chọn ảnh
                                        </label>
                                        <input type="file" class="d-none" name="avatar" id="avatar-input" accept="image/jpeg,image/jpg,image/png" required>
                                        <button type="submit" class="btn btn-sm btn-submit w-100" id="btn-save-avatar" style="display: none;">
                                            Lưu ảnh đại diện
                                        </button>
                                    </div>
                                    <p class="avatar-note">Dung lượng tối đa 2MB<br>Định dạng: JPG, JPEG, PNG</p>
                                </div>
                            </form>
                        </div>
                    </div>
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
                                    <label class="form-label fw-medium">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="mat_khau_cu" placeholder="Nhập mật khẩu hiện tại" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Mật khẩu mới <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="mat_khau_moi" placeholder="Nhập mật khẩu mới" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-medium">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('avatar-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const btnSave = document.getElementById('btn-save-avatar');

    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('Kích thước ảnh không được vượt quá 2MB!');
            e.target.value = '';
            btnSave.style.display = 'none';
            return;
        }
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Chỉ chấp nhận file JPG, JPEG hoặc PNG!');
            e.target.value = '';
            btnSave.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('avatar-preview-img').src = event.target.result;
            btnSave.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        btnSave.style.display = 'none';
    }
});

document.getElementById('logout-link')?.addEventListener('click', function(e) {
    e.preventDefault();
    if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
        window.location.href = '/client/auth/logout';
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>