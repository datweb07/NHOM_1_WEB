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
    margin-bottom: 10px;
    color: #495057;
    text-align: justify;
    line-height: 1.6;
    font-size: 15px;
    position: relative;
    padding-left: 20px;
}

.content-section ul li::before {
    content: "•";
    font-weight: bold;
    position: absolute;
    left: 0;
    top: -2px;
    font-size: 18px;
}

.footnote {
    font-size: 14px;
    color: #6c757d;
    font-style: italic;
    margin-bottom: 5px !important;
}

.content-section.large-text h3 {
    font-size: 28px;
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
            <li class="breadcrumb-item active" aria-current="page">Chính sách bảo hành</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'bao-hanh'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách bảo hành</h3>

                <p>Tất cả sản phẩm tại FPT Shop kinh doanh đều là sản phẩm chính hãng và được bảo hành theo đúng chính
                    sách của nhà sản xuất(*). Ngoài ra FPT Shop cũng hỗ trợ gửi bảo hành miễn phí giúp khách hàng đối
                    với cả sản phẩm do FPT Shop bán ra và sản phẩm Quý khách mua tại các chuỗi bán lẻ khác.</p>

                <p>Mua hàng tại FPT Shop, Quý khách sẽ được hưởng những đặc quyền sau:</p>
                <ul>
                    <li>Bảo hành đổi sản phẩm mới ngay tại shop trong 30 ngày nếu có lỗi NSX.(**)</li>
                    <li>Gửi bảo hành chính hãng không mất phí vận chuyển.(***)</li>
                    <li>Theo dõi tiến độ bảo hành nhanh chóng qua kênh hotline hoặc tự tra cứu <a href="#">Tại đây</a>.
                    </li>
                    <li>Hỗ trợ làm việc với hãng để xử lý phát sinh trong quá trình bảo hành.</li>
                </ul>

                <p>Bên cạnh đó Quý khách có thể tham khảo một số các trường hợp thường gặp nằm ngoài chính sách bảo hành
                    sau để xác định sơ bộ máy có đủ điều kiện bảo hành hãng:</p>
                <ul>
                    <li>Sản phẩm hết hạn bảo hành (Vui lòng tra cứu thời hạn bảo hành sản phẩm <a href="#">Tại đây</a>).
                    </li>
                    <li>Sản phẩm đã bị thay đổi, sửa chữa không thuộc các Trung Tâm Bảo Hành Ủy Quyền của Hãng.</li>
                    <li>Sản phẩm lắp đặt, bảo trì, sử dụng không đúng theo hướng dẫn của Nhà sản xuất gây ra hư hỏng.
                    </li>
                    <li>Sản phẩm lỗi do ngấm nước, chất lỏng và bụi bẩn. Quy định này áp dụng cho cả những thiết bị đạt
                        chứng nhận kháng nước/kháng bụi cao nhất là IP68.</li>
                    <li>Sản phẩm bị biến dạng, nứt vỡ, cấn móp, trầy xước nặng do tác động nhiệt, tác động bên ngoài.
                    </li>
                    <li>Sản phẩm có vết mốc, rỉ sét hoặc bị ăn mòn, oxy hóa bởi hóa chất.</li>
                    <li>Sản phẩm bị hư hại do thiên tai, hỏa hoạn, lụt lội, sét đánh, côn trùng, động vật vào.</li>
                    <li>Sản phẩm trong tình trạng bị khóa tài khoản cá nhân như: Tài khoản khóa máy/màn hình, khóa tài
                        khoản trực tuyến Xiaomi Cloud, Samsung Cloud, iCloud, Gmail...</li>
                    <li>Khách hàng sử dụng phần mềm, ứng dụng không chính hãng, không bản quyền.</li>
                    <li>Màn hình có bốn (04) điểm chết trở xuống.</li>
                </ul>

                <p class="fw-bold mt-4 mb-2 text-dark">Lưu ý:</p>
                <ul>
                    <li>Chương trình bảo hành bắt đầu có hiệu lực từ thời điểm FPT Shop xuất hóa đơn cho Quý khách.</li>
                    <li>Với mỗi dòng sản phẩm khác nhau sẽ có chính sách bảo hành khác nhau tùy theo chính sách của
                        Hãng/Nhà cung cấp.</li>
                    <li>Để tìm hiểu thông tin chi tiết về chính sách bảo hành cho sản phẩm cụ thể, xin liên hệ bộ phận
                        Chăm sóc Khách hàng của FPT Shop 1800 6616.</li>
                    <li>Tra cứu tình trạng máy gửi bảo hành bất cứ lúc nào <a href="#">Tại đây</a>.</li>
                    <li>Trong quá trình thực hiện dịch vụ bảo hành, các nội dung lưu trữ trên sản phẩm của Quý khách sẽ
                        bị xóa và định dạng lại. Do đó, Quý khách vui lòng tự sao lưu toàn bộ dữ liệu trong sản phẩm,
                        đồng thời gỡ bỏ tất cả các thông tin cá nhân mà Quý khách muốn bảo mật. FPT Shop không chịu
                        trách nhiệm đối với bất kỳ mất mát nào liên quan tới các chương trình phần mềm, dữ liệu hoặc
                        thông tin nào khác lưu trữ trên sản phẩm bảo hành.</li>
                    <li>Vui lòng tắt tất cả các mật khẩu bảo vệ, FPT Shop sẽ từ chối tiếp nhận bảo hành nếu thiết bị của
                        bạn bị khóa bởi bất cứ phương pháp nào.</li>
                </ul>

                <div class="mt-4 border-top pt-3">
                    <p class="footnote">(*) Áp dụng với các sản phẩm bán mới hoặc còn hạn bảo hành mặc định nếu đã qua
                        sử dụng.</p>
                    <p class="footnote">(**) Áp dụng với các sản phẩm thuộc diện đổi mới trong 30 ngày nếu có lỗi NSX
                        được công bố trên website <a href="#">Chính sách đổi trả</a>.</p>
                    <p class="footnote">(***) Trừ các sản phẩm có chính sách bảo hành tại nhà, sản phẩm thuộc diện cồng
                        kềnh.</p>
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