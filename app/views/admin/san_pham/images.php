<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hình ảnh - <?= htmlspecialchars($sanPham['ten_san_pham'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .image-card {
            position: relative;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .image-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
        .image-actions {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; ?>
                
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Quản lý Hình ảnh: <?= htmlspecialchars($sanPham['ten_san_pham'] ?? '') ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/admin/san-pham" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
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
                        <form method="POST" action="/admin/san-pham/upload-anh?id=<?= $sanPham['id'] ?>" enctype="multipart/form-data">
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
                                <i class="fas fa-upload"></i> Tải lên
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
                                <i class="fas fa-info-circle"></i> Chưa có ảnh nào. Vui lòng tải ảnh lên.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($images as $image): ?>
                                    <div class="col-md-3">
                                        <div class="image-card <?= $image['la_anh_chinh'] ? 'main-image' : '' ?>">
                                            <?php if ($image['la_anh_chinh']): ?>
                                                <span class="badge bg-success image-badge">
                                                    <i class="fas fa-star"></i> Ảnh chính
                                                </span>
                                            <?php endif; ?>
                                            
                                            <img src="<?= htmlspecialchars($image['url_anh']) ?>" 
                                                 alt="<?= htmlspecialchars($image['alt_text'] ?? '') ?>"
                                                 class="img-fluid">
                                            
                                            <div class="mt-2">
                                                <?php if ($image['alt_text']): ?>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-tag"></i> <?= htmlspecialchars($image['alt_text']) ?>
                                                    </small>
                                                <?php endif; ?>
                                                <?php if ($image['phien_ban_id']): ?>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-box"></i> Phiên bản: 
                                                        <?php
                                                        $variant = array_filter($variants, fn($v) => $v['id'] == $image['phien_ban_id']);
                                                        $variant = reset($variant);
                                                        echo htmlspecialchars($variant['sku'] ?? 'N/A');
                                                        ?>
                                                    </small>
                                                <?php endif; ?>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-sort"></i> Thứ tự: <?= $image['thu_tu'] ?>
                                                </small>
                                            </div>
                                            
                                            <div class="image-actions d-flex gap-2">
                                                <?php if (!$image['la_anh_chinh']): ?>
                                                    <a href="/admin/san-pham/dat-anh-chinh?id=<?= $image['id'] ?>" 
                                                       class="btn btn-sm btn-success flex-fill"
                                                       onclick="return confirm('Đặt làm ảnh chính?')">
                                                        <i class="fas fa-star"></i> Đặt chính
                                                    </a>
                                                <?php endif; ?>
                                                <a href="/admin/san-pham/xoa-anh?id=<?= $image['id'] ?>" 
                                                   class="btn btn-sm btn-danger <?= $image['la_anh_chinh'] ? 'flex-fill' : '' ?>"
                                                   onclick="return confirm('Bạn có chắc muốn xóa ảnh này?')">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
