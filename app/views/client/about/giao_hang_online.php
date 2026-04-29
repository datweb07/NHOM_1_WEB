<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
body {
    background-color: #ffffff;
}

.breadcrumb-custom {
    background-color: transparent;
    padding: 15px 0;
    margin-bottom: 10px;
    font-size: 13px;
}

.breadcrumb-custom a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb-custom a:hover {
    color: #cb1c22;
}

.breadcrumb-custom .active {
    color: #212529;
}

.content-section {
    padding-left: 10px;
}

.content-section h3,
.content-section h4,
.content-section p,
.content-section ul li,
.content-section .table {
    transition: font-size 0.3s ease-in-out;
}

.content-section h3 {
    font-weight: bold;
    font-size: 24px;
    color: #212529;
    margin-bottom: 25px;
}

.content-section h4 {
    font-weight: bold;
    font-size: 18px;
    margin-top: 30px;
    margin-bottom: 15px;
    text-transform: uppercase;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 8px;
}

.content-section p {
    text-align: justify;
    color: #495057;
    line-height: 1.6;
    margin-bottom: 15px;
    font-size: 15px;
}

.content-section ul {
    list-style-type: disc;
    padding-left: 20px;
    margin-bottom: 15px;
}

.content-section ul li {
    margin-bottom: 8px;
    color: #495057;
    text-align: justify;
    line-height: 1.6;
    font-size: 15px;
}

.content-section a {
    color: #0056b3;
    text-decoration: none;
    font-weight: 500;
}

.content-section a:hover {
    text-decoration: underline;
}

.content-section .table-container {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #dee2e6;
    margin-bottom: 25px;
}

.content-section .table {
    margin-bottom: 0;
    font-size: 15px;
}

.content-section .table th {
    background-color: #f8f9fa;
    color: #212529;
    font-weight: bold;
    vertical-align: middle;
    border-bottom: 2px solid #dee2e6;
}

.content-section .table td {
    color: #495057;
    vertical-align: middle;
}

.highlight-col {
    background-color: #fafafa;
    font-weight: 500;
    color: #333 !important;
}

.content-section.large-text h3 {
    font-size: 28px;
}

.content-section.large-text h4 {
    font-size: 20px;
}

