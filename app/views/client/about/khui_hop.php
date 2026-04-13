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
        margin-bottom: 12px;
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
            <li class="breadcrumb-item"><a href="/ho-tro">Hỗ trợ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách khui hộp sản phẩm</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'khui-hop'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách khui hộp sản phẩm</h3>

                <p class="fw-bold text-dark">Áp dụng cho các sản phẩm bán ra tại FPT Shop bao gồm ĐTDĐ, MT, MTB, Đồng hồ thông minh.</p>

                <h5>Nội dung chính sách:</h5>
                <ul>
                    <li>Sản phẩm bắt buộc phải khui seal/mở hộp và kích hoạt bảo hành điện tử (Active) ngay tại shop hoặc ngay tại thời điểm nhận hàng khi có nhân viên giao hàng tại nhà.</li>
                    <li>Đối với các sản phẩm bán nguyên seal khách hàng cần phải thanh toán trước 100% giá trị đơn hàng trước khi khui seal sản phẩm.</li>
                </ul>

                <h5>Lưu ý:</h5>
                <ul>
                    <li>Trước khi kích hoạt bảo hành điện tử (Active) sản phẩm khách hàng cần kiểm tra các lỗi ngoại quan của sản phẩm (Cấn_Trầy thân máy, bụi trong camera, bụi màn hình, hở viền…). Nếu phát hiện các lỗi trên khách hàng sẽ được đổi 1 sản phẩm khác hoặc hoàn tiền.</li>
                    <li>Ngay sau khi kiểm tra lỗi ngoại quan, tiến hành bật nguồn để kiểm tra đến lỗi kỹ thuật; nếu sản phẩm có lỗi kỹ thuật của nhà sản xuất khách hàng sẽ được đổi 1 sản phẩm mới tương đương tại FPT Shop.</li>
                    <li>Nếu quý khách báo lỗi ngoại quan sau khi sản phẩm đã được kích hoạt bảo hành điện tử (Active) hoặc sau khi nhân viên giao hàng rời đi FPT shop chỉ hỗ trợ chuyển sản phẩm của khách hàng đến hãng để thẩm định và xử lý.</li>
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