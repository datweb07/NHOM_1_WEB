<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="app-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>

    <main class="app-main">
        <?php 
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'Người Dùng', 'url' => '/admin/nguoi-dung'],
            ['label' => 'Chi Tiết', 'url' => '']
        ];
        require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
        ?>

        <div class="app-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Chi Tiết Người Dùng</h3>
                        <a href="/admin/nguoi-dung" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <?php if (!empty($nguoiDung['avatar_url'])): ?>
                                <img src="<?= htmlspecialchars($nguoiDung['avatar_url']) ?>" alt="Avatar"
                                    class="img-thumbnail rounded-circle"
                                    style="width: 200px; height: 200px; object-fit: cover;">
                                <?php else: ?>
                                <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width: 200px; height: 200px; font-size: 80px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-8">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%;">ID</th>
                                            <td><?= htmlspecialchars($nguoiDung['id']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><?= htmlspecialchars($nguoiDung['email']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Họ Tên</th>
                                            <td><?= htmlspecialchars($nguoiDung['ho_ten'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Số Điện Thoại</th>
                                            <td><?= htmlspecialchars($nguoiDung['sdt'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Ngày Sinh</th>
                                            <td><?= $nguoiDung['ngay_sinh'] ? date('d/m/Y', strtotime($nguoiDung['ngay_sinh'])) : 'N/A' ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Giới Tính</th>
                                            <td>
                                                <?php
                                                $gioiTinh = [
                                                    'NAM' => 'Nam',
                                                    'NU' => 'Nữ',
                                                    'KHAC' => 'Khác'
                                                ];
                                                echo $gioiTinh[$nguoiDung['gioi_tinh']] ?? 'N/A';
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Loại Tài Khoản</th>
                                            <td>
                                                <?php if ($nguoiDung['loai_tai_khoan'] === 'ADMIN'): ?>
                                                <span class="badge bg-danger">Quản trị viên</span>
                                                <?php else: ?>
                                                <span class="badge bg-primary">Khách hàng</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Trạng Thái</th>
                                            <td>
                                                <?php
                                                $statusBadges = [
                                                    'ACTIVE' => '<span class="badge bg-success">Hoạt động</span>',
                                                    'BLOCKED' => '<span class="badge bg-danger">Bị chặn</span>',
                                                    'UNVERIFIED' => '<span class="badge bg-warning">Chưa xác thực</span>',
                                                ];
                                                echo $statusBadges[$nguoiDung['trang_thai']] ?? htmlspecialchars($nguoiDung['trang_thai']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Ngày Tạo</th>
                                            <td><?= date('d/m/Y H:i:s', strtotime($nguoiDung['ngay_tao'])) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Ngày Cập Nhật</th>
                                            <td><?= date('d/m/Y H:i:s', strtotime($nguoiDung['ngay_cap_nhat'])) ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    <?php if ($nguoiDung['trang_thai'] === 'ACTIVE'): ?>
                                    <a href="/admin/nguoi-dung/chan?id=<?= $nguoiDung['id'] ?>" class="btn btn-warning"
                                        onclick="return confirm('Bạn có chắc chắn muốn chặn người dùng này?')">
                                        <i class="bi bi-lock"></i> Chặn Người Dùng
                                    </a>
                                    <?php else: ?>
                                    <a href="/admin/nguoi-dung/mo-chan?id=<?= $nguoiDung['id'] ?>"
                                        class="btn btn-success"
                                        onclick="return confirm('Bạn có chắc chắn muốn mở chặn người dùng này?')">
                                        <i class="bi bi-unlock"></i> Mở Chặn Người Dùng
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>