.content-section.large-text p,
.content-section.large-text ul li,
.content-section.large-text .table {
    font-size: 17px;
    line-height: 1.7;
}
</style>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách giao hàng & lắp đặt Điện máy chỉ bán
                online</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'giao-hang-online'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách giao hàng & lắp đặt Điện máy chỉ bán online</h3>

                <h4>1. Chính sách giao hàng</h4>
                <ul>
                    <li><strong>Phạm vi giao hàng:</strong> TP.Hồ Chí Minh, Tỉnh Đồng Nai, Tỉnh An Giang, TP. Đà Nẵng,
                        TP. Hà Nội, TP. Hải Phòng, Tỉnh Bắc Ninh</li>
                    <li><strong>Chi phí giao hàng:</strong> Miễn phí.</li>
                    <li><strong>Thời gian giao hàng:</strong> 24 - 48 giờ tính từ thời điểm khách đặt hàng (hoặc theo
                        thoả thuận với khách hàng).</li>
                    <li><strong>Áp dụng riêng Tivi thương hiệu VSP:</strong> hỗ trợ giao hàng miễn phí toàn quốc</li>
                </ul>

                <h4>2. Chính sách lắp đặt</h4>
                <p><strong>Phạm vi áp dụng:</strong> TP.Hồ Chí Minh, Tỉnh Đồng Nai, Tỉnh An Giang, TP. Đà Nẵng, TP. Hà
                    Nội, TP. Hải Phòng, Tỉnh Bắc Ninh</p>

                <div class="table-container">
                    <table class="table table-bordered">
                        <thead class="table-primary-fpt text-center">
                            <tr>
                                <th colspan="2">Lắp đặt tiêu chuẩn (miễn phí)</th>
                                <th>Chi phí</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="highlight-col text-center" style="width: 25%;">Máy giặt, máy sấy</td>
                                <td>Vị trí lắp đặt của KH có sẵn đường ống nước</td>
                                <td rowspan="3" class="text-center align-middle text-success fw-bold"
                                    style="width: 25%;">Lắp đặt miễn phí</td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Tủ lạnh, tủ đông, tủ mát</td>
                                <td>Đường điện không quá 2m</td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Máy lạnh</td>
                                <td>Vị trí lắp đặt không cao quá 4m (tính từ mặt sàn)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-container mt-4">
                    <table class="table table-bordered">
                        <thead class="table-primary-fpt text-center">
                            <tr>
                                <th colspan="2">Lắp đặt nâng cao (có tính phí)</th>
                                <th>Chi phí</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="highlight-col text-center" style="width: 25%;">Máy giặt, máy sấy</td>
                                <td>
                                    - Vị trí lắp đặt không thuận lợi, cần xe cẩu, giàn giáo để đưa vào<br>
                                    - Nguồn nước yếu cần lắp thêm bơm tăng áp
                                </td>
                                <td rowspan="4" class="text-center align-middle text-danger fw-bold"
                                    style="width: 25%;">KH chịu phí phát sinh<br><span class="fw-normal text-muted"
                                        style="font-size: 13px;">(thỏa thuận với đơn vị thi công)</span></td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Tủ lạnh</td>
                                <td>Vị trí lắp đặt không thuận lợi cần xe cẩu, giàn giáo để đưa vào</td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Máy lạnh</td>
                                <td>
                                    - Phát sinh vật tư (vật tư chính và phụ) thêm ngoài khuyến mãi.<br>
                                    - Vị trí đặt dàn nóng cao hơn 4m và cần thuê giàn giáo<br>
                                    - Thi công đường ống âm tường<br>
                                    - Vệ sinh đường ống cũ<br>
                                    - Tháo, lắp máy cũ<br>
                                    - Hàn ống đồng
                                </td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Tivi</td>
                                <td>
                                    - Lắp đặt giá treo di động<br>
                                    <span class="text-danger fst-italic" style="font-size: 13px;">Lưu ý: TV từ 65inch
                                        khuyến cáo không treo tường, nếu khách hàng yêu cầu thì cần ký biên bản miễn trừ
                                        trách nhiệm nếu bị rơi trong quá trình sử dụng.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h4>3. Chính sách giá</h4>
                <p>Các sản phẩm <strong>Điện máy Chỉ bán Online</strong> có ưu đãi dành riêng cho các khách hàng khu vực
                    TP.Hồ Chí Minh, Đồng Nai, Bình Dương, Bà Rịa Vũng Tàu. Để nhận được ưu đãi giá bán tốt nhất quý
                    khách có thể thực hiện như sau:</p>
                <ul>
                    <li><strong>Cách 1:</strong> Truy cập vào <a href="#">đường dẫn sau đây</a> và nhập thông tin.</li>
                    <li><strong>Cách 2:</strong> Liên hệ tổng đài miễn phí <strong>1800.6601</strong>.</li>
                    <li><strong>Cách 3:</strong> Nhắn tin cho <a href="#">Zalo FPT Shop tại đây</a>.</li>
                </ul>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const btnSmall = document.getElementById('btn-font-small');
    const btnLarge = document.getElementById('btn-font-large');
    const contentSection = document.getElementById('policy-content');

    if (btnSmall && btnLarge && contentSection) {
        btnLarge.addEventListener('click', function() {
            btnLarge.classList.add('active');
            btnSmall.classList.remove('active');
            contentSection.classList.add('large-text');
        });

        btnSmall.addEventListener('click', function() {
            btnSmall.classList.add('active');
            btnLarge.classList.remove('active');
            contentSection.classList.remove('large-text');
        });
    }
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>