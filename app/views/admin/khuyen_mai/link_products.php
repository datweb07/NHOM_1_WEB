<?php
class LinkProductsViewHelper
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public static function formatCurrency($value): string
    {
        return number_format((float)$value, 0, ',', '.') . ' ₫';
    }
}

$successMessages = [
    'links_saved' => 'Đã lưu liên kết sản phẩm thành công.',
];

$errorMessages = [
    'invalid_id' => 'ID khuyến mãi không hợp lệ.',
    'not_found' => 'Không tìm thấy khuyến mãi.',
];

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Khuyến Mãi', 'url' => '/admin/khuyen-mai'],
        ['label' => 'Liên Kết Sản Phẩm', 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>

    <div class="app-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="/admin/khuyen-mai" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            <?php if (!empty($success) && isset($successMessages[$success])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle me-1"></i> <?= LinkProductsViewHelper::e($successMessages[$success]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (!empty($error) && isset($errorMessages[$error])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i> <?= LinkProductsViewHelper::e($errorMessages[$error]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body bg-light rounded d-flex align-items-center border-start border-4 border-primary">
                    <div class="me-3 fs-3 text-primary">
                        <i class="bi bi-tag-fill"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-muted text-uppercase" style="font-size: 13px;">Chương trình đang chọn</h6>
                        <h5 class="mb-0 fw-bold"><?= LinkProductsViewHelper::e($khuyenMai['ten_chuong_trinh']) ?></h5>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title text-uppercase text-muted mb-0">Chọn sản phẩm áp dụng khuyến mãi</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/khuyen-mai/lien-ket-san-pham?id=<?= (int)$khuyenMai['id'] ?>">
                        <div class="mb-3">
                            <div class="d-flex justify-content-end mb-3">
                                <div class="btn-group shadow-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="selectAll()">
                                        <i class="bi bi-check2-all"></i> Chọn tất cả
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="deselectAll()">
                                        <i class="bi bi-dash-square"></i> Bỏ chọn
                                    </button>
                                </div>
                            </div>

                            <?php if (empty($allProducts)): ?>
                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                                <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
                                <div>Không có sản phẩm nào trong hệ thống. Hãy thêm sản phẩm trước khi tạo liên kết.
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="bg-light border rounded p-3" style="max-height: 600px; overflow-y: auto;">
                                <div class="row g-3">
                                    <?php foreach ($allProducts as $product): ?>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div
                                            class="form-check custom-control border bg-white rounded p-3 h-100 position-relative shadow-sm transition-all product-card">
                                            <input class="form-check-input product-checkbox position-absolute"
                                                style="top: 15px; left: 15px; width: 1.2rem; height: 1.2rem;"
                                                type="checkbox" name="san_pham_ids[]" value="<?= (int)$product['id'] ?>"
                                                id="product_<?= (int)$product['id'] ?>"
                                                <?= in_array($product['id'], $linkedProductIds) ? 'checked' : '' ?>>

                                            <label class="form-check-label w-100 d-block"
                                                for="product_<?= (int)$product['id'] ?>"
                                                style="padding-left: 2rem; cursor: pointer;">
                                                <div class="fw-bold text-dark text-truncate mb-1"
                                                    title="<?= LinkProductsViewHelper::e($product['ten_san_pham']) ?>">
                                                    <?= LinkProductsViewHelper::e($product['ten_san_pham']) ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>ID: #<?= (int)$product['id'] ?></span>
                                                        <span
                                                            class="badge bg-secondary"><?= LinkProductsViewHelper::e($product['trang_thai']) ?></span>
                                                    </div>
                                                    <div class="text-danger fw-semibold">
                                                        <?= LinkProductsViewHelper::formatCurrency($product['gia_hien_thi']) ?>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="/admin/khuyen-mai" class="btn btn-light border px-4">Hủy</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-save me-1"></i> Lưu liên kết
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<style>
.product-card {
    transition: all 0.2s ease-in-out;
}

.product-card:hover {
    border-color: #0d6efd !important;
    background-color: #f8f9fa !important;
}

.product-checkbox:checked+label {
    opacity: 1;
}

.product-checkbox:checked~.product-card {
    border-color: #0d6efd !important;
    background-color: #f0f8ff !important;
}
</style>

<script>
function selectAll() {
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>