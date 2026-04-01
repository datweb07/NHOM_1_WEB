<?php
$danhGia = $danhGia ?? [];

require_once dirname(__DIR__) . '/layouts/header.php';
?>

<?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Đánh Giá', 'url' => '/admin/danh-gia'],
        ['label' => 'Chi Tiết #' . ($danhGia['id'] ?? ''), 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Thông Tin Người Dùng</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Họ Tên</th>
                                    <td><?= htmlspecialchars($danhGia['ho_ten'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= htmlspecialchars($danhGia['email'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Số Điện Thoại</th>
                                    <td><?= htmlspecialchars($danhGia['sdt'] ?? 'N/A') ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông Tin Sản Phẩm</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Tên Sản Phẩm</th>
                                    <td><?= htmlspecialchars($danhGia['ten_san_pham'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Slug</th>
                                    <td><?= htmlspecialchars($danhGia['slug'] ?? 'N/A') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h5>Chi Tiết Đánh Giá</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="20%">ID Đánh Giá</th>
                            <td><?= (int)($danhGia['id'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <th>Số Sao</th>
                            <td>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= (int)($danhGia['so_sao'] ?? 0) ? '-fill text-warning' : '' ?>" style="font-size: 1.5rem;"></i>
                                <?php endfor; ?>
                                <span class="ms-2">(<?= (int)($danhGia['so_sao'] ?? 0) ?>/5)</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Nội Dung</th>
                            <td><?= nl2br(htmlspecialchars($danhGia['noi_dung'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <th>Ngày Viết</th>
                            <td><?= date('d/m/Y H:i:s', strtotime($danhGia['ngay_viet'] ?? 'now')) ?></td>
                        </tr>
                    </table>

                    <div class="d-flex gap-2 mt-3">
                        <a href="/admin/danh-gia" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <form method="POST" action="/admin/danh-gia/xoa?id=<?= (int)($danhGia['id'] ?? 0) ?>" 
                              style="display: inline;" 
                              onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?');">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Xóa Đánh Giá
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
