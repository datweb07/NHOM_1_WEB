<?php
require_once dirname(__DIR__) . '/layouts/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Sản phẩm yêu thích</h2>

    <?php if (empty($sanPhams)): ?>
        <div class="alert alert-info">
            <p class="mb-0">Bạn chưa có sản phẩm yêu thích nào.</p>
            <a href="/san-pham" class="btn btn-primary mt-2">Khám phá sản phẩm</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($sanPhams as $sp): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="<?= $sp['anh_chinh'] ?? '/assets/images/no-image.png' ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>"
                                 style="height: 200px; object-fit: cover;">
                            <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 btn-remove-favorite" 
                                    data-id="<?= $sp['id'] ?>">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($sp['ten_san_pham']) ?></h6>
                            <p class="text-danger fw-bold"><?= number_format($sp['gia_hien_thi']) ?>đ</p>
                            <p class="text-muted small">Thêm vào: <?= date('d/m/Y', strtotime($sp['ngay_them'])) ?></p>
                            <a href="/san-pham/<?= $sp['slug'] ?>" class="btn btn-primary btn-sm w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($tongTrang > 1): ?>
            <nav>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xóa khỏi yêu thích
    document.querySelectorAll('.btn-remove-favorite').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const sanPhamId = this.dataset.id;
            
            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
                fetch('/yeu-thich/xoa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'san_pham_id=' + sanPhamId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                    }
                });
            }
        });
    });
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
