<?php
require_once dirname(__DIR__) . '/layouts/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/san-pham">Sản phẩm</a></li>
            <?php if (isset($sanPham['ten_danh_muc'])): ?>
            <li class="breadcrumb-item"><a
                    href="/san-pham?danh_muc=<?= $sanPham['danh_muc_id'] ?>"><?= htmlspecialchars($sanPham['ten_danh_muc']) ?></a>
            </li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?= htmlspecialchars($sanPham['ten_san_pham']) ?></li>
        </ol>
    </nav>

    <div class="row">

        <div class="col-md-5">
            <div class="card">
                <img src="<?= $hinhAnhs[0]['url_anh'] ?? '/assets/images/no-image.png' ?>" class="card-img-top"
                    id="main-image" alt="<?= htmlspecialchars($sanPham['ten_san_pham']) ?>"
                    style="height: 400px; object-fit: contain;">
                <div class="card-body">
                    <div class="row g-2">
                        <?php foreach ($hinhAnhs as $index => $ha): ?>
                        <div class="col-3">
                            <img src="<?= $ha['url_anh'] ?>" class="img-thumbnail thumbnail-image"
                                style="cursor: pointer; height: 80px; object-fit: contain;"
                                onclick="changeMainImage('<?= $ha['url_anh'] ?>')">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <h2><?= htmlspecialchars($sanPham['ten_san_pham']) ?></h2>

            <div class="mb-3">
                <span class="text-warning">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa<?= $i <= $sanPham['diem_danh_gia'] ? 's' : 'r' ?> fa-star"></i>
                    <?php endfor; ?>
                </span>
                <span class="text-muted ms-2">(<?= $tongDanhGia ?? 0 ?> đánh giá)</span>
            </div>

            <h3 class="text-danger mb-3"><?= number_format($sanPham['gia_hien_thi']) ?>đ</h3>

            <?php if (!empty($sanPham['mo_ta'])): ?>
            <div class="mb-3">
                <h5>Mô tả:</h5>
                <p><?= nl2br(htmlspecialchars($sanPham['mo_ta'])) ?></p>
            </div>
            <?php endif; ?>

            <!-- Chọn phiên bản -->
            <?php if (!empty($phienBans)): ?>
            <div class="mb-3">
                <h5>Chọn phiên bản:</h5>
                <div class="btn-group" role="group">
                    <?php foreach ($phienBans as $pb): ?>
                    <input type="radio" class="btn-check" name="phien_ban" id="pb<?= $pb['id'] ?>"
                        value="<?= $pb['id'] ?>" <?= $pb === reset($phienBans) ? 'checked' : '' ?>>
                    <label class="btn btn-outline-primary" for="pb<?= $pb['id'] ?>">
                        <?= htmlspecialchars($pb['ten_phien_ban']) ?>
                        <br><small><?= number_format($pb['gia']) ?>đ</small>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Nút hành động -->
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-danger btn-lg flex-fill" onclick="themVaoGio()">
                    <i class="fa fa-cart-plus"></i> Thêm vào giỏ
                </button>
                <button class="btn btn-outline-danger btn-lg" id="btn-favorite" onclick="toggleFavorite()">
                    <i class="far fa-heart"></i>
                </button>
            </div>

            <div class="alert alert-info">
                <i class="fa fa-truck"></i> Miễn phí vận chuyển toàn quốc
            </div>
        </div>
    </div>

    <!-- Tabs thông tin -->
    <div class="mt-4">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#thong-so">Thông số kỹ
                    thuật</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#danh-gia">Đánh giá
                    (<?= $tongDanhGia ?? 0 ?>)</button>
            </li>
        </ul>

        <div class="tab-content p-3 border border-top-0">
            <!-- Thông số kỹ thuật -->
            <div class="tab-pane fade show active" id="thong-so">
                <?php if (!empty($thongSos)): ?>
                <table class="table">
                    <?php foreach ($thongSos as $ts): ?>
                    <tr>
                        <td class="fw-bold" style="width: 30%;"><?= htmlspecialchars($ts['ten_thong_so']) ?></td>
                        <td><?= htmlspecialchars($ts['gia_tri']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php else: ?>
                <p class="text-muted">Chưa có thông số kỹ thuật</p>
                <?php endif; ?>
            </div>

            <!-- Đánh giá -->
            <div class="tab-pane fade" id="danh-gia">
                <?php if (\App\Core\Session::isLoggedIn()): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>Viết đánh giá</h5>
                        <form id="form-danh-gia">
                            <div class="mb-3">
                                <label class="form-label">Đánh giá của bạn:</label>
                                <div class="rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="so_sao" value="<?= $i ?>" id="star<?= $i ?>">
                                    <label for="star<?= $i ?>"><i class="fas fa-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="noi_dung" rows="3"
                                    placeholder="Nhận xét của bạn..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <div id="danh-sach-danh-gia">
                    <?php if (!empty($danhGias)): ?>
                    <?php foreach ($danhGias as $dg): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?= $dg['avatar_url'] ?? '/assets/images/default-avatar.png' ?>"
                                    class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <strong><?= htmlspecialchars($dg['ho_ten']) ?></strong>
                                    <div class="text-warning small">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa<?= $i <= $dg['so_sao'] ? 's' : 'r' ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <small
                                    class="text-muted ms-auto"><?= date('d/m/Y', strtotime($dg['ngay_viet'])) ?></small>
                            </div>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($dg['noi_dung'])) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="text-muted">Chưa có đánh giá nào</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input {
    display: none;
}

.rating-input label {
    cursor: pointer;
    font-size: 24px;
    color: #ddd;
}

.rating-input label:hover,
.rating-input label:hover~label,
.rating-input input:checked~label {
    color: #ffc107;
}
</style>

<script>
const sanPhamId = <?= $sanPham['id'] ?>;
let isFavorite = false;

function changeMainImage(url) {
    document.getElementById('main-image').src = url;
}

function checkFavorite() {
    fetch('/yeu-thich/kiem-tra?san_pham_id=' + sanPhamId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.is_favorite) {
                isFavorite = true;
                document.querySelector('#btn-favorite i').classList.replace('far', 'fas');
            }
        });
}

function toggleFavorite() {
    const url = isFavorite ? '/yeu-thich/xoa' : '/yeu-thich/them';

    fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'san_pham_id=' + sanPhamId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                isFavorite = !isFavorite;
                const icon = document.querySelector('#btn-favorite i');
                icon.classList.toggle('far');
                icon.classList.toggle('fas');
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
}

function themVaoGio() {
    const phienBanId = document.querySelector('input[name="phien_ban"]:checked')?.value;

    if (!phienBanId) {
        alert('Vui lòng chọn phiên bản');
        return;
    }

    fetch('/gio-hang/them', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'phien_ban_id=' + phienBanId + '&so_luong=1'
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                updateCartCount();
            }
        });
}

document.getElementById('form-danh-gia')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('san_pham_id', sanPhamId);

    fetch('/danh-gia/them', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        });
});

document.addEventListener('DOMContentLoaded', function() {
    checkFavorite();
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>