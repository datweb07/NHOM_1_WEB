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

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Danh Mục', 'url' => '/admin/danh-muc'],
        ['label' => 'Thêm Mới', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="POST" action="/admin/danh-muc/them" enctype="multipart/form-data">
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
                            <label class="form-label" for="icon_url">Icon danh mục</label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="icon-preview-container" class="border rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: #f8f9fa;">
                                    <i class="bi bi-image text-muted" id="icon-placeholder"></i>
                                    <img id="icon-preview" src="" class="d-none" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                                <div class="flex-grow-1">
                                    <input class="form-control" id="icon_url" name="icon_url" type="file" accept="image/*">
                                </div>
                            </div>
                            <small class="text-muted">Định dạng: JPG, PNG, SVG. Tối đa 2MB.</small>
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
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label" for="thu_tu">Thứ tự hiển thị</label>
                            <input class="form-control" id="thu_tu" name="thu_tu" type="number" min="0" value="<?= DanhMucCreateViewHelper::e($old['thu_tu'] ?? '0') ?>">
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
    </div>
</main>

<script>
// Script xem trước ảnh khi chọn file
document.getElementById('icon_url').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('icon-preview');
    const placeholder = document.getElementById('icon-placeholder');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            preview.src = event.target.result;
            preview.classList.remove('d-none');
            placeholder.classList.add('d-none');
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>