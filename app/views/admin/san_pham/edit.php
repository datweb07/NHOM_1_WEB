<?php
class SanPhamEditViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$old = $old ?? [];
$errors = $errors ?? [];
$sanPham = $sanPham ?? [];
$sanPhamId = (int)($sanPham['id'] ?? 0);

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Sản Phẩm', 'url' => '/admin/san-pham'],
        ['label' => 'Sửa ID = ' . $sanPhamId, 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>

    <div class="app-content">
        <div class="container-fluid">
            <div class="mb-3">
                <div class="btn-group btn-group-sm">
                    <a href="/admin/san-pham/phien-ban?id=<?= $sanPhamId ?>" class="btn btn-outline-info">
                        <i class="bi bi-box"></i> Phiên bản
                    </a>
                    <a href="/admin/san-pham/hinh-anh?id=<?= $sanPhamId ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-image"></i> Hình ảnh
                    </a>
                    <a href="/admin/san-pham/thong-so?id=<?= $sanPhamId ?>" class="btn btn-outline-warning">
                        <i class="bi bi-list-ul"></i> Thông số
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="POST" action="/admin/san-pham/sua?id=<?= $sanPhamId ?>">
                        <div class="col-12">
                            <label class="form-label" for="ten_san_pham">Tên sản phẩm *</label>
                            <input class="form-control" id="ten_san_pham" name="ten_san_pham" type="text"
                                value="<?= SanPhamEditViewHelper::e($old['ten_san_pham'] ?? $sanPham['ten_san_pham'] ?? '') ?>"
                                required>
                            <?php if (!empty($errors['ten_san_pham'])): ?><div class="text-danger small mt-1">
                                <?= SanPhamEditViewHelper::e($errors['ten_san_pham']) ?></div><?php endif; ?>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="slug">Slug</label>
                            <input class="form-control" id="slug" name="slug" type="text"
                                value="<?= SanPhamEditViewHelper::e($old['slug'] ?? $sanPham['slug'] ?? '') ?>"
                                placeholder="vi-du: iphone-16-pro-max">
                            <?php if (!empty($errors['slug'])): ?><div class="text-danger small mt-1">
                                <?= SanPhamEditViewHelper::e($errors['slug']) ?></div><?php endif; ?>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="hang_san_xuat">Hãng sản xuất</label>
                            <input class="form-control" id="hang_san_xuat" name="hang_san_xuat" type="text"
                                value="<?= SanPhamEditViewHelper::e($old['hang_san_xuat'] ?? $sanPham['hang_san_xuat'] ?? '') ?>"
                                placeholder="Apple, Samsung, Xiaomi...">
                            <?php if (!empty($errors['hang_san_xuat'])): ?><div class="text-danger small mt-1">
                                <?= SanPhamEditViewHelper::e($errors['hang_san_xuat']) ?></div><?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="mo_ta">Mô tả</label>
                            <textarea class="form-control" id="mo_ta" name="mo_ta"
                                rows="4"><?= SanPhamEditViewHelper::e($old['mo_ta'] ?? $sanPham['mo_ta'] ?? '') ?></textarea>
                            <?php if (!empty($errors['mo_ta'])): ?><div class="text-danger small mt-1">
                                <?= SanPhamEditViewHelper::e($errors['mo_ta']) ?></div><?php endif; ?>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="danh_muc_id">Danh mục</label>
                            <select class="form-select" id="danh_muc_id" name="danh_muc_id">
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($danhSachDanhMuc as $danhMuc): ?>
                                <?php 
                                $currentValue = $old['danh_muc_id'] ?? $sanPham['danh_muc_id'] ?? '';
                                $isSelected = (string)$currentValue === (string)$danhMuc['id'];
                                ?>
                                <option value="<?= (int)$danhMuc['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                    <?= SanPhamEditViewHelper::e($danhMuc['ten']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['danh_muc_id'])): ?><div class="text-danger small mt-1">
                                <?= SanPhamEditViewHelper::e($errors['danh_muc_id']) ?></div><?php endif; ?>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="trang_thai">Trạng thái</label>
                            <select class="form-select" id="trang_thai" name="trang_thai">
                                <?php 
                            $currentStatus = $old['trang_thai'] ?? $sanPham['trang_thai'] ?? 'CON_BAN';
                            ?>
                                <option value="CON_BAN" <?= $currentStatus === 'CON_BAN' ? 'selected' : '' ?>>Còn bán
                                </option>
                                <option value="NGUNG_BAN" <?= $currentStatus === 'NGUNG_BAN' ? 'selected' : '' ?>>Ngừng
                                    bán</option>
                                <option value="SAP_RA_MAT" <?= $currentStatus === 'SAP_RA_MAT' ? 'selected' : '' ?>>Sắp
                                    ra mắt</option>
                                <option value="HET_HANG" <?= $currentStatus === 'HET_HANG' ? 'selected' : '' ?>>Hết hàng
                                </option>
                            </select>
                            <?php if (!empty($errors['trang_thai'])): ?><div class="text-danger small mt-1">
                                <?= SanPhamEditViewHelper::e($errors['trang_thai']) ?></div><?php endif; ?>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <?php 
                            $noiBat = $old['noi_bat'] ?? $sanPham['noi_bat'] ?? '0';
                            $isChecked = (string)$noiBat === '1';
                            ?>
                                <input class="form-check-input" type="checkbox" id="noi_bat" name="noi_bat" value="1"
                                    <?= $isChecked ? 'checked' : '' ?>>
                                <label class="form-check-label" for="noi_bat">
                                    Sản phẩm nổi bật (hiển thị trên trang chủ)
                                </label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2 pt-1">
                            <button class="btn btn-primary" type="submit">Cập nhật sản phẩm</button>
                            <a class="btn btn-outline-secondary" href="/admin/san-pham">Quay lại danh sách</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>