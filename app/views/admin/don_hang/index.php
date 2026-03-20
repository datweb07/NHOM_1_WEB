<?php
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$successMessages = [
    'status_updated' => 'Cap nhat trang thai thanh cong.',
];

$errorMessages = [
    'invalid_id' => 'ID don hang khong hop le.',
    'not_found' => 'Khong tim thay don hang.',
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quan ly don hang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4 py-lg-5">
        <h1 class="h3 mb-3">Danh sach don hang</h1>

        <?php if (!empty($success) && isset($successMessages[$success])): ?>
            <div class="alert alert-success"><?= e($successMessages[$success]) ?></div>
        <?php endif; ?>
        <?php if (!empty($error) && isset($errorMessages[$error])): ?>
            <div class="alert alert-danger"><?= e($errorMessages[$error]) ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body border-bottom">
                <form class="row g-2" method="GET" action="/admin/don-hang">
                    <div class="col-12 col-md-6 col-lg-4">
                        <select class="form-select" name="trang_thai">
                            <option value="" <?= ($trangThaiFilter ?? '') === '' ? 'selected' : '' ?>>Tat ca trang thai</option>
                            <option value="CHO_DUYET" <?= ($trangThaiFilter ?? '') === 'CHO_DUYET' ? 'selected' : '' ?>>Cho duyet</option>
                            <option value="DANG_GIAO" <?= ($trangThaiFilter ?? '') === 'DANG_GIAO' ? 'selected' : '' ?>>Dang giao</option>
                            <option value="HOAN_THANH" <?= ($trangThaiFilter ?? '') === 'HOAN_THANH' ? 'selected' : '' ?>>Hoan thanh</option>
                            <option value="DA_HUY" <?= ($trangThaiFilter ?? '') === 'DA_HUY' ? 'selected' : '' ?>>Da huy</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 col-lg-2 d-grid">
                        <button class="btn btn-outline-secondary" type="submit">Loc</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Ma don</th>
                            <th>Khach hang</th>
                            <th>Trang thai</th>
                            <th>Tong thanh toan</th>
                            <th>Ngay tao</th>
                            <th>Chi tiet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($danhSachDonHang)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">Khong co don hang.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($danhSachDonHang as $item): ?>
                                <tr>
                                    <td><?= (int)$item['id'] ?></td>
                                    <td><?= e($item['ma_don_hang'] ?? '-') ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= e($item['ho_ten'] ?? 'Khach vang lai') ?></div>
                                        <small class="text-secondary"><?= e($item['email'] ?? '') ?></small>
                                    </td>
                                    <td><span class="badge text-bg-secondary"><?= e($item['trang_thai']) ?></span></td>
                                    <td><?= number_format((float)($item['tong_thanh_toan'] ?? 0), 0, ',', '.') ?> VND</td>
                                    <td><?= e($item['ngay_tao'] ?? '-') ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary" href="/admin/don-hang/chi-tiet?id=<?= (int)$item['id'] ?>">Xem</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>