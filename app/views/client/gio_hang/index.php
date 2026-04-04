<?php
$pageTitle = 'Giỏ hàng - FPT Shop';
ob_start();
?>

<div class="container-xl py-4">
    <h1 class="h4 mb-4 fw-bold"><i class="fa fa-shopping-cart text-danger me-2"></i>Giỏ hàng của bạn</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($chiTietGioList)): ?>
        <div class="text-center py-5">
            <i class="fa fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
            <p class="mt-3 text-muted fs-5">Giỏ hàng của bạn đang trống</p>
            <a href="/san-pham" class="btn btn-danger mt-2">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3">Sản phẩm</th>
                                    <th class="text-center" style="width:120px;">Đơn giá</th>
                                    <th class="text-center" style="width:130px;">Số lượng</th>
                                    <th class="text-center" style="width:110px;">Thành tiền</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chiTietGioList as $item): ?>
                                    <tr id="row-<?= $item['id'] ?>">
                                        <td class="px-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= htmlspecialchars($item['anh_chinh'] ?? '/public/assets/client/images/products/14.png') ?>"
                                                     alt="<?= htmlspecialchars($item['ten_san_pham'] ?? '') ?>"
                                                     style="width:65px;height:65px;object-fit:contain;border:1px solid #eee;border-radius:6px;">
                                                <div>
                                                    <a href="/san-pham/<?= htmlspecialchars($item['slug'] ?? '') ?>" class="text-dark text-decoration-none fw-medium small">
                                                        <?= htmlspecialchars($item['ten_san_pham'] ?? '') ?>
                                                    </a>
                                                    <?php if (!empty($item['ten_phien_ban'])): ?>
                                                        <div class="text-muted" style="font-size:0.78rem;"><?= htmlspecialchars($item['ten_phien_ban']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center text-danger fw-bold small">
                                            <?= number_format($item['gia_ban'], 0, ',', '.') ?>đ
                                        </td>
                                        <td class="text-center">
                                            <form class="d-inline-flex align-items-center" action="/gio-hang/cap-nhat" method="POST">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <div class="input-group input-group-sm" style="width:100px;">
                                                    <button class="btn btn-outline-secondary btn-qty" type="button" onclick="changeQty(this,-1)">-</button>
                                                    <input type="number" name="so_luong" class="form-control text-center qty-input" value="<?= $item['so_luong'] ?>" min="1" max="99">
                                                    <button class="btn btn-outline-secondary btn-qty" type="button" onclick="changeQty(this,1)">+</button>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-warning ms-1 d-none update-btn">OK</button>
                                            </form>
                                        </td>
                                        <td class="text-center text-danger fw-bold small">
                                            <?= number_format($item['gia_ban'] * $item['so_luong'], 0, ',', '.') ?>đ
                                        </td>
                                        <td class="text-center">
                                            <form action="/gio-hang/xoa" method="POST" onsubmit="return confirm('Xóa sản phẩm này?')">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="/san-pham" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Tóm tắt đơn hàng</h6>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Tổng tiền hàng</span>
                            <span class="fw-medium"><?= number_format($tongTien, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Phí vận chuyển</span>
                            <span class="fw-medium">30.000đ</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Tổng thanh toán</span>
                            <span class="fw-bold text-danger fs-5"><?= number_format($tongTien + 30000, 0, ',', '.') ?>đ</span>
                        </div>
                        <a href="/thanh-toan" class="btn btn-danger w-100 fw-medium">
                            Tiến hành thanh toán <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function changeQty(btn, delta) {
    const group = btn.closest('.input-group');
    const input = group.querySelector('.qty-input');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 99) val = 99;
    input.value = val;
    // Tự submit form khi thay đổi
    btn.closest('form').submit();
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
