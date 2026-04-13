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
    .content-section h5,
    .content-section p,
    .content-section table {
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
        font-size: 16px;
        color: #212529;
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

    /* BẢNG BIỂU (TABLE) */
    .content-section .table-container {
        max-height: 600px; /* Giới hạn chiều cao bảng để dễ cuộn */
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .content-section .table {
        margin-bottom: 0;
        font-size: 14.5px;
    }

    .content-section .table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: 600;
        vertical-align: middle;
        position: sticky;
        top: 0; /* Giữ cố định tiêu đề bảng khi cuộn */
        z-index: 1;
        box-shadow: 0 1px 0 #dee2e6;
    }

    .content-section .table td {
        color: #495057;
        vertical-align: middle;
    }

    .note-text {
        font-style: italic;
        color: #6c757d;
        font-size: 14px;
    }

    /* TRẠNG THÁI CỠ CHỮ LỚN */
    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h5 { font-size: 18px; }
    .content-section.large-text p,
    .content-section.large-text .table td,
    .content-section.large-text .table th {
        font-size: 16.5px;
        line-height: 1.7;
    }
</style>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/ho-tro">Hỗ trợ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh sách điểm cung cấp dịch vụ viễn thông FPT</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'diem-cung-cap'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Danh sách điểm cung cấp dịch vụ viễn thông FPT</h3>

                <h5>1. Danh sách điểm cung cấp dịch vụ viễn thông cố định cung cấp dịch vụ viễn thông FPT</h5>
                <p>Hiện tại, Quý Khách có thể tới bất kỳ cửa hàng FPTShop trên toàn quốc để được hỗ trợ hòa mạng thuê bao FPT. Danh sách cửa hàng Quý Khách có thể tra cứu <a href="#">Tại đây</a>.</p>

                <h5>2. Danh sách điểm cung cấp dịch vụ viễn thông lưu động cung cấp dịch vụ viễn thông FPT</h5>
                <p>Triển khai các điểm cung cấp dịch vụ viễn thông lưu động tại các trụ sở/chi nhánh/văn phòng của FPT để hỗ trợ hòa mạng thuê bao di động FPT.</p>

                <div class="table-container">
                    <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Tỉnh/Thành phố</th>
                                <th style="width: 20%;">Quận/Huyện</th>
                                <th>Địa chỉ các trụ sở/chi nhánh/văn phòng của FPT triển khai</th>
                                <th style="width: 15%;">Thời gian triển khai (*)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Hà Nội</td><td>Quận Thanh Xuân</td><td class="text-start">154 Khuất Duy Tiến, quận Thanh Xuân, Hà Nội</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">2 Phạm Văn Bạch, Yên Hòa, CG, HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Đống Đa</td><td class="text-start">27 Yên Lãng, Đống Đa, HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Đống Đa</td><td class="text-start">402 Xã Đàn, Quận Đống Đa</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Ba ĐÌnh</td><td class="text-start">48 Vạn Bảo, Ba Đình, HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">FPT 17 Duy Tân, Cầu Giấy, Hà Nội</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">KeangNam Building, HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Huyện Thạch Thất</td><td class="text-start">Khu CNC Hòa Lạc, Hà Nội</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Huyện Thạch Thất</td><td class="text-start">Trường Đại học FPT tại Hòa Lạc</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">Số 34 Trần Quốc Hoàn, phường Dịch Vọng Hậu, quận Cầu Giấy, TP HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Nam Từ Liêm</td><td class="text-start">Số 46 Nguyễn Hoàng, Phường mỹ đình 2, Q Nam Từ Liêm, HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Bắc Từ Liêm</td><td class="text-start">Số 92, Block 12, đường số 23, KĐT thành phố Giao Lưu, thành phố Hà Nội</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">Tòa nhà Detech 107 Nguyễn Phong Sắc, CG, HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">Tòa nhà FPT Tower, Số 10 Phạm Văn Bạch, phường Dịch Vọng, quận Cầu Giấy, TPHN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">Tòa nhà Zodiac, ngõ 19 đường Duy Tân, Quận Cầu Giấy, HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Nam Từ Liêm</td><td class="text-start">Trường Cao Đẳng Thực hành FPT Polytechnic, Trịnh Văn Bộ, Nam Từ Liêm, HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">Trường Tiểu học và THCS FPT Cầu Giấy, 15 Đông Quan, Cầu Giấy, HN</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận Gò Vấp</td><td class="text-start">1015 Phan Văn Trị, Phường 7, Quận Gò Vấp</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 01</td><td class="text-start">124 Sương Nguyệt Ánh, P.Bến Thành, Q.01</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Huyện Cần Giờ</td><td class="text-start">148 Đào Cử, KP Phong Thạnh, TT Cần Thạnh, Cần Giờ</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận Bình Thạnh</td><td class="text-start">155 Đinh Bộ Lĩnh, P26, Q. Bình Thạnh</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 12</td><td class="text-start">156 Lê Văn Khương, Phường Thới An, Quận 12</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Huyện Nhà Bè</td><td class="text-start">161 Nguyễn Bình, Xã Phú Xuân, Huyện Nhà Bè</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 7</td><td class="text-start">198 Lâm Văn Bền, P.Tân Quy, Quận 7</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 6</td><td class="text-start">227 Hậu Giang, Phường 5, Quận 6</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận Bình Tân</td><td class="text-start">31 Lê Văn Quới, Phường Bình Trị Đông, Quận Bình Tân</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận Tân Bình</td><td class="text-start">340E-F Hoàng Văn Thụ, P.4, Quận Tân Bình</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Thành phố Thủ Đức</td><td class="text-start">385 Man Thiện, Phường Tăng Nhơn Phú A, Quận 9, HCM</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Huyện Củ Chi</td><td class="text-start">408 Tỉnh lộ 8, KP4, Thị Trấn Củ Chi, Huyện Củ Chi</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 8</td><td class="text-start">42 Dương Quang Đông, phường 5, Quận 8</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận Tân Phú</td><td class="text-start">61 Vườn Lài, Phường Phú Thọ Hòa, Quận Tân Phú</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>huyện Hóc Môn</td><td class="text-start">63/1 Bà Triệu, Thị Trấn Hóc Môn, huyện Hóc Môn</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 2</td><td class="text-start">66 Hoàng Diệu 2, P.Linh Chiểu, Q.Thủ Đức</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Huyện Bình Chánh</td><td class="text-start">B11B/13C Võ Văn Vân, Ấp 2, Xã Vĩnh Lộc B, H.Bình Chánh</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 12</td><td class="text-start">Công viên phần mềm quang trung quận 12, TP HCM</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Huyện Bình Chánh</td><td class="text-start">E6/160 Quốc Lộ 50, xã Phong Phú, H. Bình Chánh</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 7</td><td class="text-start">FPT Tân Thuận 1-2, khu chế xuất tân thuận quận 7, TP HCM</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Thành phố Thủ Đức</td><td class="text-start">F-Town 1-2-3 Thủ Đức (Khu công nghệ cao), TP HCM</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận Tân Bình</td><td class="text-start">Greenwich 20 Đường Cộng hoà, tân bình, TP HCM</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Thành phố Thủ Đức</td><td class="text-start">Lô E2a-7, Đường D1, Khu Công nghệ cao, TP HCM</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận Tân Bình</td><td class="text-start">Swinburne A35 Bạch Đằng, TP HCM</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 7</td><td class="text-start">Tòa nhà 678, Quận 7, TP HCM</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Quận Ngô Quyền</td><td class="text-start">271 Lê Thánh Tông, phường Máy Chai, quận Ngô Quyền, TP Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Liên Chiểu</td><td class="text-start">137 Nguyễn Thị Thập, phường Hòa Minh, quận Liên Chiểu, TP Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Bình Định</td><td>Thành phố Quy Nhơn</td><td class="text-start">107A Thanh Niên, phường Quang Trung, TP Quy Nhơn, tỉnh Bình Định</td><td>2024</td></tr>
                            <tr><td>Bình Định</td><td>Thành phố Quy Nhơn</td><td class="text-start">124 Trần Hưng Đạo, P. Hải Cảng, TP. Quy Nhơn, Bình Định</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Quận Cái Răng</td><td class="text-start">Toà nhà FPT Polytechnic, đường số 22, phường Thường Thạnh, quận Cái Răng, TP Cần Thơ</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Quận Cái Răng</td><td class="text-start">Toà nhà A2, đường Quang Trung, P. Hưng Phú. Q. Cái Răng, TP. Cần Thơ</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Ngũ Hành Sơn</td><td class="text-start">Trường TH & THCS FPT Đà Nẵng: Tòa nhà Epsilon, Khu đô thị FPT, Phường Hoà Hải, Quận Ngũ Hành Sơn, Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Ngũ Hành Sơn</td><td class="text-start">Trường THPT FPT Đà Nẵng: Khuôn viên đại học FPT Đà Nẵng, Tòa nhà FPT, KCN An Đồn, Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Quận Ninh Kiều</td><td class="text-start">Trường THPT FPT Cần Thơ: Khuôn viên Đại học FPT, đường Nguyễn Văn Cừ nối dài, Phường An Bình, Quận Ninh Kiều, Cần Thơ</td><td>2024</td></tr>
                            <tr><td>Bình Định</td><td>Thành phố Quy Nhơn</td><td class="text-start">Trường THPT FPT Quy Nhơn: Khuôn viên Đại học FPT, Khu đô thị mới An Phú Thịnh, Phường Nhơn Bình & Phường Đống Đa, Thành phố Quy Nhơn, Quy Nhơn</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Quận Hải An</td><td class="text-start">Trường THCS & THPT FPT Hải Phòng: Khu tái định cư Đằng Lâm 1, phường Thành Tô, quận Hải An, thành phố Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Hải Châu</td><td class="text-start">Swinburne: 2Q, 2A Đ. 2 Tháng 9, P, Hải Châu, Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Sơn Trà</td><td class="text-start">Greenwich: 658 Ngô Quyền, An Hải Bắc, Sơn Trà, Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Sơn Trà</td><td class="text-start">Greenwich: Toà nhà FPT, đường số 1, KCN An Đồn, An Hải Bắc, Sơn Trà, Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Liên Chiểu</td><td class="text-start">Đại học Bách Khoa Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Quận Ninh Kiều</td><td class="text-start">Greenwich: Số 160 đường 30/4 - Phường An phú - Quận Ninh Kiều – TP. Cần Thơ</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Ngũ Hành Sơn</td><td class="text-start">ĐH Cơ sở Đà Nẵng: Khu Đô thị Công nghệ FPT, P. Hòa Hải, Q. Ngũ Hành Sơn, Tp. Đà Nẵng.</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Quận Ninh Kiều</td><td class="text-start">Số 600 đường Nguyễn Văn Cừ nối dài, Khu vực 6, phường An Bình, quận Ninh Kiều, TP. Cần Thơ</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Sơn Trà</td><td class="text-start">Đường số 4 - KCN Đà Nẵng - P.An Hải Bắc - Q.Sơn Trà - TP Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Sơn Trà</td><td class="text-start">Tòa nhà FPT, Khu công nghiệp Đà Nẵng, Đường số 1, P. An Hải Bắc, Q. Sơn Trà, Tp. Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Ngũ Hành Sơn</td><td class="text-start">FPT Complex, Đường Nam Kỳ Khởi Nghĩa, Phường Hòa Hải, Quận Ngũ Hành Sơn, Thành Phố Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Bình Định</td><td>Thành phố Quy Nhơn</td><td class="text-start">12 đại lộ khoa học, khu vực 2, thành phố quy nhơn, bình định</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Quận Bình Thủy</td><td class="text-start">49 Cách Mạng Tháng 8, Q. Bình Thủy, TP. Cần Thơ</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Quận Lê Chân</td><td class="text-start">Thửa đất số 1, tờ bản đồ số 01-2020, tổ 19, phường Vĩnh Niệm, quận Lê Chân, thành phố Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Sơn Trà</td><td class="text-start">909 Ngô Quyền, Phường An Hải Đông, Quận Sơn Trà, Thành Phố Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Bình Định</td><td>Thành phố Quy Nhơn</td><td class="text-start">94 Phạm Hùng, P.Lý Thường Kiệt, TP Quy Nhơn, Bình Định</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Quận Ninh Kiều</td><td class="text-start">10 Phan Văn Trị , Phường An Phú, Quận Ninh Kiều, Cần Thơ</td><td>2024</td></tr>
                            <tr><td>Bắc Giang</td><td>Thành phố Bắc Giang</td><td class="text-start">Lô A-LK 36-06, Khu số 2, Khu đô thị phía Nam, Xã Tân Tiến, Thành phố Bắc Giang, Tỉnh Bắc Giang</td><td>2024</td></tr>
                            <tr><td>Bắc Giang</td><td>Huyện Lục Nam</td><td class="text-start">108 Thanh Hưng, Thị Trấn Đồi Ngô, Huyện Lục Nam, Tỉnh Bắc Giang</td><td>2024</td></tr>
                            <tr><td>Bắc Giang</td><td>Huyện Lục Ngạn</td><td class="text-start">Số 142 Khu Trường Chinh, Thị trấn Chũ, Huyện Lục Ngạn, Bắc Giang</td><td>2024</td></tr>
                            <tr><td>Bắc Giang</td><td>Huyện Yên Dũng</td><td class="text-start">Tổ dân phố 5, Thị trấn Nham Biền, Huyện Yên Dũng, Tỉnh Bắc Giang</td><td>2024</td></tr>
                            <tr><td>Bắc Giang</td><td>Huyện Việt Yên</td><td class="text-start">Thôn Phúc Lâm, Xã Hoàng Ninh, Huyện Việt Yên, Tỉnh Bắc Giang</td><td>2024</td></tr>
                            <tr><td>Bắc Giang</td><td>Huyện Tân Yên</td><td class="text-start">Thôn Tiến Phan 2, Xã Nhã Nam, Huyện Tân Yên, Tỉnh Bắc Giang</td><td>2024</td></tr>
                            <tr><td>Bắc Giang</td><td>Huyện Hiệp Hòa</td><td class="text-start">61 Hoàng Văn Thái, Thị trấn Thắng, Huyện Hiệp Hòa, Tỉnh Bắc Giang</td><td>2024</td></tr>
                            <tr><td>Bắc Ninh</td><td>Thành phố Từ Sơn</td><td class="text-start">Số 6 Lý Thánh Tông, Phường Đông Ngàn, Thành phố Từ Sơn, Bắc Ninh</td><td>2024</td></tr>
                            <tr><td>Bắc Ninh</td><td>Huyện Thuận Thành</td><td class="text-start">Phố Khám, Xã Gia Đông, Huyện Thuận Thành, Tỉnh Bắc Ninh</td><td>2024</td></tr>
                            <tr><td>Bắc Ninh</td><td>Huyện Yên Phong</td><td class="text-start">248 Phố Mới, Thị trấn Chờ, Huyện Yên Phong, Tỉnh Bắc Ninh</td><td>2024</td></tr>
                            <tr><td>Bắc Ninh</td><td>Huyện Lương Tài</td><td class="text-start">Số 450 Hàn Thuyên, Thị Trấn Thứa, Huyện Lương Tài, Tỉnh Bắc Ninh</td><td>2024</td></tr>
                            <tr><td>Bắc Ninh</td><td>Huyện Quế Võ</td><td class="text-start">Khu Nhà Ở Xã Hội Và Thương Mại Kinh Bắc, Thị Trấn Phố Mới, Huyện Quế Võ, Bắc Ninh</td><td>2024</td></tr>
                            <tr><td>Bắc Ninh</td><td>Thành phố Bắc Ninh</td><td class="text-start">Số 58, Đường Thành Cổ, Phường Vệ An, Thành phố Bắc Ninh, Tỉnh Bắc Ninh</td><td>2024</td></tr>
                            <tr><td>Cao Bằng</td><td>Thành phố Cao Bằng</td><td class="text-start">08 Kim Đồng, Tổ 15, Phường Hợp Giang, Thành phố Cao Bằng, Tỉnh Cao Bằng</td><td>2024</td></tr>
                            <tr><td>Hưng Yên</td><td>Huyện Tiên Lữ</td><td class="text-start">434 Phố Nguyễn Trãi, Tiểu Khu 3, Thị trấn Vương, Huyện Tiên Lữ, Tỉnh Hưng Yên</td><td>2024</td></tr>
                            <tr><td>Hưng Yên</td><td>Huyện Văn Lâm</td><td class="text-start">Nhà Liền kề 34, Khu TTTM Như Quỳnh, Thị trấn Như Quỳnh, Huyện Văn Lâm, Hưng Yên</td><td>2024</td></tr>
                            <tr><td>Hưng Yên</td><td>huyện Khoái Châu</td><td class="text-start">Thị Tứ Bô Thời, xã Hồng Tiến, huyện Khoái Châu, tỉnh Hưng Yên</td><td>2024</td></tr>
                            <tr><td>Hưng Yên</td><td>Huyện Văn Giang</td><td class="text-start">401 Thị trấn Văn Giang, Huyện Văn Giang, Tỉnh Hưng Yên</td><td>2024</td></tr>
                            <tr><td>Hưng Yên</td><td>Thành phố Hưng Yên</td><td class="text-start">88 Tuệ Tĩnh, Phường Hiến Nam, Thành phố Hưng Yên, Tỉnh Hưng Yên</td><td>2024</td></tr>
                            <tr><td>Hưng Yên</td><td>Thị xã Mỹ Hào</td><td class="text-start">Số 1, Khu Bến xe Mỹ Hào, Phường Nhân Hòa, Thị xã Mỹ Hào, Tỉnh Hưng Yên</td><td>2024</td></tr>
                            <tr><td>Hưng Yên</td><td>Huyện Ân Thi</td><td class="text-start">Số 6, Khu Trung tâm thương mại Thị trấn Ân Thi, Huyện Ân Thi, Tỉnh Hưng Yên</td><td>2024</td></tr>
                            <tr><td>Lào Cai</td><td>Thành phố Lào Cai</td><td class="text-start">228 Hoàng Liên, Phường Cốc Lếu, Thành phố Lào Cai, Tỉnh Lào Cai</td><td>2024</td></tr>
                            <tr><td>Lào Cai</td><td>Huyện Bảo Thắng</td><td class="text-start">39 Khuất Quang Chiến, Thị trấn Phố Lu, Huyện Bảo Thắng, Tỉnh Lào Cai</td><td>2024</td></tr>
                            <tr><td>Lạng Sơn</td><td>Thị trấn Lộc Bình</td><td class="text-start">Số 110, Khu Hòa Bình, Thị trấn Lộc Bình, Tỉnh Lạng Sơn</td><td>2024</td></tr>
                            <tr><td>Lạng Sơn</td><td>Huyện Hữu Lũng</td><td class="text-start">Số 215, Đường Chi Lăng, Thị trấn Hữu Lũng, Huyện Hữu Lũng, Tỉnh Lạng Sơn</td><td>2024</td></tr>
                            <tr><td>Lạng Sơn</td><td>Thành phố Lạng Sơn</td><td class="text-start">322, Đường Bà Triệu, Phường Vĩnh Trại, Thành phố Lạng Sơn, Tỉnh Lạng Sơn</td><td>2024</td></tr>
                            <tr><td>Phú Thọ</td><td>Thành phố Việt Trì</td><td class="text-start">1121 Đại lộ Hùng Vương, Phường Tiên Cát, Thành phố Việt Trì, Tỉnh Phú Thọ</td><td>2024</td></tr>
                            <tr><td>Phú Thọ</td><td>Thị Xã Phú Thọ</td><td class="text-start">78 Phố Hòa Bình, Phường Âu Cơ, Thị Xã Phú Thọ, Tỉnh Phú Thọ</td><td>2024</td></tr>
                            <tr><td>Phú Thọ</td><td>Huyện Lâm Thao</td><td class="text-start">Số 469 Khu 14, Thị trấn Hùng Sơn, Huyện Lâm Thao, Tỉnh Phú Thọ</td><td>2024</td></tr>
                            <tr><td>Phú Thọ</td><td>Huyện Thanh Thủy</td><td class="text-start">Khu 9, Thị trấn Thanh Thủy, Huyện Thanh Thủy, Tỉnh Phú Thọ</td><td>2024</td></tr>
                            <tr><td>Quảng Ninh</td><td>Thành phố Hạ Long</td><td class="text-start">152 An Tiêm, Phường Hà Khẩu, Thành phố Hạ Long, Tỉnh Quảng Ninh</td><td>2024</td></tr>
                            <tr><td>Quảng Ninh</td><td>Thành phố Hạ Long</td><td class="text-start">351-353 Nguyễn Văn Cừ, Phường Hồng Hải, Thành phố Hạ Long, Tỉnh Quảng Ninh</td><td>2024</td></tr>
                            <tr><td>Quảng Ninh</td><td>Thành phố Móng Cái</td><td class="text-start">21 Lý Tự Trọng, Phường Hòa Lạc, Thành phố Móng Cái, Tỉnh Quảng Ninh</td><td>2024</td></tr>
                            <tr><td>Quảng Ninh</td><td>Thành phố Cẩm Phả</td><td class="text-start">484 Trần Phú, Phường Cẩm Thuỷ, Thành phố Cẩm Phả, Tỉnh Quảng Ninh</td><td>2024</td></tr>
                            <tr><td>Quảng Ninh</td><td>Thành phố Cẩm Phả</td><td class="text-start">487 Lý Thường Kiệt, Phường Cửa Ông, Thành phố Cẩm Phả, Tỉnh Quảng Ninh</td><td>2024</td></tr>
                            <tr><td>Quảng Ninh</td><td>Phường Mạo Khê</td><td class="text-start">633, Khu Vĩnh Hòa, Phường Mạo Khê, Thị xã Ðông Triều, Tỉnh Quảng Ninh</td><td>2024</td></tr>
                            <tr><td>Quảng Ninh</td><td>Thành phố Uông Bí</td><td class="text-start">258 Quang Trung, Thành phố Uông Bí, Tỉnh Quảng Ninh</td><td>2024</td></tr>
                            <tr><td>Quảng Ninh</td><td>Thị Xã Quảng Yên</td><td class="text-start">34 Trần Hưng Đạo, Phường Quảng Yên, Thị Xã Quảng Yên, Tỉnh Quảng Ninh</td><td>2024</td></tr>
                            <tr><td>Thái Nguyên</td><td>Huyện Phú Lương</td><td class="text-start">Tiểu khu Dương Tự Minh, Thị trấn Đu, Huyện Phú Lương, Tỉnh Thái Nguyên</td><td>2024</td></tr>
                            <tr><td>Thái Nguyên</td><td>Huyện Phú Bình</td><td class="text-start">Tổ 4, Thị trấn Hương Sơn, Huyện Phú Bình, Tỉnh Thái Nguyên</td><td>2024</td></tr>
                            <tr><td>Thái Nguyên</td><td>Huyện Định Hóa</td><td class="text-start">517 Bãi Á 1, Thị trấn Chợ Chu, Huyện Định Hóa, Thái Nguyên</td><td>2024</td></tr>
                            <tr><td>Thái Nguyên</td><td>Huyện Đại Từ</td><td class="text-start">Tổ dân phố Cầu Thành 2, Thị trấn Hùng Sơn, Huyện Đại Từ, Thái Nguyên</td><td>2024</td></tr>
                            <tr><td>Thái Nguyên</td><td>Thành phố Thái Nguyên</td><td class="text-start">66 Bắc Sơn, Tổ 11, Phường Hoàng Văn Thụ, Thành phố Thái Nguyên, Tỉnh Thái Nguyên</td><td>2024</td></tr>
                            <tr><td>Thái Nguyên</td><td>Thành phố Phổ Yên</td><td class="text-start">Số 52, Đường Tôn Đức Thắng, Tổ 1, Phường Ba Hàng, Thành phố Phổ Yên, Tỉnh Thái Nguyên</td><td>2024</td></tr>
                            <tr><td>Tuyên Quang</td><td>Thành phố Tuyên Quang</td><td class="text-start">118 Trần Phú, Tổ 17, Phường Tân Quang, Thành phố Tuyên Quang, Tỉnh Tuyên Quang</td><td>2024</td></tr>
                            <tr><td>Tuyên Quang</td><td>Huyện Sơn Dương</td><td class="text-start">Tổ dân phố Quyết Tiến, Thị trấn Sơn Dương, Huyện Sơn Dương, Tỉnh Tuyên Quang</td><td>2024</td></tr>
                            <tr><td>Vĩnh Phúc</td><td>Huyện Yên Lạc</td><td class="text-start">247 Khu 3 Đoài, Thị Trấn Yên Lạc, Huyện Yên Lạc, Vĩnh Phúc</td><td>2024</td></tr>
                            <tr><td>Vĩnh Phúc</td><td>Huyện Vĩnh Tường</td><td class="text-start">Đường Lê Xoay, Thị Trấn Vĩnh Tường, Huyện Vĩnh Tường, Tỉnh Vĩnh Phúc</td><td>2024</td></tr>
                            <tr><td>Vĩnh Phúc</td><td>Huyện Lập Thạch</td><td class="text-start">TDP Vĩnh Thịnh, Thị trấn Lập Thạch, Huyện Lập Thạch, Tỉnh Vĩnh Phúc</td><td>2024</td></tr>
                            <tr><td>Vĩnh Phúc</td><td>Thành phố Vĩnh Yên</td><td class="text-start">18 Nguyễn Trãi, Phường Liên Bảo, Thành phố Vĩnh Yên, Tỉnh Vĩnh Phúc</td><td>2024</td></tr>
                            <tr><td>Vĩnh Phúc</td><td>Thành phố Phúc Yên</td><td class="text-start">LK 2909 Khu đô thị TMS Grand City Phúc Yên, Phường Hùng Vương, Thành phố Phúc Yên, Tỉnh Vĩnh Phúc</td><td>2024</td></tr>
                            <tr><td>Yên Bái</td><td>Thành phố Yên Bái</td><td class="text-start">Nhà phố thương mại Số LK-D04, Tổ 8, Phường Minh Tân, Thành phố Yên Bái, Tỉnh Yên Bái</td><td>2024</td></tr>
                            <tr><td>Yên Bái</td><td>Huyện Văn Yên</td><td class="text-start">176 Đường Tuệ Tĩnh, Trị trấn Mậu A, Huyện Văn Yên, Tỉnh Yên Bái</td><td>2024</td></tr>
                            <tr><td>Điện Biên</td><td>Tp. Điện Biên Phủ</td><td class="text-start">584 Đường Võ Nguyên Giáp,Tổ Dân Phố 1, P.Tân Thanh, Tp. Điện Biên Phủ, Tỉnh Điện Biên</td><td>2024</td></tr>
                            <tr><td>Hòa Bình</td><td>Thành phố Hòa Bình</td><td class="text-start">313 Trần Hưng Đạo, Phường Quỳnh Lâm, Thành phố Hòa Bình, Hòa Bình</td><td>2024</td></tr>
                            <tr><td>Hòa Bình</td><td>Huyện Tân Lạc</td><td class="text-start">Số 33, Tiểu khu 2, Thị trấn Mường Khén, Huyện Tân Lạc, Tỉnh Hòa Bình</td><td>2024</td></tr>
                            <tr><td>Hải Dương</td><td>huyện Cẩm Giàng</td><td class="text-start">Khu thương mại dịch vụ Ghẽ,  Xã Tân Trường, huyện Cẩm Giàng, tỉnh Hải Dương</td><td>2024</td></tr>
                            <tr><td>Hải Dương</td><td>Thành phố Hải Dương</td><td class="text-start">Số 18 Bà Triệu, Phường Phạm Ngũ Lão, Thành phố Hải Dương, Hải Dương</td><td>2024</td></tr>
                            <tr><td>Hải Dương</td><td>Thị xã Chí Linh</td><td class="text-start">34 Nguyễn Trãi 1, Phường Sao Đỏ, Thị xã Chí Linh, Hải Dương</td><td>2024</td></tr>
                            <tr><td>Hải Dương</td><td>Thị Xã Kinh Môn</td><td class="text-start">42 Đường Thanh Niên, Thị Xã Kinh Môn, Hải Dương</td><td>2024</td></tr>
                            <tr><td>Hải Dương</td><td>Huyện Thanh Hà</td><td class="text-start">Khu Đường Mới, Thị trấn Thanh Hà, Huyện Thanh Hà, Hải Dương</td><td>2024</td></tr>
                            <tr><td>Hải Dương</td><td>Huyện Thanh Miện</td><td class="text-start">Số 91 Nguyễn Lương Bằng, Thị trấn Thanh Miện, Huyện Thanh Miện, Hải Dương</td><td>2024</td></tr>
                            <tr><td>Hải Dương</td><td>Huyện Ninh Giang</td><td class="text-start">Quốc lộ 37, Xã Ninh Thành, Huyện Ninh Giang, Hải Dương</td><td>2024</td></tr>
                            <tr><td>Hải Dương</td><td>Huyện Bình Giang</td><td class="text-start">36 Hùng Vương, Xã Tráng Liệt, Huyện Bình Giang, Hải Dương</td><td>2024</td></tr>
                            <tr><td>Hà Nam</td><td>Thành phố Phủ Lý</td><td class="text-start">PG02-05 Khu nhà liền kề Vincom, Đường Châu Cầu, Phường Minh Khai, Thành phố Phủ Lý, Hà Nam</td><td>2024</td></tr>
                            <tr><td>Hà Nam</td><td>Thị xã Duy Tiên</td><td class="text-start">KĐT Đồng Văn Xanh, Phường Duy Hải, Thị xã Duy Tiên, Tỉnh Hà Nam</td><td>2024</td></tr>
                            <tr><td>Hà Nam</td><td>Huyện Lý Nhân</td><td class="text-start">Số 238 đường Trần Nhân Tông, Thị trấn Vĩnh Trụ, Huyện Lý Nhân, Tỉnh Hà Nam</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Quận Lê Chân</td><td class="text-start">Đường Bùi Viện, P. Vĩnh Niệm, Q. Lê Chân, TP. Hải Phòng, Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Quận Lê Chân</td><td class="text-start">137 Trần Nguyên Hãn, Quận Lê Chân, Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Huyện An Dương</td><td class="text-start">63 Khu 2, Thị trấn An Dương, Huyện An Dương, Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Huyện Thủy Nguyên</td><td class="text-start">Khu Ba Toa, Xã Thủy Đường, Huyện Thủy Nguyên, Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Huyện Kiến Thụy</td><td class="text-start">114 Cầu Ðen, Thị trấn Núi Đôi, Huyện Kiến Thụy, Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Quận Kiến An</td><td class="text-start">255 Phan Đăng Lưu, Phường Ngọc Sơn, Quận Kiến An, Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Huyện Tiên Lãng</td><td class="text-start">146 Khu 8, Thị trấn Tiên Lãng, Huyện Tiên Lãng, Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Huyện Vĩnh Bảo</td><td class="text-start">178 Đông Thái, Thị trấn Vĩnh Bảo, Huyện Vĩnh Bảo, Hải Phòng</td><td>2024</td></tr>
                            <tr><td>Hà Tĩnh</td><td>Huyện Kỳ Anh</td><td class="text-start">Xóm Xuân Thọ, Xã Kỳ Tân, Huyện Kỳ Anh, Hà Tĩnh</td><td>2024</td></tr>
                            <tr><td>Hà Tĩnh</td><td>Thành phố Hà Tĩnh</td><td class="text-start">115 Nguyễn Biểu, phường Nam Hà, Thành phố Hà Tĩnh, Hà Tĩnh</td><td>2024</td></tr>
                            <tr><td>Hà Tĩnh</td><td>Thị xã Hồng Lĩnh</td><td class="text-start">25 Trần Phú, Phường Nam Hồng, Thị xã Hồng Lĩnh, Hà Tĩnh</td><td>2024</td></tr>
                            <tr><td>Nghệ An</td><td>Huyện Đô Lương</td><td class="text-start">Xóm 4, Xã Đông Sơn, Huyện Đô Lương, Tỉnh Nghệ An</td><td>2024</td></tr>
                            <tr><td>Nghệ An</td><td>Thị xã Hoàng Mai</td><td class="text-start">Khối Thịnh Mỹ, Phường Quỳnh Thiện, Thị xã Hoàng Mai, Nghệ An</td><td>2024</td></tr>
                            <tr><td>Nghệ An</td><td>Huyện Quỳnh Lưu</td><td class="text-start">Số 16, Khối 2, Thị trấn Cầu Giát, Huyện Quỳnh Lưu, Tỉnh Nghệ An</td><td>2024</td></tr>
                            <tr><td>Nghệ An</td><td>Huyện Diễn Châu</td><td class="text-start">Xóm 2, Xã Diễn Thành, Huyện Diễn Châu, Nghệ An</td><td>2024</td></tr>
                            <tr><td>Nghệ An</td><td>Thị xã Cửa Lò</td><td class="text-start">Khối 1 đường Nguyễn Cảnh Quế, Phường Thu Thủy, Thị xã Cửa Lò, Nghệ An</td><td>2024</td></tr>
                            <tr><td>Nghệ An</td><td>Thị Xã Thái Hòa</td><td class="text-start">Đường Lý Thường Kiệt, Khối Tây Hồ 1, Phường Quang Tiến, Thị Xã Thái Hòa, Nghệ An</td><td>2024</td></tr>
                            <tr><td>Nghệ An</td><td>Thành phố Vinh</td><td class="text-start">Số 38, Nhà liền kề KĐT Trung Đô, Đại Lộ Lê Nin, P.Hưng Dũng, TP. Vinh, T. Nghệ An</td><td>2024</td></tr>
                            <tr><td>Ninh Bình</td><td>Thành phố Ninh Bình</td><td class="text-start">195 đường Lê Đại Hành, Phố Thanh Sơn, Phường Thanh Bình, Thành phố Ninh Bình, Tỉnh Ninh Bình</td><td>2024</td></tr>
                            <tr><td>Ninh Bình</td><td>Thành phố Tam Điệp</td><td class="text-start">263 Quang Trung, Tổ 9, Phường Trung Sơn, Thành phố Tam Điệp, Tỉnh Ninh Bình</td><td>2024</td></tr>
                            <tr><td>Ninh Bình</td><td>Huyện Kim Sơn</td><td class="text-start">Xóm 11, Xã Quang Thiện, Huyện Kim Sơn, Tỉnh Ninh Bình</td><td>2024</td></tr>
                            <tr><td>Ninh Bình</td><td>Huyện Yên Khánh</td><td class="text-start">248 Lê Thánh Tông, Thị trấn Yên Ninh, Huyện Yên Khánh, Tỉnh Ninh Bình</td><td>2024</td></tr>
                            <tr><td>Nam Định</td><td>Huyện Hải Hậu</td><td class="text-start">Đội 6A, Quốc lộ 37B , Xã Hải Thanh, Huyện Hải Hậu, Nam Định</td><td>2024</td></tr>
                            <tr><td>Nam Định</td><td>Huyện Nam Trực</td><td class="text-start">Ngã tư phố Cầu, Xã Nam Hùng, Huyện Nam Trực, Nam Định</td><td>2024</td></tr>
                            <tr><td>Nam Định</td><td>Huyện Giao Thủy</td><td class="text-start">Xóm 14 Hoành Sơn, Huyện Giao Thủy, Tỉnh Nam Định</td><td>2024</td></tr>
                            <tr><td>Nam Định</td><td>Huyện Ý Yên</td><td class="text-start">Quốc lộ 38B, Thôn Tu Cổ, Xã Yên Khánh, Huyện Ý Yên, Tỉnh Nam Định</td><td>2024</td></tr>
                            <tr><td>Nam Định</td><td>Thành phố Nam Định</td><td class="text-start">Số 6 đường Đông A, Khu đô thị Hòa Vượng, Phường Lộc Vượng, Thành phố Nam Định, Nam Định</td><td>2024</td></tr>
                            <tr><td>Sơn La</td><td>Thành phố Sơn La</td><td class="text-start">08 Nguyễn Lương Bằng, Tổ 4, Phường Quyết Thắng, Thành phố Sơn La, Tỉnh Sơn La</td><td>2024</td></tr>
                            <tr><td>Sơn La</td><td>Huyện Mai Sơn</td><td class="text-start">Tiểu Khu 5, Thị trấn Hát Lót, Huyện Mai Sơn, Tỉnh Sơn La</td><td>2024</td></tr>
                            <tr><td>Sơn La</td><td>Huyện Thuận Châu</td><td class="text-start">Số 41, Tiểu khu 2, Thị Trấn Thuận Châu, Huyện Thuận Châu, Sơn La</td><td>2024</td></tr>
                            <tr><td>Sơn La</td><td>Huyện Mộc Châu</td><td class="text-start">55 Phan Đình Giót, Tổ 4, Thị trấn Mộc Châu, Huyện Mộc Châu, Tỉnh Sơn La</td><td>2024</td></tr>
                            <tr><td>Thái Bình</td><td>Huyện Thái Thụy</td><td class="text-start">Tổ dân phố Bao Trình, Thị trấn Diêm Điền, Huyện Thái Thụy, Thái Bình</td><td>2024</td></tr>
                            <tr><td>Thái Bình</td><td>Huyện Tiền Hải</td><td class="text-start">Thôn Đông, Xã Tây Giang, Huyện Tiền Hải, Thái Bình</td><td>2024</td></tr>
                            <tr><td>Thái Bình</td><td>Huyện Đông Hưng</td><td class="text-start">Số 253 Tổ 1, Thị Trấn Đông Hưng, Huyện Đông Hưng, Tỉnh Thái Bình</td><td>2024</td></tr>
                            <tr><td>Thái Bình</td><td>Huyện Quỳnh Phụ</td><td class="text-start">200 Trần Hưng Đạo, Thị trấn Quỳnh Côi, Huyện Quỳnh Phụ, Tỉnh Thái Bình</td><td>2024</td></tr>
                            <tr><td>Thái Bình</td><td>Phường Bồ Xuyên</td><td class="text-start">Số 168 Nguyễn Thị Minh Khai, Tổ 46, Phường Bồ Xuyên, Thành phố Thái Bình</td><td>2024</td></tr>
                            <tr><td>Thanh Hóa</td><td>Thị Xã Nghi Sơn</td><td class="text-start">Số 30, Đường Lê Đình Châu, Phường Hải Hòa, Thị Xã Nghi Sơn, Thanh Hóa</td><td>2024</td></tr>
                            <tr><td>Thanh Hóa</td><td>Thị Xã Bỉm Sơn</td><td class="text-start">114 Nguyễn Huệ, P. Ngọc Trạo, Thị Xã Bỉm Sơn, Thanh Hóa</td><td>2024</td></tr>
                            <tr><td>Thanh Hóa</td><td>Thành phố Sầm Sơn</td><td class="text-start">Số 07, Hub 4, Phường Trường Sơn, Thành phố Sầm Sơn, Thanh Hóa</td><td>2024</td></tr>
                            <tr><td>Thanh Hóa</td><td>Huyện Hậu Lộc</td><td class="text-start">227, Khu 2, Thị Trấn Hậu Lộc, Huyện Hậu Lộc, Thanh Hóa</td><td>2024</td></tr>
                            <tr><td>Thanh Hóa</td><td>Thành phố Thanh Hóa</td><td class="text-start">Lô 04-05, MBQH 2072, Đường Nguyễn Duy Hiệu, Phường Đông Hương, Thành phố Thanh Hóa, Tỉnh Thanh Hoá</td><td>2024</td></tr>
                            <tr><td>Bình Định</td><td>Thị xã An Nhơn</td><td class="text-start">302 Ngô Gia Tự ,P.Bình Ðịnh, TX. An Nhơn, Bình Ðịnh</td><td>2024</td></tr>
                            <tr><td>Bình Định</td><td>Huyện Phù Cát</td><td class="text-start">466 Quang Trung, Thị trấn Ngô Mây, Huyện Phù Cát, Bình Định</td><td>2024</td></tr>
                            <tr><td>Bình Định</td><td>Huyện Hoài Nhơn</td><td class="text-start">222 Quang Trung, Thị trấn Bồng Sơn, Huyện Hoài Nhơn, Bình Định</td><td>2024</td></tr>
                            <tr><td>Đắk Lắk</td><td>Thành phố Buôn Ma Thuột</td><td class="text-start">11 Trần Hưng Đạo, Phường Thắng Lợi, Thành phố Buôn Ma Thuột, Tỉnh Đăk Lăk</td><td>2024</td></tr>
                            <tr><td>Đắk Lắk</td><td>Huyện Krông Pắc</td><td class="text-start">560 Giải Phóng, Tổ Dân Phố 3, Thị Trấn Phước An, Huyện Krông Pắc, Đăk Lăk</td><td>2024</td></tr>
                            <tr><td>Đắk Lắk</td><td>Thị xã Buôn Hồ</td><td class="text-start">749 Hùng Vương, Phường An Lạc, Thị xã Buôn Hồ, Đắk Lắk</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Huyện Hòa Vang</td><td class="text-start">224 Phạm Hùng, Xã Hòa Châu, Huyện Hòa Vang, Thành Phố Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Hải Châu</td><td class="text-start">182-184 Đường 2-9, Phường Hòa Cường Bắc, Quận Hải Châu, Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Thanh Khê</td><td class="text-start">399 Điện Biên Phủ, Phường Hoa Khê, Quận Thanh Khê, Thành Phố Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Gia Lai</td><td>Thành phố Pleiku</td><td class="text-start">67 Tăng Bạt Hổ, Phường Yên Đỗ, Thành phố Pleiku, Gia Lai</td><td>2024</td></tr>
                            <tr><td>Gia Lai</td><td>Thị xã An Khê</td><td class="text-start">1079 Quang Trung, P.An Phu, TX.An Khê, Tỉnh Gia Lai</td><td>2024</td></tr>
                            <tr><td>Huế</td><td>Thành phố Huế</td><td class="text-start">72 Phạm Văn Đồng, Phường Vỹ Dạ, TP. Huế, Thừa Thiên Huế</td><td>2024</td></tr>
                            <tr><td>Huế</td><td>Thị Trấn Phú Lộc</td><td class="text-start">133 Lý Thánh Tông, Thị Trấn Phú Lộc, Huế</td><td>2024</td></tr>
                            <tr><td>Khánh Hòa</td><td>Thành phố Nha Trang</td><td class="text-start">STH 22.32-STH 22.33, đường số 4, KĐT Lê Hồng Phong I, Phường Phước Hải, TP Nha Trang, tỉnh Khánh Hoà</td><td>2024</td></tr>
                            <tr><td>Khánh Hòa</td><td>Thị xã Ninh Hòa</td><td class="text-start">03 Nguyễn Thị Ngọc Oanh, Phường Ninh Hiệp, Thị xã Ninh Hòa, Khánh Hòa</td><td>2024</td></tr>
                            <tr><td>Khánh Hòa</td><td>Thành phố Nha Trang</td><td class="text-start">42 Lê Thành Phương, P.Phương Sài,TP Nha Trang, Khánh Hòa</td><td>2024</td></tr>
                            <tr><td>Khánh Hòa</td><td>Huyện Vạn Ninh</td><td class="text-start">350 đường Hùng Vương, Thị Trấn Vạn Giã, Huyen Vạn Ninh, Khánh Hòa</td><td>2024</td></tr>
                            <tr><td>Khánh Hòa</td><td>Thành phố Cam Ranh</td><td class="text-start">467 Đường 3/4, phường Cam Linh, Thành phố Cam Ranh, Khánh Hòa</td><td>2024</td></tr>
                            <tr><td>Khánh Hòa</td><td>Huyện Diên Khánh</td><td class="text-start">Thôn Phú Ân Nam 4, Xã Diên An, Huyện Diên Khánh, Khánh Hòa</td><td>2024</td></tr>
                            <tr><td>Khánh Hòa</td><td>Huyện Cam Lâm</td><td class="text-start">20 Nguyễn Du, Thị Trấn Cam Đức, Huyện Cam Lâm, Khánh Hòa</td><td>2024</td></tr>
                            <tr><td>Kom Tum</td><td>Thành phố Kon Tum</td><td class="text-start">128 Phan Chu Trinh, Phường Quyết Thắng, Thành phố Kon Tum, Kon Tum</td><td>2024</td></tr>
                            <tr><td>Kom Tum</td><td>Huyện Ngọc Hồi</td><td class="text-start">771 Hùng Vương, Thị Trấn PleiKan, Huyện Ngọc Hồi, Kon Tum</td><td>2024</td></tr>
                            <tr><td>Phú Yên</td><td>Thành phố Tuy Hòa</td><td class="text-start">A11 Khu Đô Thị Hưng Phú, P.5, TP Tuy Hòa, Phú Yên</td><td>2024</td></tr>
                            <tr><td>Quảng Bình</td><td>Thành Phố Đồng Hới</td><td class="text-start">Đường Minh Mạng, TDP15, Phường Bắc Lý, Thành Phố Đồng Hới, Tỉnh Quảng Bình</td><td>2024</td></tr>
                            <tr><td>Quảng Bình</td><td>Huyện Quảng Trạch</td><td class="text-start">144 Hùng Vương, Phường Ba Đồn, Thị xã Ba Đồn, Huyện Quảng Trạch, Quảng Bình</td><td>2024</td></tr>
                            <tr><td>Quảng Ngãi</td><td>Thành phố Quảng Ngãi</td><td class="text-start">249 Phan Bội Châu, Phường Trần Hưng Đạo, Thành phố Quảng Ngãi, Quảng Ngãi</td><td>2024</td></tr>
                            <tr><td>Quảng Nam</td><td>Thành phố Hội An</td><td class="text-start">490 Hai Bà Trưng, P.Tân An, TP Hội An, Quảng Nam</td><td>2024</td></tr>
                            <tr><td>Quảng Nam</td><td>Thành phố Tam Kỳ</td><td class="text-start">53 Phan Bội Châu, Phường Tân Thanh, Thành phố Tam Kỳ, Quảng Nam</td><td>2024</td></tr>
                            <tr><td>Quảng Nam</td><td>Huyện Đại Lộc</td><td class="text-start">83 Đỗ Đăng Tuyển, TT. Ái Nghĩa, Huyện Đại Lộc, Quảng Nam</td><td>2024</td></tr>
                            <tr><td>Quảng Trị</td><td>Thành phố Đông Hà</td><td class="text-start">Số 20 Lê Lợi, Phường 5, Thành phố Đông Hà, Quảng Trị</td><td>2024</td></tr>
                            <tr><td>Bình Dương</td><td>Thành phố Thủ Dầu Một</td><td class="text-start">Ô 9 - 10, Lô B1, Đường D9, KDC Chánh Nghĩa, P.Chánh Nghĩa, TP. Thủ Dầu Một, Bình Dương</td><td>2024</td></tr>
                            <tr><td>Bình Dương</td><td>Thành phố Thuận An</td><td class="text-start">Ô 8, KDC Phú Hồng Lộc, Đường 22 tháng 12, Phường Thuận Giao, Thuận An, Bình Dương</td><td>2024</td></tr>
                            <tr><td>Bình Dương</td><td>Thị Xã Bến Cát</td><td class="text-start">Căn 01 Lô LK A11, Đường N1, Khu Đô Thị Thịnh Gia, Phường Tân Định, Thị Xã Bến Cát, Bình Dương</td><td>2024</td></tr>
                            <tr><td>Bình Dương</td><td>Huyện Dầu Tiếng</td><td class="text-start">Số 260 Đường 13/3 Khu Phố 4B, Thị Trấn Dầu Tiếng, Huyện Dầu Tiếng, Tỉnh Bình Dương</td><td>2024</td></tr>
                            <tr><td>Bình Dương</td><td>Huyện Phú Giáo</td><td class="text-start">606 DT741, TT. Phước Vĩnh, H. Phú Giáo, Bình Dương</td><td>2024</td></tr>
                            <tr><td>Bình Dương</td><td>Thành phố Dĩ An</td><td class="text-start">24B Lý Thường Kiệt, P. Dĩ An, TP. Dĩ An, Tỉnh Bình Dương</td><td>2024</td></tr>
                            <tr><td>Bình Dương</td><td>TX.Tân Uyên</td><td class="text-start">Số 39, Tổ 2, Đường DT746, Ấp Tân Hóa, X.Tân Vĩnh Hiệp, TX.Tân Uyên, Bình Dương</td><td>2024</td></tr>
                            <tr><td>Bình Phước</td><td>TX. Phước Long</td><td class="text-start">Đường 759, KP6, P. Long Phước, TX. Phước Long, T. Bình Phước</td><td>2024</td></tr>
                            <tr><td>Bình Phước</td><td>TP. Đồng Xoài</td><td class="text-start">498 Quốc Lộ 14, KP. Phú Thịnh, P. Tân Phú, TP. Đồng Xoài, Bình Phước</td><td>2024</td></tr>
                            <tr><td>Bình Thuận</td><td>Huyện Tuy Phong</td><td class="text-start">59 Quang Trung, Thị trấn Phan Rí Cửa, Huyện Tuy Phong, Bình Thuận</td><td>2024</td></tr>
                            <tr><td>Bình Thuận</td><td>Thành phố Phan Thiết</td><td class="text-start">59 Thủ Khoa Huân, Phường Phú Thủy, Thành phố Phan Thiết, Tỉnh Bình Thuận</td><td>2024</td></tr>
                            <tr><td>Bình Thuận</td><td>Thị xã La Gi</td><td class="text-start">170 Thống Nhất, Phường Tân Thiện, Thị xã La Gi, Tỉnh Bình Thuận</td><td>2024</td></tr>
                            <tr><td>Bình Thuận</td><td>Huyện Đức Linh</td><td class="text-start">407-409 Cách Mạng Tháng 8, Võ Xu, Đức Linh, Bình Thuận</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>TP. Biên Hòa</td><td class="text-start">791 đường Đồng Khởi, KP8, P. Tân Phong, TP. Biên Hòa, T. Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Huyện Định Quán</td><td class="text-start">107 Tổ 2, Ấp 114, Thị trấn Định Quán, Huyện Định Quán, Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Huyện Thống Nhất</td><td class="text-start">42/1A Đức Long 1, Xã Gia Tân 2, Huyện Thống Nhất, Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Huyện Trảng Bom</td><td class="text-start">1887 Quốc lộ 1A, Ấp Quảng Hòa, Xã Quang Tiến, Huyện Trảng Bom, Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Huyện Tân Phú</td><td class="text-start">1002, Khu 11, Thị Trấn Tân Phú, Huyện Tân Phú, Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Huyện Vĩnh Cửu</td><td class="text-start">43 Quang Trung, Khu Phố 5, Thị Trấn Vĩnh An, Huyện Vĩnh Cửu, Tỉnh Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Thành phố Biên Hòa</td><td class="text-start">88 Bùi Văn Hòa, Khu phố 3, Phường Long Bình Tân, Thành phố Biên Hòa, Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Huyện Nhơn Trạch</td><td class="text-start">B2/18 Ấp 1, Xã Long Thọ, Huyện Nhơn Trạch, Tỉnh Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Huyện Xuân Lộc</td><td class="text-start">Số 388B Hùng Vương, Thị trấn Gia Ray, Huyện Xuân Lộc, Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Huyện Long Thành</td><td class="text-start">Số 12 Lê Duẩn, Xã An Phước, Huyện Long Thành, Tỉnh Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Thị xã Long Khánh</td><td class="text-start">47 Cách Mạng Tháng 8, Phường Xuân An, Thị xã Long Khánh, Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Lâm Đồng</td><td>Thành phố Đà Lạt</td><td class="text-start">36 Pasteur, Phường 4, Thành phố Đà Lạt, Lâm Đồng</td><td>2024</td></tr>
                            <tr><td>Lâm Đồng</td><td>Huyện Đức Trọng</td><td class="text-start">21 Đường 2 Tháng 4, Thị Trấn Liên Nghĩa , Huyện Đức Trọng, Lâm Đồng</td><td>2024</td></tr>
                            <tr><td>Lâm Đồng</td><td>Thành phố Bảo Lộc</td><td class="text-start">104A Phan Đình Phùng, Phường 2, Thành phố Bảo Lộc, Lâm Đồng</td><td>2024</td></tr>
                            <tr><td>Lâm Đồng</td><td>Huyện Di Linh</td><td class="text-start">745A Hùng Vương, Thị trấn Di Linh, Huyện Di Linh, Lâm Đồng</td><td>2024</td></tr>
                            <tr><td>Lâm Đồng</td><td>Huyện Đơn Dương</td><td class="text-start">283 Đường 2 Tháng 4 , Thị trấn Thạnh Mỹ, Huyện Đơn Dương, Lâm Đồng</td><td>2024</td></tr>
                            <tr><td>Ninh Thuận</td><td>Huyện Ninh Sơn</td><td class="text-start">275 Lê Duẩn, Khu phố 2, Thị trấn Tân Sơn, Huyện Ninh Sơn, Ninh Thuận</td><td>2024</td></tr>
                            <tr><td>Ninh Thuận</td><td>Thành phố Phan Rang - Tháp Chàm</td><td class="text-start">352-354 Ngô Gia Tự, Phường Tấn Tài, Thành phố Phan Rang - Tháp Chàm , Ninh Thuận</td><td>2024</td></tr>
                            <tr><td>Tây Ninh</td><td>Thành phố Tây Ninh</td><td class="text-start">476 Đường 30/4, Khu phố 5, Phường 3, Thành phố Tây Ninh, Tây Ninh</td><td>2024</td></tr>
                            <tr><td>Tây Ninh</td><td>Huyện Trảng Bàng</td><td class="text-start">Khu phố Lộc An, Thị xã Trảng Bàng, Huyện Trảng Bàng, Tây Ninh</td><td>2024</td></tr>
                            <tr><td>Tây Ninh</td><td>Huyện Tân Châu</td><td class="text-start">197 Đường Tôn Đức Thắng, Thị Trấn Tân Châu, Huyện Tân Châu, Tỉnh Tây Ninh</td><td>2024</td></tr>
                            <tr><td>Bà Rịa-Vũng Tàu</td><td>Huyện Châu Đức</td><td class="text-start">265 Lê Hồng Phong, Thị trấn Ngãi Giao, Huyện Châu Đức, Bà Rịa - Vũng Tàu</td><td>2024</td></tr>
                            <tr><td>Bà Rịa-Vũng Tàu</td><td>Huyện Xuyên Mộc</td><td class="text-start">80 Quốc Lộ 55, Khu phố Phước Hòa, Thị trấn Phước Bửu, Huyện Xuyên Mộc, Bà Rịa - Vũng Tàu</td><td>2024</td></tr>
                            <tr><td>Bà Rịa-Vũng Tàu</td><td>Thị xã Phú Mỹ</td><td class="text-start">321 Trường Chinh, Phường Phú Mỹ, Thị xã Phú Mỹ, Bà Rịa - Vũng Tàu</td><td>2024</td></tr>
                            <tr><td>Bà Rịa-Vũng Tàu</td><td>Thành phố Vũng Tàu</td><td class="text-start">186 Trương Công Định, Phường 3, Thành phố Vũng Tàu, Bà Rịa - Vũng Tàu</td><td>2024</td></tr>
                            <tr><td>Bà Rịa-Vũng Tàu</td><td>Thành phố Bà Rịa</td><td class="text-start">251 Nguyễn Văn Linh, Phường Phước Nguyên, Thành phố Bà Rịa, Bà Rịa - Vũng Tàu</td><td>2024</td></tr>
                            <tr><td>An Giang</td><td>Thành phố Long Xuyên</td><td class="text-start">132 Trần Hưng Đạo, Phường Mỹ Bình, Thành phố Long Xuyên, An Giang</td><td>2024</td></tr>
                            <tr><td>An Giang</td><td>TP Châu Đốc</td><td class="text-start">513 Thủ khoa Huân, P.Châu Phú B, TP Châu Đốc, An Giang</td><td>2024</td></tr>
                            <tr><td>An Giang</td><td>TX Tân Châu</td><td class="text-start">84 Tôn Đức Thắng, P. Long Thạnh, TX Tân Châu, An Giang</td><td>2024</td></tr>
                            <tr><td>An Giang</td><td>Huyện Chợ Mới</td><td class="text-start">9C Trần Hưng Đạo, Thị trấn Chợ Mới, Huyện Chợ Mới, An Giang</td><td>2024</td></tr>
                            <tr><td>An Giang</td><td>Huyện Châu Phú</td><td class="text-start">281 Quốc Lộ 91, Thị trấn Cái Dầu, Huyện Châu Phú, An Giang</td><td>2024</td></tr>
                            <tr><td>An Giang</td><td>Huyện Phú Tân</td><td class="text-start">Số 05, Đường Lê Duẩn, Khóm Trung 3, Thị trấn Phú Mỹ, Huyện Phú Tân, tỉnh An Giang</td><td>2024</td></tr>
                            <tr><td>Bạc Liêu</td><td>Huyện Giá Rai</td><td class="text-start">Số 250, Quốc lộ 1A, Thị Trấn Hộ Phòng, Huyện Giá Rai, Bạc Liêu</td><td>2024</td></tr>
                            <tr><td>Bạc Liêu</td><td>Thành phố Bạc Liêu</td><td class="text-start">44-45 Đường Ninh Bình Khóm 4, Phường 2, Thành phố Bạc Liêu, Bạc Liêu</td><td>2024</td></tr>
                            <tr><td>Bến Tre</td><td>Thành phố Bến Tre</td><td class="text-start">285K, Khu Phố 3, Phường Phú Tân, Thành phố Bến Tre, Bến Tre</td><td>2024</td></tr>
                            <tr><td>Bến Tre</td><td>Huyện Ba Tri</td><td class="text-start">Lô số 4, Đường 1, Khu Đô Thị Việt Sinh, TT Ba Tri, H. Ba Tri, Bến Tre</td><td>2024</td></tr>
                            <tr><td>Cà Mau</td><td>Huyện Cái Nước</td><td class="text-start">288A Quốc lộ 1A, Thị trấn Cái Nước, Huyện Cái Nước, Cà Mau</td><td>2024</td></tr>
                            <tr><td>Cà Mau</td><td>Thành phố Cà Mau</td><td class="text-start">135 Trần Hưng Đạo, Khóm 8, Phường 5, Thành phố Cà Mau, Cà Mau</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Quận Thốt Nốt</td><td class="text-start">328 Nguyễn Công Trứ , Phường Thốt Nốt, Quận Thốt Nốt, Cần Thơ</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Huyện Cờ Đỏ</td><td class="text-start">Số 08  Đường Hà Huy Giáp, Ấp Thới Hòa, Thị Trấn Cờ Đỏ, Huyện Cờ Đỏ, TP.Cần Thơ</td><td>2024</td></tr>
                            <tr><td>Đồng Tháp</td><td>Thị trấn Lấp Vò</td><td class="text-start">Lý Thái Tổ, Khóm Bình Thành 2, Thị trấn Lấp Vò, Đồng Tháp</td><td>2024</td></tr>
                            <tr><td>Đồng Tháp</td><td>Thị xã Hồng Ngự</td><td class="text-start">50 Nguyễn Huệ, Phường An Thạnh, Thị xã Hồng Ngự, Đồng Tháp</td><td>2024</td></tr>
                            <tr><td>Đồng Tháp</td><td>Thành phố Cao Lãnh</td><td class="text-start">4 Lý Thường Kiệt, Phường 1, Thành phố Cao Lãnh, Đồng Tháp</td><td>2024</td></tr>
                            <tr><td>Đồng Tháp</td><td>Thành phố Sa Đéc</td><td class="text-start">225 Nguyễn Sinh Sắc, Khóm 2, Phường 2, Thành phố Sa Đéc, Đồng Tháp</td><td>2024</td></tr>
                            <tr><td>Hậu Giang</td><td>Huyện Châu Thành A</td><td class="text-start">281, Quốc Lộ 61, Ấp Xẻo Cao, Xã Thạnh Xuân , Huyện Châu Thành A, Tỉnh Hậu Giang</td><td>2024</td></tr>
                            <tr><td>Hậu Giang</td><td>Thành phố Vị Thanh</td><td class="text-start">193H, Trần Hưng Đạo, Khu vực 3, Phường 5, Thành phố Vị Thanh, Hậu Giang</td><td>2024</td></tr>
                            <tr><td>Hậu Giang</td><td>Thị xã Ngã Bảy</td><td class="text-start">13, Nguyễn Thị Minh Khai, Khu vực 3, Phường Ngã Bảy, Thị xã Ngã Bảy, Hậu Giang</td><td>2024</td></tr>
                            <tr><td>Kiên Giang</td><td>Huyện Tân Hiệp</td><td class="text-start">14 Khu phố Đông An , Thị trấn Tân Hiệp, Huyện Tân Hiệp, Kiên Giang</td><td>2024</td></tr>
                            <tr><td>Kiên Giang</td><td>Châu Thành</td><td class="text-start">Số 831 Quốc Lộ 61 Khu phố Minh An, Thị Trấn Minh Lương, Châu Thành, Kiên Giang</td><td>2024</td></tr>
                            <tr><td>Kiên Giang</td><td>Thành phố Rạch Giá</td><td class="text-start">259 Nguyễn Bỉnh Khiêm, Phường Vĩnh Thanh, Thành phố Rạch Giá, Kiên Giang</td><td>2024</td></tr>
                            <tr><td>Kiên Giang</td><td>Huyện Kiên Lương</td><td class="text-start">347 Quốc Lộ 80, Thị trấn Kiên Lương, Huyện Kiên Lương, Kiên Giang</td><td>2024</td></tr>
                            <tr><td>Long An</td><td>Thành phố Tân An</td><td class="text-start">142 Hùng Vương, Phường 2, Thành phố Tân An, Long An</td><td>2024</td></tr>
                            <tr><td>Long An</td><td>Huyện Bến Lức</td><td class="text-start">65, Phan Văn Mãng, Thị trấn Bến Lức, Bến Lức,Long An.</td><td>2024</td></tr>
                            <tr><td>Long An</td><td>Huyện Đức Hòa</td><td class="text-start">197/1C Khu 03, Thị Trấn Đức Hòa, Huyện Đức Hòa, Long An</td><td>2024</td></tr>
                            <tr><td>Long An</td><td>Huyện Cần Giuộc</td><td class="text-start">80 Tỉnh Lộ 835, Xã Trường Bình, Huyện Cần Giuộc, Long An</td><td>2024</td></tr>
                            <tr><td>Long An</td><td>TX Kiến Tường</td><td class="text-start">177 Lý thường Kiệt, Phường 1, TX Kiến Tường, Long An</td><td>2024</td></tr>
                            <tr><td>Sóc Trăng</td><td>huyện Trần Đề</td><td class="text-start">Ấp Giồng Chùa, Thị Trấn Trần Đề, huyện Trần Đề, tỉnh Sóc Trăng, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Sóc Trăng</td><td>Thị xã Vĩnh Châu</td><td class="text-start">319 Nguyễn Huệ, Phường 1, Thị xã Vĩnh Châu, Sóc Trăng</td><td>2024</td></tr>
                            <tr><td>Sóc Trăng</td><td>Thành Phố Sóc Trăng</td><td class="text-start">Số 76 Lê Duẩn, Phường 3, Thành Phố Sóc Trăng,Tỉnh Sóc Trăng, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Tiền Giang</td><td>Thành phố Mỹ Tho</td><td class="text-start">284 Ấp Bắc, Phường 10, Thành phố Mỹ Tho, Tiền Giang</td><td>2024</td></tr>
                            <tr><td>Tiền Giang</td><td>Thị xã Cai Lậy</td><td class="text-start">91 Võ Thanh Tâm, Phường 4, Thị xã Cai Lậy, Tiền Giang</td><td>2024</td></tr>
                            <tr><td>Tiền Giang</td><td>Thị Xã Gò Công</td><td class="text-start">31 Đồng Khởi, Khu Phố 1, Phường 4, Thị Xã Gò Công, Tiền Giang</td><td>2024</td></tr>
                            <tr><td>Tiền Giang</td><td>Huyện Cái Bè</td><td class="text-start">405, Tổ 6, Khu 3, Thị trấn Cái Bè, Huyện Cái Bè,tỉnh  Tiền Giang</td><td>2024</td></tr>
                            <tr><td>Trà Vinh</td><td>Huyện Cầu Ngang</td><td class="text-start">Số 335 Khóm Minh Thuận B, Thị Trấn Cầu Ngang, Huyện Cầu Ngang, Tỉnh Trà Vinh</td><td>2024</td></tr>
                            <tr><td>Trà Vinh</td><td>Thành phố Trà Vinh</td><td class="text-start">45 Nguyễn Đáng, Khóm 6, Phường 7, Thành phố Trà Vinh, Trà Vinh</td><td>2024</td></tr>
                            <tr><td>Vĩnh Long</td><td>Thành phố Vĩnh Long</td><td class="text-start">68/11A Phạm Thái Bường, Phường 4, Thành phố Vĩnh Long, Vĩnh Long</td><td>2024</td></tr>
                            <tr><td>Vĩnh Long</td><td>Huyện Vũng Liêm</td><td class="text-start">Số 18B, Khóm 2, Thị trấn Vũng Liêm, Vũng Liêm, Vĩnh Long</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">Tầng1, Tòa nhà Housing, 299 Trung Kính, Phường Yên Hòa, Quận Cầu Giấy, Thành phố Hà Nội, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Cầu Giấy</td><td class="text-start">46 Hồ Tùng Mậu, Quận Cầu Giấy, Thành Phố Hà Nội</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Quận Đống Đa</td><td class="text-start">216 Thái Hà, Quận Đống Đa, TP HN</td><td>2024</td></tr>
                            <tr><td>Hà Nội</td><td>Huyện Thạch Thất</td><td class="text-start">Thôn 4, Xã Thạch Hòa, Huyện Thạch Thất, Thành Phố Hà Nội, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 7</td><td class="text-start">Số 376A Nguyễn Thị Thập, Phường Tân Quy, Quận 7, Thành phố Hồ Chí Minh, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 7</td><td class="text-start">489-491 Huỳnh Tấn Phát- P.Tân Thuận Đông- Quận 7 - TP. HCM</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Thành phố Thủ Đức</td><td class="text-start">157 Lê Văn Việt, Phường Hiệp Phú, Quận 9, Thành phố Hồ Chí Minh, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Hồ Chí Minh</td><td>Quận 12</td><td class="text-start">368 Tô Ký, P. Tân Chánh Hiệp, Q. 12, TP Hồ Chí Minh, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Bắc Giang</td><td>Thành phố Bắc Giang</td><td class="text-start">Số 100 đường Nguyễn Thị Lưu, P. Ngô Quyền, TP. Bắc Giang, Tỉnh Bắc Giang</td><td>2024</td></tr>
                            <tr><td>Bắc Kạn</td><td>Thành Phố Bắc Kạn</td><td class="text-start">Ki-ốt Bán Hàng A10-A11-A12 Chợ Bắc Kạn, Đường Võ Nguyên Giáp, Phường Sông Cầu, Thành Phố Bắc Kạn, Tỉnh Bắc Kạn, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Bắc Ninh</td><td>Thành phố Bắc Ninh</td><td class="text-start">Số 2 Nguyễn Văn Cừ, Phường Ninh Xá, Thành phố Bắc Ninh, Tỉnh Bắc Ninh, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Cao Bằng</td><td>Thành phố Cao Bằng</td><td class="text-start">Số 01 Phố Kim Đồng, Phường Hợp Giang, Thành phố Cao Bằng, Tỉnh Cao Bằng, Việt Nam.</td><td>2024</td></tr>
                            <tr><td>Hà Giang</td><td>Thành phố Hà Giang</td><td class="text-start">Số nhà 135-137 đường Trần Hưng Đạo, tổ 8, phường Trần Phú, thành phố Hà Giang, tỉnh Hà Giang</td><td>2024</td></tr>
                            <tr><td>Lạng Sơn</td><td>Thành phố Lạng Sơn</td><td class="text-start">Số 117, Đường Trần Đăng Ninh, P. Hoàng Văn Thụ, TP. Lạng Sơn, Tỉnh Lạng Sơn, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Phú Thọ</td><td>Thành phố Việt Trì</td><td class="text-start">Số 1826 đường Hùng Vương, P. Nông Trang, TP. Việt Trì, Tỉnh Phú Thọ, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Quảng Ninh</td><td>Thành phố Móng Cái</td><td class="text-start">Gian hàng số 03.05.06 HV Tầng 01, Số 01, đường Hùng Vương, Phường Trần Phú, TP Móng Cái, Tỉnh Quảng Ninh</td><td>2024</td></tr>
                            <tr><td>Thái Nguyên</td><td>Thành phố Thái Nguyên</td><td class="text-start">477, Đường Lương Ngọc Quyến, P Phan Đình Phùng, TP Thái Nguyên, Tỉnh Thái Nguyên</td><td>2024</td></tr>
                            <tr><td>Tuyên Quang</td><td>Thành phố Tuyên Quang</td><td class="text-start">164- 166- 168 Bình Thuận, Tổ 30, P. Tân Quang, TP.Tuyên Quang, Tỉnh Tuyên Quang, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Vĩnh Phúc</td><td>Thành phố Vĩnh Yên</td><td class="text-start">Số nhà 11-13 Đường Trần Phú, Phường Liên Bảo, Thành Phố Vĩnh Yên, Tỉnh Vĩnh Phúc, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Hà Nam</td><td>Thành phố Phủ Lý</td><td class="text-start">Số 34 Biên Hoà, phường Minh Khai, thành phố Phủ lý, tỉnh Hà Nam</td><td>2024</td></tr>
                            <tr><td>Hà Tĩnh</td><td>Thành phố Hà Tĩnh</td><td class="text-start">30-32 Trần Phú, P. Bắc Hà, TP. Hà Tĩnh, Tỉnh Hà Tĩnh, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Nam Định</td><td>Thành phố Nam Định</td><td class="text-start">Số 174,176,178 đường Trần Hưng Đạo,  Phường Trần Hưng Đạo, TP Nam Định, Tỉnh Nam Định, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Nghệ An</td><td>Thành phố Vinh</td><td class="text-start">Số 129 Nguyễn Thị Minh Khai, Phường Lê Mao, Thành Phố Vinh, Tỉnh Nghệ An, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Ninh Bình</td><td>Thành phố Ninh Bình</td><td class="text-start">Số 953 đường Trần Hưng Đạo, phường Vân Giang, thành phố Ninh Bình, tỉnh Ninh Bình, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Quảng Bình</td><td>Thành phố Đồng Hới</td><td class="text-start">Số 156 Đường Trần Hưng Đạo, Phường Đồng Phú, TP. Đồng Hới, Tỉnh Quảng Bình, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Quảng Trị</td><td>Thành phố Đông Hà</td><td class="text-start">Số 21 Hùng Vương, Khu phố 8, Phường 1, TP Đông Hà, Tỉnh Quảng Trị, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Thanh Hóa</td><td>Thành phố Thanh Hóa</td><td class="text-start">Số 175 Trần Phú - Phường Ba Đình - TP Thanh Hóa - Tỉnh Thanh Hóa</td><td>2024</td></tr>
                            <tr><td>Điện biên</td><td>Thành phố Điện Biên Phủ</td><td class="text-start">Tổ dân Phố 6, Phường Tân Thanh, Thành phố Điện Biên Phủ, Tỉnh Điện Biên</td><td>2024</td></tr>
                            <tr><td>Hải Dương</td><td>Thành phố Hải Dương</td><td class="text-start">Số 108 Nguyễn Lương Bằng, Phường Bình Hàn, Thành Phố Hải Dương, Tỉnh Hải Dương, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Hải Phòng</td><td>Quận Ngô Quyền</td><td class="text-start">112 Lạch Tray, P. Lạch Tray, Q. Ngô Quyền, TP Hải Phòng, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Hòa Bình</td><td>Thành phố Hòa Bình</td><td class="text-start">Số 240, tổ 22, Phường Phương Lâm, TP. Hòa Bình, Tỉnh Hòa Bình</td><td>2024</td></tr>
                            <tr><td>Hưng Yên</td><td>Thành phố Hưng Yên</td><td class="text-start">Số 39-41 Đường Điện Biên 1, Phường Lê Lợi, Thành Phố Hưng Yên, Tỉnh Hưng Yên</td><td>2024</td></tr>
                            <tr><td>Lai Châu</td><td>Thành phố Lai Châu</td><td class="text-start">Số 330 Đường Trần Phú, phường Tân Phong, Tp. Lai Châu, tỉnh Lai Châu</td><td>2024</td></tr>
                            <tr><td>Lào Cai</td><td>Thành phố Lào Cai</td><td class="text-start">Số nhà 030 tổ 10 đường Nhạc Sơn, Phường Duyên Hải, TP Lào Cai, Tỉnh Lào Cai, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Sơn La</td><td>Thành phố Sơn La</td><td class="text-start">TỔ 12, PHƯỜNG CHIỀNG LỀ, THÀNH PHỐ SƠN LA, TỈNH SƠN LA, VIỆT NAM</td><td>2024</td></tr>
                            <tr><td>Thái Bình</td><td>Thành phố Thái Bình</td><td class="text-start">355 Lý Bôn, P Đề Thám, TP Thái Bình</td><td>2024</td></tr>
                            <tr><td>Yên Bái</td><td>Thành phố Yên Bái</td><td class="text-start">Số nhà 769, Đ. Điện Biên, tổ 48, P. Minh Tân, TP. Yên Bái, Tỉnh Yên Bái, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Bình Định</td><td>Thành phố Quy Nhơn</td><td class="text-start">267-269 đường Lê Hồng Phong, Phường Lê Hồng Phong, TP Quy nhơn- Tỉnh Bình Định</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Hải Châu</td><td class="text-start">Lô A1 Nguyễn Văn Linh nối dài, Phường Nam Dương, Quận Hải Châu, TP Đà Nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Liên Chiểu</td><td class="text-start">Số 671-673 Tôn Đức Thắng - Phường Hòa Khánh Bắc - Quận Liên Chiểu - TP Đà nẵng</td><td>2024</td></tr>
                            <tr><td>Đà Nẵng</td><td>Quận Sơn Trà</td><td class="text-start">07-09 Nguyễn Văn Thoại, Phường An Hải Đông, Quận Sơn Trà, Thành Phố Đà Nẵng, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Đắk Lắk</td><td>Thành phố Buôn Ma Thuột</td><td class="text-start">Số 37 Lê Thánh Tông, P Thắng Lợi, TP Buôn Ma Thuột, Tỉnh Đắk Lắk</td><td>2024</td></tr>
                            <tr><td>Đắk Nông</td><td>Thành phố Gia Nghĩa</td><td class="text-start">Số 81 Huỳnh Thúc Kháng, Phường Nghĩa Thành, Thành phố Gia Nghĩa, Tỉnh Đắk Nông, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Gia Lai</td><td>Thành phố Pleiku</td><td class="text-start">39 Trần Phú, P. Diên Hồng, TP. Pleiku, Tỉnh Gia Lai, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Khánh Hòa</td><td>Thành phố Nha Trang</td><td class="text-start">Số 69 Quang Trung, P. Lộc Thọ, TP. Nha Trang, Tỉnh Khánh Hòa, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Kom Tum</td><td>Thành phố Kon Tum</td><td class="text-start">Số 390 Trần Hưng Đạo, Phường Quyết Thắng, Thành phố Kon Tum, Tỉnh Kon Tum, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Lâm Đồng</td><td>Huyện Đức Trọng</td><td class="text-start">Số 39 - 41 Thống Nhất, Thị trấn Liên Nghĩa, Huyền Đức Trọng, Tỉnh Lâm Đồng, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Ninh Thuận</td><td>Thành phố Phan Rang-Tháp Chàm</td><td class="text-start">Số 362-364 Thống nhất, Phường Phủ Hà, TP Phan Rang-Tháp chàm, Tỉnh Ninh Thuận, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Phú Yên</td><td>Thành phố Tuy Hòa</td><td class="text-start">363 Trần Hưng Đạo, Phường 6, TP Tuy Hòa, Tỉnh Phú Yên</td><td>2024</td></tr>
                            <tr><td>Quảng Nam</td><td>Thành phố Tam Kỳ</td><td class="text-start">295 Phan Chu Trinh, Phường Phước Hòa, Thành phố Tam Kỳ, Tỉnh Quảng Nam, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Quảng Ngãi</td><td>Thành phố Quảng Ngãi</td><td class="text-start">Số 411 Quang Trung, Phường Nguyễn Nghiêm, TP Quảng Ngãi, Tỉnh Quãng Ngãi, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Huế</td><td>Thành phố Huế</td><td class="text-start">10 Hùng Vương, Phường Phú Thuận,TP Huế, Huế</td><td>2024</td></tr>
                            <tr><td>Bà Rịa-Vũng Tàu</td><td>Thành phố Vũng Tàu</td><td class="text-start">Số 155 Nguyễn Thái Học, phường 7, Thành phố Vũng Tàu, Tỉnh Bà Rịa Vũng Tàu, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Bình Dương</td><td>Thành phố Thủ Dầu Một</td><td class="text-start">Số 15 Yersin, Tổ 15, Khu phố 1, Phường Phú Cường , TP. Thủ Đầu Một, Tỉnh Bình Dương, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Bình Phước</td><td>Thành Phố Đồng Xoài</td><td class="text-start">976-978-980 Phú Riềng Đỏ, Khu phố Phước Thọ, Phường Tân Thiện, Thị Xã Đồng Xoài, Tỉnh Bình Phước</td><td>2024</td></tr>
                            <tr><td>Bình Thuận</td><td>Thành phố Phan Thiết</td><td class="text-start">245 Thủ Khoa Huân, Khu phố 7, Phường Phú Thủy, Thành Phố Phan Thiết, Tỉnh Bình Thuận, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Đồng Nai</td><td>Thành phố Biên Hòa</td><td class="text-start">282 Phạm Văn Thuận, Thành phố Biên Hoà, Đồng Nai</td><td>2024</td></tr>
                            <tr><td>Tây Ninh</td><td>Thành phố Tây Ninh</td><td class="text-start">Số 619 Đường Cách Mạng Tháng Tám, khu phố 2, P. 3, TP Tây Ninh, Tỉnh Tây Ninh, Việt Nam</td><td>2024</td></tr>
                            <tr><td>An Giang</td><td>Thành phố Long Xuyên</td><td class="text-start">Số 311/2B Trần Hưng Đạo, Khóm 7, Phường Mỹ Long, TP Long Xuyên, Tỉnh An Giang</td><td>2024</td></tr>
                            <tr><td>Bạc Liêu</td><td>Thành phố Bạc Liêu</td><td class="text-start">66 Đường Hòa Bình, Phường 3, TP Bạc Liêu, Tỉnh Bạc Liêu</td><td>2024</td></tr>
                            <tr><td>Bến Tre</td><td>Thành phố Bến Tre</td><td class="text-start">298A-299 đại lộ Đồng Khởi, P. Phú Tân, TP. Bến Tre, Tỉnh Bến Tre, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Cà Mau</td><td>Thành phố Cà Mau</td><td class="text-start">Số 11, Trần Hưng Đạo, Khu phố 6, P. 5, TP. Cà Mau, Tỉnh Cà Mau, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Cần Thơ</td><td>Quận Ninh Kiều</td><td class="text-start">198B đường 3/2, P. Hưng Lợi, Q. Ninh Kiều, TP. Cần Thơ, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Đồng Tháp</td><td>Thành phố Cao Lãnh</td><td class="text-start">Số 162-164 Nguyễn Huệ, phường 2, Thành phố Cao Lãnh, Tỉnh Đồng Tháp, VN</td><td>2024</td></tr>
                            <tr><td>Hậu Giang</td><td>Thành phố Vị Thanh</td><td class="text-start">Số 2, đường Ngô Quốc Trị, Phường V, Thành phố Vị Thanh, Tỉnh Hậu Giang, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Kiên Giang</td><td>Thành phố Rạch Giá</td><td class="text-start">Số 159 đường Trần Phú, Phường Vĩnh Thanh, Thành phố Rạch Giá, Tỉnh Kiên Giang</td><td>2024</td></tr>
                            <tr><td>Long An</td><td>Thành phố Tân An</td><td class="text-start">Số 68 Hùng Vương, Phường 2, Thành Phố Tân An, Tỉnh Long An, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Sóc Trăng</td><td>Thành phố Sóc Trăng</td><td class="text-start">89 - 91 Hùng Vương, Phường 6, Thành Phố Sóc Trăng, Tỉnh Sóc Trăng</td><td>2024</td></tr>
                            <tr><td>Tiền Giang</td><td>Thành phố Mỹ Tho</td><td class="text-start">Số 152 Lý Thường Kiệt, P.6, TP. Mỹ Tho, Tỉnh Tiền Giang, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Trà Vinh</td><td>Thành phố Trà Vinh</td><td class="text-start">Số 289, Nguyễn Đáng, Khóm 6, Phường 7, TP. Trà Vinh, Tỉnh Trà Vinh, Việt Nam</td><td>2024</td></tr>
                            <tr><td>Vĩnh Long</td><td>Thành phố Vĩnh Long</td><td class="text-start">Số 139-139C đường Lê Thái Tổ, Phường 2, TP Vĩnh Long, Tỉnh Vĩnh Long</td><td>2024</td></tr>
                        </tbody>
                    </table>
                </div>
                
                <p class="note-text mt-3 mb-0">Ghi chú: (*) Thời gian triển khai có thể linh động điều chỉnh phụ thuộc vào thực tế triển khai.</p>
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