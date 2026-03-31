<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="app-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>
    
    <main class="app-main">
        <?php 
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'Người Dùng', 'url' => '']
        ];
        require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
        ?>
        
        <div class="app-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Quản Lý Người Dùng</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php
                                $messages = [
                                    'user_blocked' => 'Chặn người dùng thành công.',
                                    'user_unblocked' => 'Mở chặn người dùng thành công.',
                                    'bulk_updated' => 'Cập nhật trạng thái thành công cho ' . ($_GET['count'] ?? 0) . ' người dùng.',
                                ];
                                echo htmlspecialchars($messages[$success] ?? $success);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php
                                $messages = [
                                    'invalid_id' => 'ID không hợp lệ.',
                                    'not_found' => 'Không tìm thấy người dùng.',
                                    'cannot_block_self' => 'Bạn không thể chặn chính mình.',
                                    'invalid_bulk_action' => 'Thao tác hàng loạt không hợp lệ.',
                                ];
                                echo htmlspecialchars($messages[$error] ?? $error);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Filter and Search Form -->
                        <form method="GET" action="/admin/nguoi-dung" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="loai_tai_khoan" class="form-label">Loại Tài Khoản</label>
                                    <select name="loai_tai_khoan" id="loai_tai_khoan" class="form-select">
                                        <option value="">Tất cả</option>
                                        <option value="ADMIN" <?= $loaiTaiKhoan === 'ADMIN' ? 'selected' : '' ?>>Quản trị viên</option>
                                        <option value="MEMBER" <?= $loaiTaiKhoan === 'MEMBER' ? 'selected' : '' ?>>Khách hàng</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="trang_thai" class="form-label">Trạng Thái</label>
                                    <select name="trang_thai" id="trang_thai" class="form-select">
                                        <option value="">Tất cả</option>
                                        <option value="ACTIVE" <?= $trangThai === 'ACTIVE' ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="BLOCKED" <?= $trangThai === 'BLOCKED' ? 'selected' : '' ?>>Bị chặn</option>
                                        <option value="UNVERIFIED" <?= $trangThai === 'UNVERIFIED' ? 'selected' : '' ?>>Chưa xác thực</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Tìm Kiếm</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Email, họ tên, hoặc số điện thoại" 
                                           value="<?= htmlspecialchars($search) ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Tìm
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Bulk Actions Form -->
                        <form method="POST" action="/admin/nguoi-dung/cap-nhat-hang-loat" id="bulkForm">
                            <div class="mb-3 d-flex gap-2">
                                <button type="submit" name="action" value="block" class="btn btn-warning btn-sm" 
                                        onclick="return confirm('Bạn có chắc chắn muốn chặn các người dùng đã chọn?')">
                                    <i class="bi bi-lock"></i> Chặn đã chọn
                                </button>
                                <button type="submit" name="action" value="unblock" class="btn btn-success btn-sm"
                                        onclick="return confirm('Bạn có chắc chắn muốn mở chặn các người dùng đã chọn?')">
                                    <i class="bi bi-unlock"></i> Mở chặn đã chọn
                                </button>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>ID</th>
                                            <th>Email</th>
                                            <th>Họ Tên</th>
                                            <th>Số Điện Thoại</th>
                                            <th>Loại Tài Khoản</th>
                                            <th>Trạng Thái</th>
                                            <th>Ngày Tạo</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($danhSachNguoiDung)): ?>
                                            <tr>
                                                <td colspan="9" class="text-center">Không có người dùng nào.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($danhSachNguoiDung as $nguoiDung): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="user_ids[]" value="<?= $nguoiDung['id'] ?>" class="user-checkbox">
                                                    </td>
                                                    <td><?= htmlspecialchars($nguoiDung['id']) ?></td>
                                                    <td><?= htmlspecialchars($nguoiDung['email']) ?></td>
                                                    <td><?= htmlspecialchars($nguoiDung['ho_ten'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($nguoiDung['sdt'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <?php if ($nguoiDung['loai_tai_khoan'] === 'ADMIN'): ?>
                                                            <span class="badge bg-danger">Quản trị viên</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-primary">Khách hàng</span>
                                                        <?php endif; ?>
                                                    </td>
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
                                                    <td><?= date('d/m/Y H:i', strtotime($nguoiDung['ngay_tao'])) ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <?php if ($nguoiDung['trang_thai'] === 'ACTIVE'): ?>
                                                                <a href="/admin/nguoi-dung/chan?id=<?= $nguoiDung['id'] ?>" 
                                                                   class="btn btn-sm btn-warning" 
                                                                   title="Chặn"
                                                                   onclick="return confirm('Bạn có chắc chắn muốn chặn người dùng này?')">
                                                                    <i class="bi bi-lock"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="/admin/nguoi-dung/mo-chan?id=<?= $nguoiDung['id'] ?>" 
                                                                   class="btn btn-sm btn-success" 
                                                                   title="Mở chặn"
                                                                   onclick="return confirm('Bạn có chắc chắn muốn mở chặn người dùng này?')">
                                                                    <i class="bi bi-unlock"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php if ($currentPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= $loaiTaiKhoan ? '&loai_tai_khoan=' . urlencode($loaiTaiKhoan) : '' ?><?= $trangThai ? '&trang_thai=' . urlencode($trangThai) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                                Trước
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?><?= $loaiTaiKhoan ? '&loai_tai_khoan=' . urlencode($loaiTaiKhoan) : '' ?><?= $trangThai ? '&trang_thai=' . urlencode($trangThai) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($currentPage < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= $loaiTaiKhoan ? '&loai_tai_khoan=' . urlencode($loaiTaiKhoan) : '' ?><?= $trangThai ? '&trang_thai=' . urlencode($trangThai) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                                Sau
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <p class="text-center text-muted">
                                Hiển thị <?= ($currentPage - 1) * $limit + 1 ?> - <?= min($currentPage * $limit, $totalNguoiDung) ?> 
                                trong tổng số <?= $totalNguoiDung ?> người dùng
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

