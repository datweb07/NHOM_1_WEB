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
    .content-section ul li {
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
        margin-bottom: 10px;
        color: #495057;
        text-align: justify;
        line-height: 1.6;
        font-size: 15px;
        position: relative;
        padding-left: 18px;
    }

    .content-section ul li::before {
        content: "-";
        color: #cb1c22;
        font-weight: bold;
        position: absolute;
        left: 0;
        top: 0;
    }

    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h5 { font-size: 18px; }
    .content-section.large-text p,
    .content-section.large-text ul li {
        font-size: 17px;
        line-height: 1.7;
    }
</style>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/ho-tro">Hỗ trợ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách bảo mật</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'bao-mat'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách bảo mật</h3>

                <p>FPTshop.com.vn cam kết sẽ bảo mật những thông tin mang tính riêng tư của bạn. Bạn vui lòng đọc bản “Chính sách bảo mật” dưới đây để hiểu hơn những cam kết mà chúng tôi thực hiện, nhằm tôn trọng và bảo vệ quyền lợi của người truy cập.</p>

                <h5>1. Mục đích và phạm vi thu thập?</h5>
                <p>Để truy cập và sử dụng một số dịch vụ tại FPTshop.com.vn, bạn có thể sẽ được yêu cầu đăng ký với chúng tôi thông tin cá nhân (Email, Họ tên, Số ĐT liên lạc…). Mọi thông tin khai báo phải đảm bảo tính chính xác và hợp pháp. FPTshop.com.vn không chịu mọi trách nhiệm liên quan đến pháp luật của thông tin khai báo.<br>
                Chúng tôi cũng có thể thu thập thông tin về số lần viếng thăm, bao gồm số trang bạn xem, số links (liên kết) bạn click và những thông tin khác liên quan đến việc kết nối đến site FPTshop.com.vn. Chúng tôi cũng thu thập các thông tin mà trình duyệt Web (Browser) bạn sử dụng mỗi khi truy cập vào FPTshop.com.vn, bao gồm: địa chỉ IP, loại Browser, ngôn ngữ sử dụng, thời gian và những địa chỉ mà Browser truy xuất đến.</p>

                <h5>2. Phạm vi sử dụng thông tin</h5>
                <p>FPTshop.com.vn thu thập và sử dụng thông tin cá nhân bạn với mục đích phù hợp và hoàn toàn tuân thủ nội dung của “Chính sách bảo mật” này. Khi cần thiết, chúng tôi có thể sử dụng những thông tin này để liên hệ trực tiếp với bạn dưới các hình thức như: gởi thư ngỏ, đơn đặt hàng, thư cảm ơn, sms, thông tin về kỹ thuật và bảo mật…</p>

                <h5>3. Thời gian lưu trữ thông tin</h5>
                <p>Dữ liệu cá nhân của Thành viên sẽ được lưu trữ cho đến khi có yêu cầu hủy bỏ hoặc tự thành viên đăng nhập và thực hiện hủy bỏ. Còn lại trong mọi trường hợp thông tin cá nhân thành viên sẽ được bảo mật trên máy chủ của FPTshop.com.vn.</p>

                <h5>4. Địa chỉ của đơn vị thu thập và quản lý thông tin cá nhân</h5>
                <p>
                    Công Ty Cổ Phần Bán Lẻ Kỹ Thuật Số FPT<br>
                    Địa chỉ đăng ký kinh doanh: 261 - 263 Khánh Hội, P. Vĩnh Hội, TP. Hồ Chí Minh<br>
                    Văn phòng: 261 - 263 Khánh Hội, P. Vĩnh Hội, TP. Hồ Chí Minh<br>
                    Điện thoại văn phòng: 028.38345837
                </p>

                <h5>5. Phương tiện và công cụ để người dùng tiếp cận và chỉnh sửa dữ liệu cá nhân</h5>
                <p>Hiện website chưa triển khai trang quản lý thông tin cá nhân của thành viên, vì thế việc tiếp cận và chỉnh sửa dữ liệu cá nhân dựa vào yêu cầu của khách hàng bằng cách hình thức sau:</p>
                <ul>
                    <li>Gọi điện thoại đến tổng đài chăm sóc khách hàng 1800 6601, bằng nghiệp vụ chuyên môn xác định thông tin cá nhân và nhân viên tổng đài sẽ hỗ trợ chỉnh sửa thay người dùng</li>
                    <li>Để lại bình luận hoặc gửi góp ý trực tiếp từ website FPTshop.com.vn, quản trị viên kiểm tra thông tin và xem xét nội dung bình luận có phù hợp với pháp luật và chính sách của FPTshop.com.vn</li>
                </ul>

                <h5>6. Cam kết bảo mật thông tin cá nhân khách hàng</h5>
                <ul>
                    <li>Thông tin cá nhân của thành viên trên FPTshop.com.vn được FPTshop.com.vn cam kết bảo mật tuyệt đối theo chính sách bảo vệ thông tin cá nhân của FPTshop.com.vn. Việc thu thập và sử dụng thông tin của mỗi thành viên chỉ được thực hiện khi có sự đồng ý của khách hàng đó trừ những trường hợp pháp luật có quy định khác.</li>
                    <li>Không sử dụng, không chuyển giao, cung cấp hay tiết lộ cho bên thứ 3 nào về thông tin cá nhân của thành viên khi không có sự cho phép đồng ý từ thành viên.</li>
                    <li>Trong trường hợp máy chủ lưu trữ thông tin bị hacker tấn công dẫn đến mất mát dữ liệu cá nhân thành viên, FPTshop.com.vn sẽ có trách nhiệm thông báo vụ việc cho cơ quan chức năng điều tra xử lý kịp thời và thông báo cho thành viên được biết.</li>
                    <li>Bảo mật tuyệt đối mọi thông tin giao dịch trực tuyến của Thành viên bao gồm thông tin hóa đơn kế toán chứng từ số hóa tại khu vực dữ liệu trung tâm an toàn cấp 1 của FPTshop.com.vn.</li>
                    <li>Hệ thống thanh toán thẻ được cung cấp bởi các đối tác cổng thanh toán (“Đối Tác Cổng Thanh Toán”) đã được cấp phép hoạt động hợp pháp tại Việt Nam. Theo đó, các tiêu chuẩn bảo mật thanh toán thẻ tại FPTShop đảm bảo tuân thủ theo các tiêu chuẩn bảo mật ngành.</li>
                    <li>Ban quản lý FPTshop.com.vn yêu cầu các cá nhân khi đăng ký/mua hàng là thành viên, phải cung cấp đầy đủ thông tin cá nhân có liên quan như: Họ và tên, địa chỉ liên lạc, email, số chứng minh nhân dân, điện thoại, số tài khoản, số thẻ thanh toán …., và chịu trách nhiệm về tính pháp lý của những thông tin trên. Ban quản lý FPTshop.com.vn không chịu trách nhiệm cũng như không giải quyết mọi khiếu nại có liên quan đến quyền lợi của Thành viên đó nếu xét thấy tất cả thông tin cá nhân của thành viên đó cung cấp khi đăng ký ban đầu là không chính xác.</li>
                </ul>

                <h5>7. Quy định bảo mật</h5>
                <p>Chính sách giao dịch thanh toán bằng thẻ quốc tế và thẻ nội địa (internet banking) đảm bảo tuân thủ các tiêu chuẩn bảo mật của các Đối Tác Cổng Thanh Toán gồm:</p>
                <ul>
                    <li>Thông tin tài chính của Khách hàng sẽ được bảo vệ trong suốt quá trình giao dịch bằng giao thức SSL 256-bit (Secure Sockets Layer).</li>
                    <li>Mật khẩu sử dụng một lần (OTP) được gửi qua SMS để đảm bảo việc truy cập tài khoản được xác thực.</li>
                    <li>Các nguyên tắc và quy định bảo mật thông tin trong ngành tài chính ngân hàng theo quy định của Ngân hàng nhà nước Việt Nam.</li>
                </ul>

                <p>Chính sách bảo mật giao dịch trong thanh toán của FPTShop áp dụng với Khách hàng:</p>
                <ul>
                    <li>Thông tin thẻ thanh toán của Khách hàng mà có khả năng sử dụng để xác lập giao dịch KHÔNG được lưu trên hệ thống của FPTShop. Đối Tác Cổng Thanh Toán sẽ lưu giữ và bảo mật theo tiêu chuẩn quốc tế PCI DSS.</li>
                    <li>Đối với thẻ nội địa (internet banking), FPTShop chỉ lưu trữ mã đơn hàng, mã giao dịch và tên ngân hàng. FPTShop cam kết đảm bảo thực hiện nghiêm túc các biện pháp bảo mật cần thiết cho mọi hoạt động thanh toán thực hiện trên trang FPTShop.</li>
                </ul>

                <h5>8. Làm cách nào để yêu cầu xóa dữ liệu?</h5>
                <p>Bạn có thể gửi yêu cầu xóa dữ liệu qua email Trung tâm hỗ trợ của chúng tôi: fptshop@fpt.com.vn. Vui lòng cung cấp càng nhiều thông tin càng tốt về dữ liệu nào bạn muốn xóa. Yêu cầu sẽ được chuyển đến nhóm thích hợp để đánh giá và xử lý. Chúng tôi sẽ liên hệ từng bước để cập nhật cho bạn về tiến trình xóa.</p>

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