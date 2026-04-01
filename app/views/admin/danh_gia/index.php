<?php
$danhSachDanhGia = $danhSachDanhGia ?? [];
$danhSachSanPham = $danhSachSanPham ?? [];
$soSao = $soSao ?? null;
$sanPhamId = $sanPhamId ?? null;
$keyword = $keyword ?? '';
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalReviews = $totalReviews ?? 0;
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

require_once dirname(__DIR__) . '/layouts/header.php';
?>

<?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Đánh Giá', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php
                            $messages = [
                                'deleted' => 'Xóa đánh giá thành công!',
                            ];
                            echo htmlspecialchars($messages[$success] ?? $success);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php
                            $messages = [
                                'invalid_id' => 'ID không hợp lệ!',
                                'not_found' => 'Không tìm thấy đánh giá!',
                                'delete_failed' => 'Xóa đánh giá thất bại!',
                            ];
                            echo htmlspecialchars($messages[$error] ?? $error);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Search and Filter Form -->
                    <form method="GET" action="/admin/danh-gia" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="keyword" class="form-label">Tìm Kiếm</label>
                                <input type="text" class="form-control" id="keyword" name="keyword" 
                                       value="<?= htmlspecialchars($keyword) ?>" 
                                       placeholder="Nội dung hoặc tên người dùng...">
                            </div>
                            <div class="col-md-3">
                                <label for="san_pham_id" class="form-label">Sản Phẩm</label>
                                <select class="form-select" id="san_pham_id" name="san_pham_id">
                                    <option value="">-- Tất cả --</option>
                                    <?php foreach ($danhSachSanPham as $sp): ?>
                                        <option value="<?= (int)$sp['id'] ?>" 
                                                <?= $sanPhamId === (int)$sp['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="so_sao" class="form-label">Số Sao</label>
                                <select class="form-select" id="so_sao" name="so_sao">
                                    <option value="">-- Tất cả --</option>
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?= $i ?>" <?= $soSao === $i ? 'selected' : '' ?>>
                                            <?= $i ?> sao
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Tìm
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Reviews Table -->
                    <div class="table-responsive">
                        <?php if (empty($danhSachDanhGia)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-3">Không có đánh giá nào</p>
                            </div>
                        <?php else: ?>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Người Dùng</th>
                                        <th>Sản Phẩm</th>
                                        <th>Số Sao</th>
                                        <th>Nội Dung</th>
                                        <th>Ngày Viết</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($danhSachDanhGia as $dg): ?>
                                        <tr>
                                            <td><?= (int)$dg['id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($dg['ho_ten'] ?? 'N/A') ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($dg['email'] ?? '') ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($dg['ten_san_pham'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?= $i <= (int)$dg['so_sao'] ? '-fill text-warning' : '' ?>"></i>
                                                <?php endfor; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $noiDung = $dg['noi_dung'] ?? '';
                                                echo htmlspecialchars(mb_substr($noiDung, 0, 50)) . (mb_strlen($noiDung) > 50 ? '...' : '');
                                                ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($dg['ngay_viet'])) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="/admin/danh-gia/chi-tiet?id=<?= (int)$dg['id'] ?>" 
                                                       class="btn btn-outline-info" title="Xem chi tiết">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <form method="POST" action="/admin/danh-gia/xoa?id=<?= (int)$dg['id'] ?>" 
                                                          style="display: inline;" 
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?');">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-3">
                            <ul class="pagination justify-content-center">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>&keyword=<?= urlencode($keyword) ?>&san_pham_id=<?= $sanPhamId ?? '' ?>&so_sao=<?= $soSao ?? '' ?>">
                                            Trước
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>&san_pham_id=<?= $sanPhamId ?? '' ?>&so_sao=<?= $soSao ?? '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>&keyword=<?= urlencode($keyword) ?>&san_pham_id=<?= $sanPhamId ?? '' ?>&so_sao=<?= $soSao ?? '' ?>">
                                            Sau
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <p class="text-center text-muted">
                            Hiển thị <?= ($currentPage - 1) * 20 + 1 ?> - <?= min($currentPage * 20, $totalReviews) ?> 
                            trong tổng số <?= $totalReviews ?> đánh giá
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
