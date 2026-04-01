<?php
class BannerCreateViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <?php
            $breadcrumbs = [
                ['url' => '/admin/dashboard', 'label' => 'Dashboard'],
                ['url' => '/admin/banner', 'label' => 'Banner Quảng Cáo'],
                ['url' => '', 'label' => 'Thêm Mới']
            ];
            require_once dirname(__DIR__) . '/layouts/breadcrumb.php';
            ?>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Thêm banner mới</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/admin/banner/them">
                                <div class="mb-3">
                                    <label for="tieu_de" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control <?= isset($errors['tieu_de']) ? 'is-invalid' : '' ?>"
                                        id="tieu_de"
                                        name="tieu_de"
                                        value="<?= BannerCreateViewHelper::e($old['tieu_de'] ?? '') ?>"
                                        required>
                                    <?php if (isset($errors['tieu_de'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= BannerCreateViewHelper::e($errors['tieu_de']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="hinh_anh_desktop" class="form-label">Link hình ảnh Desktop <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            class="form-control <?= isset($errors['hinh_anh_desktop']) ? 'is-invalid' : '' ?>"
                                            id="hinh_anh_desktop"
                                            name="hinh_anh_desktop"
                                            value="<?= BannerCreateViewHelper::e($old['hinh_anh_desktop'] ?? '') ?>"
                                            placeholder="https://example.com/banner-desktop.jpg"
                                            required>
                                        <?php if (isset($errors['hinh_anh_desktop'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= BannerCreateViewHelper::e($errors['hinh_anh_desktop']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="hinh_anh_mobile" class="form-label">Link hình ảnh Mobile</label>
                                        <input
                                            type="text"
                                            class="form-control <?= isset($errors['hinh_anh_mobile']) ? 'is-invalid' : '' ?>"
                                            id="hinh_anh_mobile"
                                            name="hinh_anh_mobile"
                                            value="<?= BannerCreateViewHelper::e($old['hinh_anh_mobile'] ?? '') ?>"
                                            placeholder="https://example.com/banner-mobile.jpg">
                                        <?php if (isset($errors['hinh_anh_mobile'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= BannerCreateViewHelper::e($errors['hinh_anh_mobile']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <small class="text-muted">Để trống nếu dùng chung ảnh desktop</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="link_dich" class="form-label">Link đích <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control <?= isset($errors['link_dich']) ? 'is-invalid' : '' ?>"
                                        id="link_dich"
                                        name="link_dich"
                                        value="<?= BannerCreateViewHelper::e($old['link_dich'] ?? '') ?>"
                                        placeholder="/san-pham hoặc https://example.com"
                                        required>
                                    <?php if (isset($errors['link_dich'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= BannerCreateViewHelper::e($errors['link_dich']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="vi_tri" class="form-label">Vị trí hiển thị <span class="text-danger">*</span></label>
                                        <select
                                            class="form-select <?= isset($errors['vi_tri']) ? 'is-invalid' : '' ?>"
                                            id="vi_tri"
                                            name="vi_tri"
                                            required>
                                            <option value="">-- Chọn vị trí --</option>
                                            <option value="HOME_HERO" <?= ($old['vi_tri'] ?? '') === 'HOME_HERO' ? 'selected' : '' ?>>Trang chủ - Hero</option>
                                            <option value="HOME_SIDE" <?= ($old['vi_tri'] ?? '') === 'HOME_SIDE' ? 'selected' : '' ?>>Trang chủ - Sidebar</option>
                                            <option value="FLOATING_BOTTOM_LEFT" <?= ($old['vi_tri'] ?? '') === 'FLOATING_BOTTOM_LEFT' ? 'selected' : '' ?>>Nổi góc dưới trái</option>
                                            <option value="POPUP" <?= ($old['vi_tri'] ?? '') === 'POPUP' ? 'selected' : '' ?>>Popup</option>
                                            <option value="CATEGORY_TOP" <?= ($old['vi_tri'] ?? '') === 'CATEGORY_TOP' ? 'selected' : '' ?>>Đầu trang danh mục</option>
                                        </select>
                                        <?php if (isset($errors['vi_tri'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= BannerCreateViewHelper::e($errors['vi_tri']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="thu_tu" class="form-label">Thứ tự</label>
                                        <input
                                            type="number"
                                            class="form-control <?= isset($errors['thu_tu']) ? 'is-invalid' : '' ?>"
                                            id="thu_tu"
                                            name="thu_tu"
                                            value="<?= BannerCreateViewHelper::e($old['thu_tu'] ?? '0') ?>">
                                        <?php if (isset($errors['thu_tu'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= BannerCreateViewHelper::e($errors['thu_tu']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <small class="text-muted">Số nhỏ sẽ hiển thị trước</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu</label>
                                        <input
                                            type="datetime-local"
                                            class="form-control <?= isset($errors['ngay_bat_dau']) ? 'is-invalid' : '' ?>"
                                            id="ngay_bat_dau"
                                            name="ngay_bat_dau"
                                            value="<?= BannerCreateViewHelper::e($old['ngay_bat_dau'] ?? '') ?>">
                                        <?php if (isset($errors['ngay_bat_dau'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= BannerCreateViewHelper::e($errors['ngay_bat_dau']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc</label>
                                        <input
                                            type="datetime-local"
                                            class="form-control <?= isset($errors['ngay_ket_thuc']) ? 'is-invalid' : '' ?>"
                                            id="ngay_ket_thuc"
                                            name="ngay_ket_thuc"
                                            value="<?= BannerCreateViewHelper::e($old['ngay_ket_thuc'] ?? '') ?>">
                                        <?php if (isset($errors['ngay_ket_thuc'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= BannerCreateViewHelper::e($errors['ngay_ket_thuc']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="trang_thai"
                                            name="trang_thai"
                                            value="1"
                                            <?= ($old['trang_thai'] ?? 1) == 1 ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="trang_thai">
                                            Hiển thị banner
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Thêm banner
                                    </button>
                                    <a href="/admin/banner" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
