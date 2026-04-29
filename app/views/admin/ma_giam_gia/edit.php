<?php
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <?php
            $breadcrumbs = [
                ['url' => '/admin/dashboard', 'label' => 'Dashboard'],
                ['url' => '/admin/ma-giam-gia', 'label' => 'Mã Giảm Giá'],
                ['url' => '', 'label' => 'Chỉnh Sửa']
            ];
            require_once dirname(__DIR__) . '/layouts/breadcrumb.php';
            ?>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Sửa mã giảm giá: <?= htmlspecialchars($maGiamGia['ma_code']) ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/admin/ma-giam-gia/sua?id=<?= $maGiamGia['id'] ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="ma_code" class="form-label">Mã Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control <?= isset($errors['ma_code']) ? 'is-invalid' : '' ?>"
                                                id="ma_code" name="ma_code"
                                                value="<?= htmlspecialchars($old['ma_code'] ?? $maGiamGia['ma_code']) ?>"
                                                placeholder="VD: SUMMER2024" style="text-transform: uppercase;">
                                            <small class="form-text text-muted">Chỉ chữ in hoa và số, tối đa 50 ký
                                                tự</small>
                                            <?php if (isset($errors['ma_code'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= htmlspecialchars($errors['ma_code']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="loai_giam" class="form-label">Loại Giảm <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-select <?= isset($errors['loai_giam']) ? 'is-invalid' : '' ?>"
                                                id="loai_giam" name="loai_giam">
                                                <option value="">-- Chọn loại giảm --</option>
                                                <option value="PHAN_TRAM"
                                                    <?= ($old['loai_giam'] ?? $maGiamGia['loai_giam']) === 'PHAN_TRAM' ? 'selected' : '' ?>>
                                                    Phần trăm</option>
                                                <option value="SO_TIEN"
                                                    <?= ($old['loai_giam'] ?? $maGiamGia['loai_giam']) === 'SO_TIEN' ? 'selected' : '' ?>>
                                                    Số tiền</option>
                                            </select>
                                            <?php if (isset($errors['loai_giam'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= htmlspecialchars($errors['loai_giam']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gia_tri_giam" class="form-label">Giá Trị Giảm <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01"
                                                class="form-control <?= isset($errors['gia_tri_giam']) ? 'is-invalid' : '' ?>"
                                                id="gia_tri_giam" name="gia_tri_giam"
                                                value="<?= htmlspecialchars($old['gia_tri_giam'] ?? $maGiamGia['gia_tri_giam']) ?>">
                                            <small class="form-text text-muted">Nếu phần trăm: 0-100, nếu số tiền: giá
                                                trị VNĐ</small>
                                            <?php if (isset($errors['gia_tri_giam'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= htmlspecialchars($errors['gia_tri_giam']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="giam_toi_da" class="form-label">Giảm Tối Đa (VNĐ)</label>
                                            <input type="number" step="0.01"
                                                class="form-control <?= isset($errors['giam_toi_da']) ? 'is-invalid' : '' ?>"
                                                id="giam_toi_da" name="giam_toi_da"
                                                value="<?= htmlspecialchars($old['giam_toi_da'] ?? $maGiamGia['giam_toi_da'] ?? '') ?>">
                                            <small class="form-text text-muted">Áp dụng cho loại giảm phần trăm</small>
                                            <?php if (isset($errors['giam_toi_da'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= htmlspecialchars($errors['giam_toi_da']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="don_toi_thieu" class="form-label">Đơn Tối Thiểu (VNĐ)</label>
                                            <input type="number" step="0.01"
                                                class="form-control <?= isset($errors['don_toi_thieu']) ? 'is-invalid' : '' ?>"
                                                id="don_toi_thieu" name="don_toi_thieu"
                                                value="<?= htmlspecialchars($old['don_toi_thieu'] ?? $maGiamGia['don_toi_thieu']) ?>">
                                            <small class="form-text text-muted">Giá trị đơn hàng tối thiểu để áp dụng
                                                mã</small>
                                            <?php if (isset($errors['don_toi_thieu'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= htmlspecialchars($errors['don_toi_thieu']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gioi_han_su_dung" class="form-label">Giới Hạn Sử Dụng</label>
                                            <input type="number"
                                                class="form-control <?= isset($errors['gioi_han_su_dung']) ? 'is-invalid' : '' ?>"
                                                id="gioi_han_su_dung" name="gioi_han_su_dung"
                                                value="<?= htmlspecialchars($old['gioi_han_su_dung'] ?? $maGiamGia['gioi_han_su_dung'] ?? '') ?>">
                                            <small class="form-text text-muted">Để trống nếu không giới hạn. Đã dùng:
                                                <?= $maGiamGia['so_luot_da_dung'] ?></small>
                                            <?php if (isset($errors['gioi_han_su_dung'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= htmlspecialchars($errors['gioi_han_su_dung']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="ngay_bat_dau" class="form-label">Ngày Bắt Đầu <span
                                                    class="text-danger">*</span></label>
                                            <input type="datetime-local"
                                                class="form-control <?= isset($errors['ngay_bat_dau']) ? 'is-invalid' : '' ?>"
                                                id="ngay_bat_dau" name="ngay_bat_dau"
                                                value="<?= htmlspecialchars($old['ngay_bat_dau'] ?? date('Y-m-d\TH:i', strtotime($maGiamGia['ngay_bat_dau']))) ?>">
                                            <?php if (isset($errors['ngay_bat_dau'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= htmlspecialchars($errors['ngay_bat_dau']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="ngay_ket_thuc" class="form-label">Ngày Kết Thúc <span
                                                    class="text-danger">*</span></label>
                                            <input type="datetime-local"
                                                class="form-control <?= isset($errors['ngay_ket_thuc']) ? 'is-invalid' : '' ?>"
                                                id="ngay_ket_thuc" name="ngay_ket_thuc"
                                                value="<?= htmlspecialchars($old['ngay_ket_thuc'] ?? date('Y-m-d\TH:i', strtotime($maGiamGia['ngay_ket_thuc']))) ?>">
                                            <?php if (isset($errors['ngay_ket_thuc'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= htmlspecialchars($errors['ngay_ket_thuc']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="mo_ta" class="form-label">Mô Tả</label>
                                    <textarea class="form-control <?= isset($errors['mo_ta']) ? 'is-invalid' : '' ?>"
                                        id="mo_ta" name="mo_ta"
                                        rows="3"><?= htmlspecialchars($old['mo_ta'] ?? $maGiamGia['mo_ta'] ?? '') ?></textarea>
                                    <?php if (isset($errors['mo_ta'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= htmlspecialchars($errors['mo_ta']) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="/admin/ma-giam-gia" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Cập nhật mã giảm giá
                                    </button>
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