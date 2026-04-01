<?php
use App\Core\Session;
Session::start();

$admin = $data['admin'] ?? [];
$successMessage = Session::get('success_message');
$errorMessage = Session::get('error_message');

// Clear messages after displaying
Session::remove('success_message');
Session::remove('error_message');

// Generate avatar URL
$avatarUrl = '';
if (!empty($admin['avatar_url']) && file_exists(__DIR__ . '/../../../../public/uploads/avatars/' . $admin['avatar_url'])) {
    $avatarUrl = '/public/uploads/avatars/' . $admin['avatar_url'];
} else {
    $initials = '';
    $nameParts = explode(' ', $admin['ho_ten'] ?? 'Admin');
    foreach ($nameParts as $part) {
        if (!empty($part)) {
            $initials .= mb_substr($part, 0, 1);
        }
    }
    $initials = mb_strtoupper(mb_substr($initials, 0, 2));
    $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&background=0d6efd&color=fff&size=200';
}
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <?php require_once __DIR__ . '/../layouts/breadcrumb.php'; ?>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- Profile Card -->
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img src="<?= htmlspecialchars($avatarUrl) ?>" 
                                     alt="Avatar" 
                                     class="rounded-circle"
                                     style="width: 150px; height: 150px; object-fit: cover;"
                                     id="avatarPreview">
                            </div>
                            <h4><?= htmlspecialchars($admin['ho_ten'] ?? 'Admin') ?></h4>
                            <p class="text-muted"><?= htmlspecialchars($admin['email'] ?? '') ?></p>
                            <p class="badge bg-primary">Quản trị viên</p>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('avatarInput').click()">
                                    <i class="bi bi-camera"></i> Đổi ảnh đại diện
                                </button>
                                <form id="avatarForm" style="display: none;">
                                    <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="uploadAvatar()">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($successMessage) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($errorMessage) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Profile Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin cá nhân</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/admin/profile/update">
                                <div class="mb-3">
                                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ho_ten" 
                                           value="<?= htmlspecialchars($admin['ho_ten'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" 
                                           value="<?= htmlspecialchars($admin['email'] ?? '') ?>" disabled>
                                    <small class="text-muted">Email không thể thay đổi</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" name="sdt" 
                                           value="<?= htmlspecialchars($admin['sdt'] ?? '') ?>" 
                                           pattern="[0-9]{10}" placeholder="0123456789">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Ngày sinh</label>
                                        <input type="date" class="form-control" name="ngay_sinh" 
                                               value="<?= htmlspecialchars($admin['ngay_sinh'] ?? '') ?>">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Giới tính</label>
                                        <select class="form-select" name="gioi_tinh">
                                            <option value="">-- Chọn giới tính --</option>
                                            <option value="NAM" <?= ($admin['gioi_tinh'] ?? '') === 'NAM' ? 'selected' : '' ?>>Nam</option>
                                            <option value="NU" <?= ($admin['gioi_tinh'] ?? '') === 'NU' ? 'selected' : '' ?>>Nữ</option>
                                            <option value="KHAC" <?= ($admin['gioi_tinh'] ?? '') === 'KHAC' ? 'selected' : '' ?>>Khác</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Lưu thay đổi
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Đổi mật khẩu</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/admin/profile/change-password">
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="new_password" 
                                           minlength="6" required>
                                    <small class="text-muted">Tối thiểu 6 ký tự</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="confirm_password" 
                                           minlength="6" required>
                                </div>

                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-key"></i> Đổi mật khẩu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function uploadAvatar() {
    const fileInput = document.getElementById('avatarInput');
    const file = fileInput.files[0];
    
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Vui lòng chọn file ảnh');
        return;
    }
    
    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Kích thước file không được vượt quá 2MB');
        return;
    }
    
    const formData = new FormData();
    formData.append('avatar', file);
    
    // Show loading
    const preview = document.getElementById('avatarPreview');
    const originalSrc = preview.src;
    
    fetch('/admin/profile/update-avatar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            preview.src = data.avatar_url + '?t=' + new Date().getTime();
            alert('Cập nhật avatar thành công');
            location.reload();
        } else {
            alert('Lỗi: ' + data.message);
            preview.src = originalSrc;
        }
    })
    .catch(error => {
        alert('Có lỗi xảy ra khi upload avatar');
        preview.src = originalSrc;
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
