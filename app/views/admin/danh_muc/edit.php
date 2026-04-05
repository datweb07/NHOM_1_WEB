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
$danhMucId = (int)($danhMuc['id'] ?? 0);

$valueTen = $old['ten'] ?? ($danhMuc['ten'] ?? '');
$valueSlug = $old['slug'] ?? ($danhMuc['slug'] ?? '');
$valueIcon = $old['icon_url'] ?? ($danhMuc['icon_url'] ?? '');
$valueCha = (string)($old['danh_muc_cha_id'] ?? ($danhMuc['danh_muc_cha_id'] ?? ''));
$valueThuTu = $old['thu_tu'] ?? ($danhMuc['thu_tu'] ?? '0');
$valueTrangThai = (string)($old['trang_thai'] ?? ($danhMuc['trang_thai'] ?? '1'));

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

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
                    <form class="row g-3" method="POST" action="/admin/danh-muc/sua?id=<?= $danhMucId ?>" enctype="multipart/form-data">
                        <div class="col-12">
                            <label class="form-label" for="ten">Tên danh mục *</label>
                            <input class="form-control" id="ten" name="ten" type="text" value="<?= DanhMucEditViewHelper::e($valueTen) ?>" required>
                            <?php if (!empty($errors['ten'])): ?><div class="text-danger small mt-1"><?= DanhMucEditViewHelper::e($errors['ten']) ?></div><?php endif; ?>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="slug">Slug</label>
                            <input class="form-control" id="slug" name="slug" type="text" value="<?= DanhMucEditViewHelper::e($valueSlug) ?>" placeholder="vi-du: dien-thoai">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="icon_url">Thay đổi Icon</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="border rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: #f8f9fa;">
                                    <?php if (!empty($valueIcon)): ?>
                                        <img id="icon-preview" src="<?= $valueIcon ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted" id="icon-placeholder"></i>
                                        <img id="icon-preview" src="" class="d-none" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <input class="form-control" id="icon_url" name="icon_url" type="file" accept="image/*">
                                </div>
                            </div>
                            <small class="text-muted">Để trống nếu muốn giữ nguyên icon cũ.</small>
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
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label" for="thu_tu">Thứ tự hiển thị</label>
                            <input class="form-control" id="thu_tu" name="thu_tu" type="number" min="0" value="<?= DanhMucEditViewHelper::e($valueThuTu) ?>">
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

<script>
document.getElementById('icon_url').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('icon-preview');
    const placeholder = document.getElementById('icon-placeholder');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            preview.src = event.target.result;
            preview.classList.remove('d-none');
            if(placeholder) placeholder.classList.add('d-none');
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>