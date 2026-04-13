<?php
$pageTitle = 'Giỏ hàng - FPT Shop';
ob_start();
?>

<style>
    /* CSS tùy chỉnh loại bỏ mũi tên tăng giảm mặc định của input number */
    .cart-qty-input::-webkit-outer-spin-button,
    .cart-qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .cart-qty-input {
        -moz-appearance: textfield;
        appearance: textfield;
        box-shadow: none !important;
    }
    .btn-qty {
        color: #495057;
        background-color: #fff;
        border-color: #ced4da;
    }
    .btn-qty:hover {
        background-color: #f8f9fa;
        color: #cb1c22;
    }
    .btn-remove-item {
        color: #6c757d;
        background: transparent;
        border: none;
        padding: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
</style>

<div class="container-xl mx-auto py-4 py-md-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="fa fa-shopping-cart text-danger me-2"></i>Giỏ hàng của bạn</h1>
            <?php if (!empty($chiTietGioList)): ?>
                <span class="badge bg-danger rounded-pill ms-3 fs-6 px-3"><?= count($chiTietGioList) ?> sản phẩm</span>
            <?php endif; ?>
        </div>
        <?php if (!empty($chiTietGioList)): ?>
            <form action="/gio-hang/xoa-tat-ca" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi giỏ hàng?')" class="m-0">
                <button type="submit" class="btn btn-outline-danger btn-sm fw-medium">
                    <i class="fa fa-trash-alt me-1"></i> Xóa tất cả
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
            <i class="fa fa-check-circle me-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($chiTietGioList)): ?>
       <div class="card border-0 shadow-sm rounded-4 mt-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0 align-items-center">
                    <div class="col-md-6 p-5">
                        <h4 class="fw-bold text-dark mb-3" style="font-size: 26px;">Chưa có sản phẩm nào trong giỏ hàng</h4>
                        <p class="text-muted mb-4" style="font-size: 15px;">Cùng mua sắm hàng ngàn sản phẩm tại FPTShop nhé!</p>
                        <a href="/san-pham" class="btn btn-danger px-4 py-2 fw-medium rounded-3" style="background-color: #d71920; border-color: #d71920; font-size: 15px;">
                            Mua hàng
                        </a>
                    </div>
                    
                    <div class="col-md-6 text-center bg-white p-4">
                        <img src="<?= ASSET_URL ?>/assets/client/images/carts/empty-cart.png" alt="Giỏ hàng trống" class="img-fluid" style="max-height: 220px; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th class="px-4 py-3 text-uppercase text-secondary fw-semibold" style="font-size: 13px;">Sản phẩm</th>
                                        <th class="text-center py-3 text-uppercase text-secondary fw-semibold" style="font-size: 13px; width: 130px;">Đơn giá</th>
                                        <th class="text-center py-3 text-uppercase text-secondary fw-semibold" style="font-size: 13px; width: 140px;">Số lượng</th>
                                        <th class="text-end pe-4 py-3 text-uppercase text-secondary fw-semibold" style="font-size: 13px; width: 130px;">Thành tiền</th>
                                        <th class="text-center py-3 text-uppercase text-secondary fw-semibold" style="font-size: 13px; width: 80px;">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($chiTietGioList as $item): ?>
                                        <tr id="row-<?= $item['id'] ?>">
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-white border rounded p-1" style="width: 80px; height: 80px; flex-shrink: 0;">
                                                        <img src="<?= htmlspecialchars($item['anh_chinh'] ?? ASSET_URL . '/assets/client/images/products/14.png') ?>"
                                                             alt="<?= htmlspecialchars($item['ten_san_pham'] ?? '') ?>"
                                                             style="width: 100%; height: 100%; object-fit: contain;">
                                                    </div>
                                                    <div>
                                                        <a href="/san-pham/<?= htmlspecialchars($item['slug'] ?? '') ?>" class="text-dark text-decoration-none fw-bold hover-danger" style="font-size: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                            <?= htmlspecialchars($item['ten_san_pham'] ?? '') ?>
                                                        </a>
                                                        <?php if (!empty($item['ten_phien_ban'])): ?>
                                                            <div class="mt-2">
                                                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1">
                                                                    <?= htmlspecialchars($item['ten_phien_ban']) ?>
                                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td class="text-center fw-medium">
                                                <?= number_format($item['gia_ban'], 0, ',', '.') ?>đ
                                            </td>
                                            
                                            <td class="text-center">
                                                <form class="d-flex justify-content-center m-0" action="/gio-hang/cap-nhat" method="POST">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <div class="input-group input-group-sm" style="width: 110px;">
                                                        <button class="btn btn-qty" type="button" onclick="changeQty(this,-1)"><i class="fa fa-minus" style="font-size: 10px;"></i></button>
                                                        <input type="number" name="so_luong" class="form-control text-center cart-qty-input fw-medium px-1" value="<?= $item['so_luong'] ?>" min="1" max="99">
                                                        <button class="btn btn-qty" type="button" onclick="changeQty(this,1)"><i class="fa fa-plus" style="font-size: 10px;"></i></button>
                                                    </div>
                                                </form>
                                            </td>
                                            
                                            <td class="text-end text-danger fw-bold pe-4">
                                                <?= number_format($item['gia_ban'] * $item['so_luong'], 0, ',', '.') ?>đ
                                            </td>
                                            
                                            <td class="text-center">
                                                <form action="/gio-hang/xoa" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')" class="m-0 d-inline-block">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn-remove-item" title="Xóa sản phẩm">
                                                        <i class="fa fa-trash-alt" style="font-size: 18px;"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-top p-3 p-md-4">
                        <a href="/san-pham" class="text-decoration-none fw-medium" style="color: #0056b3;">
                            <i class="fa fa-long-arrow-alt-left me-2"></i> Chọn thêm sản phẩm khác
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4" style="position: sticky; top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 text-dark border-bottom pb-3">Tóm tắt đơn hàng</h5>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Tổng tiền hàng</span>
                            <span class="fw-medium text-dark"><?= number_format($tongTien, 0, ',', '.') ?>đ</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Phí vận chuyển</span>
                            <span class="fw-medium text-dark">30.000đ</span>
                        </div>
                        
                        <div class="border-top border-dashed my-3"></div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold text-dark fs-6">Tổng thanh toán</span>
                            <div class="text-end">
                                <span class="fw-bold text-danger fs-4 d-block"><?= number_format($tongTien + 30000, 0, ',', '.') ?>đ</span>
                                <small class="text-muted" style="font-size: 12px;">(Đã bao gồm VAT nếu có)</small>
                            </div>
                        </div>
                        
                        <a href="/thanh-toan" class="btn btn-danger w-100 fw-bold py-3 text-uppercase rounded-3 shadow-sm" style="background-color: #cb1c22; border-color: #cb1c22; letter-spacing: 0.5px;">
                            Tiến hành thanh toán
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Hàm xử lý tăng giảm bằng nút bấm
function changeQty(btn, delta) {
    const input = btn.parentElement.querySelector('.cart-qty-input');
    let val = parseInt(input.value) + delta;
    if (isNaN(val) || val < 1) val = 1;
    if (val > 99) val = 99;
    
    if (input.value != val) {
        input.value = val;
        // Bắt sự kiện submit tự động cập nhật giỏ hàng
        btn.closest('form').submit();
    }
}

// Xử lý khi người dùng nhập số trực tiếp bằng bàn phím
document.querySelectorAll('.cart-qty-input').forEach(function(input) {
    let originalValue = input.value;
    input.addEventListener('blur', function() {
        let val = parseInt(this.value);
        if (isNaN(val) || val < 1) val = 1;
        if (val > 99) val = 99;
        this.value = val;
        
        if (this.value != originalValue) {
            this.closest('form').submit();
        }
    });
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.blur();
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>