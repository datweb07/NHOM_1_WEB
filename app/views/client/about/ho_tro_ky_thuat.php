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
    .content-section ul li {
        transition: font-size 0.3s ease-in-out;
    }

    .content-section h3 {
        font-weight: bold;
        font-size: 24px;
        color: #212529;
        margin-bottom: 25px;
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
        font-weight: bold;
        position: absolute;
        left: 0;
        top: 0;
    }

    .content-section.large-text h3 { font-size: 28px; }
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
            <li class="breadcrumb-item active" aria-current="page">Quy định hỗ trợ kỹ thuật và sao lưu dữ liệu</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'ho-tro-ky-thuat'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Quy định hỗ trợ kỹ thuật và sao lưu dữ liệu</h3>

                <p><strong>Đối tượng áp dụng:</strong> Khách hàng có nhu cầu hỗ trợ phần mềm bảo hành sửa chữa sản phẩm tại FPT Shop.</p>
                <p>Nhằm đảm bảo đầy đủ quyền lợi của khách hàng khi cài đặt, bảo hành sửa chữa sản phẩm, FPT Shop xin thông báo Quy định như sau:</p>
                
                <ul>
                    <li>Để bảo vệ dữ liệu cá nhân, Quý khách vui lòng sao lưu và XOÁ các dữ liệu cá nhân trước khi bàn giao sản phẩm cho nhân viên FPT Shop.</li>
                    <li>FPT Shop không chịu trách nhiệm về việc mất dữ liệu của Quý khách trong quá trình cài đặt, bảo hành sửa chữa.</li>
                    <li>Để đảm bảo Quyền lợi, Quý khách vui lòng ký xác nhận để thông tin bàn giao thiết bị của Quý khách được ghi nhận trên hệ thống FPT Shop.</li>
                    <li>FPT Shop không hỗ trợ cài đặt phần mềm không có bản quyền trên máy tính của Quý khách.</li>
                    <li>Quý khách vui lòng kiểm tra tài khoản iCloud/ Google và các tài khoản xã hội khác trên máy trước khi rời cửa hàng.</li>
                    <li>Tài khoản cài đặt trên máy phải là tài khoản cá nhân của Quý khách (chủ sở hữu máy).</li>
                    <li>Nếu chưa có tài khoản iCloud, Quý khách liên hệ NV Kỹ thuật để được hỗ trợ tạo Tài khoản iCloud (Apple ID)/ Google và các tài khoản khác miễn phí tại cửa hàng. Đồng thời yêu cầu nhân viên cung cấp thông tin, mật khẩu tài khoản vừa được tạo trước khi rời cửa hàng.</li>
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