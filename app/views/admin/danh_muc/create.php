<?php
class DanhMucCreateViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$old = $old ?? [];
$errors = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4 py-lg-5" style="max-width: 920px;">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h1 class="h4 mb-1">Thêm danh mục mới</h1>
                <p class="text-secondary mb-0">Có thể để trống slug để hệ thống tự tạo từ tên danh mục.</p>
            </div>

            <div class="card-body">
                <form class="row g-3" method="POST" action="/admin/danh-muc/them">
                    <div class="col-12">
                        <label class="form-label" for="ten">Tên danh mục *</label>
                        <input class="form-control" id="ten" name="ten" type="text" value="<?= DanhMucCreateViewHelper::e($old['ten'] ?? '') ?>" required>
                        <?php if (!empty($errors['ten'])): ?><div class="text-danger small mt-1"><?= DanhMucCreateViewHelper::e($errors['ten']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="slug">Slug</label>
                        <input class="form-control" id="slug" name="slug" type="text" value="<?= DanhMucCreateViewHelper::e($old['slug'] ?? '') ?>" placeholder="vi-du: dien-thoai">
                        <?php if (!empty($errors['slug'])): ?><div class="text-danger small mt-1"><?= DanhMucCreateViewHelper::e($errors['slug']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="icon_url">Icon URL</label>
                        <input class="form-control" id="icon_url" name="icon_url" type="text" value="<?= DanhMucCreateViewHelper::e($old['icon_url'] ?? '') ?>" placeholder="https://...">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="danh_muc_cha_id">Danh mục cha</label>
                        <select class="form-select" id="danh_muc_cha_id" name="danh_muc_cha_id">
                            <option value="">-- Không có --</option>
                            <?php foreach ($danhMucChaOptions as $item): ?>
                                <option value="<?= (int)$item['id'] ?>" <?= (string)($old['danh_muc_cha_id'] ?? '') === (string)$item['id'] ? 'selected' : '' ?>>
                                    <?= DanhMucCreateViewHelper::e($item['ten']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['danh_muc_cha_id'])): ?><div class="text-danger small mt-1"><?= DanhMucCreateViewHelper::e($errors['danh_muc_cha_id']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label" for="thu_tu">Thứ tự hiển thị</label>
                        <input class="form-control" id="thu_tu" name="thu_tu" type="number" min="0" value="<?= DanhMucCreateViewHelper::e($old['thu_tu'] ?? '0') ?>">
                        <?php if (!empty($errors['thu_tu'])): ?><div class="text-danger small mt-1"><?= DanhMucCreateViewHelper::e($errors['thu_tu']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label" for="trang_thai">Trạng thái</label>
                        <select class="form-select" id="trang_thai" name="trang_thai">
                            <option value="1" <?= (string)($old['trang_thai'] ?? '1') === '1' ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="0" <?= (string)($old['trang_thai'] ?? '1') === '0' ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2 pt-1">
                        <button class="btn btn-primary" type="submit">Thêm danh mục</button>
                        <a class="btn btn-outline-secondary" href="/admin/danh-muc">Quay lại danh sách</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>