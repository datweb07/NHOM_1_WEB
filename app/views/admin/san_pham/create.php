<?php
class SanPhamCreateViewHelper
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
    <title>Thêm sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4 py-lg-5" style="max-width: 920px;">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h1 class="h4 mb-1">Thêm sản phẩm mới</h1>
                <p class="text-secondary mb-0">Có thể để trống slug để hệ thống tự tạo từ tên sản phẩm.</p>
            </div>

            <div class="card-body">
                <form class="row g-3" method="POST" action="/admin/san-pham/them">
                    <div class="col-12">
                        <label class="form-label" for="ten_san_pham">Tên sản phẩm *</label>
                        <input class="form-control" id="ten_san_pham" name="ten_san_pham" type="text" value="<?= SanPhamCreateViewHelper::e($old['ten_san_pham'] ?? '') ?>" required>
                        <?php if (!empty($errors['ten_san_pham'])): ?><div class="text-danger small mt-1"><?= SanPhamCreateViewHelper::e($errors['ten_san_pham']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="slug">Slug</label>
                        <input class="form-control" id="slug" name="slug" type="text" value="<?= SanPhamCreateViewHelper::e($old['slug'] ?? '') ?>" placeholder="vi-du: iphone-16-pro-max">
                        <?php if (!empty($errors['slug'])): ?><div class="text-danger small mt-1"><?= SanPhamCreateViewHelper::e($errors['slug']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="hang_san_xuat">Hãng sản xuất</label>
                        <input class="form-control" id="hang_san_xuat" name="hang_san_xuat" type="text" value="<?= SanPhamCreateViewHelper::e($old['hang_san_xuat'] ?? '') ?>" placeholder="Apple, Samsung, Xiaomi...">
                        <?php if (!empty($errors['hang_san_xuat'])): ?><div class="text-danger small mt-1"><?= SanPhamCreateViewHelper::e($errors['hang_san_xuat']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="mo_ta">Mô tả</label>
                        <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4"><?= SanPhamCreateViewHelper::e($old['mo_ta'] ?? '') ?></textarea>
                        <?php if (!empty($errors['mo_ta'])): ?><div class="text-danger small mt-1"><?= SanPhamCreateViewHelper::e($errors['mo_ta']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="danh_muc_id">Danh mục</label>
                        <select class="form-select" id="danh_muc_id" name="danh_muc_id">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($danhSachDanhMuc as $danhMuc): ?>
                                <option value="<?= (int)$danhMuc['id'] ?>" <?= (string)($old['danh_muc_id'] ?? '') === (string)$danhMuc['id'] ? 'selected' : '' ?>>
                                    <?= SanPhamCreateViewHelper::e($danhMuc['ten']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['danh_muc_id'])): ?><div class="text-danger small mt-1"><?= SanPhamCreateViewHelper::e($errors['danh_muc_id']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="trang_thai">Trạng thái</label>
                        <select class="form-select" id="trang_thai" name="trang_thai">
                            <option value="CON_BAN" <?= (string)($old['trang_thai'] ?? 'CON_BAN') === 'CON_BAN' ? 'selected' : '' ?>>Còn bán</option>
                            <option value="NGUNG_BAN" <?= (string)($old['trang_thai'] ?? 'CON_BAN') === 'NGUNG_BAN' ? 'selected' : '' ?>>Ngừng bán</option>
                            <option value="SAP_RA_MAT" <?= (string)($old['trang_thai'] ?? 'CON_BAN') === 'SAP_RA_MAT' ? 'selected' : '' ?>>Sắp ra mắt</option>
                            <option value="HET_HANG" <?= (string)($old['trang_thai'] ?? 'CON_BAN') === 'HET_HANG' ? 'selected' : '' ?>>Hết hàng</option>
                        </select>
                        <?php if (!empty($errors['trang_thai'])): ?><div class="text-danger small mt-1"><?= SanPhamCreateViewHelper::e($errors['trang_thai']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="noi_bat" name="noi_bat" value="1" <?= (string)($old['noi_bat'] ?? '0') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="noi_bat">
                                Sản phẩm nổi bật (hiển thị trên trang chủ)
                            </label>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 pt-1">
                        <button class="btn btn-primary" type="submit">Thêm sản phẩm</button>
                        <a class="btn btn-outline-secondary" href="/admin/san-pham">Quay lại danh sách</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
