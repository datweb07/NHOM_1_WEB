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
        background-color: rgba(237, 238, 239, 1) !important;
        font-weight: 600;
        text-transform: uppercase;
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

    /* TABS */
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #cb1c22;
        font-weight: bold;
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

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="text-center">
                            <tr>
                                <th>TT</th>
                                <th>Mã gói</th>
                                <th>Giá gói (VNĐ, đã bao gồm VAT)</th>
                                <th>Chu kỳ</th>
                                <th>Thông tin gói cước/ 01 chu kỳ</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="6" class="group-row">A. NHÓM GÓI NỀN CƠ BẢN (Đăng ký đơn lẻ, không đăng ký được đồng thời với nhau. Tại 01 thời điểm chỉ được đăng ký tối đa 01 gói cước thuộc nhóm gói nền cơ bản. Đăng ký đồng thời được với gói đệm cơ bản (add-on))</td></tr>
                            
                            <tr><td colspan="6" class="bg-light fw-bold">1. Nhóm gói F69</td></tr>
                            <tr><td class="text-center">1.1</td><td class="fw-bold">F69</td><td class="text-center">69,000 đ</td><td class="text-center">30 ngày</td><td>- Data: 2GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">1.2</td><td class="fw-bold">3F69</td><td class="text-center">207,000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Data: 2GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">1.3</td><td class="fw-bold">6F69</td><td class="text-center">414,000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- Data: 2GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet.</td><td></td></tr>
                            <tr><td class="text-center">1.4</td><td class="fw-bold">12F69</td><td class="text-center">828,000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- Data: 2GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">2. Nhóm gói F79</td></tr>
                            <tr><td class="text-center">2.1</td><td class="fw-bold">F79</td><td class="text-center">79.000 đ</td><td class="text-center">30 ngày</td><td>- Data: 3GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">2.2</td><td class="fw-bold">3F79</td><td class="text-center">237.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Data: 3GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">2.3</td><td class="fw-bold">6F79</td><td class="text-center">474.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- Data: 3GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">2.4</td><td class="fw-bold">12F79</td><td class="text-center">948.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- Data: 3GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">3. Nhóm gói F89</td></tr>
                            <tr><td class="text-center">3.1</td><td class="fw-bold">F89</td><td class="text-center">89.000 đ</td><td class="text-center">30 ngày</td><td>- Data: 3GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">3.2</td><td class="fw-bold">3F89</td><td class="text-center">267.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Data: 3GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">3.3</td><td class="fw-bold">6F89</td><td class="text-center">534.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- Data: 3GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">3.4</td><td class="fw-bold">12F89</td><td class="text-center">1.068.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- Data: 3GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">4. Nhóm gói F99</td></tr>
                            <tr><td class="text-center">4.1</td><td class="fw-bold">F99</td><td class="text-center">99.000 đ</td><td class="text-center">30 ngày</td><td>- Data: 5GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">4.2</td><td class="fw-bold">3F99</td><td class="text-center">297.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Data: 5GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">4.3</td><td class="fw-bold">6F99</td><td class="text-center">594.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- Data: 5GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">4.4</td><td class="fw-bold">12F99</td><td class="text-center">1.188.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- Data: 5GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">5. Nhóm gói F99T</td></tr>
                            <tr><td class="text-center">5.1</td><td class="fw-bold">F99T</td><td class="text-center">99.000 đ</td><td class="text-center">30 ngày</td><td>- Data: 2GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>- Thoại trong nước từ thuê bao FPT tới mạng khác ngoài FPT và MobiFone: 60 phút/chu kỳ.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">5.2</td><td class="fw-bold">3F99T</td><td class="text-center">297.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Data: 2GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>- Thoại trong nước từ thuê bao FPT tới mạng khác ngoài FPT và MobiFone: 60 phút/chu kỳ.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">5.3</td><td class="fw-bold">6F99T</td><td class="text-center">594.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- Data: 2GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>- Thoại trong nước từ thuê bao FPT tới mạng khác ngoài FPT và MobiFone: 60 phút/chu kỳ.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">5.4</td><td class="fw-bold">12F99T</td><td class="text-center">1.188.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- Data: 2GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>- Thoại trong nước từ thuê bao FPT tới mạng khác ngoài FPT và MobiFone: 60 phút/chu kỳ.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">6. Nhóm gói F99S</td></tr>
                            <tr><td class="text-center">6.1</td><td class="fw-bold">F99S</td><td class="text-center">99.000 đ</td><td class="text-center">30 ngày</td><td>- Data: 2GB tốc độ cao/ngày.<br>- Khác: Miễn phí data truy cập Youtube, Facebook, Tiktok.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">6.2</td><td class="fw-bold">3F99S</td><td class="text-center">297.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Data: 2GB tốc độ cao/ngày.<br>- Khác: Miễn phí data truy cập Youtube, Facebook, Tiktok.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">6.3</td><td class="fw-bold">6F99S</td><td class="text-center">594.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- Data: 2GB tốc độ cao/ngày.<br>- Khác: Miễn phí data truy cập Youtube, Facebook, Tiktok.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">6.4</td><td class="fw-bold">12F99S</td><td class="text-center">1.188.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- Data: 2GB tốc độ cao/ngày.<br>- Khác: Miễn phí data truy cập Youtube, Facebook, Tiktok.<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">7. Nhóm gói FVIP150</td></tr>
                            <tr><td class="text-center">7.1</td><td class="fw-bold">FVIP150</td><td class="text-center">150.000 đ</td><td class="text-center">30 ngày</td><td>- Data: 5GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>- Thoại trong nước từ thuê bao FPT tới mạng khác ngoài FPT và MobiFone: 200 phút/chu kỳ<br>- Miễn phí data truy cập ứng dụng Youtube, Tiktok, Facebook trong 01 chu kỳ đầu (Mỗi thuê bao chỉ được ưu đãi miễn phí data truy cập các ứng dụng trên 1 lần khi đăng ký gói lần đầu, không áp dụng cho gia hạn)<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">7.2</td><td class="fw-bold">3FVIP150</td><td class="text-center">450.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Data: 5GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>- Thoại trong nước từ thuê bao FPT tới mạng khác ngoài FPT và MobiFone: 200 phút/chu kỳ<br>- Miễn phí data truy cập ứng dụng Youtube, Tiktok, Facebook trong 03 chu kỳ đầu (Mỗi thuê bao chỉ được ưu đãi miễn phí data truy cập các ứng dụng trên 1 lần khi đăng ký gói lần đầu, không áp dụng cho gia hạn)<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">7.3</td><td class="fw-bold">6FVIP150</td><td class="text-center">900.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- Data: 5GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>- Thoại trong nước từ thuê bao FPT tới mạng khác ngoài FPT và MobiFone: 200 phút/chu kỳ<br>- Miễn phí data truy cập ứng dụng Youtube, Tiktok, Facebook trong 07 chu kỳ đầu (Mỗi thuê bao chỉ được ưu đãi miễn phí data truy cập các ứng dụng trên 1 lần khi đăng ký gói lần đầu, không áp dụng cho gia hạn)<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">7.4</td><td class="fw-bold">12FVIP150</td><td class="text-center">1.800.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- Data: 5GB tốc độ cao/ngày<br>- Thoại nội mạng FPT và tới MobiFone trong nước: 500 phút/chu kỳ.<br>- Thoại trong nước từ thuê bao FPT tới mạng khác ngoài FPT và MobiFone: 200 phút/chu kỳ<br>- Miễn phí data truy cập ứng dụng Youtube, Tiktok, Facebook trong 14 chu kỳ đầu (Mỗi thuê bao chỉ được ưu đãi miễn phí data truy cập các ứng dụng trên 1 lần khi đăng ký gói lần đầu, không áp dụng cho gia hạn)<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">8. Gói FTV119</td></tr>
                            <tr><td class="text-center">8.1</td><td class="fw-bold">FTV119 (Tạm ngừng kinh doanh)</td><td class="text-center">119.000đ</td><td class="text-center">30 ngày</td><td>- Data: 2GB/ngày<br>- Miễn phí Data tốc độ cao truy cập ứng dụng FPT Play<br>Cung cấp các dịch vụ phổ biến xem truyền hình, thể thao, phim truyện và giải trí trên FPT Play:<br>- Hơn 140 kênh truyền hình trong nước và quốc tế (ngoại trừ kênh K+)<br>- Trực tiếp & độc quyền bóng đá cùng các giải thể thao khác<br>- Kho phim bộ & phim lẻ (ngoại trừ HBO Go, Phim thuê)<br>- Kho TV show và các chương trình đặc sắc khác do FPT Play sản xuất<br>- Kho phim Anime phong phú<br>- Kho nội dung thiếu nhi, hoạt hình, học hành từ lớp 1 đến 12.<br>- Tắt quảng cáo<br>- Số lượng thiết bị kích hoạt và xem đồng thời: Đăng nhập và xem cùng lúc trên 2 thiết bị mobile (bao gồm Phone và Tablet).</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">9. Nhóm gói YTE</td></tr>
                            <tr><td class="text-center">9.1</td><td class="fw-bold">YTE</td><td class="text-center">129.000 đ</td><td class="text-center">30 ngày</td><td>- Tặng gói tư vấn sức khỏe online: miễn phí 2 lượt tư vấn với bác sĩ chuyên khoa, chăm sóc sức khỏe chủ động với bác sĩ gia đình<br>- Data: 2GB data/ngày<br>- Miễn phí data truy cập app Long Châu, TrueDoc, FPTShop<br>- Freeship khi mua thuốc, nhắc nhở lịch uống thuốc và nhận tư vấn từ dược sỹ Long Châu qua ứng dụng Long Châu<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">9.2</td><td class="fw-bold">3YTE</td><td class="text-center">387.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Tặng gói tư vấn sức khỏe online: miễn phí 6 lượt tư vấn với bác sĩ chuyên khoa, chăm sóc sức khỏe chủ động với bác sĩ gia đình<br>- Data: 2GB data/ngày<br>- Miễn phí data truy cập app Long Châu, TrueDoc, FPTShop<br>- Freeship khi mua thuốc, nhắc nhở lịch uống thuốc và nhận tư vấn từ dược sỹ Long Châu qua ứng dụng Long Châu<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">9.3</td><td class="fw-bold">6YTE</td><td class="text-center">774.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- Tặng gói tư vấn sức khỏe online: miễn phí tư vấn sức khỏe online, chăm sóc sức khỏe chủ động với bác sĩ gia đình không giới hạn số lượt.<br>- Data: 2GB data/ngày<br>- Miễn phí data truy cập app Long Châu, TrueDoc, FPTShop<br>- Freeship khi mua thuốc, nhắc nhở lịch uống thuốc và nhận tư vấn từ dược sỹ Long Châu qua ứng dụng Long Châu<br>- Giảm giá 10% khi đặt mua sản phẩm sức khỏe trên app TrueDoc<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">9.4</td><td class="fw-bold">12YTE</td><td class="text-center">1.548.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- Tặng gói tư vấn sức khỏe online: miễn phí tư vấn sức khỏe online, chăm sóc sức khỏe chủ động với bác sĩ gia đình không giới hạn số lượt.<br>- Data: 2GB data/ngày<br>- Miễn phí data truy cập app Long Châu, TrueDoc, FPTShop<br>- Freeship khi mua thuốc, nhắc nhở lịch uống thuốc và nhận tư vấn từ dược sỹ Long Châu qua ứng dụng Long Châu<br>- Giảm giá 10% khi đặt mua sản phẩm sức khỏe trên app TrueDoc<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">10. Nhóm gói YTEVIP</td></tr>
                            <tr><td class="text-center">10.1</td><td class="fw-bold">YTEVIP</td><td class="text-center">179.000 đ</td><td class="text-center">30 ngày</td><td>- Tặng gói tư vấn sức khỏe online: miễn phí 3 lượt tư vấn với bác sĩ chuyên khoa, chăm sóc sức khỏe chủ động với bác sĩ gia đình<br>- Data: 2GB data/ngày<br>- Thoại: 500ph nội mạng, 30ph ngoại mạng<br>- Miễn phí data truy cập app Long Châu, TrueDoc, FPTShop<br>- Freeship khi mua thuốc, nhắc nhở lịch uống thuốc và nhận tư vấn từ dược sỹ Long Châu qua ứng dụng Long Châu<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">10.2</td><td class="fw-bold">3YTEVIP</td><td class="text-center">537.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Tặng gói tư vấn sức khỏe online: miễn phí 9 lượt tư vấn với bác sĩ chuyên khoa, chăm sóc sức khỏe chủ động với bác sĩ gia đình<br>- Data: 2GB data/ngày<br>- Thoại: 500ph nội mạng, 30ph ngoại mạng<br>- Miễn phí data truy cập app Long Châu, TrueDoc, FPTShop<br>- Freeship khi mua thuốc, nhắc nhở lịch uống thuốc và nhận tư vấn từ dược sỹ Long Châu qua ứng dụng Long Châu<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">10.3</td><td class="fw-bold">6YTEVIP</td><td class="text-center">1.074.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- Tặng gói tư vấn sức khỏe online: miễn phí tư vấn sức khỏe online, chăm sóc sức khỏe chủ động với bác sĩ gia đình không giới hạn số lượt.<br>- Data: 2GB data/ngày<br>- Thoại: 500ph nội mạng, 30ph ngoại mạng<br>- Miễn phí data truy cập app Long Châu, TrueDoc, FPTShop<br>- Freeship khi mua thuốc, nhắc nhở lịch uống thuốc và nhận tư vấn từ dược sỹ Long Châu qua ứng dụng Long Châu<br>- Giảm giá 10% khi đặt mua sản phẩm sức khỏe trên app TrueDoc<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">10.4</td><td class="fw-bold">12YTEVIP</td><td class="text-center">2.148.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- Tặng gói tư vấn sức khỏe online: miễn phí tư vấn sức khỏe online, chăm sóc sức khỏe chủ động với bác sĩ gia đình không giới hạn số lượt.<br>- Tặng 1 lần xét nghiệm tại nhà miễn phí: Đánh giá sức khỏe, tầm soát các bệnh lý về máu, gan, thận và chuyển hóa<br>- Data: 2GB data/ngày<br>- Thoại: 500ph nội mạng, 30ph ngoại mạng<br>- Miễn phí data truy cập app Long Châu, TrueDoc, FPTShop<br>- Freeship khi mua thuốc, nhắc nhở lịch uống thuốc và nhận tư vấn từ dược sỹ Long Châu qua ứng dụng Long Châu<br>- Giảm giá 10% khi đặt mua sản phẩm sức khỏe trên app TrueDoc<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">11. Nhóm gói F299</td></tr>
                            <tr><td class="text-center">11.1</td><td class="fw-bold">F299</td><td class="text-center">299.000 đ</td><td class="text-center">30 ngày</td><td>- Data: 10GB tốc độ cao/ngày<br>- Thoại: 1500 phút gọi nội mạng và 200 phút gọi ngoại mạng/chu kỳ<br>- Free Data truy cập Netflix, TikTok, YouTube, Facebook<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">11.2</td><td class="fw-bold">3F299</td><td class="text-center">897.000 đ</td><td class="text-center">3 chu kỳ x 30 ngày</td><td>- Data: 10GB tốc độ cao/ngày<br>- Thoại: 1500 phút gọi nội mạng và 200 phút gọi ngoại mạng/chu kỳ<br>- Free Data truy cập Netflix, TikTok, YouTube, Facebook<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">11.3</td><td class="fw-bold">6F299</td><td class="text-center">1.794.000 đ</td><td class="text-center">6 chu kỳ x 30 ngày</td><td>- Data: 10GB tốc độ cao/ngày<br>- Thoại: 1500 phút gọi nội mạng và 200 phút gọi ngoại mạng/chu kỳ<br>- Free Data truy cập Netflix, TikTok, YouTube, Facebook<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">11.4</td><td class="fw-bold">12F299</td><td class="text-center">3.588.000 đ</td><td class="text-center">14 chu kỳ x 30 ngày</td><td>- Data: 10GB tốc độ cao/ngày<br>- Thoại: 1500 phút gọi nội mạng và 200 phút gọi ngoại mạng/chu kỳ<br>- Free Data truy cập Netflix, TikTok, YouTube, Facebook<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">12. Nhóm gói F399</td></tr>
                            <tr><td class="text-center">12.1</td><td class="fw-bold">F399</td><td class="text-center">399.000 đ</td><td class="text-center">30 ngày</td><td>- Data: 20GB tốc độ cao/ngày<br>- Thoại: 1500 phút gọi nội mạng và 300 phút gọi ngoại mạng/chu kỳ<br>- Free Data truy cập Netflix, TikTok, YouTube, Facebook<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">12.2</td><td class="fw-bold">3F399</td><td class="text-center">1.197.000 đ</td><td class="text-center">3 chu kỳ x 30 ngày</td><td>- Data: 20GB tốc độ cao/ngày<br>- Thoại: 1500 phút gọi nội mạng và 300 phút gọi ngoại mạng/chu kỳ<br>- Free Data truy cập Netflix, TikTok, YouTube, Facebook<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">12.3</td><td class="fw-bold">6F399</td><td class="text-center">2.394.000 đ</td><td class="text-center">6 chu kỳ x 30 ngày</td><td>- Data: 20GB tốc độ cao/ngày<br>- Thoại: 1500 phút gọi nội mạng và 300 phút gọi ngoại mạng/chu kỳ<br>- Free Data truy cập Netflix, TikTok, YouTube, Facebook<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">12.4</td><td class="fw-bold">12F399</td><td class="text-center">4.788.000 đ</td><td class="text-center">14 chu kỳ x 30 ngày</td><td>- Data: 20GB tốc độ cao/ngày<br>- Thoại: 1500 phút gọi nội mạng và 300 phút gọi ngoại mạng/chu kỳ<br>- Free Data truy cập Netflix, TikTok, YouTube, Facebook<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">13. Nhóm gói cước VieON99</td></tr>
                            <tr><td class="text-center">13.1</td><td class="fw-bold">VO99</td><td class="text-center">99.000 đ</td><td class="text-center">30 ngày</td><td>- Tặng tài khoản gói VIP VieON<br>- Data: 2GB/ngày<br>- Free data truy cập ứng dụng VieON<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">13.2</td><td class="fw-bold">3VO99</td><td class="text-center">297.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Tặng tài khoản gói VIP VieON<br>- Data: 2GB/ngày<br>- Free data truy cập ứng dụng VieON<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">13.3</td><td class="fw-bold">6VO99</td><td class="text-center">594.000 đ</td><td class="text-center">30 ngày x 6 chu kỳ</td><td>- Tặng tài khoản gói VIP VieON<br>- Data: 2GB/ngày<br>- Free data truy cập ứng dụng VieON<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">13.4</td><td class="fw-bold">12VO99</td><td class="text-center">1.188.000 đ</td><td class="text-center">30 ngày x 12 chu kỳ</td><td>- Tặng tài khoản gói VIP VieON<br>- Data: 2GB/ngày<br>- Free data truy cập ứng dụng VieON<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">14. Nhóm gói cước VieON119</td></tr>
                            <tr><td class="text-center">14.1</td><td class="fw-bold">VO119</td><td class="text-center">119.000 đ</td><td class="text-center">30 ngày</td><td>- Tặng tài khoản gói VIP VieON<br>- Data: 4GB/ngày<br>- Free data truy cập ứng dụng VieON<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">14.2</td><td class="fw-bold">3VO119</td><td class="text-center">357.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- Tặng tài khoản gói VIP VieON<br>- Data: 4GB/ngày<br>- Free data truy cập ứng dụng VieON<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">14.3</td><td class="fw-bold">6VO119</td><td class="text-center">714.000 đ</td><td class="text-center">30 ngày x 6 chu kỳ</td><td>- Tặng tài khoản gói VIP VieON<br>- Data: 4GB/ngày<br>- Free data truy cập ứng dụng VieON<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">14.4</td><td class="fw-bold">12VO119</td><td class="text-center">1.428.000 đ</td><td class="text-center">30 ngày x 12 chu kỳ</td><td>- Tặng tài khoản gói VIP VieON<br>- Data: 4GB/ngày<br>- Free data truy cập ứng dụng VieON<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">15. Nhóm gói cước Tân Binh</td></tr>
                            <tr><td class="text-center">15.1</td><td class="fw-bold">FPT69</td><td class="text-center">69.000 đ</td><td class="text-center">30 ngày</td><td>- 1GB/ngày<br>- Miễn phí data truy cập Youtube, TikTok<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">15.2</td><td class="fw-bold">3FPT69</td><td class="text-center">207.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- 1GB/ngày<br>- Miễn phí data truy cập Youtube, TikTok<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">15.3</td><td class="fw-bold">6FPT69</td><td class="text-center">414.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- 1GB/ngày<br>- Miễn phí data truy cập Youtube, TikTok<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">15.4</td><td class="fw-bold">12FPT69</td><td class="text-center">828.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- 1GB/ngày<br>- Miễn phí data truy cập Youtube, TikTok<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">16. Nhóm gói cước Bao rẻ 89</td></tr>
                            <tr><td class="text-center">16.1</td><td class="fw-bold">FPT89</td><td class="text-center">89.000 đ</td><td class="text-center">30 ngày</td><td>- 5GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">16.2</td><td class="fw-bold">3FPT89</td><td class="text-center">267.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- 5GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">16.3</td><td class="fw-bold">6FPT89</td><td class="text-center">534.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- 5GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">16.4</td><td class="fw-bold">12FPT89</td><td class="text-center">1.068.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- 5GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">17. Nhóm gói cước Bao nét 99</td></tr>
                            <tr><td class="text-center">17.1</td><td class="fw-bold">FPT99</td><td class="text-center">99.000 đ</td><td class="text-center">30 ngày</td><td>- 6GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">17.2</td><td class="fw-bold">3FPT99</td><td class="text-center">297.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- 6GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">17.3</td><td class="fw-bold">6FPT99</td><td class="text-center">594.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- 6GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">17.4</td><td class="fw-bold">12FPT99</td><td class="text-center">1.188.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- 6GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">18. Nhóm gói cước Bao xịn 109</td></tr>
                            <tr><td class="text-center">18.1</td><td class="fw-bold">FPT109</td><td class="text-center">109.000 đ</td><td class="text-center">30 ngày</td><td>- 8GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">18.2</td><td class="fw-bold">3FPT109</td><td class="text-center">327.000 đ</td><td class="text-center">30 ngày x 3 chu kỳ</td><td>- 8GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">18.3</td><td class="fw-bold">6FPT109</td><td class="text-center">654.000 đ</td><td class="text-center">30 ngày x 7 chu kỳ</td><td>- 8GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>
                            <tr><td class="text-center">18.4</td><td class="fw-bold">12FPT109</td><td class="text-center">1.308.000 đ</td><td class="text-center">30 ngày x 14 chu kỳ</td><td>- 8GB data tốc độ cao/ngày<br>- Miễn phí 100 phút gọi nội mạng/ chu kỳ<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">19. Gói cước TD149</td></tr>
                            <tr><td class="text-center"></td><td class="fw-bold">TD149</td><td class="text-center">149.000 đ</td><td class="text-center">30 ngày</td><td>- 5GB tốc độ cao/ngày<br>- Thoại nội mạng: 1000 phút<br>- Thoại ngoại mạng: 30 phút<br>- Miễn phí data truy cập ứng dụng FPT Shop, Long Châu<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">20. Gói cước TD199</td></tr>
                            <tr><td class="text-center"></td><td class="fw-bold">TD199</td><td class="text-center">199.000 đ</td><td class="text-center">30 ngày</td><td>- 8GB tốc độ cao/ngày<br>- Thoại nội mạng: 1500 phút<br>- Thoại ngoại mạng: 50 phút<br>- Miễn phí data truy cập ứng dụng FPT Shop, Long Châu<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">21. Nhóm gói Galaxy99</td></tr>
                            <tr><td class="text-center">21.1</td><td class="fw-bold">GX99</td><td class="text-center">99,000</td><td class="text-center">30 ngày</td><td>- Tặng gói Galaxy Play Platinum<br>- Data: 2GB/ngày<br>- Miễn phí data truy cập Galaxy Play</td><td></td></tr>
                            <tr><td class="text-center">21.2</td><td class="fw-bold">3GX99</td><td class="text-center">297,000</td><td class="text-center">3 chu kỳ x 30 ngày</td><td>- Tặng gói Galaxy Play Platinum<br>- Data: 2GB/ngày<br>- Miễn phí data truy cập Galaxy Play</td><td></td></tr>
                            <tr><td class="text-center">21.3</td><td class="fw-bold">6GX99</td><td class="text-center">594,000</td><td class="text-center">6 chu kỳ x 30 ngày</td><td>- Tặng gói Galaxy Play Platinum<br>- Data: 2GB/ngày<br>- Miễn phí data truy cập Galaxy Play</td><td></td></tr>
                            <tr><td class="text-center">21.4</td><td class="fw-bold">12GX99</td><td class="text-center">1,188,000</td><td class="text-center">12 chu kỳ x 30 ngày</td><td>- Tặng gói Galaxy Play Platinum<br>- Data: 2GB/ngày<br>- Miễn phí data truy cập Galaxy Play</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">22. Nhóm gói Galaxy119</td></tr>
                            <tr><td class="text-center">22.1</td><td class="fw-bold">GX119</td><td class="text-center">119,000</td><td class="text-center">30 ngày</td><td>- Tặng gói Galaxy Play Platinum<br>- Data: 4GB/ngày<br>- Miễn phí data truy cập Galaxy Play</td><td></td></tr>
                            <tr><td class="text-center">22.2</td><td class="fw-bold">3GX119</td><td class="text-center">357,000</td><td class="text-center">3 chu kỳ x 30 ngày</td><td>- Tặng gói Galaxy Play Platinum<br>- Data: 4GB/ngày<br>- Miễn phí data truy cập Galaxy Play</td><td></td></tr>
                            <tr><td class="text-center">22.3</td><td class="fw-bold">6GX119</td><td class="text-center">714,000</td><td class="text-center">6 chu kỳ x 30 ngày</td><td>- Tặng gói Galaxy Play Platinum<br>- Data: 4GB/ngày<br>- Miễn phí data truy cập Galaxy Play</td><td></td></tr>
                            <tr><td class="text-center">22.4</td><td class="fw-bold">12GX119</td><td class="text-center">4,284,000</td><td class="text-center">12 chu kỳ x 30 ngày</td><td>- Tặng gói Galaxy Play Platinum<br>- Data: 4GB/ngày<br>- Miễn phí data truy cập Galaxy Play</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">23. Nhóm gói F199</td></tr>
                            <tr><td class="text-center">23.1</td><td class="fw-bold">F199</td><td class="text-center">199.000</td><td class="text-center">30 ngày</td><td>- Data 8GB/ngày<br>- Thoại nội mạng: miễn phí cuộc gọi dưới 10 phút, tối đa 750 phút/chu kỳ<br>- Thoại ngoại mạng: 200 phút/chu kỳ<br>- Miễn phí data truy cập Spotify, Youtube, Tiktok, Facebook</td><td></td></tr>
                            <tr><td class="text-center">23.2</td><td class="fw-bold">3F199</td><td class="text-center">597.000</td><td class="text-center">3 chu kỳ x 30 ngày</td><td>- Data 8GB/ngày<br>- Thoại nội mạng: miễn phí cuộc gọi dưới 10 phút, tối đa 750 phút/chu kỳ<br>- Thoại ngoại mạng: 200 phút/chu kỳ<br>- Miễn phí data truy cập Spotify, Youtube, Tiktok, Facebook</td><td></td></tr>
                            <tr><td class="text-center">23.3</td><td class="fw-bold">6F199</td><td class="text-center">1.194.000</td><td class="text-center">6 chu kỳ x 30 ngày</td><td>- Data 8GB/ngày<br>- Thoại nội mạng: miễn phí cuộc gọi dưới 10 phút, tối đa 750 phút/chu kỳ<br>- Thoại ngoại mạng: 200 phút/chu kỳ<br>- Miễn phí data truy cập Spotify, Youtube, Tiktok, Facebook</td><td></td></tr>
                            <tr><td class="text-center">23.4</td><td class="fw-bold">12F199</td><td class="text-center">2.388.000</td><td class="text-center">14 chu kỳ x 30 ngày</td><td>- Data 8GB/ngày<br>- Thoại nội mạng: miễn phí cuộc gọi dưới 10 phút, tối đa 750 phút/chu kỳ<br>- Thoại ngoại mạng: 200 phút/chu kỳ<br>- Miễn phí data truy cập Spotify, Youtube, Tiktok, Facebook</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">24. Nhóm gói F249</td></tr>
                            <tr><td class="text-center">24.1</td><td class="fw-bold">F249</td><td class="text-center">249.000</td><td class="text-center">30 ngày</td><td>- Data 9GB/ngày<br>- Thoại nội mạng: miễn phí cuộc gọi dưới 10 phút, tối đa 1000 phút/chu kỳ<br>- Thoại ngoại mạng: 200 phút/chu kỳ<br>- Miễn phí data truy cập Netflix, Youtube, Tiktok, Facebook</td><td></td></tr>
                            <tr><td class="text-center">24.2</td><td class="fw-bold">3F249</td><td class="text-center">747.000</td><td class="text-center">3 chu kỳ x 30 ngày</td><td>- Data 9GB/ngày<br>- Thoại nội mạng: miễn phí cuộc gọi dưới 10 phút, tối đa 1000 phút/chu kỳ<br>- Thoại ngoại mạng: 200 phút/chu kỳ<br>- Miễn phí data truy cập Netflix, Youtube, Tiktok, Facebook</td><td></td></tr>
                            <tr><td class="text-center">24.3</td><td class="fw-bold">6F249</td><td class="text-center">1.494.000</td><td class="text-center">6 chu kỳ x 30 ngày</td><td>- Data 9GB/ngày<br>- Thoại nội mạng: miễn phí cuộc gọi dưới 10 phút, tối đa 1000 phút/chu kỳ<br>- Thoại ngoại mạng: 200 phút/chu kỳ<br>- Miễn phí data truy cập Netflix, Youtube, Tiktok, Facebook</td><td></td></tr>
                            <tr><td class="text-center">24.4</td><td class="fw-bold">12F249</td><td class="text-center">2.988.000</td><td class="text-center">14 chu kỳ x 30 ngày</td><td>- Data 9GB/ngày<br>- Thoại nội mạng: miễn phí cuộc gọi dưới 10 phút, tối đa 1000 phút/chu kỳ<br>- Thoại ngoại mạng: 200 phút/chu kỳ<br>- Miễn phí data truy cập Netflix, Youtube, Tiktok, Facebook</td><td></td></tr>

                            <tr><td colspan="6" class="bg-light fw-bold">25. Nhóm gói FVVIP</td></tr>
                            <tr><td class="text-center">25.1</td><td class="fw-bold">FVVIP</td><td class="text-center">250.000</td><td class="text-center">30 ngày</td><td>- Tặng tài khoản gói V.VIP 1 xem các giải thể thao như Ngoại hạng Anh, FA Cup, V League 1, UFC, NBA... và kho phim bản quyền trên FPT Play<br>- Data: 2GB/ngày<br>- Miễn phí data truy cập FPT Play</td><td></td></tr>
                            <tr><td class="text-center">25.2</td><td class="fw-bold">3FVVIP</td><td class="text-center">550.000</td><td class="text-center">3 chu kỳ x 30 ngày</td><td>- Tặng tài khoản gói V.VIP 1 xem các giải thể thao như Ngoại hạng Anh, FA Cup, V League 1, UFC, NBA... và kho phim bản quyền trên FPT Play<br>- Data: 2GB/ngày<br>- Miễn phí data truy cập FPT Play</td><td></td></tr>
                            <tr><td class="text-center">25.3</td><td class="fw-bold">6FVVIP</td><td class="text-center">1.000.000</td><td class="text-center">6 chu kỳ x 30 ngày</td><td>- Tặng tài khoản gói V.VIP 1 xem các giải thể thao như Ngoại hạng Anh, FA Cup, V League 1, UFC, NBA... và kho phim bản quyền trên FPT Play<br>- Data: 2GB/ngày<br>- Miễn phí data truy cập FPT Play</td><td></td></tr>
                            <tr><td class="text-center">25.4</td><td class="fw-bold">12FVVIP</td><td class="text-center">1.900.000</td><td class="text-center">12 chu kỳ x 30 ngày</td><td>- Tặng tài khoản gói V.VIP 1 xem các giải thể thao như Ngoại hạng Anh, FA Cup, V League 1, UFC, NBA... và kho phim bản quyền trên FPT Play<br>- Data: 2GB/ngày<br>- Miễn phí data truy cập FPT Play</td><td></td></tr>

                        </tbody>
                    </table>
                </div>

                <hr class="my-4">

                <h5>B. NHÓM GÓI ĐỆM CƠ BẢN (ADD-ON)</h5>
                <p><em>(Đăng ký được đơn lẻ, không đăng ký được đồng thời với nhau. Tại 01 thời điểm chỉ được đăng ký tối đa 01 gói cước thuộc nhóm gói đệm cơ bản. Đăng ký đồng thời được với gói nền cơ bản)</em></p>

                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>TT</th>
                                <th>Mã gói</th>
                                <th>Giá (VNĐ)</th>
                                <th>Chu kỳ</th>
                                <th>Ưu đãi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="fw-bold">F5D</td>
                                <td>5.000 đ</td>
                                <td>1 ngày</td>
                                <td class="text-start">Data: 1GB tốc độ cao/ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td class="fw-bold">F10D</td>
                                <td>10.000 đ</td>
                                <td>3 ngày</td>
                                <td class="text-start">Data: 8GB tốc độ cao/3 ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td class="fw-bold">F25D</td>
                                <td>25.000đ</td>
                                <td>7 ngày</td>
                                <td class="text-start">- Thoại nội mạng FPT và tới MobiFone trong nước: 300 phút/chu kỳ.<br>- Thoại trong nước từ thuê bao FPT tới mạng khác ngoài FPT và MobiFone: 30 phút/chu kỳ</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td class="fw-bold">F30D</td>
                                <td>30.000 đ</td>
                                <td>7 ngày</td>
                                <td class="text-start">Data: 15GB tốc độ cao/7 ngày<br>Hết dung lượng tốc độ cao, ngắt kết nối Internet</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td class="fw-bold">FFB</td>
                                <td>10,000 đ</td>
                                <td>30 ngày</td>
                                <td class="text-start">Miễn phí data truy cập Facebook</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td class="fw-bold">FYT</td>
                                <td>10,000 đ</td>
                                <td>30 ngày</td>
                                <td class="text-start">Miễn phí data truy cập Youtube</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td class="fw-bold">FVO</td>
                                <td>10,000 đ</td>
                                <td>30 ngày</td>
                                <td class="text-start">Miễn phí data truy cập VieOn</td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td class="fw-bold">FNET</td>
                                <td>10,000 đ</td>
                                <td>30 ngày</td>
                                <td class="text-start">Miễn phí data truy cập Netflix</td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td class="fw-bold">FPL</td>
                                <td>10,000 đ</td>
                                <td>30 ngày</td>
                                <td class="text-start">Miễn phí data truy cập FPTPlay</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="fw-bold mt-3">Khi Khách hàng mua thiết bị tại FPT Shop, khách hàng có quyền lựa chọn gói cước bảo hành thiết bị 1 đổi 1 theo tháng.</p>

                <h4 class="mt-5">II. GÓI CƯỚC QUỐC TẾ</h4>
                <div class="note-box">
                    <p><strong>Cú pháp Đăng ký:</strong> DK &lt;Tên gói&gt; gửi 9199</p>
                    <p><strong>Lưu ý:</strong> Trước khi đăng ký gói cước, cần thực hiện đăng ký CVQT và bật Data Roaming, chi tiết xem <a href="#">Tại Đây</a>.</p>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Tên gói</th>
                                <th>Giá gói (VND)</th>
                                <th>Phạm vi (Quốc gia)</th>
                                <th>Dung lượng Data (GB)</th>
                                <th>Thông tin gói cước</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">F250R</td>
                                <td>250.000</td>
                                <td>7</td>
                                <td>3,5GB/15 ngày</td>
                                <td class="text-start">- Thời hạn: 15 ngày<br>- Phạm vi: 7 nước ASEAN<br>- Dung lượng: 3.5GB data</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">F350R</td>
                                <td>350.000</td>
                                <td>42</td>
                                <td>5GB/3 ngày</td>
                                <td class="text-start">- Thời hạn: 03 ngày.<br>- Phạm vi: 42 quốc gia,<br>- Không giới hạn truy cập data trong 03 ngày<br>- 05 GB đầu tốc độ cao.<br>- Vượt quá 5GB, tốc độ giảm xuống 128Kbps.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">F500R</td>
                                <td>500.000</td>
                                <td>63</td>
                                <td>2GB/15 ngày</td>
                                <td class="text-start">- Thời hạn: 15 ngày.<br>- Phạm vi: 63 quốc gia.<br>- Dung lượng: 02 GB.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5>Phạm vi cung cấp:</h5>
                <ul class="nav nav-tabs" id="roamingTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="f250r-tab" data-bs-toggle="tab" data-bs-target="#f250r" type="button" role="tab">Gói cước F250R</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="f350r-tab" data-bs-toggle="tab" data-bs-target="#f350r" type="button" role="tab">Gói cước F350R</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="f500r-tab" data-bs-toggle="tab" data-bs-target="#f500r" type="button" role="tab">Gói cước F500R</button>
                    </li>
                </ul>
                <div class="tab-content border border-top-0 p-3 mb-4" id="roamingTabContent">
                    
                    <div class="tab-pane fade show active" id="f250r" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered text-center">
                                <thead><tr><th>TT</th><th>Quốc gia</th><th>Nhà mạng</th><th>Mã Nhà mạng</th></tr></thead>
                                <tbody>
                                    <tr><td>1</td><td>Cambodia</td><td>Smart Axiata<br>Metfone<br>MobiTel</td><td>KHMSM, KHML1<br>KHMVC<br>KHMGM</td></tr>
                                    <tr><td>2</td><td>Indonesia</td><td>Telkomsel</td><td>IDNTS</td></tr>
                                    <tr><td>3</td><td>Laos</td><td>Beeline<br>Lao Unitel</td><td>LAOTL<br>LAOAS</td></tr>
                                    <tr><td>4</td><td>Malaysia</td><td>Celcom<br>Digi<br>Maxis</td><td>MYSCC, MYSMR<br>MYSMT<br>MYSBC</td></tr>
                                    <tr><td>5</td><td>Philippines</td><td>Globe PH</td><td>PHLGT</td></tr>
                                    <tr><td>6</td><td>Singapore</td><td>SingTel</td><td>SGPST</td></tr>
                                    <tr><td>7</td><td>Thailand</td><td>AIS Thailand</td><td>THAWN</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="f350r" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered text-center">
                                <thead><tr><th>TT</th><th>Quốc gia</th><th>Đối tác</th><th>TADIG</th></tr></thead>
                                <tbody>
                                    <tr><td>1</td><td>Australia</td><td>Optus</td><td>AUSOP</td></tr>
                                    <tr><td>2</td><td>Austria</td><td>T-Mobile</td><td>AUTK9</td></tr>
                                    <tr><td>3</td><td>Bangladesh</td><td>Grameen Phone</td><td>BGDGP</td></tr>
                                    <tr><td>4</td><td>Belgium</td><td>Proximus<br>Telenet Group</td><td>BELTB<br>BELKO</td></tr>
                                    <tr><td>5</td><td>Cambodia</td><td>Metfone<br>MobiTel<br>Smart Axiata</td><td>KHMVC<br>KHMGM<br>KHMSM</td></tr>
                                    <tr><td>6</td><td>Croatia</td><td>T-Mobile</td><td>HRVCN</td></tr>
                                    <tr><td>7</td><td>Czech</td><td>T-Mobile</td><td>CZERM</td></tr>
                                    <tr><td>8</td><td>Denmark</td><td>Telenor</td><td>DNKDM</td></tr>
                                    <tr><td>9</td><td>Estonia</td><td>Tele2</td><td>ESTRB</td></tr>
                                    <tr><td>10</td><td>France</td><td>Orange</td><td>FRAF1</td></tr>
                                    <tr><td>11</td><td>Germany</td><td>T-Mobile</td><td>DEUD1</td></tr>
                                    <tr><td>12</td><td>Greece</td><td>Cosmote</td><td>GRCCO</td></tr>
                                    <tr><td>13</td><td>Hongkong</td><td>CSL Hongkong</td><td>HKGTC</td></tr>
                                    <tr><td>14</td><td>Hungary</td><td>T-Mobile</td><td>HUNH2</td></tr>
                                    <tr><td>15</td><td>Iceland</td><td>Nova</td><td>ISLNO</td></tr>
                                    <tr><td>16</td><td>Indonesia</td><td>Telkomsel</td><td>IDNTS</td></tr>
                                    <tr><td>17</td><td>Italy</td><td>TIM<br>Hutchison</td><td>ITASI<br>ITAWI</td></tr>
                                    <tr><td>18</td><td>Kazakhstan</td><td>Beeline<br>Tele2</td><td>KAZKT<br>KAZ77</td></tr>
                                    <tr><td>19</td><td>Korea</td><td>KT<br>SK Telecom</td><td>KORKF<br>KORSK</td></tr>
                                    <tr><td>20</td><td>Latvia</td><td>BITE<br>Tele2</td><td>LVABT<br>LVABC</td></tr>
                                    <tr><td>21</td><td>Lithuania</td><td>BITE<br>Tele2</td><td>LTUMT<br>LTU03</td></tr>
                                    <tr><td>22</td><td>Luxembourg</td><td>POST Luxembourg<br>Tango</td><td>LUXK9<br>LUXTG</td></tr>
                                    <tr><td>23</td><td>Macau</td><td>CTM</td><td>MACCT</td></tr>
                                    <tr><td>24</td><td>Malaysia</td><td>Celcom<br>Digi<br>Maxis</td><td>MYSCC, MYSMR<br>MYSMT<br>MYSBC</td></tr>
                                    <tr><td>25</td><td>Malta</td><td>Go Mobile</td><td>MLTGO</td></tr>
                                    <tr><td>26</td><td>Netherlands</td><td>KPN</td><td>NLDPT</td></tr>
                                    <tr><td>27</td><td>Norway</td><td>Telenor</td><td>NORTM</td></tr>
                                    <tr><td>28</td><td>Oman</td><td>Omantel</td><td>OMNGT</td></tr>
                                    <tr><td>29</td><td>Philippines</td><td>Globe PH</td><td>PHLGT</td></tr>
                                    <tr><td>30</td><td>Qatar</td><td>Ooredoo</td><td>QATQT</td></tr>
                                    <tr><td>31</td><td>Romania</td><td>RCS&RDS</td><td>ROM05</td></tr>
                                    <tr><td>32</td><td>Russia</td><td>MegaFon<br>Tele2</td><td>RUSNW<br>RUST2</td></tr>
                                    <tr><td>33</td><td>Singapore</td><td>SingTel</td><td>SGPST</td></tr>
                                    <tr><td>34</td><td>Spain</td><td>Yoigo (Xfera)</td><td>ESPXF</td></tr>
                                    <tr><td>35</td><td>Srilanka</td><td>Dialog<br>Hutchison</td><td>LKADG<br>LKAHT</td></tr>
                                    <tr><td>36</td><td>Sweden</td><td>Tele2<br>Telenor</td><td>SWEIQ<br>SWEEP</td></tr>
                                    <tr><td>37</td><td>Taiwan</td><td>Taiwan Mobile</td><td>TWNPC</td></tr>
                                    <tr><td>38</td><td>Thailand</td><td>AIS Thailand<br>DTAC</td><td>THAWN<br>THADT</td></tr>
                                    <tr><td>39</td><td>Turkey</td><td>Turkcell</td><td>TURTC</td></tr>
                                    <tr><td>40</td><td>Ukraine</td><td>Kyivstar</td><td>UKRKS</td></tr>
                                    <tr><td>41</td><td>United Kingdom</td><td>Hutchison</td><td>GBRK7</td></tr>
                                    <tr><td>42</td><td>Uzbekistan</td><td>Beeline</td><td>UZBDU</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="f500r" role="tabpanel">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-sm table-striped table-bordered text-center">
                                <thead><tr style="position: sticky; top: 0; background: #fff; z-index: 1;"><th>TT</th><th>Quốc gia</th><th>Đối tác</th><th>TADIG</th></tr></thead>
                                <tbody>
                                    <tr><td>1</td><td>Algeria</td><td>Mobilis</td><td>DZAA1</td></tr>
                                    <tr><td>2</td><td>Australia</td><td>Optus</td><td>AUSOP</td></tr>
                                    <tr><td>3</td><td>Austria</td><td>T-Mobile<br>Hutchison</td><td>AUTK9<br>ITAWI</td></tr>
                                    <tr><td>4</td><td>Bangladesh</td><td>Garmeen Phone</td><td>BGDGP</td></tr>
                                    <tr><td>5</td><td>Belarus</td><td>Life</td><td>BLRBT</td></tr>
                                    <tr><td>6</td><td>Belgium</td><td>Proximus<br>Telenet Group</td><td>BELTB<br>BELKO</td></tr>
                                    <tr><td>7</td><td>Bulgaria</td><td>Yettel</td><td>YUGK7</td></tr>
                                    <tr><td>8</td><td>Cambodia</td><td>Metfone<br>Mobitel<br>Smart Axiata</td><td>KHMVC<br>KHMGM<br>KHMSM</td></tr>
                                    <tr><td>9</td><td>Canada</td><td>Bell<br>Telus</td><td>CANBM<br>CANTS</td></tr>
                                    <tr><td>10</td><td>China</td><td>China Mobile<br>Unicom</td><td>CHNCT<br>CHNCU</td></tr>
                                    <tr><td>11</td><td>Croatia</td><td>T-Mobile</td><td>AUTK9</td></tr>
                                    <tr><td>12</td><td>Cyprus</td><td>Epic</td><td>CYPK9</td></tr>
                                    <tr><td>13</td><td>Czech republic</td><td>T-mobile<br>O2</td><td>AUTK9<br>CZEET</td></tr>
                                    <tr><td>14</td><td>Denmark</td><td>Telenor</td><td>BGRCM</td></tr>
                                    <tr><td>15</td><td>Egypt</td><td>Orange</td><td>EGYAR</td></tr>
                                    <tr><td>16</td><td>Estonia</td><td>Tele2</td><td>ESTRB</td></tr>
                                    <tr><td>17</td><td>France</td><td>Orange</td><td>EGYAR</td></tr>
                                    <tr><td>18</td><td>Germany</td><td>T-Mobile</td><td>AUTK9</td></tr>
                                    <tr><td>19</td><td>Greece</td><td>Cosmote<br>Wind Hellas</td><td>GRCCO<br>GRCSH</td></tr>
                                    <tr><td>20</td><td>Hongkong</td><td>China Mobile<br>CLS HK</td><td>CHNCT<br>HKGTC</td></tr>
                                    <tr><td>21</td><td>Hungary</td><td>T-Mobile<br>Telenor</td><td>AUTK9<br>BGRCM</td></tr>
                                    <tr><td>22</td><td>Iceland</td><td>Nova</td><td>ISLNO</td></tr>
                                    <tr><td>23</td><td>India</td><td>Aritel Group</td><td>INDAT, INDBL, INDH1, INDA6, INDA5, INDA2, INDA3, INDA1, INDA8, INDA4, INDA7, INDA9, INDJH, INDJB, INDSC, INDMT, IND10, IND11, IND12, IND13, IND14, IND15, IND16</td></tr>
                                    <tr><td>24</td><td>Indonesia</td><td>Telkomsel<br>XL</td><td>IDNTS<br>IDNEX</td></tr>
                                    <tr><td>25</td><td>Ireland</td><td>Meteor</td><td>IRLME</td></tr>
                                    <tr><td>26</td><td>Israel</td><td>Pelephone</td><td>ISRPL</td></tr>
                                    <tr><td>27</td><td>Italy</td><td>TIM<br>Hutchison</td><td>ITASI<br>ITAWI</td></tr>
                                    <tr><td>28</td><td>Japan</td><td>Softbank</td><td>JPNJP</td></tr>
                                    <tr><td>29</td><td>Kazakhstan</td><td>Beeline<br>Tele2</td><td>KAZKT<br>ESTRB</td></tr>
                                    <tr><td>30</td><td>Korea</td><td>SK Telecom<br>KT</td><td>KORSK<br>KORKF</td></tr>
                                    <tr><td>31</td><td>Kuwait</td><td>STC<br>Zain</td><td>KWTKT<br>KWTMT</td></tr>
                                    <tr><td>32</td><td>Kyrgyzstan</td><td>Megacom</td><td>KGZMC</td></tr>
                                    <tr><td>33</td><td>Laos</td><td>TPLUS<br>Telecom<br>Unitel</td><td>LAOTL<br>LAOTC<br>LAOAS</td></tr>
                                    <tr><td>34</td><td>Latvia</td><td>BITE<br>Tele2</td><td>LVABT<br>ESTRB</td></tr>
                                    <tr><td>35</td><td>Lithuania</td><td>BITE<br>Tele2</td><td>LVABT<br>ESTRB</td></tr>
                                    <tr><td>36</td><td>Luxembourg</td><td>Orange<br>POST Luxembourg<br>Tango</td><td>EGYAR<br>LUXK9<br>LUXTG</td></tr>
                                    <tr><td>37</td><td>Macau</td><td>CTM</td><td>MACCT</td></tr>
                                    <tr><td>38</td><td>Malaysia</td><td>Celcom<br>Digi<br>Maxis</td><td>MYSCC, MYSMR<br>MYSMT<br>MYSBC</td></tr>
                                    <tr><td>39</td><td>Malta</td><td>Go Mobile<br>Vodafone</td><td>MLTGO<br>MLTTL</td></tr>
                                    <tr><td>40</td><td>Moldova</td><td>Orange</td><td>EGYAR</td></tr>
                                    <tr><td>41</td><td>Mongolia</td><td>Unitel</td><td>LAOAS</td></tr>
                                    <tr><td>42</td><td>Netherlands</td><td>KPN</td><td>NLDPT</td></tr>
                                    <tr><td>43</td><td>New zealand</td><td>2 degrees<br>SPARK</td><td>NZLNH<br>NZLK8</td></tr>
                                    <tr><td>44</td><td>Norway</td><td>Telenor</td><td>BGRCM</td></tr>
                                    <tr><td>45</td><td>Oman</td><td>Omantel</td><td>OMNGT</td></tr>
                                    <tr><td>46</td><td>Philippines</td><td>Globe PH</td><td>PHLGT</td></tr>
                                    <tr><td>47</td><td>Poland</td><td>Orange</td><td>EGYAR</td></tr>
                                    <tr><td>48</td><td>Qatar</td><td>Ooredoo</td><td>QATQT</td></tr>
                                    <tr><td>49</td><td>Romania</td><td>Orange<br>RCS&RDS</td><td>EGYAR<br>ROM05</td></tr>
                                    <tr><td>50</td><td>Russia</td><td>Beeline<br>Megafon<br>Tele2</td><td>KAZKT<br>RUSNW<br>ESTRB</td></tr>
                                    <tr><td>51</td><td>Serbia</td><td>Yettel</td><td>YUGK7</td></tr>
                                    <tr><td>52</td><td>Singapore</td><td>Singtel</td><td>SGPST</td></tr>
                                    <tr><td>53</td><td>Spain</td><td>Movistar<br>Orange<br>Yoigo (xFera)</td><td>COLTM<br>EGYAR<br>ESPXF</td></tr>
                                    <tr><td>54</td><td>Srilanka</td><td>Dialog<br>Hutchison</td><td>LKADG<br>ITAWI</td></tr>
                                    <tr><td>55</td><td>Sweden</td><td>Tele2<br>Telenor</td><td>ESTRB<br>BGRCM</td></tr>
                                    <tr><td>56</td><td>Switzerland</td><td>Salt CH</td><td>CHEOR</td></tr>
                                    <tr><td>57</td><td>Taiwan</td><td>Taiwan Mobile</td><td>TWNPC</td></tr>
                                    <tr><td>58</td><td>Thailand</td><td>AIS Thailand<br>DTAC (telenor)<br>Truemove</td><td>THAWN<br>THADT<br>THACT</td></tr>
                                    <tr><td>59</td><td>Turkey</td><td>Turkcell</td><td>TURTC</td></tr>
                                    <tr><td>60</td><td>Ukraine</td><td>Kyivstar</td><td>UKRKS</td></tr>
                                    <tr><td>61</td><td>United kingdom</td><td>Everything Everywhere</td><td>GBRME</td></tr>
                                    <tr><td>62</td><td>United States</td><td>T-moble<br>AT&T</td><td>USA16, USA27, USA31, USAW0, USAW4, USAW5, USAW6, USASC<br>USACG</td></tr>
                                    <tr><td>63</td><td>Uzbekistan</td><td>Beeline<br>Ucell</td><td>KAZKT<br>UZB05</td></tr>
                                </tbody>
                            </table>
                        </div>
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