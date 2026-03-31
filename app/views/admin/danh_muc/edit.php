<?php
class DanhMucEditViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$old = $old ?? [];
$errors = $errors ?? [];
$danhMuc = $danhMuc ?? [];
$danhMucChaOptions = $danhMucChaOptions ?? [];

$danhMucId = (int)($danhMuc['id'] ?? 0);

$valueTen = $old['ten'] ?? ($danhMuc['ten'] ?? '');
$valueSlug = $old['slug'] ?? ($danhMuc['slug'] ?? '');
$valueIcon = $old['icon_url'] ?? ($danhMuc['icon_url'] ?? '');
$valueCha = (string)($old['danh_muc_cha_id'] ?? ($danhMuc['danh_muc_cha_id'] ?? ''));
$valueThuTu = $old['thu_tu'] ?? ($danhMuc['thu_tu'] ?? '0');
$valueTrangThai = (string)($old['trang_thai'] ?? ($danhMuc['trang_thai'] ?? '1'));

require_once dirname(__DIR__) . '/layouts/header.php';
?>

<?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Danh Mục', 'url' => '/admin/danh-muc'],
        ['label' => 'Sửa #' . $danhMucId, 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                <form class="row g-3" method="POST" action="/admin/danh-muc/sua?id=<?= $danhMucId ?>">
                    <div class="col-12">
                        <label class="form-label" for="ten">Tên danh mục *</label>
                        <input class="form-control" id="ten" name="ten" type="text" value="<?= DanhMucEditViewHelper::e($valueTen) ?>" required>
                        <?php if (!empty($errors['ten'])): ?><div class="text-danger small mt-1"><?= DanhMucEditViewHelper::e($errors['ten']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="slug">Slug</label>
                        <input class="form-control" id="slug" name="slug" type="text" value="<?= DanhMucEditViewHelper::e($valueSlug) ?>" placeholder="vi-du: dien-thoai">
                        <?php if (!empty($errors['slug'])): ?><div class="text-danger small mt-1"><?= DanhMucEditViewHelper::e($errors['slug']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="icon_url">Icon URL</label>
                        <input class="form-control" id="icon_url" name="icon_url" type="text" value="<?= DanhMucEditViewHelper::e($valueIcon) ?>" placeholder="https://...">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="danh_muc_cha_id">Danh mục cha</label>
                        <select class="form-select" id="danh_muc_cha_id" name="danh_muc_cha_id">
                            <option value="">-- Không có --</option>
                            <?php foreach ($danhMucChaOptions as $item): ?>
                                <option value="<?= (int)$item['id'] ?>" <?= $valueCha === (string)$item['id'] ? 'selected' : '' ?>>
                                    <?= DanhMucEditViewHelper::e($item['ten']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['danh_muc_cha_id'])): ?><div class="text-danger small mt-1"><?= DanhMucEditViewHelper::e($errors['danh_muc_cha_id']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label" for="thu_tu">Thứ tự hiển thị</label>
                        <input class="form-control" id="thu_tu" name="thu_tu" type="number" min="0" value="<?= DanhMucEditViewHelper::e($valueThuTu) ?>">
                        <?php if (!empty($errors['thu_tu'])): ?><div class="text-danger small mt-1"><?= DanhMucEditViewHelper::e($errors['thu_tu']) ?></div><?php endif; ?>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label" for="trang_thai">Trạng thái</label>
                        <select class="form-select" id="trang_thai" name="trang_thai">
                            <option value="1" <?= $valueTrangThai === '1' ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="0" <?= $valueTrangThai === '0' ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2 pt-1">
                        <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                        <a class="btn btn-outline-secondary" href="/admin/danh-muc">Quay lại danh sách</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>