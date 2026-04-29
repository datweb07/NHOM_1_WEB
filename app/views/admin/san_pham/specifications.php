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
        ['label' => 'Thông số ID = ' . $sanPhamId, 'url' => '']
    ];
    require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; 
    ?>

    <div class="app-content">
        <div class="container-fluid">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Thông số Kỹ thuật: <?= htmlspecialchars($sanPham['ten_san_pham'] ?? '') ?></h4>
                <div class="btn-group btn-group-sm">
                    <a href="/admin/san-pham/sua?id=<?= $sanPhamId ?>" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Sửa SP
                    </a>
                    <a href="/admin/san-pham/phien-ban?id=<?= $sanPhamId ?>" class="btn btn-outline-info">
                        <i class="bi bi-box"></i> Phiên bản
                    </a>
                    <a href="/admin/san-pham/hinh-anh?id=<?= $sanPhamId ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-image"></i> Hình ảnh
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                    $messages = [
                        'specs_updated' => 'Cập nhật thông số kỹ thuật thành công!'
                    ];
                    echo $messages[$_GET['success']] ?? 'Thao tác thành công!';
                    ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quản lý Thông số Kỹ thuật</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/san-pham/cap-nhat-thong-so?id=<?= $sanPhamId ?>" id="specsForm">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="specsTable">
                                <thead>
                                    <tr>
                                        <th width="5%">Thứ tự</th>
                                        <th width="35%">Tên thông số</th>
                                        <th width="50%">Giá trị</th>
                                        <th width="10%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="specsTableBody">
                                    <?php if (empty($specifications)): ?>
                                    <tr class="spec-row">
                                        <td>
                                            <input type="number" name="specifications[0][thu_tu]"
                                                class="form-control form-control-sm" value="0" min="0">
                                        </td>
                                        <td>
                                            <input type="text" name="specifications[0][ten_thong_so]"
                                                class="form-control form-control-sm" placeholder="VD: Màn hình">
                                        </td>
                                        <td>
                                            <input type="text" name="specifications[0][gia_tri]"
                                                class="form-control form-control-sm" placeholder="VD: 6.7 inch, OLED">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="removeSpecRow(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($specifications as $index => $spec): ?>
                                    <tr class="spec-row">
                                        <td>
                                            <input type="number" name="specifications[<?= $index ?>][thu_tu]"
                                                class="form-control form-control-sm" value="<?= $spec['thu_tu'] ?>"
                                                min="0">
                                        </td>
                                        <td>
                                            <input type="text" name="specifications[<?= $index ?>][ten_thong_so]"
                                                class="form-control form-control-sm"
                                                value="<?= htmlspecialchars($spec['ten_thong_so']) ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="specifications[<?= $index ?>][gia_tri]"
                                                class="form-control form-control-sm"
                                                value="<?= htmlspecialchars($spec['gia_tri']) ?>">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="removeSpecRow(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" class="btn btn-secondary" onclick="addSpecRow()">
                                <i class="bi bi-plus"></i> Thêm thông số
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu thông số
                            </button>
                        </div>
                    </form>

                    <div class="alert alert-info mt-4">
                        <h6><i class="bi bi-info-circle"></i> Hướng dẫn:</h6>
                        <ul class="mb-0">
                            <li>Thứ tự: Số thứ tự hiển thị (thông số quan trọng nên để số nhỏ để hiện trước)</li>
                            <li>Tên thông số: VD: Màn hình, CPU, RAM, Camera, Pin, v.v.</li>
                            <li>Giá trị: Mô tả chi tiết của thông số</li>
                            <li>Nhấn "Thêm thông số" để thêm dòng mới</li>
                            <li>Nhấn nút xóa để xóa dòng không cần thiết</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

<script>
let specIndex = <?= count($specifications) ?>;

function addSpecRow() {
    const tbody = document.getElementById('specsTableBody');
    const newRow = document.createElement('tr');
    newRow.className = 'spec-row';
    newRow.innerHTML = `
            <td>
                <input type="number" name="specifications[${specIndex}][thu_tu]" class="form-control form-control-sm" value="0" min="0">
            </td>
            <td>
                <input type="text" name="specifications[${specIndex}][ten_thong_so]" class="form-control form-control-sm" placeholder="VD: Màn hình">
            </td>
            <td>
                <input type="text" name="specifications[${specIndex}][gia_tri]" class="form-control form-control-sm" placeholder="VD: 6.7 inch, OLED">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSpecRow(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
    tbody.appendChild(newRow);
    specIndex++;
}

function removeSpecRow(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('specsTableBody');

    if (tbody.querySelectorAll('tr').length > 1) {
        row.remove();
    } else {
        row.querySelectorAll('input').forEach(input => input.value = '');
    }
}
</script>