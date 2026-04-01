<?php
class KhuyenMaiEditViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public static function formatDateForInput($date): string
    {
        if (empty($date)) return '';
        return date('Y-m-d\TH:i', strtotime($date));
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
                ['url' => '/admin/khuyen-mai', 'label' => 'Khuyến Mãi'],
                ['url' => '', 'label' => 'Chỉnh Sửa']
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
                            <h3 class="card-title">Sửa khuyến mãi</h3>
                        </div>
                        <div class="card-body">
                <form method="POST" action="/admin/khuyen-mai/sua?id=<?= (int)$khuyenMai['id'] ?>">
                    <div class="mb-3">
                        <label for="ten_chuong_trinh" class="form-label">Tên chương trình <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control <?= isset($errors['ten_chuong_trinh']) ? 'is-invalid' : '' ?>"
                            id="ten_chuong_trinh"
                            name="ten_chuong_trinh"
                            value="<?= KhuyenMaiEditViewHelper::e($old['ten_chuong_trinh'] ?? $khuyenMai['ten_chuong_trinh']) ?>"
                            required>
                        <?php if (isset($errors['ten_chuong_trinh'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= KhuyenMaiEditViewHelper::e($errors['ten_chuong_trinh']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="loai_giam" class="form-label">Loại giảm <span class="text-danger">*</span></label>
                        <select
                            class="form-select <?= isset($errors['loai_giam']) ? 'is-invalid' : '' ?>"
                            id="loai_giam"
                            name="loai_giam"
                            required>
                            <option value="">-- Chọn loại giảm --</option>
                            <option value="PHAN_TRAM" <?= ($old['loai_giam'] ?? $khuyenMai['loai_giam']) === 'PHAN_TRAM' ? 'selected' : '' ?>>Phần trăm (%)</option>
                            <option value="SO_TIEN" <?= ($old['loai_giam'] ?? $khuyenMai['loai_giam']) === 'SO_TIEN' ? 'selected' : '' ?>>Số tiền (₫)</option>
                        </select>
                        <?php if (isset($errors['loai_giam'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= KhuyenMaiEditViewHelper::e($errors['loai_giam']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gia_tri_giam" class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                            <input
                                type="number"
                                step="0.01"
                                class="form-control <?= isset($errors['gia_tri_giam']) ? 'is-invalid' : '' ?>"
                                id="gia_tri_giam"
                                name="gia_tri_giam"
                                value="<?= KhuyenMaiEditViewHelper::e($old['gia_tri_giam'] ?? $khuyenMai['gia_tri_giam']) ?>"
                                required>
                            <?php if (isset($errors['gia_tri_giam'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= KhuyenMaiEditViewHelper::e($errors['gia_tri_giam']) ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">Nếu chọn phần trăm, nhập từ 0-100</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="giam_toi_da" class="form-label">Giảm tối đa (₫)</label>
                            <input
                                type="number"
                                step="0.01"
                                class="form-control <?= isset($errors['giam_toi_da']) ? 'is-invalid' : '' ?>"
                                id="giam_toi_da"
                                name="giam_toi_da"
                                value="<?= KhuyenMaiEditViewHelper::e($old['giam_toi_da'] ?? $khuyenMai['giam_toi_da'] ?? '') ?>">
                            <?php if (isset($errors['giam_toi_da'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= KhuyenMaiEditViewHelper::e($errors['giam_toi_da']) ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">Bắt buộc nếu loại giảm là phần trăm</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input
                                type="datetime-local"
                                class="form-control <?= isset($errors['ngay_bat_dau']) ? 'is-invalid' : '' ?>"
                                id="ngay_bat_dau"
                                name="ngay_bat_dau"
                                value="<?= KhuyenMaiEditViewHelper::e($old['ngay_bat_dau'] ?? KhuyenMaiEditViewHelper::formatDateForInput($khuyenMai['ngay_bat_dau'])) ?>"
                                required>
                            <?php if (isset($errors['ngay_bat_dau'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= KhuyenMaiEditViewHelper::e($errors['ngay_bat_dau']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input
                                type="datetime-local"
                                class="form-control <?= isset($errors['ngay_ket_thuc']) ? 'is-invalid' : '' ?>"
                                id="ngay_ket_thuc"
                                name="ngay_ket_thuc"
                                value="<?= KhuyenMaiEditViewHelper::e($old['ngay_ket_thuc'] ?? KhuyenMaiEditViewHelper::formatDateForInput($khuyenMai['ngay_ket_thuc'])) ?>"
                                required>
                            <?php if (isset($errors['ngay_ket_thuc'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= KhuyenMaiEditViewHelper::e($errors['ngay_ket_thuc']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Cập nhật
                        </button>
                        <a href="/admin/khuyen-mai" class="btn btn-secondary">
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
