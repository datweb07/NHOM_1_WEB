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
                            <form method="POST" action="/admin/banner/them" enctype="multipart/form-data">
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
                                        <label for="hinh_anh_desktop" class="form-label">Hình ảnh Desktop <span class="text-danger">*</span></label>
                                        <input
                                            type="file"
                                            class="form-control <?= isset($errors['hinh_anh_desktop']) ? 'is-invalid' : '' ?>"
                                            id="hinh_anh_desktop"
                                            name="hinh_anh_desktop"
                                            accept="image/*"
                                            required>
                                        <?php if (isset($errors['hinh_anh_desktop'])): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= BannerCreateViewHelper::e($errors['hinh_anh_desktop']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <small class="text-muted">Chọn ảnh cho giao diện máy tính</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="hinh_anh_mobile" class="form-label">Hình ảnh Mobile</label>
                                        <input
                                            type="file"
                                            class="form-control <?= isset($errors['hinh_anh_mobile']) ? 'is-invalid' : '' ?>"
                                            id="hinh_anh_mobile"
                                            name="hinh_anh_mobile"
                                            accept="image/*">
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
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control <?= isset($errors['link_dich']) ? 'is-invalid' : '' ?>"
                                            id="link_dich"
                                            name="link_dich"
                                            value="<?= BannerCreateViewHelper::e($old['link_dich'] ?? '') ?>"
                                            placeholder="/san-pham/ten-san-pham hoặc https://example.com"
                                            required>
                                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalChonSanPham">
                                            <i class="bi bi-box-seam me-1"></i> Chọn sản phẩm
                                        </button>
                                    </div>
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
                                            <option value="HOME_MID" <?= ($old['vi_tri'] ?? '') === 'HOME_MID' ? 'selected' : '' ?>>Trang chủ - Giữa (2 banner)</option>
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
                                        <!-- Hidden input để đảm bảo luôn có giá trị 0 khi checkbox không được tick -->
                                        <input type="hidden" name="trang_thai" value="0">
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

<div class="modal fade" id="modalChonSanPham" tabindex="-1" aria-labelledby="modalChonSanPhamLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalChonSanPhamLabel">Chọn sản phẩm đích</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchInput" class="form-control mb-3" placeholder="Nhập tên sản phẩm để tìm kiếm...">
                <div id="productList" class="list-group">
                    <div class="text-center text-muted py-3">Đang tải danh sách sản phẩm...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('modalChonSanPham');
    const productList = document.getElementById('productList');
    const searchInput = document.getElementById('searchInput');
    let allProducts = [];
    let isLoaded = false;

    modalElement.addEventListener('show.bs.modal', function () {
        if (!isLoaded) {
            fetchProducts();
        }
    });

    searchInput.addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase();
        const filtered = allProducts.filter(p => p.ten_san_pham.toLowerCase().includes(keyword));
        renderProducts(filtered);
    });

    async function fetchProducts() {
        try {
            const response = await fetch('/admin/api/san-pham');
            
            if (!response.ok) {
                throw new Error('Không thể tải danh sách sản phẩm');
            }
            
            const data = await response.json();
            allProducts = data;
            isLoaded = true;
            renderProducts(allProducts);

        } catch (error) {
            productList.innerHTML = '<div class="text-danger text-center py-3">Lỗi tải dữ liệu. Vui lòng thử lại.</div>';
            console.error(error);
        }
    }

    // Hàm vẽ danh sách sản phẩm ra HTML
    function renderProducts(products) {
        productList.innerHTML = ''; // Xóa chữ "Đang tải..."
        
        if(products.length === 0) {
            productList.innerHTML = '<div class="text-center text-muted py-3">Không tìm thấy sản phẩm phù hợp.</div>';
            return;
        }

        products.forEach(product => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'list-group-item list-group-item-action';
            button.textContent = product.ten_san_pham;
            
            // Sự kiện khi click vào 1 sản phẩm
            button.onclick = function() {
                // Điền link vào ô input ngoài form
                // Giả định website bạn dùng slug (nếu dùng ID thì đổi thành product.id)
                document.getElementById('link_dich').value = '/san-pham/' + product.slug;
                
                // Đóng modal
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                modalInstance.hide();
            };
            
            productList.appendChild(button);
        });
    }
});
</script>