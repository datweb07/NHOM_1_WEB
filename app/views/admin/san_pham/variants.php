<?php
$sanPhamId = (int)($sanPham['id'] ?? 0);

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<main class="app-main">
    <?php 
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['label' => 'Sản Phẩm', 'url' => '/admin/san-pham'],
        ['label' => 'Phiên bản #' . $sanPhamId, 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Quản lý Phiên bản: <?= htmlspecialchars($sanPham['ten_san_pham'] ?? '') ?></h4>
                <div class="btn-group btn-group-sm">
                    <a href="/admin/san-pham/sua?id=<?= $sanPhamId ?>" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Sửa SP
                    </a>
                    <a href="/admin/san-pham/hinh-anh?id=<?= $sanPhamId ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-image"></i> Hình ảnh
                    </a>
                    <a href="/admin/san-pham/thong-so?id=<?= $sanPhamId ?>" class="btn btn-outline-warning">
                        <i class="bi bi-list-ul"></i> Thông số
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    $messages = [
                        'variant_created' => 'Thêm phiên bản thành công!',
                        'variant_updated' => 'Cập nhật phiên bản thành công!',
                        'variant_deleted' => 'Xóa phiên bản thành công!'
                    ];
                    echo $messages[$_GET['success']] ?? 'Thao tác thành công!';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    $messages = [
                        'validation' => 'Vui lòng kiểm tra lại thông tin!',
                        'not_found' => 'Không tìm thấy phiên bản!'
                    ];
                    echo $messages[$_GET['error']] ?? 'Có lỗi xảy ra!';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Add Variant Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thêm Phiên bản mới</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/san-pham/phien-ban/them?id=<?= $sanPhamId ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                                    <input type="text" name="sku" class="form-control <?= isset($_SESSION['variant_errors']['sku']) ? 'is-invalid' : '' ?>" 
                                           value="<?= htmlspecialchars($_SESSION['variant_old']['sku'] ?? '') ?>" required>
                                    <?php if (isset($_SESSION['variant_errors']['sku'])): ?>
                                        <div class="invalid-feedback"><?= $_SESSION['variant_errors']['sku'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tên phiên bản</label>
                                    <input type="text" name="ten_phien_ban" class="form-control" 
                                           value="<?= htmlspecialchars($_SESSION['variant_old']['ten_phien_ban'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Màu sắc</label>
                                    <input type="text" name="mau_sac" class="form-control" 
                                           value="<?= htmlspecialchars($_SESSION['variant_old']['mau_sac'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Dung lượng</label>
                                    <input type="text" name="dung_luong" class="form-control" 
                                           value="<?= htmlspecialchars($_SESSION['variant_old']['dung_luong'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">RAM</label>
                                    <input type="text" name="ram" class="form-control" 
                                           value="<?= htmlspecialchars($_SESSION['variant_old']['ram'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                    <input type="number" name="gia_ban" class="form-control <?= isset($_SESSION['variant_errors']['gia_ban']) ? 'is-invalid' : '' ?>" 
                                           value="<?= htmlspecialchars($_SESSION['variant_old']['gia_ban'] ?? '') ?>" step="0.01" required>
                                    <?php if (isset($_SESSION['variant_errors']['gia_ban'])): ?>
                                        <div class="invalid-feedback"><?= $_SESSION['variant_errors']['gia_ban'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Giá gốc</label>
                                    <input type="number" name="gia_goc" class="form-control <?= isset($_SESSION['variant_errors']['gia_goc']) ? 'is-invalid' : '' ?>" 
                                           value="<?= htmlspecialchars($_SESSION['variant_old']['gia_goc'] ?? '') ?>" step="0.01">
                                    <?php if (isset($_SESSION['variant_errors']['gia_goc'])): ?>
                                        <div class="invalid-feedback"><?= $_SESSION['variant_errors']['gia_goc'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Số lượng tồn <span class="text-danger">*</span></label>
                                    <input type="number" name="so_luong_ton" class="form-control <?= isset($_SESSION['variant_errors']['so_luong_ton']) ? 'is-invalid' : '' ?>" 
                                           value="<?= htmlspecialchars($_SESSION['variant_old']['so_luong_ton'] ?? '0') ?>" required>
                                    <?php if (isset($_SESSION['variant_errors']['so_luong_ton'])): ?>
                                        <div class="invalid-feedback"><?= $_SESSION['variant_errors']['so_luong_ton'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-plus"></i> Thêm phiên bản
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Variants List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Danh sách Phiên bản (<?= count($variants) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($variants)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Chưa có phiên bản nào. Vui lòng thêm phiên bản mới.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Tên phiên bản</th>
                                        <th>Màu sắc</th>
                                        <th>Dung lượng</th>
                                        <th>RAM</th>
                                        <th>Giá bán</th>
                                        <th>Giá gốc</th>
                                        <th>Tồn kho</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($variants as $variant): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($variant['sku']) ?></strong></td>
                                            <td><?= htmlspecialchars($variant['ten_phien_ban'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($variant['mau_sac'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($variant['dung_luong'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($variant['ram'] ?? '-') ?></td>
                                            <td><?= number_format($variant['gia_ban'], 0, ',', '.') ?>đ</td>
                                            <td><?= $variant['gia_goc'] ? number_format($variant['gia_goc'], 0, ',', '.') . 'đ' : '-' ?></td>
                                            <td><?= number_format($variant['so_luong_ton']) ?></td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'CON_HANG' => 'success',
                                                    'HET_HANG' => 'danger',
                                                    'NGUNG_BAN' => 'secondary'
                                                ];
                                                $labels = [
                                                    'CON_HANG' => 'Còn hàng',
                                                    'HET_HANG' => 'Hết hàng',
                                                    'NGUNG_BAN' => 'Ngừng bán'
                                                ];
                                                $badge = $badges[$variant['trang_thai']] ?? 'secondary';
                                                $label = $labels[$variant['trang_thai']] ?? $variant['trang_thai'];
                                                ?>
                                                <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" 
                                                        onclick="editVariant(<?= htmlspecialchars(json_encode($variant)) ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="/admin/san-pham/phien-ban/xoa?id=<?= $variant['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Bạn có chắc muốn xóa phiên bản này?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Edit Variant Modal -->
<div class="modal fade" id="editVariantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editVariantForm">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa Phiên bản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" name="sku" id="edit_sku" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên phiên bản</label>
                                <input type="text" name="ten_phien_ban" id="edit_ten_phien_ban" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Màu sắc</label>
                                <input type="text" name="mau_sac" id="edit_mau_sac" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Dung lượng</label>
                                <input type="text" name="dung_luong" id="edit_dung_luong" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">RAM</label>
                                <input type="text" name="ram" id="edit_ram" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                <input type="number" name="gia_ban" id="edit_gia_ban" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá gốc</label>
                                <input type="number" name="gia_goc" id="edit_gia_goc" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Số lượng tồn <span class="text-danger">*</span></label>
                                <input type="number" name="so_luong_ton" id="edit_so_luong_ton" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

<script>
    function editVariant(variant) {
        document.getElementById('edit_sku').value = variant.sku || '';
        document.getElementById('edit_ten_phien_ban').value = variant.ten_phien_ban || '';
        document.getElementById('edit_mau_sac').value = variant.mau_sac || '';
        document.getElementById('edit_dung_luong').value = variant.dung_luong || '';
        document.getElementById('edit_ram').value = variant.ram || '';
        document.getElementById('edit_gia_ban').value = variant.gia_ban || '';
        document.getElementById('edit_gia_goc').value = variant.gia_goc || '';
        document.getElementById('edit_so_luong_ton').value = variant.so_luong_ton || '';
        
        document.getElementById('editVariantForm').action = '/admin/san-pham/phien-ban/sua?id=' + variant.id;
        
        new bootstrap.Modal(document.getElementById('editVariantModal')).show();
    }
</script>

<?php
// Clear session errors and old data
unset($_SESSION['variant_errors']);
unset($_SESSION['variant_old']);
?>
