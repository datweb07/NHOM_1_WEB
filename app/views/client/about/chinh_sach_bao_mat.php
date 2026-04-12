<?php require_once dirname(__DIR__) . '/layout/header.php'; ?>

<style>
    .support-sidebar .list-group-item {
        border: none;
        padding: 12px 15px;
        font-size: 14.5px;
        color: #333;
        border-radius: 6px !important;
        margin-bottom: 4px;
        background-color: transparent;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    .support-sidebar .list-group-item:hover {
        color: #cb1c22;
        background-color: #f8f9fa;
    }
    .support-sidebar .list-group-item.active {
        background-color: #fce8e9;
        color: #cb1c22;
        border-left: 4px solid #cb1c22;
        border-radius: 4px 6px 6px 4px !important;
    }
    .support-sidebar .menu-header {
        background-color: #f8f9fa;
        color: #212529;
        font-weight: bold;
        text-transform: uppercase;
        border-radius: 6px !important;
    }
    .content-section h3 {
        color: #212529;
        margin-bottom: 1.5rem;
    }
    .content-section h5 {
        font-weight: bold;
        margin-top: 1.8rem;
        margin-bottom: 1rem;
        font-size: 1.15rem;
        color: #cb1c22;
    }
    .content-section p {
        text-align: justify;
        color: #495057;
        line-height: 1.6;
        margin-bottom: 1rem;
        font-size: 15px;
    }
    .content-section ul {
        padding-left: 1.2rem;
        margin-bottom: 1.5rem;
    }
    .content-section ul li {
        margin-bottom: 0.6rem;
        color: #495057;
        text-align: justify;
        line-height: 1.6;
        font-size: 15px;
    }
    .breadcrumb-item a {
        color: #cb1c22;
        text-decoration: none;
    }
</style>

<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#">Hỗ trợ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách bảo mật</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Menu bên trái -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="list-group support-sidebar sticky-top" style="top: 20px;">
                <div class="list-group-item menu-header disabled mb-2">Danh mục chính sách</div>
                <a href="#" class="list-group-item list-group-item-action">Câu hỏi thường gặp</a>
                <a href="/gioi-thieu" class="list-group-item list-group-item-action">Giới thiệu về FPT Shop</a>
                <a href="#" class="list-group-item list-group-item-action">Đại lý uỷ quyền và TTBH uỷ quyền của Apple</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách mạng di động FPT</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách gói cước di động FPT</a>
                <a href="#" class="list-group-item list-group-item-action">Danh sách điểm cung cấp dịch vụ viễn thông FPT</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách giao hàng & lắp đặt</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách giao hàng & lắp đặt Điện máy, Gia dụng</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách giao hàng & lắp đặt Điện máy chỉ bán online</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách Chương trình Khách hàng thân thiết tại FPT Shop</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách khui hộp sản phẩm</a>
                <a href="#" class="list-group-item list-group-item-action">Hướng dẫn mua hàng và thanh toán online</a>
                <a href="/gioi-thieu-may-doi-tra" class="list-group-item list-group-item-action">Giới thiệu máy đổi trả</a>
                <a href="/chinh-sach-doi-tra" class="list-group-item list-group-item-action">Chính sách đổi trả</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách bảo mật dữ liệu cá nhân khách hàng</a>
                <a href="/quy-che-hoat-dong" class="list-group-item list-group-item-action">Quy chế hoạt động</a>
                <a href="#" class="list-group-item list-group-item-action active" aria-current="true">Chính sách bảo mật</a>
                <a href="#" class="list-group-item list-group-item-action">Quy định hỗ trợ kỹ thuật và sao lưu dữ liệu</a>
                <a href="/chinh-sach-bao-hanh" class="list-group-item list-group-item-action">Chính sách bảo hành</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách trả góp</a>
            </div>
        </div>

        <!-- Nội dung chính bên phải -->
        <div class="col-lg-9 col-md-8">
            <div class="content-section bg-white p-4 rounded shadow-sm border">
                <h3 class="fw-bold mb-4 text-center">Chính sách bảo mật</h3>
                
                <p>FPTshop.com.vn cam kết sẽ bảo mật những thông tin mang tính riêng tư của bạn. Bạn vui lòng đọc bản “Chính sách bảo mật” dưới đây để hiểu hơn những cam kết mà chúng tôi thực hiện, nhằm tôn trọng và bảo vệ quyền lợi của người truy cập.</p>
                
                <h5>1. Mục đích và phạm vi thu thập?</h5>
                <p>Để truy cập và sử dụng một số dịch vụ tại FPTshop.com.vn, bạn có thể sẽ được yêu cầu đăng ký với chúng tôi thông tin cá nhân (Email, Họ tên, Số ĐT liên lạc…). Mọi thông tin khai báo phải đảm bảo tính chính xác và hợp pháp. FPTshop.com.vn không chịu mọi trách nhiệm liên quan đến pháp luật của thông tin khai báo.</p>
                <p>Chúng tôi cũng có thể thu thập thôngত্তি tin về số lần viếng thăm, bao gồm số trang bạn xem, số links (liên kết) bạn click và những thông tin khác liên quan đến việc kết nối đến site FPTshop.com.vn. Chúng tôi cũng thu thập các thông tin mà trình duyệt Web (Browser) bạn sử dụng mỗi khi truy cập vào FPTshop.com.vn, bao gồm: địa chỉ IP, loại Browser, ngôn ngữ sử dụng, thời gian và những địa chỉ mà Browser truy xuất đến.</p>
                
                <h5>2. Phạm vi sử dụng thông tin</h5>
                <p>FPTshop.com.vn thu thập và sử dụng thông tin cá nhân bạn với mục đích phù hợp và hoàn toàn tuân thủ nội dung của “Chính sách bảo mật” này. Khi cần thiết, chúng tôi có thể sử dụng những thông tin này để liên hệ trực tiếp với bạn dưới các hình thức như: gởi thư ngỏ, đơn đặt hàng, thư cảm ơn, sms, thông tin về kỹ thuật và bảo mật…</p>
                
                <h5>3. Thời gian lưu trữ thông tin</h5>
                <p>Dữ liệu cá nhân của Thành viên sẽ được lưu trữ cho đến khi có yêu cầu hủy bỏ hoặc tự thành viên đăng nhập và thực hiện hủy bỏ. Còn lại trong mọi trường hợp thông tin cá nhân thành viên sẽ được bảo mật trên máy chủ của FPTshop.com.vn.</p>
                
                <h5>4. Địa chỉ của đơn vị thu thập và quản lý thông tin cá nhân</h5>
                <p class="fw-bold text-dark mb-1">Công Ty Cổ Phần Bán Lẻ Kỹ Thuật Số FPT</p>
                <p class="mb-1"><strong>Địa chỉ đăng ký kinh doanh:</strong> 261 - 263 Khánh Hội, P. Vĩnh Hội, TP. Hồ Chí Minh</p>
                <p class="mb-1"><strong>Văn phòng:</strong> 261 - 263 Khánh Hội, P. Vĩnh Hội, TP. Hồ Chí Minh</p>
                <p><strong>Điện thoại văn phòng:</strong> 028.38345837</p>
                
                <h5>5. Phương tiện và công cụ để người dùng tiếp cận và chỉnh sửa dữ liệu cá nhân</h5>
                <p>Hiện website chưa triển khai trang quản lý thông tin cá nhân của thành viên, vì thế việc tiếp cận và chỉnh sửa dữ liệu cá nhân dựa vào yêu cầu của khách hàng bằng cách hình thức sau:</p>
                <ul>
                    <li>Gọi điện thoại đến tổng đài chăm sóc khách hàng 1800 6601, bằng nghiệp vụ chuyên môn xác định thông tin cá nhân và nhân viên tổng đài sẽ hỗ trợ chỉnh sửa thay người dùng</li>
                    <li>Để lại bình luận hoặc gửi góp ý trực tiếp từ website FPTshop.com.vn, quản trị viên kiểm tra thông tin và xem xét nội dung bình luận có phù hợp với pháp luật và chính sách của FPTshop.com.vn</li>
                </ul>
                
                <h5>6. Cam kết bảo mật thông tin cá nhân khách hàng</h5>
                <p>Thông tin cá nhân của thành viên trên FPTshop.com.vn được FPTshop.com.vn cam kết bảo mật tuyệt đối theo chính sách bảo vệ thông tin cá nhân của FPTshop.com.vn. Việc thu thập và sử dụng thông tin của mỗi thành viên chỉ được thực hiện khi có sự đồng ý của khách hàng đó trừ những trường hợp pháp luật có quy định khác.</p>
                <p>Không sử dụng, không chuyển giao, cung cấp hay tiết lộ cho bên thứ 3 nào về thông tin cá nhân của thành viên khi không có sự cho phép đồng ý từ thành viên.</p>
                <p>Trong trường hợp máy chủ lưu trữ thông tin bị hacker tấn công dẫn đến mất mát dữ liệu cá nhân thành viên, FPTshop.com.vn sẽ có trách nhiệm thông báo vụ việc cho cơ quan chức năng điều tra xử lý kịp thời và thông báo cho thành viên được biết.</p>
                <p>Bảo mật tuyệt đối mọi thông tin giao dịch trực tuyến của Thành viên bao gồm thông tin hóa đơn kế toán chứng từ số hóa tại khu vực dữ liệu trung tâm an toàn cấp 1 của FPTshop.com.vn.</p>
                <p>Hệ thống thanh toán thẻ được cung cấp bởi các đối tác cổng thanh toán (“Đối Tác Cổng Thanh Toán”) đã được cấp phép hoạt động hợp pháp tại Việt Nam. Theo đó, các tiêu chuẩn bảo mật thanh toán thẻ tại FPTShop đảm bảo tuân thủ theo các tiêu chuẩn bảo mật ngành.</p>
                <p>Ban quản lý FPTshop.com.vn yêu cầu các cá nhân khi đăng ký/mua hàng là thành viên, phải cung cấp đầy đủ thông tin cá nhân có liên quan như: Họ và tên, địa chỉ liên lạc, email, số chứng minh nhân dân, điện thoại, số tài khoản, số thẻ thanh toán …., và chịu trách nhiệm về tính pháp lý của những thông tin trên. Ban quản lý FPTshop.com.vn không chịu trách nhiệm cũng như không giải quyết mọi khiếu nại có liên quan đến quyền lợi của Thành viên đó nếu xét thấy tất cả thông tin cá nhân của thành viên đó cung cấp khi đăng ký ban đầu là không chính xác.</p>
                
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
                    <li>Đối với thẻ nội địa (internet banking), FPTShop chỉ lưu trữ mã đơn hàng, mã giao dịch và tên ngân hàng. FPTShop cam kết đảm bảo thực hiện nghiêm thực các biện pháp bảo mật cần thiết cho mọi hoạt động thanh toán thực hiện trên trang FPTShop.</li>
                </ul>
                
                <h5>8. Làm cách nào để yêu cầu xóa dữ liệu?</h5>
                <p>Bạn có thể gửi yêu cầu xóa dữ liệu qua email Trung tâm hỗ trợ của chúng tôi: fptshop@fpt.com.vn. Vui lòng cung cấp càng nhiều thông tin càng tốt về dữ liệu nào bạn muốn xóa. Yêu cầu sẽ được chuyển đến nhóm thích hợp để đánh giá và xử lý. Chúng tôi sẽ liên hệ từng bước để cập nhật cho bạn về tiến trình xóa.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>