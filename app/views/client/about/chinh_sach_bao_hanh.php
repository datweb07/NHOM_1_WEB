<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

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
            <li class="breadcrumb-item active" aria-current="page">Chính sách bảo hành</li>
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
                <a href="#" class="list-group-item list-group-item-action">Chính sách đổi trả</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách bảo mật dữ liệu cá nhân khách hàng</a>
                <a href="#" class="list-group-item list-group-item-action">Quy chế hoạt động</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách bảo mật</a>
                <a href="#" class="list-group-item list-group-item-action">Quy định hỗ trợ kỹ thuật và sao lưu dữ liệu</a>
                <a href="#" class="list-group-item list-group-item-action active" aria-current="true">Chính sách bảo hành</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách trả góp</a>
            </div>
        </div>

        <!-- Nội dung chính bên phải -->
        <div class="col-lg-9 col-md-8">
            <div class="content-section bg-white p-4 rounded shadow-sm border">
                <h3 class="fw-bold mb-4">Chính sách bảo hành</h3>
                
                <p>Tất cả sản phẩm tại FPT Shop kinh doanh đều là sản phẩm chính hãng và được bảo hành theo đúng chính sách của nhà sản xuất(*). Ngoài ra FPT Shop cũng hỗ trợ gửi bảo hành miễn phí giúp khách hàng đối với cả sản phẩm do FPT Shop bán ra và sản phẩm Quý khách mua tại các chuỗi bán lẻ khác.</p>
                
                <p>Mua hàng tại FPT Shop, Quý khách sẽ được hưởng những đặc quyền sau:</p>
                <ul>
                    <li>• Bảo hành đổi sản phẩm mới ngay tại shop trong 30 ngày nếu có lỗi NSX.(**)</li>
                    <li>• Gửi bảo hành chính hãng không mất phí vận chuyển.(***)</li>
                    <li>• Theo dõi tiến độ bảo hành nhanh chóng qua kênh hotline hoặc tự tra cứu Tại đây.</li>
                    <li>• Hỗ trợ làm việc với hãng để xử lý phát sinh trong quá trình bảo hành.</li>
                </ul>

                <p>Bên cạnh đó Quý khách có thể tham khảo một số các trường hợp thường gặp nằm ngoài chính sách bảo hành sau để xác định sơ bộ máy có đủ điều kiện bảo hành hãng:</p>
                <ul>
                    <li>• Sản phẩm hết hạn bảo hành (Vui lòng tra cứu thời hạn bảo hành sản phẩm Tại đây).</li>
                    <li>• Sản phẩm đã bị thay đổi, sửa chữa không thuộc các Trung Tâm Bảo Hành Ủy Quyền của Hãng.</li>
                    <li>• Sản phẩm lắp đặt, bảo trì, sử dụng không đúng theo hướng dẫn của Nhà sản xuất gây ra hư hỏng.</li>
                    <li>• Sản phẩm lỗi do ngấm nước, chất lỏng và bụi bẩn. Quy định này áp dụng cho cả những thiết bị đạt chứng nhận kháng nước/kháng bụi cao nhất là IP68.</li>
                    <li>• Sản phẩm bị biến dạng, nứt vỡ, cấn móp, trầy xước nặng do tác động nhiệt, tác động bên ngoài.</li>
                    <li>• Sản phẩm có vết mốc, rỉ sét hoặc bị ăn mòn, oxy hóa bởi hóa chất.</li>
                    <li>• Sản phẩm bị hư hại do thiên tai, hỏa hoạn, lụt lội, sét đánh, côn trùng, động vật vào.</li>
                    <li>• Sản phẩm trong tình trạng bị khóa tài khoản cá nhân như: Tài khoản khóa máy/màn hình, khóa tài khoản trực tuyến Xiaomi Cloud, Samsung Cloud, iCloud, Gmail...</li>
                    <li>• Khách hàng sử dụng phần mềm, ứng dụng không chính hãng, không bản quyền.</li>
                    <li>• Màn hình có bốn (04) điểm chết trở xuống.</li>
                </ul>

                <p class="fw-bold text-dark mt-4">Lưu ý:</p>
                <ul>
                    <li>• Chương trình bảo hành bắt đầu có hiệu lực từ thời điểm FPT Shop xuất hóa đơn cho Quý khách.</li>
                    <li>• Với mỗi dòng sản phẩm khác nhau sẽ có chính sách bảo hành khác nhau tùy theo chính sách của Hãng/Nhà cung cấp.</li>
                    <li>• Để tìm hiểu thông tin chi tiết về chính sách bảo hành cho sản phẩm cụ thể, xin liên hệ bộ phận Chăm sóc Khách hàng của FPT Shop 1800 6616.</li>
                    <li>• Tra cứu tình trạng máy gửi bảo hành bất cứ lúc nào Tại đây.</li>
                    <li>• Trong quá trình thực hiện dịch vụ bảo hành, các nội dung lưu trữ trên sản phẩm của Quý khách sẽ bị xóa và định dạng lại. Do đó, Quý khách vui lòng tự sao lưu toàn bộ dữ liệu trong sản phẩm, đồng thời gỡ bỏ tất cả các thông tin cá nhân mà Quý khách muốn bảo mật. FPT Shop không chịu trách nhiệm đối với bất kỳ mất mát nào liên quan tới các chương trình phần mềm, dữ liệu hoặc thông tin nào khác lưu trữ trên sản phẩm bảo hành.</li>
                    <li>• Vui lòng tắt tất cả các mật khẩu bảo vệ, FPT Shop sẽ từ chối tiếp nhận bảo hành nếu thiết bị của bạn bị khóa bởi bất cứ phương pháp nào.</li>
                </ul>

                <div class="mt-4 pt-3 border-top">
                    <p class="mb-1 text-muted small"><em>(*) Áp dụng với các sản phẩm bán mới hoặc còn hạn bảo hành mặc định nếu đã qua sử dụng.</em></p>
                    <p class="mb-1 text-muted small"><em>(**) Áp dụng với các sản phẩm thuộc diện đổi mới trong 30 ngày nếu có lỗi NSX được công bố trên website Chính sách đổi trả.</em></p>
                    <p class="mb-0 text-muted small"><em>(***) Trừ các sản phẩm có chính sách bảo hành tại nhà, sản phẩm thuộc diện cồng kềnh.</em></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>