<?php
$pageTitle = 'Sản phẩm yêu thích - FPT Shop';
ob_start();
?>

<div class="container-xl py-4">
    <h1 class="h4 mb-4 fw-bold"><i class="fa fa-heart text-danger me-2"></i>Sản phẩm yêu thích</h1>

    <?php if (empty($sanPhams)): ?>
        <div class="text-center py-5">
            <i class="fa fa-heart text-muted" style="font-size:4rem;"></i>
            <p class="mt-3 text-muted fs-5">Bạn chưa có sản phẩm yêu thích nào</p>
            <a href="/san-pham" class="btn btn-danger mt-2">Khám phá sản phẩm</a>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($sanPhams as $sp): ?>
                <div class="col-6 col-md-3 col-lg-2-4" style="flex: 0 0 20%; max-width: 20%;">
                    <div class="card border-0 shadow-sm h-100 position-relative">
                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 btn-remove-favorite rounded-circle"
                            data-id="<?= $sp['id'] ?>" style="width:30px;height:30px;padding:0;z-index:1;">
                            <i class="fa fa-times" style="font-size:0.7rem;"></i>
                        </button>
                        <a href="/san-pham/<?= htmlspecialchars($sp['slug']) ?>" class="text-decoration-none">
                            <img src="<?= htmlspecialchars($sp['anh_chinh'] ?? '/public/assets/client/images/products/14.png') ?>"
                                class="card-img-top p-2"
                                alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>"
                                style="height:160px;object-fit:contain;">
                            <div class="card-body pt-0 px-3 pb-3">
                                <p class="small mb-1 text-dark" style="display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                </p>
                                <p class="text-danger fw-bold mb-0 small"><?= number_format($sp['gia_hien_thi'], 0, ',', '.') ?>đ</p>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Phân trang -->
        <?php if (($tongTrang ?? 1) > 1): ?>
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

<script>
    document.querySelectorAll('.btn-remove-favorite').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!confirm('Xóa khỏi danh sách yêu thích?')) return;
            fetch('/yeu-thich/xoa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'san_pham_id=' + this.dataset.id
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert(data.message || 'Có lỗi xảy ra');
                });
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/master.php';
?>