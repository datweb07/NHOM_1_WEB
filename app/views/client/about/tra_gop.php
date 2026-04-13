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
    .content-section h5,
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

    .content-section h5 {
        font-weight: bold;
        margin-top: 25px;
        margin-bottom: 12px;
        font-size: 18px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 8px;
    }

    .content-section p {
        text-align: justify;
        color: #495057;
        line-height: 1.6;
        margin-bottom: 15px;
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

    .content-section ul {
        list-style-type: none;
        padding-left: 0;
        margin-bottom: 15px;
    }

    .content-section ul li {
        margin-bottom: 8px;
        color: #495057;
        text-align: justify;
        line-height: 1.6;
        font-size: 15px;
        position: relative;
        padding-left: 18px;
    }

    .content-section ul li::before {
        content: "•";
        font-weight: bold;
        position: absolute;
        left: 0;
        top: -2px;
        font-size: 18px;
    }
    
    .content-section ul.no-bullet li::before {
        content: "";
    }
    .content-section ul.no-bullet li {
        padding-left: 0;
    }

    .content-section .table-container {
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        margin-bottom: 25px;
    }

    .content-section .table {
        margin-bottom: 0;
        font-size: 14.5px;
    }

    .content-section .table th {
        background-color: #f8f9fa;
        color: #212529;
        font-weight: bold;
        vertical-align: middle;
        border-bottom: 2px solid #dee2e6;
        text-align: center;
    }

    .content-section .table td {
        color: #495057;
        vertical-align: middle;
    }

    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h5 { font-size: 20px; }
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
            <li class="breadcrumb-item active" aria-current="page">Chính sách trả góp</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'tra-gop'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách trả góp</h3>

                <p>Nhằm mang tới sự thuận tiện trong quá trình mua hàng, giúp Quý khách nhanh chóng sở hữu sản phẩm mong muốn, đi kèm với đó là các chương trình hấp dẫn. FPT Shop cung cấp dịch vụ trả góp đa dạng, dễ dàng tiếp cận, trong đó bao gồm trả góp qua thẻ tín dụng, trả góp qua Kredivo, trả góp qua Home PayLater và trả góp qua Công ty tài chính.</p>

                <h5>1. Trả góp qua thẻ tín dụng</h5>
                <ul class="no-bullet">
                    <li>Hiệu lực còn lại của thẻ phải lớn hơn kỳ hạn trả góp, riêng MB, Kiên Long Bank thì hiệu lực của thẻ phải lớn hơn kỳ hạn trả góp ít nhất (01) tháng.</li>
                    <li>Số dư thẻ phải lớn hơn hoặc bằng tổng giá trị trả góp.</li>
                    <li>Khách hàng phải nhập đúng số thẻ, ngày hết hạn và số CVV khi thực hiện giao dịch.</li>
                    <li>Thời gian trả góp 3, 6, 9, 12, 15, 18, 24, 36 tháng (tuỳ từng ngân hàng).</li>
                    <li>Số lần mua trả góp tuỳ thuộc vào hạn mức thẻ tín dụng.</li>
                    <li>Giá trị thanh toán phải đạt số tiền trả góp tối thiểu như sau:</li>
                </ul>
                <ul>
                    <li>Từ 500.000đ trở lên với Muadee by HDBank.</li>
                    <li>Từ 1.000.000đ trở lên với NCB, Sacombank.</li>
                    <li>Từ 2.000.000đ trở lên đối với Techcombank, VIB, Home Credit và Lotte Finance.</li>
                    <li>Từ 3.000.000đ trở lên đối với các ngân hàng còn lại.</li>
                </ul>

                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 40%;">NGÂN HÀNG</th>
                                <th>CÁCH THỨC CHUYỂN ĐỔI TRẢ GÓP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-center">Vietcombank, MB, SHB, LPBank, HDBank, PVcomBank, TPBank, Shinhan Finance (SVFC), Mcredit, Woori Bank, Lotte Finance, Home Credit, Standard Chartered, Vietbank.</td>
                                <td>Ngân hàng sẽ không hỗ trợ chuyển đổi trả góp sau khi giao dịch đã lên sao kê.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Các ngân hàng còn lại.</td>
                                <td>
                                    Sau 7 - 10 ngày làm việc hệ thống tự chuyển đổi. Trường hợp trong 7 - 10 ngày chờ chuyển đổi mà tài khoản của Quý khách đã lên sao kê thì khi thanh toán với ngân hàng Quý khách hãy trừ khoản thanh toán này ra.<br>
                                    Ngân hàng hỗ trợ chuyển đổi sau kì sao kê. Thời gian cụ thể sẽ phụ thuộc vào chính sách kinh doanh từ thời kì của Ngân hàng.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="fw-bold text-dark mb-2">Lưu ý:</p>
                <ul class="no-bullet">
                    <li>Không nên giao dịch cận ngày sao kê. Đối với riêng các ngân hàng bao gồm Vietcombank, MB, SHB, LPBank, HDBank, PVcomBank, TPBank, Shinhan Finance (SVFC), Mcredit, Woori Bank, Lotte Finance, Home Credit, Standard Chartered, Vietbank, Ngân Lượng khoá trên hệ thống các ngày gần sao kê tuỳ theo từng Ngân hàng, loại thẻ. Trong thời gian đó chủ thẻ vui lòng sử dụng thẻ của các Ngân hàng còn lại để thực hiện giao dịch trả góp.</li>
                    <li>Chương trình không áp dụng cho thẻ phụ, thẻ Debit và thẻ tín dụng phát hành tại nước ngoài.</li>
                </ul>

                <h5>2. Trả góp qua nhà tài chính</h5>
                <p>Khách hàng mang hồ sơ được yêu cầu tới FPT Shop gần nhất để đăng ký, hoàn tất thủ tục trả góp qua nhà tài chính.</p>

                <div class="table-container">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>CÔNG TY TÀI CHÍNH</th>
                                <th>ĐỘ TUỔI</th>
                                <th>HỒ SƠ</th>
                                <th>YÊU CẦU KHÁC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">HDS</td>
                                <td>18 - 60</td>
                                <td class="text-start">Căn cước/CMND.<br>Bằng lái xe/sổ hộ khẩu.</td>
                                <td class="text-start">Hồ sơ được đơn vị trả góp duyệt.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">HOME CREDIT</td>
                                <td>19 - 60</td>
                                <td class="text-start">Căn cước/CMND.<br>Bằng lái xe/sổ hộ khẩu.</td>
                                <td class="text-start">Hồ sơ được đơn vị trả góp duyệt.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">SHINHAN FINANCE</td>
                                <td>18 - 60</td>
                                <td class="text-start">Căn cước/CMND.</td>
                                <td class="text-start">Hồ sơ được đơn vị trả góp duyệt.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">FE CREDIT</td>
                                <td>Nam: 21 - 60<br>Nữ: 18 - 60</td>
                                <td class="text-start">Căn cước/CMND.<br>Bằng lái xe/sổ hộ khẩu.</td>
                                <td class="text-start">Hồ sơ được đơn vị trả góp duyệt.<br>Thu nhập từ 4.000.000 VNĐ/tháng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">MIRAE ASSET</td>
                                <td>18 - 60</td>
                                <td class="text-start">Căn cước/CMND.<br>Bằng lái xe/sổ hộ khẩu.</td>
                                <td class="text-start">Có tài khoản Kredivo.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">SAMSUNG FINANCE PLUS</td>
                                <td></td>
                                <td class="text-start">Email.<br>SIM chính chủ.</td>
                                <td class="text-start">Hồ sơ được đơn vị trả góp duyệt.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5>3. Trả góp qua Kredivo</h5>
                <p>Khách hàng thanh toán bằng Kredivo – Mua trước trả sau, đăng ký và được duyệt hạn mức trực tiếp trên ứng dụng Kredivo.</p>
                <ul class="no-bullet">
                    <li><strong>Độ tuổi:</strong> 18 – 60 tuổi</li>
                    <li><strong>Hồ sơ:</strong>
                        <ul class="no-bullet mt-1 mb-1">
                            <li>Căn cước công dân/CMND</li>
                            <li>Tài khoản Kredivo đã được xác thực</li>
                        </ul>
                    </li>
                    <li><strong>Yêu cầu khác:</strong>
                        <ul class="no-bullet mt-1 mb-1">
                            <li>Tải ứng dụng Kredivo và đăng ký tài khoản</li>
                            <li>Hệ thống xét duyệt tự động trong vài phút</li>
                        </ul>
                    </li>
                    <li><strong>Hạn mức tín dụng:</strong> từ 1.500.000đ – 50.000.000đ</li>
                    <li><strong>Các kỳ hạn thanh toán:</strong> 1 tháng, 3 tháng, 6 tháng, 9 tháng, 12 tháng</li>
                </ul>

                <h5>4. Trả góp qua Home PayLater</h5>
                <p>Khách hàng có thể sử dụng Home PayLater – Mua trước trả sau để thanh toán tại FPT Shop.</p>
                <ul class="no-bullet">
                    <li><strong>Độ tuổi:</strong> 20 – 60 tuổi</li>
                    <li><strong>Hồ sơ:</strong> Căn cước công dân/CMND</li>
                    <li><strong>Yêu cầu khác:</strong>
                        <ul class="no-bullet mt-1 mb-1">
                            <li>Đăng ký tài khoản Home PayLater</li>
                            <li>Có thể đăng ký online hoặc tại cửa hàng</li>
                            <li>Không bắt buộc cài ứng dụng</li>
                        </ul>
                    </li>
                    <li><strong>Hạn mức tín dụng:</strong> từ 1.000.000đ – 25.000.000đ</li>
                    <li><strong>Các kỳ hạn thanh toán:</strong> 3 tháng, 6 tháng, 12 tháng</li>
                </ul>

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