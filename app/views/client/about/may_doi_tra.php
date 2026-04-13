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
        margin-bottom: 15px;
        font-size: 18px;
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
        list-style-type: none;
        padding-left: 0;
        margin-bottom: 20px;
    }

    .content-section ul li {
        margin-bottom: 12px;
        color: #495057;
        text-align: justify;
        line-height: 1.6;
        font-size: 15px;
        position: relative;
        padding-left: 20px;
    }

    .content-section ul li::before {
        content: "-";
        font-weight: bold;
        position: absolute;
        left: 0;
        top: 0;
    }

    .footnote {
        font-size: 14px;
        color: #6c757d;
        font-style: italic;
        margin-top: 10px;
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
            <li class="breadcrumb-item active" aria-current="page">Giới thiệu máy đổi trả</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 

            $active_page = 'may-doi-tra'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Giới thiệu máy đổi trả</h3>

                <p>Máy cũ kinh doanh tại FPT Shop là các sản phẩm có nguồn gốc tin cậy, còn đủ điều kiện bảo hành được kiểm tra kỹ lưỡng bởi FPT Shop, bao gồm:</p>
                <ul>
                    <li><strong>Máy trưng bày (demo):</strong> là máy được dùng để trưng bày tại cửa hàng, phục vụ nhu cầu trải nghiệm của khách hàng tại shop, sau khi hết thời gian trưng bày, được điều chuyển để kinh doanh.</li>
                    <li><strong>Máy đã qua sử dụng:</strong> là máy thu lại từ khách hàng theo chính sách đổi trả/bảo hành, đã được bảo hành chính hãng và được FPT Shop kiểm tra chất lượng.</li>
                </ul>

                <h5>Chế độ bảo hành:</h5>
                <ul>
                    <li>1 đổi 1 máy tương đương trong vòng 30 ngày nếu máy có lỗi nhà sản xuất (*) nếu không có máy tương đương, khách hàng có thể đổi sang sản phẩm khác cao tiền hơn hoặc FPT Shop thu hồi lại máy.</li>
                    <li>Áp dụng bảo hành theo chính sách của Hãng nếu máy còn bảo hành mặc định của Hãng, trường hợp hết bảo hành mặc định, máy sẽ được bảo hành từ 1 đến 12 tháng theo chính sách của FPT Shop tùy từng loại sản phẩm.(**)</li>
                    <li>Tiếp nhận bảo hành tại tất cả các cửa hàng FPT Shop trên toàn quốc.</li>
                </ul>

                <p>Với mẫu mã đa dạng, giá cả hợp lý, chất lượng tốt, Khách hàng có thể hoàn toàn yên tâm chọn mua và sử dụng các sản phẩm máy cũ tại FPT Shop đang kinh doanh phù hợp với nhu cầu của mình.</p>
                
                <p>Quý khách có thể đến trực tiếp <strong>FPT Shop</strong> để xem và mua máy, hoặc tìm kiếm máy đổi trả phù hợp về mức giá và nhu cầu sử dụng trên Website. Nếu tìm thấy sản phẩm vừa ý trên website, Quý khách có thể đặt giữ hàng trong 24 tiếng.</p>

                <div class="mt-4">
                    <p class="footnote">(*) Theo kết quả kết luận của hãng</p>
                    <p class="footnote">(**) Hạn bảo hành của sản phẩm được thể hiện trên hóa đơn bán hàng và trên website <a href="https://fptshop.com.vn/" target="_blank">https://fptshop.com.vn/</a></p>
                </div>
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