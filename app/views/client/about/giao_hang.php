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
.content-section p,
.content-section ul li,
.policy-card-title {
    transition: font-size 0.3s ease-in-out;
}

.content-section h3 {
    font-weight: bold;
    font-size: 24px;
    color: #212529;
    margin-bottom: 20px;
}

.content-section>p.intro-text {
    text-align: justify;
    color: #495057;
    line-height: 1.6;
    margin-bottom: 25px;
    font-size: 15.5px;
}

.policy-card {
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    transition: box-shadow 0.3s ease, border-color 0.3s ease;
}

.policy-card:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border-color: #cb1c22;
}

.policy-card-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 15px;
}

.policy-card-title {
    font-size: 18px;
    font-weight: bold;
    color: #212529;
    margin: 0;
    text-transform: uppercase;
}

.policy-card-body p {
    color: #495057;
    line-height: 1.6;
    margin-bottom: 10px;
    font-size: 15px;
    text-align: justify;
}

.policy-card-body ul {
    list-style-type: none;
    padding-left: 0;
    margin-bottom: 0;
}

.policy-card-body ul li {
    color: #495057;
    line-height: 1.6;
    margin-bottom: 12px;
    font-size: 15px;
    position: relative;
    padding-left: 20px;
    text-align: justify;
}

.policy-card-body ul li:last-child {
    margin-bottom: 0;
}

.policy-card-body ul li::before {
    content: "•";
    font-weight: bold;
    font-size: 18px;
    position: absolute;
    left: 0;
    top: -2px;
}

.warning-card {
    background-color: #fffaf0;
    border-color: #fbd5d5;
}

.content-section.large-text h3 {
    font-size: 28px;
}

.content-section.large-text p.intro-text,
.content-section.large-text .policy-card-body p,
.content-section.large-text .policy-card-body ul li {
    font-size: 17px;
    line-height: 1.7;
}

.content-section.large-text .policy-card-title {
    font-size: 20px;
}
</style>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách giao hàng & lắp đặt</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'giao-hang'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách giao hàng & lắp đặt</h3>

                <p class="intro-text fw-bold text-dark">Giao hàng tại nhà</p>
                <p class="intro-text">Mua hàng tại FPT Shop, khách hàng sẽ được hỗ trợ giao hàng tại nhà hầu như trên
                    toàn quốc. Với độ phủ trên khắp 63 tỉnh thành, Quý khách sẽ nhận được sản phẩm nhanh chóng mà không
                    cần mất thời gian di chuyển tới cửa hàng.</p>

                <div class="policy-card">
                    <div class="policy-card-header">
                        <h4 class="policy-card-title">Giao hàng</h4>
                    </div>
                    <div class="policy-card-body">
                        <ul>
                            <li>Áp dụng với tất cả các sản phẩm có áp dụng giao hàng tại nhà, không giới hạn giá trị.
                            </li>
                            <li>Miễn phí giao hàng trong bán kính 20km có đặt shop (Đơn hàng giá trị < 100.000 VNĐ thu
                                    phí 10.000 VNĐ).</li>
                            <li>Với khoảng cách lớn hơn 20km, nhân viên FPT Shop sẽ tư vấn chi tiết về cách thức giao
                                nhận thuận tiện nhất.</li>
                        </ul>
                    </div>
                </div>

                <div class="policy-card">
                    <div class="policy-card-header">
                        <h4 class="policy-card-title">Thanh toán</h4>
                    </div>
                    <div class="policy-card-body">
                        <ul>
                            <li><strong>Đơn hàng có giá trị từ 50 triệu trở lên:</strong> Quý khách phải thanh toán
                                trước 100% giá trị đơn hàng nếu muốn giao hàng tại nhà.</li>
                            <li><strong>Đơn hàng có giá trị dưới 50 triệu:</strong> Quý khách có thể nhận hàng và thanh
                                toán tại nhà khi đồng ý mua hàng.</li>
                        </ul>
                    </div>
                </div>

                <div class="policy-card">
                    <div class="policy-card-header">
                        <h4 class="policy-card-title">Hỗ trợ lắp đặt</h4>
                    </div>
                    <div class="policy-card-body">
                        <p>Đối với các sản phẩm có chính sách lắp đặt tại nhà (VD: TV, Điều hòa,...) sau khi sản phẩm
                            được giao tới nơi. FPT Shop sẽ hỗ trợ tư vấn, lắp đặt và hướng dẫn sử dụng miễn phí cho
                            khách hàng.</p>
                    </div>
                </div>

                <div class="policy-card warning-card mt-4">
                    <div class="policy-card-header">
                        <h4 class="policy-card-title">Riêng đối với các sản phẩm Chỉ bán Online:</h4>
                    </div>
                    <div class="policy-card-body">
                        <ul>
                            <li>Khi nhận hàng, quý khách không đồng kiểm chi tiết với nhà vận chuyển (chỉ kiểm tra ngoại
                                quan kiện hàng, không bóc và kiểm tra chi tiết sản phẩm bên trong). Trường hợp Quý khách
                                không nhận sản phẩm, kiện hàng sẽ được nhà vận chuyển chuyển hoàn về nơi gửi.</li>
                            <li>Quý khách cần quay video khi nhận hàng mở kiện để được thực hiện đổi trả nếu hàng hoá có
                                phát sinh vấn đề.</li>
                            <li>Quý khách có 01 ngày để gọi lên tổng đài khiếu kiện trong trường hợp phát sinh đến hàng
                                hoá.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const btnSmall = document.getElementById('btn-font-small');
    const btnLarge = document.getElementById('btn-font-large');
    const contentSection = document.getElementById('policy-content');

    if (btnSmall && btnLarge && contentSection) {
        btnLarge.addEventListener('click', function() {
            btnLarge.classList.add('active');
            btnSmall.classList.remove('active');
            contentSection.classList.add('large-text');
        });

        btnSmall.addEventListener('click', function() {
            btnSmall.classList.add('active');
            btnLarge.classList.remove('active');
            contentSection.classList.remove('large-text');
        });
    }
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>