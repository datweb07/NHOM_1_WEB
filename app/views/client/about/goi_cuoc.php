<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    body {
        background-color: #ffffff;
    }

    /* BREADCRUMB */
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

    /* NỘI DUNG CHÍNH */
    .content-section {
        padding-left: 10px;
    }

    .content-section h3,
    .content-section h4,
    .content-section h5,
    .content-section p,
    .content-section ul li,
    .content-section table {
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
        color: #cb1c22;
        margin-top: 30px;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .content-section h5 {
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 10px;
        font-size: 16px;
        color: #212529;
    }

    .content-section p {
        text-align: justify;
        color: #495057;
        line-height: 1.6;
        margin-bottom: 15px;
        font-size: 14.5px;
    }

    .content-section a {
        color: #0056b3;
        text-decoration: none;
        font-weight: 500;
    }

    .content-section a:hover {
        text-decoration: underline;
    }

    /* BẢNG BIỂU (TABLE) */
    .content-section .table {
        margin-bottom: 25px;
        font-size: 14px;
    }

    .content-section .table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: 600;
        vertical-align: middle;
        border-color: #dee2e6;
    }

    .content-section .table td {
        color: #495057;
        vertical-align: middle;
        border-color: #dee2e6;
    }

    .group-row {
        background-color: #fce8e9 !important;
        color: #cb1c22 !important;
        font-weight: 600;
    }

    /* BỐ CỤC KHỐI CHÚ THÍCH */
    .note-box {
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .note-box p {
        margin-bottom: 5px;
    }

    /* TRẠNG THÁI CỠ CHỮ LỚN */
    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h4 { font-size: 20px; }
    .content-section.large-text h5 { font-size: 18px; }
    .content-section.large-text p,
    .content-section.large-text ul li,
    .content-section.large-text .table {
        font-size: 16px;
        line-height: 1.7;
    }
</style>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/ho-tro">Hỗ trợ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách gói cước di động FPT</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'goi-cuoc'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách gói cước di động FPT</h3>

                <h4>I. GÓI CƯỚC TRONG NƯỚC</h4>
                
                <div class="note-box">
                    <p><strong>Cú pháp Đăng ký:</strong> Soạn DK &lt;Tên gói&gt; gửi 9199</p>
                    <p><strong>Cú pháp Hủy:</strong> Soạn HUY &lt;Tên gói&gt; gửi 9199</p>
                    <p><strong>Cú pháp Kiểm tra KM:</strong> KT ALL gửi 9199</p>
                </div>

                <h5>A. NHÓM GÓI NỀN CƠ BẢN</h5>
                <p><em>(Đăng ký đơn lẻ, không đăng ký được đồng thời với nhau. Tại 01 thời điểm chỉ được đăng ký tối đa 01 gói cước thuộc nhóm gói nền cơ bản. Đăng ký đồng thời được với gói đệm cơ bản (add-on))</em></p>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th>TT</th>
                                <th>Mã gói</th>
                                <th>Giá gói (VNĐ)</th>
                                <th>Chu kỳ</th>
                                <th>Thông tin gói cước/ 01 chu kỳ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" class="group-row text-center">1. Nhóm gói F69</td></tr>
                            <tr>
                                <td class="text-center">1.1</td>
                                <td class="text-center fw-bold text-danger">F69</td>
                                <td class="text-center">69,000 đ</td>
                                <td class="text-center">30 ngày</td>
                                <td>- Data: 2GB tốc độ cao/ngày<br><em>Hết dung lượng tốc độ cao, ngắt kết nối Internet</em></td>
                            </tr>
                            <tr>
                                <td class="text-center">1.2</td>
                                <td class="text-center fw-bold text-danger">3F69</td>
                                <td class="text-center">207,000 đ</td>
                                <td class="text-center">30 ngày x 3 chu kỳ</td>
                                <td>- Data: 2GB tốc độ cao/ngày<br><em>Hết dung lượng tốc độ cao, ngắt kết nối Internet</em></td>
                            </tr>
                            <tr>
                                <td class="text-center">1.3</td>
                                <td class="text-center fw-bold text-danger">6F69</td>
                                <td class="text-center">414,000 đ</td>
                                <td class="text-center">30 ngày x 7 chu kỳ</td>
                                <td>- Data: 2GB tốc độ cao/ngày<br><em>Hết dung lượng tốc độ cao, ngắt kết nối Internet</em></td>
                            </tr>
                            <tr>
                                <td class="text-center">1.4</td>
                                <td class="text-center fw-bold text-danger">12F69</td>
                                <td class="text-center">828,000 đ</td>
                                <td class="text-center">30 ngày x 14 chu kỳ</td>
                                <td>- Data: 2GB tốc độ cao/ngày<br><em>Hết dung lượng tốc độ cao, ngắt kết nối Internet</em></td>
                            </tr>

                            <tr><td colspan="5" class="group-row text-center">2. Nhóm gói F79</td></tr>
                            <tr>
                                <td class="text-center">2.1</td>
                                <td class="text-center fw-bold text-danger">F79</td>
                                <td class="text-center">79,000 đ</td>
                                <td class="text-center">30 ngày</td>
                                <td>- Data: 3GB tốc độ cao/ngày<br><em>Hết dung lượng tốc độ cao, ngắt kết nối Internet</em></td>
                            </tr>
                            <tr>
                                <td class="text-center">2.2</td>
                                <td class="text-center fw-bold text-danger">3F79</td>
                                <td class="text-center">237,000 đ</td>
                                <td class="text-center">30 ngày x 3 chu kỳ</td>
                                <td>- Data: 3GB tốc độ cao/ngày<br><em>Hết dung lượng tốc độ cao, ngắt kết nối Internet</em></td>
                            </tr>

                            <tr><td colspan="5" class="group-row text-center">3. Nhóm gói F89</td></tr>
                            <tr>
                                <td class="text-center">3.1</td>
                                <td class="text-center fw-bold text-danger">F89</td>
                                <td class="text-center">89,000 đ</td>
                                <td class="text-center">30 ngày</td>
                                <td>- Data: 3GB tốc độ cao/ngày<br>- Thoại nội mạng FPT & MobiFone: 500 phút/chu kỳ.</td>
                            </tr>
                            <tr>
                                <td class="text-center">3.2</td>
                                <td class="text-center fw-bold text-danger">3F89</td>
                                <td class="text-center">267,000 đ</td>
                                <td class="text-center">30 ngày x 3 chu kỳ</td>
                                <td>- Data: 3GB tốc độ cao/ngày<br>- Thoại nội mạng FPT & MobiFone: 500 phút/chu kỳ.</td>
                            </tr>

                            <tr><td colspan="5" class="text-center text-muted fst-italic">... (Xem thêm tại ứng dụng FPT Shop) ...</td></tr>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">

                <h5>B. NHÓM GÓI ĐỆM CƠ BẢN (ADD-ON)</h5>
                <p><em>(Đăng ký được đơn lẻ, không đăng ký được đồng thời với nhau. Đăng ký đồng thời được với gói nền cơ bản)</em></p>

                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Mã gói</th>
                                <th>Giá (VNĐ)</th>
                                <th>Chu kỳ</th>
                                <th>Ưu đãi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-danger">F5D</td>
                                <td>5.000 đ</td>
                                <td>1 ngày</td>
                                <td class="text-start">Data: 1GB tốc độ cao/ngày</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-danger">F10D</td>
                                <td>10.000 đ</td>
                                <td>3 ngày</td>
                                <td class="text-start">Data: 8GB tốc độ cao/3 ngày</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-danger">F25D</td>
                                <td>25.000 đ</td>
                                <td>7 ngày</td>
                                <td class="text-start">- Thoại nội mạng FPT & MobiFone: 300 phút<br>- Thoại ngoại mạng: 30 phút</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-danger">FFB</td>
                                <td>10.000 đ</td>
                                <td>30 ngày</td>
                                <td class="text-start">Miễn phí data truy cập Facebook</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="text-danger fw-bold">Khi Khách hàng mua thiết bị tại FPT Shop, khách hàng có quyền lựa chọn gói cước bảo hành thiết bị 1 đổi 1 theo tháng.</p>

                <h4 class="mt-5">II. GÓI CƯỚC QUỐC TẾ</h4>
                <div class="note-box">
                    <p><strong>Cú pháp Đăng ký:</strong> DK &lt;Tên gói&gt; gửi 9199</p>
                    <p><strong>Lưu ý:</strong> Trước khi đăng ký gói cước, cần thực hiện đăng ký CVQT và bật Data Roaming.</p>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Tên gói</th>
                                <th>Giá (VNĐ)</th>
                                <th>Phạm vi</th>
                                <th>Thông tin ưu đãi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-danger">F250R</td>
                                <td>250.000</td>
                                <td>7 nước ASEAN</td>
                                <td class="text-start">- Thời hạn: 15 ngày<br>- Dung lượng: 3.5GB data</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-danger">F350R</td>
                                <td>350.000</td>
                                <td>42 quốc gia</td>
                                <td class="text-start">- Thời hạn: 03 ngày<br>- 05 GB đầu tốc độ cao. Vượt quá giảm xuống 128Kbps.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-danger">F500R</td>
                                <td>500.000</td>
                                <td>63 quốc gia</td>
                                <td class="text-start">- Thời hạn: 15 ngày<br>- Dung lượng: 02 GB data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5>Phạm vi cung cấp chi tiết:</h5>
                <ul class="nav nav-tabs" id="roamingTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-danger fw-bold" id="f250r-tab" data-bs-toggle="tab" data-bs-target="#f250r" type="button" role="tab">Gói F250R</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-danger fw-bold" id="f350r-tab" data-bs-toggle="tab" data-bs-target="#f350r" type="button" role="tab">Gói F350R</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-danger fw-bold" id="f500r-tab" data-bs-toggle="tab" data-bs-target="#f500r" type="button" role="tab">Gói F500R</button>
                    </li>
                </ul>
                <div class="tab-content border border-top-0 p-3 mb-4" id="roamingTabContent">
                    <div class="tab-pane fade show active" id="f250r" role="tabpanel">
                        <table class="table table-sm table-striped">
                            <thead><tr><th>TT</th><th>Quốc gia</th><th>Nhà mạng</th></tr></thead>
                            <tbody>
                                <tr><td>1</td><td>Cambodia</td><td>Smart Axiata, Metfone, MobiTel</td></tr>
                                <tr><td>2</td><td>Indonesia</td><td>Telkomsel</td></tr>
                                <tr><td>3</td><td>Laos</td><td>Beeline, Lao Unitel</td></tr>
                                <tr><td>4</td><td>Malaysia</td><td>Celcom, Digi, Maxis</td></tr>
                                <tr><td>5</td><td>Philippines</td><td>Globe PH</td></tr>
                                <tr><td>6</td><td>Singapore</td><td>SingTel</td></tr>
                                <tr><td>7</td><td>Thailand</td><td>AIS Thailand</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="f350r" role="tabpanel">
                        <p class="fst-italic text-muted">Bao gồm 42 quốc gia như Australia, Austria, Bangladesh, Belgium, France, Germany, Hongkong, Italy, Japan, Korea, Russia, USA, UK...</p>
                    </div>
                    <div class="tab-pane fade" id="f500r" role="tabpanel">
                        <p class="fst-italic text-muted">Bao gồm 63 quốc gia trên toàn thế giới...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Logic đổi cỡ chữ
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