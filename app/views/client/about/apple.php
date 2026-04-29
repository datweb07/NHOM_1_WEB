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
    color: #212529;
}

.content-section p {
    text-align: justify;
    color: #495057;
    line-height: 1.6;
    margin-bottom: 15px;
    font-size: 15px;
}

.content-section ul {
    list-style-type: none;
    padding-left: 0;
}

.content-section ul li {
    margin-bottom: 10px;
    color: #495057;
    text-align: justify;
    line-height: 1.6;
    font-size: 15px;
}

.content-section.large-text h3 {
    font-size: 28px;
}

.content-section.large-text h5 {
    font-size: 18px;
}

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
            <li class="breadcrumb-item active" aria-current="page">Đại lý uỷ quyền và TTBH uỷ quyền của Apple</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'apple'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Đại lý uỷ quyền và TTBH uỷ quyền của Apple</h3>

                <h5>1. Đại lý ủy quyền toàn cầu Apple (Apple Authorized Reseller)</h5>
                <p>FPT Shop là đại lý Apple ủy quyền toàn cầu tại Việt Nam và nhập khẩu, kinh doanh tất cả các sản phẩm
                    từ Apple.</p>
                <p>Khách hàng không chỉ được trải nghiệm sản phẩm mới một cách hoàn hảo với khu trải nghiệm Apple Corner
                    đạt tiêu chuẩn hãng mà còn được tư vấn đầy đủ trước khi quyết định mua.</p>
                <p>Đội ngũ chuyên viên tư vấn và cố vấn kỹ thuật FPT Shop và F.Studio by FPT được đào tạo bởi các chuyên
                    gia Apple, sẽ tư vấn cũng như hỗ trợ kỹ thuật dành cho khách hàng một cách chuyên nghiệp nhất về sản
                    phẩm, ứng dụng và các công nghệ mới nhất từ Apple.</p>

                <h5>2. Trung tâm bảo hành ủy quyền Apple F.Care by FPT – Apple Authorized Service Provider</h5>
                <p>F.Care by FPT là một chuỗi các trung tâm bảo hành được Apple Việt Nam ủy quyền về mặt pháp lý và kỹ
                    thuật để thực hiện các dịch vụ sửa chữa, bảo hành đối với sản phẩm của hãng theo tiêu chuẩn do Apple
                    quy định. Được vận hành và xây dựng bởi FPT Retail.</p>
                <p>Đến với F.Care, khách hàng sẽ được trải nghiệm dịch vụ chuẩn hãng, với đội ngũ kỹ thuật viên có kinh
                    nghiệm dày dặn, được đào tạo bài bản bởi Apple.</p>
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