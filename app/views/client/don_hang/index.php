<?php
$pageTitle = 'Đơn hàng của tôi - FPT Shop';
ob_start();

$trangThaiMap = [
    'CHO_DUYET'    => ['label' => 'Chờ duyệt',    'class' => 'bg-warning text-dark'],
    'DA_DUYET'     => ['label' => 'Đã duyệt',     'class' => 'bg-info text-white'],
    'DANG_GIAO'    => ['label' => 'Đang giao',    'class' => 'bg-primary'],
    'DA_GIAO'      => ['label' => 'Đã giao',      'class' => 'bg-success'],
    'DA_HUY'       => ['label' => 'Đã hủy',       'class' => 'bg-danger'],
    'HOAN_TIEN'    => ['label' => 'Hoàn tiền',    'class' => 'bg-secondary'],
];
?>

<div class="container-xl py-4">
    <h1 class="h4 mb-4 fw-bold"><i class="fa fa-file-invoice text-danger me-2"></i>Đơn hàng của tôi</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($donHangList)): ?>
        <div class="text-center py-5">
            <i class="fa fa-file-invoice text-muted" style="font-size:4rem;"></i>
            <p class="mt-3 text-muted fs-5">Bạn chưa có đơn hàng nào</p>
            <a href="/san-pham" class="btn btn-danger">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donHangList as $dh):
                                $ts = $trangThaiMap[$dh['trang_thai']] ?? ['label' => $dh['trang_thai'], 'class' => 'bg-secondary'];
                            ?>
                                <tr>
                                    <td class="px-3 fw-medium small"><?= htmlspecialchars($dh['ma_don_hang']) ?></td>
                                    <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($dh['ngay_tao'])) ?></td>
                                    <td class="text-end text-danger fw-bold small"><?= number_format($dh['tong_thanh_toan'], 0, ',', '.') ?>đ</td>
                                    <td class="text-center">
                                        <span class="badge <?= $ts['class'] ?> small"><?= $ts['label'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="/don-hang/<?= $dh['id'] ?>" class="btn btn-sm btn-outline-danger">Chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Phân trang -->
        <?php if ($tongTrang > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $tongTrang; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>
