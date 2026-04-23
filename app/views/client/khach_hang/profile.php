<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/BaseModel.php';
require_once __DIR__ . '/../../../controllers/client/KhachHangController.php';

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

    .profile-menu .nav-link:hover,
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
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

    .tab-content>.tab-pane {
        display: none;
    }

    .tab-content>.active {
        display: block;
    }
</style>

<div class="profile-wrapper py-4" style="background-color: #f4f4f4;">
    <div class="container-xl">
        <div class="row">

            <div class="col-lg-3 col-md-4 mb-4">
                <div class="profile-content-box" style="padding: 20px;">
                    <div class="profile-sidebar-header text-center border-bottom pb-3 mb-3">
                        <img src="<?= !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : ASSET_URL . '/assets/client/images/others/anh-avatar.jpg' ?>"
                            alt="Avatar">
                        <h3 class="fs-6 fw-bold m-0"><?= htmlspecialchars($user['ho_ten'] ?? 'Tên người dùng') ?></h3>
                    </div>
                    <ul class="nav flex-column profile-menu" id="profileTabs">
                        <li class="nav-item">
                            <a href="#ho-so" class="nav-link active" data-bs-toggle="tab">
                                <i class="bi bi-person me-2"></i> Hồ sơ của tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/don-hang" class="nav-link">
                                <i class="bi bi-receipt me-2"></i> Đơn hàng của tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#dia-chi" class="nav-link" data-bs-toggle="tab">
                                <i class="bi bi-geo-alt me-2"></i> Sổ địa chỉ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#yeu-thich" class="nav-link" data-bs-toggle="tab">
                                <i class="bi bi-heart me-2"></i> Sản phẩm yêu thích
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#danh-gia" class="nav-link" data-bs-toggle="tab">
                                <i class="bi bi-star me-2"></i> Đánh giá của tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#giao-dich" class="nav-link" data-bs-toggle="tab">
                                <i class="bi bi-wallet2 me-2"></i> Lịch sử giao dịch
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

            <div class="col-lg-9 col-md-8 tab-content">

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="tab-pane fade show active" id="ho-so">
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
                                        <input type="text" class="form-control bg-light"
                                            value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Họ và tên <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="ho_ten"
                                            value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Số điện thoại</label>
                                        <input type="tel" class="form-control" name="sdt"
                                            value="<?= htmlspecialchars($user['sdt'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Ngày sinh</label>
                                        <input type="date" class="form-control" name="ngay_sinh"
                                            value="<?= htmlspecialchars($user['ngay_sinh'] ?? '') ?>">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-medium">Giới tính</label>
                                        <select class="form-select" name="gioi_tinh">
                                            <option value="NAM" <?= (($user['gioi_tinh'] ?? '') === 'NAM') ? 'selected' : '' ?>>Nam</option>
                                            <option value="NU" <?= (($user['gioi_tinh'] ?? '') === 'NU') ? 'selected' : '' ?>>Nữ</option>
                                            <option value="KHAC" <?= (($user['gioi_tinh'] ?? '') === 'KHAC') ? 'selected' : '' ?>>Khác</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn-submit">Lưu thay đổi</button>
                                </form>
                            </div>

                            <div class="col-lg-4 mt-4 mt-lg-0">
                                <form action="/khach-hang/cap-nhat-avatar" method="POST" enctype="multipart/form-data"
                                    id="avatar-upload-form" class="h-100">
                                    <div class="avatar-upload-section">
                                        <div class="avatar-preview">
                                            <img id="avatar-preview-img"
                                                src="<?= !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : ASSET_URL . '/assets/client/images/others/anh-avatar.jpg' ?>"
                                                alt="Avatar Preview">
                                        </div>
                                        <div class="w-100 px-3">
                                            <label for="avatar-input"
                                                class="btn btn-outline-secondary btn-sm w-100 mb-2"
                                                style="cursor: pointer;">
                                                <i class="bi bi-camera me-1"></i> Chọn ảnh
                                            </label>
                                            <input type="file" class="d-none" name="avatar" id="avatar-input"
                                                accept="image/jpeg,image/jpg,image/png" required>
                                            <button type="submit" class="btn btn-sm btn-submit w-100"
                                                id="btn-save-avatar" style="display: none;">
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
                        <?php if ($auth_provider === 'LOCAL'): ?>
                            <form action="/khach-hang/doi-mat-khau" method="POST">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Mật khẩu hiện tại <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="mat_khau_cu"
                                                placeholder="Nhập mật khẩu hiện tại" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Mật khẩu mới <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="mat_khau_moi"
                                                placeholder="Nhập mật khẩu mới" required>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-medium">Xác nhận mật khẩu mới <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="xac_nhan_mat_khau"
                                                placeholder="Nhập lại mật khẩu mới" required>
                                        </div>
                                        <button type="submit" class="btn-submit">Cập nhật mật khẩu</button>
                                    </div>
                                </div>
                            </form>
                        <?php elseif ($auth_provider === 'GOOGLE'): ?>
                            <div class="alert alert-info">
                                Tài khoản của bạn được liên kết với Google. Bạn không cần đặt mật khẩu.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="dia-chi">
                    <?php

                    require_once __DIR__ . '/../dia_chi/index.php';

                    ?>
                </div>

                <div class="tab-pane fade" id="yeu-thich">
                    <div class="profile-content-box">
                        <div class="profile-content-header">
                            <h2>Sản phẩm yêu thích</h2>
                            <p>Danh sách các sản phẩm bạn đã quan tâm và lưu lại</p>
                        </div>
                        
                        <?php if (empty($sanPhamsYeuThich)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-heart text-muted" style="font-size: 4rem;"></i>
                                <p class="mt-3 text-muted fs-6">Bạn chưa có sản phẩm yêu thích nào</p>
                                <a href="/san-pham" class="btn-submit d-inline-block px-4 py-2 mt-2" style="text-decoration: none;">Khám phá sản phẩm ngay</a>
                            </div>
                        <?php else: ?>
                            <div class="row g-3 mt-2">
                                <?php foreach ($sanPhamsYeuThich as $sp): ?>
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="card border-0 shadow-sm h-100 position-relative">
                                            <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 btn-remove-favorite rounded-circle"
                                                    data-id="<?= $sp['id'] ?>" style="width:30px;height:30px;padding:0;z-index:1;">
                                                <i class="bi bi-x" style="font-size:1.2rem;"></i>
                                            </button>
                                            <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none">
                                                <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                                                     class="card-img-top p-2"
                                                     alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>"
                                                     style="height:160px;object-fit:contain;">
                                                <div class="card-body pt-0 px-3 pb-3">
                                                    <p class="small mb-1 text-dark fw-medium" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                                        <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                                    </p>
                                                    <p class="text-danger fw-bold mb-0 small"><?= number_format($sp['gia_hien_thi'], 0, ',', '.') ?>đ</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="danh-gia">
                    <div class="profile-content-box">
                        <div class="profile-content-header">
                            <h2>Đánh giá của tôi</h2>
                            <p>Những nhận xét và đánh giá của bạn về các sản phẩm đã mua</p>
                        </div>
                        <?php if (empty($danhGiaList)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-star text-muted" style="font-size: 4rem;"></i>
                                <p class="mt-3 text-muted fs-6">Bạn chưa có đánh giá nào</p>
                                <a href="/don-hang" class="btn-submit d-inline-block px-4 py-2 mt-2" style="text-decoration: none; background: #fff; border: 1px solid var(--primary-color); color: var(--primary-color);">Xem đơn hàng của tôi</a>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush mt-3">
                                <?php foreach ($danhGiaList as $dg): ?>
                                    <div class="list-group-item py-4 px-0 border-bottom">
                                        <div class="d-flex gap-3">
                                            <img src="<?= htmlspecialchars($dg['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>" 
                                                 alt="<?= htmlspecialchars($dg['ten_san_pham'] ?? '') ?>"
                                                 style="width: 80px; height: 80px; object-fit: contain; border-radius: 8px; border: 1px solid #eee;">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0">
                                                        <a href="/san-pham/<?= htmlspecialchars($dg['slug'] ?? '') ?>" class="text-dark text-decoration-none hover-danger">
                                                            <?= htmlspecialchars($dg['ten_san_pham'] ?? 'Sản phẩm') ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($dg['ngay_viet'])) ?></small>
                                                </div>
                                                <div class="text-warning mb-2 ds-sao" style="font-size: 0.9rem;">
                                                    <?php for($i=1; $i<=5; $i++): ?>
                                                        <i class="bi bi-star<?= $i <= $dg['so_sao'] ? '-fill' : '' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <p class="mb-0 text-secondary" style="font-size: 0.95rem;"><?= nl2br(htmlspecialchars($dg['noi_dung'] ?? '')) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="giao-dich">
                    <div class="profile-content-box">
                        <div class="profile-content-header">
                            <h2>Lịch sử giao dịch</h2>
                            <p>Chi tiết các giao dịch mua hàng, nạp/rút và hoàn tiền</p>
                        </div>
                        <div class="table-responsive mt-4">
                            <table class="table table-hover border align-middle">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th class="py-3 px-3 fw-medium">Mã GD</th>
                                        <th class="py-3 px-3 fw-medium">Ngày thực hiện</th>
                                        <th class="py-3 px-3 fw-medium">Loại giao dịch</th>
                                        <th class="py-3 px-3 fw-medium text-end">Số tiền</th>
                                        <th class="py-3 px-3 fw-medium text-end">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-wallet2 text-muted mb-2 d-block" style="font-size: 3rem;"></i>
                                            Chưa có dữ liệu giao dịch nào.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('avatar-input')?.addEventListener('change', function (e) {
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
            reader.onload = function (event) {
                document.getElementById('avatar-preview-img').src = event.target.result;
                btnSave.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            btnSave.style.display = 'none';
        }
    });

    document.getElementById('logout-link')?.addEventListener('click', function (e) {
        e.preventDefault();
        if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
            window.location.href = '/client/auth/logout';
        }
    });

    // ==========================================
    // TÍCH HỢP DATA HÀNH CHÍNH VIỆT NAM (dvhcvn)
    // Nguồn raw json từ Github DaoHoangSon
    // ==========================================
    document.addEventListener("DOMContentLoaded", function () {
        const tinhThanhSelect = document.getElementById('tinhThanh');
        const quanHuyenSelect = document.getElementById('quanHuyen');
        const phuongXaSelect = document.getElementById('phuongXa');
        let vnData = [];

        const dataUrl = 'https://raw.githubusercontent.com/daohoangson/dvhcvn/master/data/dvhcvn.json';

        // Fetch dữ liệu từ Github
        fetch(dataUrl)
            .then(response => response.json())
            .then(data => {
                vnData = data.data; // Lưu lại mảng data
                // Đổ dữ liệu Tỉnh Thành vào select
                vnData.forEach(tinh => {
                    const option = document.createElement('option');
                    option.value = tinh.name; // Bạn có thể dùng tinh.level1_id nếu lưu ID vào DB
                    option.dataset.id = tinh.level1_id;
                    option.textContent = tinh.name;
                    tinhThanhSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Lỗi khi tải dữ liệu hành chính:', error);
                tinhThanhSelect.innerHTML = '<option disabled>Không thể tải dữ liệu</option>';
            });

        // Khi thay đổi Tỉnh / Thành
        tinhThanhSelect.addEventListener('change', function () {
            const selectedId = this.options[this.selectedIndex].dataset.id;

            // Reset Quận/Huyện và Phường/Xã
            quanHuyenSelect.innerHTML = '<option value="" selected disabled>Chọn Quận/Huyện</option>';
            phuongXaSelect.innerHTML = '<option value="" selected disabled>Chọn Phường/Xã</option>';
            phuongXaSelect.disabled = true;

            if (selectedId) {
                const tinhData = vnData.find(t => t.level1_id === selectedId);
                if (tinhData && tinhData.level2s) {
                    tinhData.level2s.forEach(quan => {
                        const option = document.createElement('option');
                        option.value = quan.name;
                        option.dataset.id = quan.level2_id;
                        option.textContent = quan.name;
                        quanHuyenSelect.appendChild(option);
                    });
                    quanHuyenSelect.disabled = false;
                }
            }
        });

        // Khi thay đổi Quận / Huyện
        quanHuyenSelect.addEventListener('change', function () {
            const selectedTinhId = tinhThanhSelect.options[tinhThanhSelect.selectedIndex].dataset.id;
            const selectedQuanId = this.options[this.selectedIndex].dataset.id;

            // Reset Phường/Xã
            phuongXaSelect.innerHTML = '<option value="" selected disabled>Chọn Phường/Xã</option>';

            if (selectedTinhId && selectedQuanId) {
                const tinhData = vnData.find(t => t.level1_id === selectedTinhId);
                const quanData = tinhData.level2s.find(q => q.level2_id === selectedQuanId);

                if (quanData && quanData.level3s) {
                    quanData.level3s.forEach(phuong => {
                        const option = document.createElement('option');
                        option.value = phuong.name;
                        option.dataset.id = phuong.level3_id;
                        option.textContent = phuong.name;
                        phuongXaSelect.appendChild(option);
                    });
                    phuongXaSelect.disabled = false;
                }
            }
        });
    });

    document.querySelectorAll('.btn-remove-favorite').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!confirm('Xóa khỏi danh sách yêu thích?')) return;
            fetch('/yeu-thich/xoa', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'san_pham_id=' + this.dataset.id
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            });
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>