<?php
class SanPhamIndexViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public static function formatPrice($price): string
    {
        return number_format((float)$price, 0, ',', '.') . ' ₫';
    }

    public static function getStatusBadge($status): string
    {
        $badges = [
            'CON_BAN' => '<span class="badge bg-success">Còn bán</span>',
            'NGUNG_BAN' => '<span class="badge bg-danger">Ngừng bán</span>',
            'SAP_RA_MAT' => '<span class="badge bg-info">Sắp ra mắt</span>',
            'HET_HANG' => '<span class="badge bg-warning">Hết hàng</span>',
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">Không xác định</span>';
    }
}

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
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Quản lý sản phẩm</h1>
            <a href="/admin/san-pham/them" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm sản phẩm mới
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                $messages = [
                    'created' => 'Thêm sản phẩm thành công!',
                    'updated' => 'Cập nhật sản phẩm thành công!',
                    'deleted' => 'Ngừng bán sản phẩm thành công!',
                    'restored' => 'Mở bán sản phẩm thành công!',
                ];
                echo SanPhamIndexViewHelper::e($messages[$success] ?? $success);
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
                echo SanPhamIndexViewHelper::e($messages[$error] ?? $error);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="/admin/san-pham" class="row g-3">
                    <div class="col-md-4">
                        <label for="keyword" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="keyword" name="keyword" value="<?= SanPhamIndexViewHelper::e($keyword) ?>" placeholder="Tên sản phẩm, ID, hãng...">
                    </div>
                    <div class="col-md-3">
                        <label for="danh_muc_id" class="form-label">Danh mục</label>
                        <select class="form-select" id="danh_muc_id" name="danh_muc_id">
                            <option value="">-- Tất cả --</option>
                            <?php foreach ($danhSachDanhMuc as $dm): ?>
                                <option value="<?= (int)$dm['id'] ?>" <?= (int)$danhMucId === (int)$dm['id'] ? 'selected' : '' ?>>
                                    <?= SanPhamIndexViewHelper::e($dm['ten']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="gia_min" class="form-label">Giá từ</label>
                        <input type="number" class="form-control" id="gia_min" name="gia_min" value="<?= $giaMin !== null ? (int)$giaMin : '' ?>" placeholder="0">
                    </div>
                    <div class="col-md-2">
                        <label for="gia_max" class="form-label">Giá đến</label>
                        <input type="number" class="form-control" id="gia_max" name="gia_max" value="<?= $giaMax !== null ? (int)$giaMax : '' ?>" placeholder="100000000">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Danh sách sản phẩm (<?= $totalProducts ?> sản phẩm)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($danhSachSanPham)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">Không có sản phẩm nào</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Hãng</th>
                                    <th>Giá hiển thị</th>
                                    <th>Trạng thái</th>
                                    <th>Nổi bật</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($danhSachSanPham as $sp): ?>
                                    <tr>
                                        <td><?= (int)$sp['id'] ?></td>
                                        <td>
                                            <strong><?= SanPhamIndexViewHelper::e($sp['ten_san_pham']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= SanPhamIndexViewHelper::e($sp['slug']) ?></small>
                                        </td>
                                        <td><?= SanPhamIndexViewHelper::e($sp['ten_danh_muc'] ?? 'N/A') ?></td>
                                        <td><?= SanPhamIndexViewHelper::e($sp['hang_san_xuat'] ?? 'N/A') ?></td>
                                        <td><?= SanPhamIndexViewHelper::formatPrice($sp['gia_hien_thi'] ?? 0) ?></td>
                                        <td><?= SanPhamIndexViewHelper::getStatusBadge($sp['trang_thai']) ?></td>
                                        <td>
                                            <?php if ((int)$sp['noi_bat'] === 1): ?>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            <?php else: ?>
                                                <i class="bi bi-star text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="/admin/san-pham/sua?id=<?= (int)$sp['id'] ?>" class="btn btn-outline-primary" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="/admin/san-pham/phien-ban?id=<?= (int)$sp['id'] ?>" class="btn btn-outline-info" title="Phiên bản">
                                                    <i class="bi bi-box"></i>
                                                </a>
                                                <a href="/admin/san-pham/hinh-anh?id=<?= (int)$sp['id'] ?>" class="btn btn-outline-secondary" title="Hình ảnh">
                                                    <i class="bi bi-image"></i>
                                                </a>
                                                <a href="/admin/san-pham/thong-so?id=<?= (int)$sp['id'] ?>" class="btn btn-outline-warning" title="Thông số">
                                                    <i class="bi bi-list-ul"></i>
                                                </a>
                                                <?php if ($sp['trang_thai'] === 'NGUNG_BAN'): ?>
                                                    <a href="/admin/san-pham/mo-ban?id=<?= (int)$sp['id'] ?>" class="btn btn-outline-success" title="Mở bán" onclick="return confirm('Bạn có chắc muốn mở bán sản phẩm này?')">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="/admin/san-pham/xoa?id=<?= (int)$sp['id'] ?>" class="btn btn-outline-danger" title="Ngừng bán" onclick="return confirm('Bạn có chắc muốn ngừng bán sản phẩm này?')">
                                                        <i class="bi bi-x-circle"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="card-footer bg-white">
                    <nav aria-label="Phân trang">
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>&keyword=<?= urlencode($keyword) ?>&danh_muc_id=<?= $danhMucId ?>&gia_min=<?= $giaMin ?>&gia_max=<?= $giaMax ?>">Trước</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <li class="page-item active"><span class="page-link"><?= $i ?></span></li>
                                <?php else: ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>&danh_muc_id=<?= $danhMucId ?>&gia_min=<?= $giaMin ?>&gia_max=<?= $giaMax ?>"><?= $i ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>&keyword=<?= urlencode($keyword) ?>&danh_muc_id=<?= $danhMucId ?>&gia_min=<?= $giaMin ?>&gia_max=<?= $giaMax ?>">Sau</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
