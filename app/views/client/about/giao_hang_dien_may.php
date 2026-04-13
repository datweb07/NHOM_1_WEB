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

    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h4 { font-size: 20px; }
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
            <li class="breadcrumb-item"><a href="/ho-tro">Hỗ trợ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách giao hàng & lắp đặt Điện máy, Gia dụng</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 

            $active_page = 'giao-hang-dien-may'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách giao hàng & lắp đặt Điện máy, Gia dụng</h3>

                <h4>1. MỘT SỐ ĐỊNH NGHĨA</h4>
                <ul>
                    <li><strong>Khoảng cách giao hàng:</strong> Là khoảng cách tính từ nơi mua hàng (cửa hàng) đến cửa nhà khách hàng.</li>
                    <li><strong>Khoảng cách lắp đặt:</strong> Là khoảng cách từ vị trí đặt thiết bị so với các nguồn cấp điện, cấp nước, đường thoát nước, vị trí treo/khoan bắt vít (đối với thiết bị cần lắp đặt cố định như máy lạnh, máy nước nóng, máy giặt, v.v.).</li>
                </ul>

                <h4>2. CHÍNH SÁCH GIAO HÀNG</h4>
                <div class="table-container">
                    <table class="table table-bordered text-center">
                        <thead class="table-primary-fpt">
                            <tr>
                                <th style="width: 30%;">Tiêu chí</th>
                                <th>Chi tiết chính sách</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="highlight-col">Thời gian giao hàng</td>
                                <td class="text-start">24 - 48 tiếng tính từ lúc khách đặt hàng (hoặc theo thoả thuận với khách hàng).</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="highlight-col align-middle">Chi phí giao hàng</td>
                                <td class="text-start"><strong><= 30km:</strong> Miễn phí</td>
                            </tr>
                            <tr>
                                <td class="text-start"><strong>> 30km:</strong> Mỗi km tiếp theo tính phí 5.000đ/km</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h4>3. CHÍNH SÁCH LẮP ĐẶT</h4>
                <p><strong>Thời gian lắp đặt:</strong> 24 - 48 tiếng tính từ lúc khách nhận hàng (hoặc theo thời gian thoả thuận với khách hàng).</p>
                <p><strong>Thời gian phản ánh tình trạng sau lắp đặt:</strong> trong vòng 48 tiếng sau khi hoàn tất lắp đặt (chi phí phát sinh sau thời gian này sẽ do 2 bên thỏa thuận).</p>

                <h5 class="fw-bold mt-4 mb-3">3.1. Chính sách lắp đặt sản phẩm điện máy</h5>
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
                                <td rowspan="3" class="text-center align-middle text-success fw-bold">Lắp đặt miễn phí</td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Tủ lạnh, tủ đông,<br>tủ mát</td>
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
                                    - Nguồn nước yếu cần lắp thêm bơm tăng áp.
                                </td>
                                <td rowspan="4" class="text-center align-middle text-danger fw-bold" style="width: 25%;">KH chịu phí phát sinh<br><span class="fw-normal text-muted" style="font-size: 13px;">(thỏa thuận với đơn vị thi công)</span></td>
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
                                    <span class="text-danger fst-italic" style="font-size: 13px;">Lưu ý: TV từ 65inch khuyến cáo không treo tường, nếu khách hàng yêu cầu thì cần ký biên bản miễn trừ trách nhiệm nếu bị rơi trong quá trình sử dụng.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="fw-bold mt-5 mb-3">3.2. Chính sách lắp đặt sản phẩm gia dụng</h5>
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
                                <td class="highlight-col text-center" style="width: 25%;">Máy nước nóng gián tiếp/trực tiếp</td>
                                <td>Vị trí lắp đặt của KH có sẵn đường ống nước chờ tại vị trí lắp máy</td>
                                <td rowspan="4" class="text-center align-middle text-success fw-bold" style="width: 25%;">Lắp đặt miễn phí</td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Máy lọc nước</td>
                                <td rowspan="3" class="align-middle">
                                    Vị trí lắp vừa với kích thước Bếp/Hút mùi và có sẵn đường điện chờ<br>
                                    <span class="text-muted fst-italic" style="font-size: 13px;">(Loại trừ KG498, KG499, HS-I15521FG)</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Máy rửa bát</td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Bếp từ/hồng ngoại đa, Máy hút mùi</td>
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
                                <td class="highlight-col text-center" style="width: 25%;">Máy nước nóng gián tiếp/trực tiếp</td>
                                <td>
                                    - Có thêm vật tư phát sinh<br>
                                    - Thi công ống âm tường<br>
                                    - Tháo, lắp máy cũ
                                </td>
                                <td rowspan="4" class="text-center align-middle text-danger fw-bold" style="width: 25%;">KH chịu phí phát sinh<br><span class="fw-normal text-muted" style="font-size: 13px;">(thỏa thuận với đơn vị thi công)</span></td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Máy lọc nước</td>
                                <td>
                                    - Có thêm vật tư phát sinh<br>
                                    - Thi công ống âm tường<br>
                                    - Khoan/khoét lỗ tường gạch bê tông/Tủ gỗ
                                </td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Máy rửa bát<br>Máy hút mùi</td>
                                <td>
                                    - Có thêm vật tư phát sinh
                                </td>
                            </tr>
                            <tr>
                                <td class="highlight-col text-center">Bếp từ/hồng ngoại đa</td>
                                <td>
                                    - Có thêm vật tư phát sinh<br>
                                    - Khoan/cắt ghép đá, bê tông, gỗ (hãng Hafele, Pramie hỗ trợ khoan cắt đá miễn phí)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const btnSmall = document.getElementById('btn-font-small');
        const btnLarge = document.getElementById('btn-font-large');
        const contentSection = document.getElementById('policy-content');

        if(btnSmall && btnLarge && contentSection) {
            btnLarge.addEventListener('click', function () {
                btnLarge.classList.add('active');
                btnSmall.classList.remove('active');
                contentSection.classList.add('large-text');
            });

            btnSmall.addEventListener('click', function () {
                btnSmall.classList.add('active');
                btnLarge.classList.remove('active');
                contentSection.classList.remove('large-text');
            });
        }
    });
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>