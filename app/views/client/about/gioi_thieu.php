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
            <li class="breadcrumb-item active" aria-current="page">Giới thiệu về FPT Shop</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'gioi-thieu'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Giới thiệu về FPT Shop</h3>

                <h5>1. Về chúng tôi</h5>
                <p>FPT Shop là chuỗi chuyên bán lẻ các sản phẩm kỹ thuật số di động bao gồm điện thoại di động, máy tính
                    bảng, laptop, phụ kiện và dịch vụ công nghệ… cùng các mặt hàng gia dụng, điện máy chính hãng, chất
                    lượng cao đến từ các thương hiệu lớn, với mẫu mã đa dạng và mức giá tối ưu nhất cho khách hàng.</p>
                <p>FPT Shop là hệ thống bán lẻ đầu tiên ở Việt Nam được cấp chứng chỉ ISO 9001:2000 về quản lý chất
                    lượng theo tiêu chuẩn quốc tế. Hiện nay, FPT Shop là chuỗi bán lẻ lớn thứ 2 trên thị trường bán lẻ
                    hàng công nghệ.</p>

                <h5>2. Sứ mệnh</h5>
                <p>Hệ thống FPT Shop kỳ vọng mang đến cho khách hàng những trải nghiệm mua sắm tốt nhất thông qua việc
                    cung cấp các sản phẩm chính hãng, dịch vụ chuyên nghiệp cùng chính sách hậu mãi chu đáo. FPT Shop
                    không ngừng cải tiến và phát triển, hướng tới việc trở thành nhà bán lẻ công nghệ hàng đầu Việt Nam,
                    đồng thời mang lại giá trị thiết thực cho cộng đồng.</p>

                <h5>3. Giá trị cốt lõi</h5>
                <p><strong class="text-dark">• Chất lượng và Uy tín:</strong> FPT Shop cam kết cung cấp các sản phẩm
                    chính hãng, chất lượng cao với chính sách bảo hành uy tín và dịch vụ chăm sóc khách hàng chu đáo,
                    nhằm đem đến cho khách hàng sự an tâm tuyệt đối khi mua sắm các sản phẩm công nghệ, điện máy - gia
                    dụng.</p>
                <p><strong class="text-dark">• Khách hàng là trọng tâm:</strong> Phục vụ khách hàng luôn là ưu tiên số
                    1. FPT Shop luôn chú trọng hoàn thiện chất lượng dịch vụ, bồi dưỡng đội ngũ nhân viên nhiệt tình,
                    trung thực, chân thành, mang lại lợi ích và sự hài lòng tối đa cho khách hàng.</p>
                <p><strong class="text-dark">• Đổi mới và phát triển:</strong> FPT Shop luôn cập nhật và đổi mới sản
                    phẩm, công nghệ cũng như dịch vụ để đáp ứng nhu cầu thay đổi liên tục của thị trường và khách hàng.
                </p>
                <p><strong class="text-dark">• Đồng hành cùng cộng đồng:</strong> FPT Shop không chỉ tập trung vào phát
                    triển kinh doanh mà còn chú trọng đến các hoạt động xã hội, đóng góp tích cực cho sự phát triển của
                    cộng đồng và xã hội.</p>

                <h5>4. Định hướng phát triển</h5>
                <p>Với mục tiêu “Tạo trải nghiệm xuất sắc cho khách hàng”, FPT Shop tiếp tục đẩy mạnh chuyển đổi số để
                    ứng dụng vào công tác bán hàng, quản lý và đào tạo nhân sự... theo chiến lược tận tâm phục vụ nhằm
                    gia tăng trải nghiệm khách hàng. Đầu tư mạnh mẽ kinh doanh trực tuyến đa nền tảng, khai thác và ứng
                    dụng công nghệ để thấu hiểu và tiếp cận khách hàng một cách linh hoạt và hiệu quả nhất, không ngừng
                    khẳng định vị thế là một trong thương hiệu bán lẻ uy tín tại Việt Nam.</p>

                <h5>5. Cột mốc phát triển</h5>
                <ul>
                    <li><strong class="text-dark">• 2013:</strong> FPT Shop chính thức đạt mốc 100 cửa hàng.</li>
                    <li><strong class="text-dark">• 2014:</strong> Trở thành nhà nhập khẩu trực tiếp của iPhone chính
                        hàng.</li>
                    <li><strong class="text-dark">• 2015:</strong> Đạt mức tăng trưởng nhanh nhất so với các công ty
                        trực thuộc cùng Công ty Cổ phần FPT.</li>
                    <li><strong class="text-dark">• 2016:</strong> Doanh thu online tăng gấp đôi. Khai trương 80 khu
                        trải nghiệm Apple corner trên toàn quốc.</li>
                    <li><strong class="text-dark">• 08/2024:</strong> Đồng loạt khai trương 10 cửa hàng điện máy trên
                        toàn quốc, đánh dấu việc mở rộng lĩnh vực kinh doanh sang điện máy, gia dụng.</li>
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