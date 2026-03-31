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
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên kết sản phẩm - <?= LinkProductsViewHelper::e($khuyenMai['ten_chuong_trinh']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4 py-lg-5">
        <div class="mb-3">
            <a href="/admin/khuyen-mai" class="btn btn-outline-secondary btn-sm">← Quay lại danh sách</a>
        </div>

        <?php if (!empty($success) && isset($successMessages[$success])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= LinkProductsViewHelper::e($successMessages[$success]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error) && isset($errorMessages[$error])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= LinkProductsViewHelper::e($errorMessages[$error]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h1 class="h4 mb-0">Liên kết sản phẩm với khuyến mãi</h1>
                <p class="text-muted mb-0 mt-2">
                    <strong>Chương trình:</strong> <?= LinkProductsViewHelper::e($khuyenMai['ten_chuong_trinh']) ?>
                </p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST" action="/admin/khuyen-mai/lien-ket-san-pham?id=<?= (int)$khuyenMai['id'] ?>">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Chọn sản phẩm áp dụng khuyến mãi</label>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">Chọn tất cả</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Bỏ chọn tất cả</button>
                            </div>
                        </div>
                        
                        <?php if (empty($allProducts)): ?>
                            <div class="alert alert-info">Không có sản phẩm nào trong hệ thống.</div>
                        <?php else: ?>
                            <div class="border rounded p-3" style="max-height: 500px; overflow-y: auto;">
                                <div class="row g-3">
                                    <?php foreach ($allProducts as $product): ?>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-check border rounded p-3 h-100">
                                                <input
                                                    class="form-check-input product-checkbox"
                                                    type="checkbox"
                                                    name="san_pham_ids[]"
                                                    value="<?= (int)$product['id'] ?>"
                                                    id="product_<?= (int)$product['id'] ?>"
                                                    <?= in_array($product['id'], $linkedProductIds) ? 'checked' : '' ?>>
                                                <label class="form-check-label w-100" for="product_<?= (int)$product['id'] ?>">
                                                    <div class="fw-semibold"><?= LinkProductsViewHelper::e($product['ten_san_pham']) ?></div>
                                                    <small class="text-muted">
                                                        ID: <?= (int)$product['id'] ?><br>
                                                        Giá: <?= LinkProductsViewHelper::formatCurrency($product['gia_hien_thi']) ?><br>
                                                        Trạng thái: <?= LinkProductsViewHelper::e($product['trang_thai']) ?>
                                                    </small>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Lưu liên kết</button>
                        <a href="/admin/khuyen-mai" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
</body>

</html>
