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
        font-size: 18px;
    }

    .content-section p {
        text-align: justify;
        color: #495057;
        line-height: 1.6;
        margin-bottom: 10px;
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
        content: "•";
        font-weight: bold;
        position: absolute;
        left: 0;
        top: 0;
        font-size: 18px;
        line-height: 1.4;
    }

    .content-section ul li ul {
        margin-top: 10px;
        margin-bottom: 0;
    }
    
    .content-section ul li ul li {
        padding-left: 15px;
    }

    .content-section ul li ul li::before {
        content: "-";
        color: #495057;
        font-weight: normal;
    }

    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h5 { font-size: 20px; }
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
            <li class="breadcrumb-item active" aria-current="page">Hướng dẫn mua hàng và thanh toán online</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'mua-hang-online'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Hướng dẫn mua hàng và thanh toán online</h3>

                <h5>Bước 1: Tìm kiếm sản phẩm bạn muốn</h5>
                <p>Đầu tiên, Quý khách vào trang chủ của FPT Shop tại <a href="https://fptshop.com.vn/" target="_blank">https://fptshop.com.vn/</a>.<br>
                Tại thanh tìm kiếm, gõ tên sản phẩm Quý khách muốn mua (ví dụ: "iPhone 16 Pro Max "). Hệ thống sẽ hiển thị các sản phẩm liên quan để bạn lựa chọn.</p>

                <h5>Bước 2: Xem chi tiết sản phẩm</h5>
                <p>Sau khi tìm được sản phẩm ưng ý, Quý khách hãy click vào tên sản phẩm đó. Một trang mới sẽ hiện ra, hiển thị đầy đủ thông tin chi tiết về sản phẩm, bao gồm thông số kỹ thuật, chính sách, video review và các bài đánh giá để bạn tham khảo.</p>

                <h5>Bước 3: Thêm sản phẩm vào giỏ hàng</h5>
                <p>Khi đã quyết định mua, Quý khách chỉ cần click vào nút "MUA NGAY". Sản phẩm sẽ được thêm vào giỏ hàng của Quý khách. Quý khách có thể kiểm tra lại các sản phẩm đã chọn trong giỏ hàng.</p>
                <p>Sau khi kiểm tra chính xác sản phẩm muốn đặt mua, quý khách chọn "Xác nhận đơn" để tiến hành đặt hàng</p>

                <h5>Bước 4: Điền thông tin đặt hàng</h5>
                <p>Ở bước này, Quý khách sẽ kiểm tra lại thông tin sản phẩm và cung cấp thông tin cá nhân để đặt hàng.</p>
                <p class="fw-bold text-dark mt-3 mb-1">1. Chọn hình thức nhận hàng:</p>
                <ul>
                    <li>Giao hàng tận nơi: Điền địa chỉ và thời gian bạn muốn nhận hàng.</li>
                    <li>Nhận tại cửa hàng: Chọn tỉnh, huyện và cửa hàng FPT Shop thuận tiện nhất cho Quý khác.</li>
                </ul>
                <p class="fw-bold text-dark mt-3 mb-1">2. Điền thông tin cá nhân:</p>
                <p>Vui lòng nhập "Anh/Chị", "Họ và tên", "Số điện thoại". Phần "Email" bạn có thể bỏ qua nếu không muốn.</p>

                <h5>Bước 5: Chọn hình thức thanh toán</h5>
                <p>FPT Shop cung cấp rất nhiều lựa chọn thanh toán linh hoạt để Quý khách dễ dàng lựa chọn:</p>
                
                <p class="fw-bold text-dark mt-3 mb-1">1. Thanh toán khi nhận hàng:</p>
                <p>Trả tiền mặt trực tiếp cho nhân viên giao hàng khi bạn nhận được sản phẩm.</p>
                
                <p class="fw-bold text-dark mt-3 mb-1">2. Thanh toán online tiện lợi:</p>
                <ul>
                    <li>Bằng QR Code, thẻ ATM nội địa: Thanh toán nhanh chóng qua các ngân hàng trong nước.</li>
                    <li>Bằng thẻ quốc tế Visa, Master, JCB, AMEX, Apple Pay, Google Pay, Samsung Pay: Dùng các loại thẻ quốc tế phổ biến cùng các ưu đãi hấp dẫn.</li>
                </ul>

                <p class="fw-bold text-dark mt-3 mb-1">3. Thanh toán trả góp: <a href="#" class="fw-normal">Xem thêm chính sách trả góp tại đây</a></p>
                <ul>
                    <li>Trả góp qua thẻ tín dụng: Thanh toán trả góp tiện lợi qua thẻ tín dụng của bạn.</li>
                    <li>Trả góp qua nhà tài chính: Hỗ trợ mua trả góp qua các công ty tài chính.</li>
                </ul>

                <p class="fw-bold text-dark mt-3 mb-1">4. Mua trước trả sau qua:</p>
                <ul>
                    <li>Home PayLater</li>
                    <li>Kredivo</li>
                </ul>

                <p class="mt-4 fw-bold" style="color: #212529;">Sau khi chọn hình thức thanh toán và hoàn tất các thông tin, đơn hàng của Quý khách đã được đặt thành công! Nhân viên FPT Shop sẽ liên hệ Quý khách trong thời gian sớm nhất và giải đáp mọi thắc mắc.</p>

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