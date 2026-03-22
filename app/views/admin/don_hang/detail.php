<?php
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$successMessages = [
    'status_updated' => 'Da cap nhat trang thai don hang.',
];

$errorMessages = [
    'invalid_transition' => 'Khong the chuyen trang thai theo luong yeu cau.',
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiet don hang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4 py-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Chi tiet don hang #<?= (int)$donHang['id'] ?></h1>
            <a class="btn btn-outline-secondary" href="/admin/don-hang">Quay lai</a>
        </div>

        <?php if (!empty($success) && isset($successMessages[$success])): ?>
            <div class="alert alert-success"><?= e($successMessages[$success]) ?></div>
        <?php endif; ?>
        <?php if (!empty($error) && isset($errorMessages[$error])): ?>
            <div class="alert alert-danger"><?= e($errorMessages[$error]) ?></div>
        <?php endif; ?>

        <div class="row g-3 mb-3">
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h6 text-uppercase text-secondary">Thong tin don hang</h2>
                        <p class="mb-1"><strong>Ma don:</strong> <?= e($donHang['ma_don_hang'] ?? '-') ?></p>
                        <p class="mb-1"><strong>Trang thai hien tai:</strong> <span class="badge text-bg-secondary"><?= e($donHang['trang_thai'] ?? '-') ?></span></p>
                        <p class="mb-1"><strong>Tong tien hang:</strong> <?= number_format((float)($donHang['tong_tien'] ?? 0), 0, ',', '.') ?> VND</p>
                        <p class="mb-1"><strong>Phi van chuyen:</strong> <?= number_format((float)($donHang['phi_van_chuyen'] ?? 0), 0, ',', '.') ?> VND</p>
                        <p class="mb-1"><strong>Tien giam gia:</strong> <?= number_format((float)($donHang['tien_giam_gia'] ?? 0), 0, ',', '.') ?> VND</p>
                        <p class="mb-1"><strong>Tong thanh toan:</strong> <?= number_format((float)($donHang['tong_thanh_toan'] ?? 0), 0, ',', '.') ?> VND</p>
                        <p class="mb-0"><strong>Ngay tao:</strong> <?= e($donHang['ngay_tao'] ?? '-') ?></p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h6 text-uppercase text-secondary">Khach hang</h2>
                        <p class="mb-1"><strong>Ho ten:</strong> <?= e($donHang['ho_ten'] ?? 'Khach vang lai') ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?= e($donHang['email'] ?? '-') ?></p>
                        <p class="mb-0"><strong>So dien thoai:</strong> <?= e($donHang['sdt'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-secondary mb-3">Cap nhat trang thai</h2>
                <?php if (empty($trangThaiKeTiep)): ?>
                    <div class="text-secondary">Don hang da o trang thai cuoi, khong the cap nhat tiep.</div>
                <?php else: ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($trangThaiKeTiep as $next): ?>
                            <form method="POST" action="/admin/don-hang/cap-nhat-trang-thai?id=<?= (int)$donHang['id'] ?>">
                                <input type="hidden" name="trang_thai" value="<?= e($next) ?>">
                                <?php if ($next === 'DA_HUY'): ?>
                                    <button class="btn btn-outline-danger" type="submit">Chuyen sang DA_HUY</button>
                                <?php elseif ($next === 'DANG_GIAO'): ?>
                                    <button class="btn btn-outline-primary" type="submit">Chuyen sang DANG_GIAO</button>
                                <?php else: ?>
                                    <button class="btn btn-outline-success" type="submit">Chuyen sang <?= e($next) ?></button>
                                <?php endif; ?>
                            </form>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>San pham</th>
                                <th>Phien ban</th>
                                <th>So luong</th>
                                <th>Gia mua</th>
                                <th>Thanh tien</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($chiTietDon)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">Khong co chi tiet don hang.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($chiTietDon as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= e($item['ten_san_pham'] ?? '-') ?></td>
                                        <td><?= e($item['ten_phien_ban'] ?? '-') ?> / <?= e($item['mau_sac'] ?? '-') ?> / <?= e($item['dung_luong'] ?? '-') ?></td>
                                        <td><?= (int)($item['so_luong'] ?? 0) ?></td>
                                        <td><?= number_format((float)($item['gia_tai_thoi_diem_mua'] ?? 0), 0, ',', '.') ?> VND</td>
                                        <td><?= number_format(((float)($item['gia_tai_thoi_diem_mua'] ?? 0) * (int)($item['so_luong'] ?? 0)), 0, ',', '.') ?> VND</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>