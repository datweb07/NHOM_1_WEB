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

    .content-section .table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: 600;
        vertical-align: middle;
    }

    .content-section .table td {
        color: #495057;
        vertical-align: middle;
        font-size: 14.5px;
    }

    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h4 { font-size: 20px; }
    .content-section.large-text h5 { font-size: 18px; }
    .content-section.large-text p,
    .content-section.large-text ul li,
    .content-section.large-text .table td {
        font-size: 17px;
        line-height: 1.7;
    }
</style>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/ho-tro">Hỗ trợ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách mạng di động FPT</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'mang-di-dong'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách mạng di động FPT</h3>

                <h4>A. MỤC ĐÍCH VÀ PHẠM VI ÁP DỤNG</h4>
                <h5>I. Mục đích</h5>
                <p>Quy định chính sách giá cước dịch vụ viễn thông di động mạng di động FPT.</p>
                <h5>II. Phạm vi áp dụng</h5>
                <p>Chuỗi cửa hàng FPT Shop và các chuỗi Branded Store (F.Studio, S.Studio,…) theo danh sách thông báo từ Ngành hàng.</p>

                <h4>B. QUY ĐỊNH CHÍNH SÁCH GIÁ CƯỚC DỊCH VỤ</h4>
                <h5>I. Loại thuê bao</h5>
                <p>Áp dụng với thuê bao trả trước.</p>

                <h5>II. Thời hạn sử dụng</h5>
                <p>Ngay khi kích hoạt, thuê bao có thời hạn sử dụng là 60 ngày.<br>
                Khi thực hiện các giao dịch có phát sinh cước, nạp tiền thì thời hạn sử dụng sẽ tăng lên là 90 ngày tính từ ngày phát sinh giao dịch.<br>
                Trong trường hợp kết thúc thời hạn sử dụng 90 ngày, thuê bao không thực hiện một trong các giao dịch có phát sinh cước, nạp tiền, thì sẽ chuyển sang trạng thái khóa 1 chiều (khóa chiều đi: thực hiện cuộc gọi, nhắn tin đi và truy cập Internet).<br>
                Thời hạn khóa 1 chiều là 10 ngày. Hết thời hạn này, nếu thuê bao không nạp tiền thì sẽ chuyển sang trạng thái khóa 2 chiều (chiều đi và chiều đến).<br>
                Thời hạn khóa 2 chiều (giữ số) là 30 ngày. Hết thời hạn này, nếu thuê bao không nạp tiền thì thuê bao sẽ bị cắt hủy khỏi hệ thống.</p>

                <h5>III. Giá cước</h5>
                <p><strong>1. Giá cước hòa mạng</strong><br>
                Cước hòa mạng thuê bao trả trước: 25.000 đồng/thuê bao (theo quy định tại Thông tư 14/2012/TT-BTTTT ngày 12 tháng 10 năm 2012 của Bộ Thông tin và Truyền thông).</p>

                <p><strong>2. Giá cước thông tin</strong></p>
                <p><strong>2.1. Giá cước Thoại và SMS</strong><br>
                a. Giá cước áp dụng với khu vực InZone, OutZone như dưới đây:</p>
                
                <div class="table-responsive mb-3">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>TT</th>
                                <th>Nội dung</th>
                                <th>Khi thuê bao đang ở trong vùng đăng ký (InZone)</th>
                                <th>Khi thuê bao ở ngoài vùng đăng ký (OutZone)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="3">1</td>
                                <td class="text-start">Cước thông tin gọi nội mạng/liên mạng trong nước</td>
                                <td>690 đồng/phút</td>
                                <td>1.880 đồng/phút</td>
                            </tr>
                            <tr>
                                <td class="text-start">06 giây đầu</td>
                                <td>69 đồng/6 giây</td>
                                <td>188 đồng/6 giây</td>
                            </tr>
                            <tr>
                                <td class="text-start">01 giây tiếp theo</td>
                                <td>11,50 đồng/giây</td>
                                <td>31,33 đồng/giây</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td class="text-start">Cước nhắn tin SMS nội mạng / liên mạng trong nước</td>
                                <td colspan="2">250 đồng/SMS</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <p>Tính cước Inzone: Khi thuê bao liên lạc với số thuê bao phát sinh cuộc gọi và thuê bao nhận cuộc gọi trong vùng đăng ký của thuê bao gọi đi.<br>
                Tính cước Outzone: Với các trường hợp còn lại.<br>
                Ghi chú: Trường hợp thuê bao thực hiện chuyển vùng trong nước với VinaPhone, gọi qua video call, số tắt (taxi, Vietnam Airlines...) áp dụng mức cước OutZone.</p>

                <p>b. Phạm vi Zone tính trong phạm vi 1 tỉnh/thành phố với 63/63 tỉnh thành tại Việt Nam theo danh sách chi tiết bên dưới:<br>
                - Các tỉnh/thành phố áp dụng chính sách giá cước InZone:</p>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Khu vực</th>
                                <th>Danh sách tỉnh/thành phố</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Miền Bắc</td>
                                <td>Hà Nội, Phú Thọ, Điện Biên, Hà Nam, Hoà Bình, Lai Châu, Lào Cai, Nam Định, Ninh Bình, Sơn La, Vĩnh Phúc, Yên Bái, Hải Dương, Hưng Yên, Quảng Ninh, Bắc Ninh, Bắc Giang, Thái Nguyên, Tuyên Quang, Hà Giang, Lạng Sơn, Bắc Kạn, Cao Bằng, Thái Bình</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Miền Trung</td>
                                <td>Nghệ An, Hà Tĩnh, Thanh Hoá, Quảng Bình, Quảng Trị, Quảng Ngãi, Quảng Nam, Thừa Thiên Huế, Bình Định, Phú Yên, Khánh Hoà, Đắk Lắk, Đắk Nông, Kon Tum, Gia Lai</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Miền Nam</td>
                                <td>Bình Dương, Bình Thuận, Ninh Thuận, Bình Phước, Lâm Đồng, Tây Ninh, Long An, Đồng Nai, Vũng Tàu, Cần Thơ, Tiền Giang, Bến Tre, Bạc Liêu, Cà Mau, Kiên Giang, Trà Vinh, An Giang, Sóc Trăng, Hậu Giang, Đồng Tháp, Vĩnh Long.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p>Các tỉnh/ thành phố ngoài tỉnh/ thành phố áp dụng chính sách giá cước InZone nêu trên sẽ áp dụng chính sách giá cước OutZone.<br>
                Khi kích hoạt thuê bao tại tỉnh/thành phố nào thì hệ thống sẽ tự động đăng ký vùng Zone đó trong vòng 24 giờ kể từ thời điểm thuê bao kích hoạt. Trong trường hợp muốn thay đổi vùng Zone, khách hàng có thể đến các Điểm cung cấp dịch vụ viễn thông FPTShop để được hỗ trợ.</p>
                <p>Cú pháp đăng ký, kiểm tra, thay đổi vùng Zone:</p>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Nghiệp vụ</th>
                                <th>Cú pháp</th>
                                <th>Đầu số</th>
                                <th>Cước phí</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="text-start">Đăng ký vùng</td>
                                <td>DK_ FPT_Ten tinh</td>
                                <td>9199</td>
                                <td>Miễn phí</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td class="text-start">Kiểm tra vùng</td>
                                <td>KT_ FPT</td>
                                <td>9199</td>
                                <td>Miễn phí</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td class="text-start">Chuyển vùng lần đầu sau khi kích hoạt</td>
                                <td>DOI_ FPT_Ten tinh</td>
                                <td>9199</td>
                                <td>Miễn phí</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td class="text-start">Chuyển vùng từ lần thứ 2 sau khi kích hoạt</td>
                                <td>DOI_ FPT_Ten tinh</td>
                                <td>9199</td>
                                <td>20.000đ/phí chuyển vùng.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p><strong>2.2. Giá cước Data</strong><br>
                Giá cước đối với thuê bao không có gói cước hoặc gói cước hết thời gian sử dụng: 75 đồng/50KB. Trong trường hợp thuê bao đăng ký gói cước, áp dụng theo quy định cụ thể của từng gói cước.</p>

                <p><strong>3. Nguyên tắc tính cước</strong><br>
                <strong>3.1. Nguyên tắc tính cước Thoại</strong><br>
                Tính cước cuộc gọi theo block 6 giây + block 1 giây, bắt đầu từ giây đầu tiên.<br>
                Cuộc gọi nội mạng: khi thuê bao di động FPT gọi đến thuê bao khác cũng thuộc mạng FPT và mạng MobiFone.<br>
                Cuộc gọi liên mạng: khi thuê bao di động FPT gọi đến: thuê bao di động, cố định, vô tuyến cố định thuộc các mạng viễn thông không thuộc mạng FPT và MobiFone.<br>
                - Nguyên tắc làm tròn:<br>
                Khai báo mức cước của từng đơn vị cước có phần thập phân tới hai chữ số sau dấu thập phân (phần trăm của 1 đồng). Phần lẻ (nếu có) được làm tròn: <5 làm tròn xuống 0; >=5 làm tròn lên 1.<br>
                Cước sử dụng dịch vụ (cước phát sinh) được làm tròn theo tổng số tiền cước phát sinh của từng cuộc gọi, tin nhắn, giao dịch...: phần lẻ >= 0,5 đồng làm tròn thành 1 đồng, phần lẻ < 0,5 đồng làm tròn xuống 0 đồng.</p>

                <p><strong>3.2. Nguyên tắc tính cước SMS</strong><br>
                Nguyên tắc trừ cước: Khi tin nhắn gửi thành công đến trung tâm tin nhắn của nhà mạng (SMSC), thuê bao trừ tiền vào tài khoản thưởng trước (nếu có), sau đó trừ tài khoản chính.<br>
                SMS nội mạng: áp dụng cho tin nhắn từ thuê bao của mạng FPT đến thuê bao khác cũng thuộc mạng FPT, mạng MobiFone, Saymee và các mạng MVNO khác đang hợp tác với MobiFone.<br>
                SMS liên mạng: áp dụng cho tin nhắn từ thuê bao của mạng FPT đến thuê bao các mạng điện thoại không thuộc mạng FPT, mạng MobiFone, Saymee và các mạng MVNO khác đang hợp tác với MobiFone.</p>

                <p><strong>3.3. Nguyên tắc tính cước Data</strong><br>
                Thuê bao truy nhập Internet trừ tiền vào tài khoản chính theo dung lượng sử dụng và theo quy định của từng gói cước.<br>
                Dung lượng sử dụng được tính trên tổng dung lượng download và upload</p>

                <h5>IV. Quy định các gói cước</h5>
                <p>Chi tiết xem <a href="#">Tại Đây</a></p>

                <h5>V. Các dịch vụ giá trị gia tăng</h5>
                <p><strong>1. Dịch vụ Nhạc chuông chờ - Funring</strong><br>
                FunRing là dịch vụ nhạc chờ dành cho thuê bao của FPT. Khi thuê bao khác gọi đến số điện thoại của khách hàng, thuê bao gọi đến sẽ được nghe những bản nhạc chờ do khách hàng lựa chọn.</p>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Thực hiện</th>
                                <th>Cách đăng ký</th>
                                <th>Lưu ý</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Đăng ký</td>
                                <td>Soạn DK gửi 9224<br>(12.000đ/ 30 ngày)<br><br>Soạn DKY 1 gửi 9224<br>(1.000đ/ ngày)</td>
                                <td>Được miễn phí 1 bản nhạc chờ mặc định do hệ thống lựa chọn - nhạc không lời</td>
                            </tr>
                            <tr>
                                <td>Tải nhạc chờ</td>
                                <td>Soạn CHON_&lt;Mã bài hát&gt; gửi 9224<br>Lựa chọn mã bài hát tại: http://funring.vn</td>
                                <td>Nhạc chờ được phát mặc định là nhạc chờ cuối cùng tải về</td>
                            </tr>
                            <tr>
                                <td>Hủy dịch vụ</td>
                                <td>Soạn HUY gửi 9224</td>
                                <td>Dịch vụ Funring hủy, tất cả bài hát trong thư viện nhạc chờ tự động xóa</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p><strong>2. Dịch vụ Thông báo cuộc gọi nhỡ - MCA</strong><br>
                Dịch vụ Thông báo cuộc gọi nhỡ (MCA) giúp khách hàng biết thông tin về các cuộc gọi nhỡ tới số thuê bao của mình khi điện thoại di động của khách hàng đang tắt máy, hết pin hoặc ngoài vùng phủ sóng.</p>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Cách đăng ký</th>
                                <th>Mức cước</th>
                                <th>Thời gian sử dụng</th>
                                <th>Cách hủy</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Soạn DK MCAP7 gửi 9232</td>
                                <td>2500 đồng</td>
                                <td>7 ngày</td>
                                <td>soạn HUY gửi 9232</td>
                            </tr>
                            <tr>
                                <td>Soạn DK MCAP gửi 9232</td>
                                <td>9000 đồng</td>
                                <td>30 ngày</td>
                                <td>soạn HUY gửi 9232</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5>VI. Dịch vụ quốc tế</h5>
                <p><strong>1. Dịch vụ Thoại/SMS quốc tế</strong><br>
                a. Dịch vụ thoại quốc tế: (sử dụng Thuê bao mạng FPT gọi tới số thuê bao nước ngoài)<br>
                Cách 1: Gọi trực tiếp IDD (Cú pháp: 00 + Mã-nước + Mã vùng + SĐT gọi)<br>
                Cách 2: Gọi VOIP 131 (Cú pháp: 131 + 00 + Mã-nước + Mã vùng + SĐT gọi)<br>
                Cước gọi: Xem <a href="#">Giá cước gọi Quốc tế mạng FPT</a></p>

                <p>b. Dịch vụ nhắn tin SMS quốc tế: (sử dụng Thuê bao mạng FPT nhắn SMS tới số thuê bao nước ngoài)<br>
                Cách nhắn tin: 00 + Mã nước + Mã vùng + Số điện thoại muốn nhắn<br>
                Lưu ý:<br>
                01 SMS tối đa 160 kí tự (không dấu) và 70 kí tự (có dấu)<br>
                Khi nhập SĐT nhắn tin, nếu trên “mã nước”, “mã vùng” có số 0 thì cần bỏ số 0 đi<br>
                Giá tin nhắn thông thường: 2.500đ/SMS</p>

                <p><strong>2. Dịch vụ chuyển vùng quốc tế:</strong> (thuê bao giữ liên lạc khi ra nước ngoài bằng chính số điện thoại đang sử dụng của mình)<br>
                <strong>Bước 1:</strong> Đăng ký dịch vụ CVQT<br>
                CVQT Thoại, SMS: DK CVQT gửi 9199 hoặc bấm *093*1*1#OK<br>
                CVQT Thoại, SMS & Data: DK CVQT ALL gửi 9199 hoặc bấm *093*2*1#OK<br>
                <strong>Bước 2:</strong> Bật chế độ DATA Roaming ON trên điện thoại:<br>
                Đối với Hệ điều hành IOS: Settings >> Cellular Data Option >> Roaming ON >> DATA ROAMING ON<br>
                Đối với Hệ điều hành Android: Settings -> Connections -> Mobile Networks -> Access Point Names >> Roaming ON >> DATA ROAMING ON<br>
                <strong>Bước 3:</strong> Đăng ký gói cước Quốc tế<br>
                Cú pháp: Soạn DK &lt;Tên gói&gt; gửi 9199<br>
                Các gói cước Chuyển vùng quốc tế: Chi tiết xem <a href="#">Tại Đây</a><br>
                Tải và truy cập App FPTShop để kiểm tra dung lượng gói cước và quản lý thuê bao của mình.<br>
                Huỷ dịch vụ:<br>
                Hủy CVQT Data: HUY CVQT DATA gửi 9199<br>
                Hủy CVQT Thoại, SMS, Data: HUY CVQT ALL gửi 9199 hoặc bấm *093*2*2#OK</p>

                <h4>C. CÔNG BỐ CHẤT LƯỢNG DỊCH VỤ</h4>
                <h5>I. Dịch vụ được cung cấp</h5>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Dịch vụ</th>
                                <th>Địa bàn cung cấp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="text-start">Dịch vụ điện thoại trên mạng viễn thông di động mặt đất</td>
                                <td rowspan="3" class="align-middle">Toàn Quốc</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td class="text-start">Dịch vụ tin nhắn ngắn trên mạng viễn thông di động mặt đất</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td class="text-start">Dịch vụ truy cập Internet trên mạng viễn thông di động mặt đất</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5>II. Quy chuẩn kỹ thuật, tiêu chuẩn áp dụng cho các dịch vụ được cung cấp</h5>
                <ul>
                    <li>Các quy chuẩn kỹ thuật</li>
                    <li>QCVN 36:2022/BTTTT - Quy chuẩn kỹ thuật quốc gia về dịch vụ điện thoại trên mạng thông tin di động mặt đất → <a href="#">Xem tại đây</a></li>
                    <li>QCVN 81:2019/ BTTTT - Quy chuẩn kỹ thuật quốc gia về dịch vụ truy nhập Internet trên mạng viễn thông di động mặt đất → <a href="#">Xem tại đây</a></li>
                    <li>QCVN 82:2014/BTTTT - Quy chuẩn kỹ thuật quốc gia về dịch vụ tin nhắn ngắn trên mạng thông tin di động mặt đất → <a href="#">Xem tại đây</a></li>
                </ul>

                <h5>III. Bản công bố chất lượng</h5>
                <ul>
                    <li>1. Công bố chất lượng dịch vụ điện thoại theo QCVN 36</li>
                    <li>2. Công bố chất lượng truy nhập Internet theo QCVN 81</li>
                    <li>3. Công bố chất lượng dịch vụ tin nhắn ngắn trên mạng viễn thông di động mặt đất theo QCVN 82</li>
                </ul>

                <h5>IV. Báo cáo định kỳ về chất lượng</h5>
                <p>Khách hàng vui lòng xem thông tin báo cáo định kỳ về chất lượng tại đây:</p>
                <ul>
                    <li><strong>QUÝ I/2024:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ II/2024:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ III/2024:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ IV/2024:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ I/2025:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ II/2025:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ III/2025:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ IV/2025:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                </ul>

                <h5>V. Kết quả tự kiểm tra định kỳ</h5>
                <p>Khách hàng vui lòng xem thông tin kết quả tự kiểm tra định kỳ tại đây:</p>
                <ul>
                    <li><strong>QUÝ I/2024:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ II/2024:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ III/2024:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ IV/2024:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ I/2025:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ II/2025:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ III/2025:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                    <li><strong>QUÝ IV/2025:</strong> Dịch vụ thoại: <a href="#">Xem Tại Đây</a> | Dịch vụ Internet: <a href="#">Xem Tại Đây</a></li>
                </ul>

                <h5>VI. Địa chỉ tiếp nhận khiếu nại</h5>
                <ul>
                    <li>Qua Tổng đài Hỗ trợ Khách hàng 1900.6675 (1.000 đồng/phút).</li>
                    <li>Qua email: fptshop@fpt.com</li>
                    <li>Qua trang web: <a href="https://fptshop.com.vn/" target="_blank">https://fptshop.com.vn/</a></li>
                    <li>Văn bản, đơn thư được gửi trực tiếp đến: Trung tâm Trải nghiệm khách hàng FPTShop. Địa chỉ: Tầng 3A, Chung cư Jamona Heights, 210 Bùi Văn Ba, Phường Tân Thuận Đông, Quận 7, TP. Hồ Chí Minh.</li>
                </ul>

                <h5>VII. Thông tin hỗ trợ Khách hàng</h5>
                <p><strong>1. Hỗ trợ Khách hàng qua kênh trả lời tổng đài</strong></p>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Đầu số</th>
                                <th>Nội dung tư vấn</th>
                                <th>Cước áp dụng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold align-middle">19006675</td>
                                <td class="text-start">Tư vấn, hướng dẫn các vấn đề liên quan đến dịch vụ do Công ty Cổ phần Bán lẻ Kỹ thuật số FPT cung cấp.<br>Tiếp nhận, hỗ trợ xử lý các khiếu nại của Khách hàng, Đại lý, Nhà phân phối.</td>
                                <td class="align-middle">1.000 VNĐ/ phút<br>Phương thức tính block 1p + 1p</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p><strong>2. Số gọi ra hỗ trợ Khách hàng: 19006675</strong><br>
                Mục đích:<br>
                - Giải quyết phản ánh, khiếu nại, hỗ trợ khách hàng không sử dụng được dịch vụ.<br>
                - Chăm sóc khách hàng; Bán hàng, tư vấn cho khách hàng và dịch vụ, sản phẩm mới; Số hiển thị trên máy khách hàng: 19006675</p>

                <p><strong>3. Địa chỉ Website cung cấp thông tin hỗ trợ Dịch vụ</strong><br>
                - Tên Website: Website FPTShop<br>
                - Địa chỉ website: <a href="https://fptshop.com.vn/" target="_blank">https://fptshop.com.vn/</a></p>

                <p><strong>4. Bản đồ số vùng phủ sóng các dịch vụ của FPT</strong><br>
                Link vùng phủ dịch vụ:<br>
                Thoại → <a href="#">Xem tại đây</a><br>
                Truy nhập DV internet trên mạng viễn thông di động mặt đất công nghệ WCDMA → <a href="#">Xem tại đây</a><br>
                Truy nhập DV internet trên mạng viễn thông di động mặt đất công nghệ LTE, LTE-A và các phiên bản tiếp theo → <a href="#">Xem tại đây</a></p>

                <h4>D. QUY TRÌNH GIAO KẾT HỢP ĐỒNG THEO MẪU</h4>
                <h5>I. Quy định về thủ tục đăng ký thông tin thuê bao trả trước</h5>
                <p><strong>1. Thuê bao là cá nhân</strong><br>
                Giấy tờ tùy thân (bản chính hộ chiếu hoặc chứng minh nhân dân (CMND) hoặc thẻ căn cước công dân (CCCD) còn thời hạn sử dụng đối với người có quốc tịch Việt Nam hoặc hộ chiếu còn thời hạn lưu hành tại Việt Nam đối với người có quốc tịch nước ngoài;<br>
                Đối với người dưới 14 tuổi hoặc người được giám hộ theo quy định của Bộ Luật Dân sự, việc giao kết hợp đồng theo mẫu, điều kiện giao dịch chung phải do cha, mẹ hoặc người giám hộ thực hiện.<br>
                <strong>Lưu ý:</strong> Mỗi cá nhân được hòa mạng tối đa 09 thuê bao/giấy tờ.</p>
                
                <p><strong>2. Thuê bao là tổ chức</strong><br>
                Giấy tờ chứng nhận pháp nhân (bản chính hay bản sao được chứng thực từ bản chính quyết định thành lập hoặc giấy chứng nhận đăng ký kinh doanh và đăng ký thuế hoặc giấy phép đầu tư hoặc giấy chứng nhận đăng ký doanh nghiệp) (ĐKKD).<br>
                Danh sách các cá nhân thuộc tổ chức (có xác nhận hợp pháp của tổ chức, xác nhận được ký bởi Người đại diện theo pháp luật hoặc người được Người đại diện theo pháp luật ủy quyền và đóng dấu của tổ chức) được phép sử dụng dịch vụ viễn thông theo hợp đồng theo mẫu, điều kiện giao dịch chung mà tổ chức giao kết với doanh nghiệp viễn thông (trường hợp tổ chức giao cho người sử dụng) đồng thời kèm theo bản chính giấy tờ tùy thân của từng cá nhân như nêu tại Điểm I.1 ở trên.<br>
                Văn bản ủy quyền hợp pháp của Người đại diện theo pháp luật và giấy tờ tùy thân của người đại diện đó (Trường hợp người đến giao kết hợp đồng theo mẫu, điều kiện giao dịch chung không phải là Người đại diện theo pháp luật của tổ chức).</p>

                <h5>II. Quy trình giao kết hợp đồng theo mẫu</h5>
                <ol>
                    <li>Khách hàng đến Điểm cung cấp dịch vụ viễn thông (ĐCCDV), cung cấp các giấy tờ cần thiết (mục A) cho ĐCCDV.</li>
                    <li>ĐCCDV kiểm tra, đối chiếu giấy tờ & nhập đầy đủ, chính xác thông tin thuê bao theo quy định của Nghị định 49/2017/NĐ-CP.</li>
                    <li>ĐCCDV chụp và lưu bản số hóa toàn bộ các giấy tờ của cá nhân, tổ chức đã xuất trình khi đến giao kết hợp đồng theo mẫu; Ảnh chụp người đến giao kết hợp đồng theo mẫu.</li>
                    <li>Khách hàng ký xác nhận vào Hợp đồng cung cấp và sử dụng dịch vụ viễn thông di động mặt đất hình thức trả trước.</li>
                    <li>ĐCCDV thực hiện đăng ký thông tin thuê bao lên hệ thống và truyền toàn bộ hồ sơ dưới dạng bản số hóa về hệ thống cơ sở dữ liệu thông tin thuê bao tập trung của doanh nghiệp viễn thông.</li>
                    <li>Kích hoạt thuê bao.</li>
                </ol>

                <h4>E. QUY TRÌNH TIẾP NHẬN VÀ GIẢI QUYẾT KHIẾU NẠI</h4>
                <p><strong>Điều 1: Cơ chế giải quyết</strong><br>
                Trong trường hợp xảy ra sự cố do lỗi, chúng tôi sẽ ngay lập tức áp dụng các biện pháp cần thiết để đảm bảo quyền lợi cho khách hàng.</p>
                
                <p><strong>Điều 2: Phương thức gửi phản ánh</strong><br>
                Khách hàng có thể gửi phản ánh để yêu cầu FRT giải quyết bằng cách thức sau:</p>
                <ul>
                    <li><strong>Cách 1:</strong> Gọi điện thoại tới hotline của chúng tôi: 19006675 hoặc 0775256666.</li>
                    <li><strong>Cách 2:</strong> Gửi email tới địa chỉ: fptshop@fpt.com</li>
                    <li><strong>Cách 3:</strong> Qua trang web: <a href="https://fptshop.com.vn/" target="_blank">https://fptshop.com.vn/</a></li>
                    <li><strong>Cách 4:</strong> Văn bản, đơn thư được gửi trực tiếp đến: Trung tâm Trải nghiệm khách hàng FPTShop. Địa chỉ: Tầng 3A, Chung cư Jamona Heights, 210 Bùi Văn Ba, Phường Tân Thuận Đông, Quận 7, TP. Hồ Chí Minh.</li>
                </ul>

                <p><strong>Điều 3: Trình tự thực hiện</strong><br>
                <strong>Bước 1: Gửi phản ánh</strong><br>
                Khách hàng gửi phản ánh về dịch vụ hoặc quyền lợi chưa được đảm bảo đầy đủ tới FRT qua các cách thức đã quy định ở trên.<br>
                <strong>Bước 2: Tiếp nhận và xử lý phản ánh</strong><br>
                FRT sẽ tiếp nhận các phản ánh của Khách hàng và tiến hành xác minh thông tin.<br>
                <strong>Bước 3: Phản hồi tới khách hàng</strong><br>
                FRT sẽ phản hồi kết quả xử lý phản ánh tới khách hàng trong thời hạn 7 – 10 ngày làm việc kể từ ngày việc xác minh, xử lý thông tin được hoàn thành một cách nhanh chóng, kịp thời để đảm bảo quyền lợi cho Khách hàng.</p>

                <h4>F. DANH SÁCH ĐIỂM CUNG CẤP DỊCH VỤ VIỄN THÔNG FPT</h4>
                <p>Quý Khách có thể xem <a href="#">Tại Đây</a></p>

                <h4>G. ĐIỀU KIỆN GIAO DỊCH CHUNG ĐỐI VỚI DỊCH VỤ VIỄN THÔNG DI ĐỘNG MẶT ĐẤT HÌNH THỨC TRẢ TRƯỚC</h4>
                <ul>
                    <li>Điều kiện giao dịch chung đối với dịch vụ viễn thông di động mặt đất hình thức trả trước xem <a href="#">Tại Đây</a></li>
                    <li>Hợp đồng cung cấp và sử dụng dịch vụ viễn thông di động mặt đất hình thức trả trước xem <a href="#">Tại Đây</a></li>
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