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
            <li class="breadcrumb-item active" aria-current="page">Giới thiệu máy đổi trả</li>
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
                <a href="#" class="list-group-item list-group-item-action active" aria-current="true">Giới thiệu máy đổi trả</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách đổi trả</a>
            </div>
        </div>

        <!-- Nội dung chính bên phải -->
        <div class="col-lg-9 col-md-8">
            <div class="content-section bg-white p-4 rounded shadow-sm border">
                <h3 class="fw-bold mb-4">Giới thiệu máy đổi trả</h3>
                
                <p>Máy cũ kinh doanh tại FPT Shop là các sản phẩm có nguồn gốc tin cậy, còn đủ điều kiện bảo hành được kiểm tra kỹ lưỡng bởi FPT Shop, bao gồm:</p>
                <ul>
                    <li><strong>Máy trưng bày (demo):</strong> là máy được dùng để trưng bày tại cửa hàng, phục vụ nhu cầu trải nghiệm của khách hàng tại shop, sau khi hết thời gian trưng bày, được điều chuyển để kinh doanh.</li>
                    <li><strong>Máy đã qua sử dụng:</strong> là máy thu lại từ khách hàng theo chính sách đổi trả/bảo hành, đã được bảo hành chính hãng và được FPT Shop kiểm tra chất lượng.</li>
                </ul>

                <p class="fw-bold text-dark mt-4">Chế độ bảo hành:</p>
                <ul>
                    <li>1 đổi 1 máy tương đương trong vòng 30 ngày nếu máy có lỗi nhà sản xuất (*) nếu không có máy tương đương, khách hàng có thể đổi sang sản phẩm khác cao tiền hơn hoặc FPT Shop thu hồi lại máy.</li>
                    <li>Áp dụng bảo hành theo chính sách của Hãng nếu máy còn bảo hành mặc định của Hãng, trường hợp hết bảo hành mặc định, máy sẽ được bảo hành từ 1 đến 12 tháng theo chính sách của FPT Shop tùy từng loại sản phẩm.(**)</li>
                    <li>Tiếp nhận bảo hành tại tất cả các cửa hàng FPT Shop trên toàn quốc.</li>
                </ul>

                <p>Với mẫu mã đa dạng, giá cả hợp lý, chất lượng tốt, Khách hàng có thể hoàn toàn yên tâm chọn mua và sử dụng các sản phẩm máy cũ tại FPT Shop đang kinh doanh phù hợp với nhu cầu của mình.</p>
                <p>Quý khách có thể đến trực tiếp FPT Shop để xem và mua máy, hoặc tìm kiếm máy đổi trả phù hợp về mức giá và nhu cầu sử dụng trên Website. Nếu tìm thấy sản phẩm vừa ý trên website, Quý khách có thể đặt giữ hàng trong 24 tiếng.</p>
                
                <div class="mt-4 pt-3 border-top">
                    <p class="mb-1 text-muted small"><em>(*) Theo kết quả kết luận của hãng</em></p>
                    <p class="mb-0 text-muted small"><em>(**) Hạn bảo hành của sản phẩm được thể hiện trên hóa đơn bán hàng và trên website <a href="https://fptshop.com.vn/" target="_blank" class="text-primary text-decoration-none">https://fptshop.com.vn/</a></em></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>