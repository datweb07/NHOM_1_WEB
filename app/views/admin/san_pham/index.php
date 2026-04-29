<?php 
$keyword = $keyword ?? '';
$danhMucId = $danhMucId ?? 0;
$giaMin = $giaMin ?? null;
$giaMax = $giaMax ?? null;
$danhSachDanhMuc = $danhSachDanhMuc ?? [];
$danhSachSanPham = $danhSachSanPham ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalProducts = $totalProducts ?? 0;
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

require_once dirname(__DIR__) . '/layouts/header.php'; 
?>

<?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Sản Phẩm', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>

    <div class="app-content">
        <div class="container-fluid">
            <!-- Action Bar -->
            <div class="d-flex justify-content-end mb-3">
                <a href="/admin/san-pham/them" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm Sản Phẩm
                </a>
            </div>

            <div class="card">
                <div class="card-body">

                    <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                                $messages = [
                                    'created' => 'Thêm sản phẩm thành công!',
                                    'updated' => 'Cập nhật sản phẩm thành công!',
                                    'deleted' => 'Ngừng bán sản phẩm thành công!',
                                    'restored' => 'Mở bán sản phẩm thành công!',
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
                                    'not_found' => 'Không tìm thấy sản phẩm!',
                                ];
                                echo htmlspecialchars($messages[$error] ?? $error);
                                ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Search and Filter Form -->
                    <form method="GET" action="/admin/san-pham" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="keyword" class="form-label">Tìm Kiếm</label>
                                <input type="text" class="form-control" id="keyword" name="keyword"
                                    value="<?= htmlspecialchars($keyword ?? '') ?>"
                                    placeholder="Tên sản phẩm, ID, hãng...">
                            </div>
                            <div class="col-md-3">
                                <label for="danh_muc_id" class="form-label">Danh Mục</label>
                                <select class="form-select" id="danh_muc_id" name="danh_muc_id">
                                    <option value="">-- Tất cả --</option>
                                    <?php foreach ($danhSachDanhMuc ?? [] as $dm): ?>
                                    <option value="<?= (int)$dm['id'] ?>"
                                        <?= (int)($danhMucId ?? 0) === (int)$dm['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dm['ten']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="gia_min" class="form-label">Giá Từ</label>
                                <input type="number" class="form-control" id="gia_min" name="gia_min"
                                    value="<?= ($giaMin ?? null) !== null ? (int)$giaMin : '' ?>" placeholder="0">
                            </div>
                            <div class="col-md-2">
                                <label for="gia_max" class="form-label">Giá Đến</label>
                                <input type="number" class="form-control" id="gia_max" name="gia_max"
                                    value="<?= ($giaMax ?? null) !== null ? (int)$giaMax : '' ?>"
                                    placeholder="100000000">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- Product Table -->
                    <div class="table-responsive">
                        <?php if (empty($danhSachSanPham ?? [])): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Không có sản phẩm nào</p>
                        </div>
                        <?php else: ?>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Sản Phẩm</th>
                                    <th>Danh Mục</th>
                                    <th>Hãng</th>
                                    <th>Giá Hiển Thị</th>
                                    <th>Trạng Thái</th>
                                    <th>Nổi Bật</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($danhSachSanPham as $sp): ?>
                                <tr>
                                    <td><?= (int)$sp['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($sp['ten_san_pham']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($sp['slug']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($sp['ten_danh_muc'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($sp['hang_san_xuat'] ?? 'N/A') ?></td>
                                    <td><?= number_format((float)($sp['gia_hien_thi'] ?? 0), 0, ',', '.') ?> ₫</td>
                                    <td>
                                        <?php
                                                    $statusBadges = [
                                                        'CON_BAN' => '<span class="badge bg-success">Còn bán</span>',
                                                        'NGUNG_BAN' => '<span class="badge bg-danger">Ngừng bán</span>',
                                                        'SAP_RA_MAT' => '<span class="badge bg-info">Sắp ra mắt</span>',
                                                        'HET_HANG' => '<span class="badge bg-warning">Hết hàng</span>',
                                                    ];
                                                    echo $statusBadges[$sp['trang_thai']] ?? '<span class="badge bg-secondary">Không xác định</span>';
                                                    ?>
                                    </td>
                                    <td>
                                        <?php if ((int)$sp['noi_bat'] === 1): ?>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <?php else: ?>
                                        <i class="bi bi-star text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/admin/san-pham/sua?id=<?= (int)$sp['id'] ?>"
                                                class="btn btn-outline-primary" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="/admin/san-pham/phien-ban?id=<?= (int)$sp['id'] ?>"
                                                class="btn btn-outline-info" title="Phiên bản">
                                                <i class="bi bi-box"></i>
                                            </a>
                                            <a href="/admin/san-pham/hinh-anh?id=<?= (int)$sp['id'] ?>"
                                                class="btn btn-outline-secondary" title="Hình ảnh">
                                                <i class="bi bi-image"></i>
                                            </a>
                                            <a href="/admin/san-pham/thong-so?id=<?= (int)$sp['id'] ?>"
                                                class="btn btn-outline-warning" title="Thông số">
                                                <i class="bi bi-list-ul"></i>
                                            </a>
                                            <?php if ($sp['trang_thai'] === 'NGUNG_BAN'): ?>
                                            <a href="/admin/san-pham/mo-ban?id=<?= (int)$sp['id'] ?>"
                                                class="btn btn-outline-success" title="Mở bán"
                                                onclick="return confirm('Bạn có chắc muốn mở bán sản phẩm này?')">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </a>
                                            <?php else: ?>
                                            <a href="/admin/san-pham/xoa?id=<?= (int)$sp['id'] ?>"
                                                class="btn btn-outline-danger" title="Ngừng bán"
                                                onclick="return confirm('Bạn có chắc muốn ngừng bán sản phẩm này?')">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                    <!-- Pagination -->
                    <?php if (($totalPages ?? 1) > 1): ?>
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <?php if (($currentPage ?? 1) > 1): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=<?= $currentPage - 1 ?>&keyword=<?= urlencode($keyword ?? '') ?>&danh_muc_id=<?= $danhMucId ?? '' ?>&gia_min=<?= $giaMin ?? '' ?>&gia_max=<?= $giaMax ?? '' ?>">
                                    Trước
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                            <li class="page-item <?= $i === ($currentPage ?? 1) ? 'active' : '' ?>">
                                <a class="page-link"
                                    href="?page=<?= $i ?>&keyword=<?= urlencode($keyword ?? '') ?>&danh_muc_id=<?= $danhMucId ?? '' ?>&gia_min=<?= $giaMin ?? '' ?>&gia_max=<?= $giaMax ?? '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <?php if (($currentPage ?? 1) < ($totalPages ?? 1)): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=<?= $currentPage + 1 ?>&keyword=<?= urlencode($keyword ?? '') ?>&danh_muc_id=<?= $danhMucId ?? '' ?>&gia_min=<?= $giaMin ?? '' ?>&gia_max=<?= $giaMax ?? '' ?>">
                                    Sau
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <p class="text-center text-muted">
                        Hiển thị <?= (($currentPage ?? 1) - 1) * 20 + 1 ?> -
                        <?= min(($currentPage ?? 1) * 20, $totalProducts ?? 0) ?>
                        trong tổng số <?= $totalProducts ?? 0 ?> sản phẩm
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>