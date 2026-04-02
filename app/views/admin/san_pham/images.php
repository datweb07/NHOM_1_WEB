<?php
$sanPhamId = (int)($sanPham['id'] ?? 0);

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<style>
    .image-card {
        position: relative;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 20px;
    }
    .image-card.main-image {
        border-color: #198754;
        background-color: #f8fff9;
    }
    .image-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 4px;
    }
    .image-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 10;
    }
</style>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Sản Phẩm', 'url' => '/admin/san-pham'],
        ['label' => 'Hình ảnh ID = ' . $sanPhamId, 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Quản lý Hình ảnh: <?= htmlspecialchars($sanPham['ten_san_pham'] ?? '') ?></h4>
                <div class="btn-group btn-group-sm">
                    <a href="/admin/san-pham/sua?id=<?= $sanPhamId ?>" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Sửa SP
                    </a>
                    <a href="/admin/san-pham/phien-ban?id=<?= $sanPhamId ?>" class="btn btn-outline-info">
                        <i class="bi bi-box"></i> Phiên bản
                    </a>
                    <a href="/admin/san-pham/thong-so?id=<?= $sanPhamId ?>" class="btn btn-outline-warning">
                        <i class="bi bi-list-ul"></i> Thông số
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    $messages = [
                        'image_uploaded' => 'Tải ảnh lên thành công!',
                        'image_deleted' => 'Xóa ảnh thành công!',
                        'main_image_set' => 'Đã đặt ảnh chính!'
                    ];
                    echo $messages[$_GET['success']] ?? 'Thao tác thành công!';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    $messages = [
                        'no_file' => 'Vui lòng chọn file ảnh!',
                        'upload_failed' => $_SESSION['image_error'] ?? 'Tải ảnh lên thất bại!',
                        'not_found' => 'Không tìm thấy ảnh!'
                    ];
                    echo $messages[$_GET['error']] ?? 'Có lỗi xảy ra!';
                    unset($_SESSION['image_error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Upload Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tải ảnh mới</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/san-pham/upload-anh?id=<?= $sanPhamId ?>" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Chọn ảnh <span class="text-danger">*</span></label>
                                    <input type="file" name="image" class="form-control" accept="image/*" required>
                                    <small class="text-muted">Định dạng: JPG, PNG, GIF, WEBP. Tối đa 5MB</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Mô tả ảnh (Alt text)</label>
                                    <input type="text" name="alt_text" class="form-control" placeholder="Mô tả ngắn gọn">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Thứ tự</label>
                                    <input type="number" name="thu_tu" class="form-control" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Phiên bản</label>
                                    <select name="phien_ban_id" class="form-select">
                                        <option value="">Chung</option>
                                        <?php foreach ($variants as $variant): ?>
                                            <option value="<?= $variant['id'] ?>"><?= htmlspecialchars($variant['sku']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="mb-3">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="la_anh_chinh" class="form-check-input" id="la_anh_chinh">
                                        <label class="form-check-label" for="la_anh_chinh">Ảnh chính</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Tải lên
                        </button>
                    </form>
                </div>
            </div>

            <!-- Images Gallery -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thư viện ảnh (<?= count($images) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($images)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Chưa có ảnh nào. Vui lòng tải ảnh lên.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($images as $image): ?>
                                <div class="col-md-3">
                                    <div class="image-card <?= $image['la_anh_chinh'] ? 'main-image' : '' ?>">
                                        <?php if ($image['la_anh_chinh']): ?>
                                            <span class="badge bg-success image-badge">
                                                <i class="bi bi-star-fill"></i> Ảnh chính
                                            </span>
                                        <?php endif; ?>
                                        
                                        <img src="<?= htmlspecialchars($image['url_anh']) ?>" 
                                             alt="<?= htmlspecialchars($image['alt_text'] ?? '') ?>"
                                             class="img-fluid">
                                        
                                        <div class="mt-2">
                                            <?php if ($image['alt_text']): ?>
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-tag"></i> <?= htmlspecialchars($image['alt_text']) ?>
                                                </small>
                                            <?php endif; ?>
                                            <?php if ($image['phien_ban_id']): ?>
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-box"></i> Phiên bản: 
                                                    <?php
                                                    $variant = array_filter($variants, fn($v) => $v['id'] == $image['phien_ban_id']);
                                                    $variant = reset($variant);
                                                    echo htmlspecialchars($variant['sku'] ?? 'N/A');
                                                    ?>
                                                </small>
                                            <?php endif; ?>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-sort-down"></i> Thứ tự: <?= $image['thu_tu'] ?>
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex gap-2 mt-2">
                                            <?php if (!$image['la_anh_chinh']): ?>
                                                <a href="/admin/san-pham/dat-anh-chinh?id=<?= $image['id'] ?>" 
                                                   class="btn btn-sm btn-success flex-fill"
                                                   onclick="return confirm('Đặt làm ảnh chính?')">
                                                    <i class="bi bi-star"></i> Đặt chính
                                                </a>
                                            <?php endif; ?>
                                            <a href="/admin/san-pham/xoa-anh?id=<?= $image['id'] ?>" 
                                               class="btn btn-sm btn-danger <?= $image['la_anh_chinh'] ? 'flex-fill' : '' ?>"
                                               onclick="return confirm('Bạn có chắc muốn xóa ảnh này?')">
                                                <i class="bi bi-trash"></i> Xóa
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
