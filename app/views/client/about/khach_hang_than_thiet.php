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
    .content-section h6,
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
        font-size: 17px;
        color: #212529;
    }
    
    .content-section h6 {
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 10px;
        font-size: 15.5px;
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
        margin-bottom: 10px;
        color: #495057;
        text-align: justify;
        line-height: 1.6;
        font-size: 15px;
        position: relative;
        padding-left: 15px;
    }

    .content-section ul li::before {
        content: "•";
        color: #495057;
        font-weight: bold;
        position: absolute;
        left: 0;
        top: 0;
    }

    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h5 { font-size: 19px; }
    .content-section.large-text h6 { font-size: 17.5px; }
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
            <li class="breadcrumb-item active" aria-current="page">Chính sách Chương trình Khách hàng thân thiết tại FPT Shop</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'khach-hang-than-thiet'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách Chương trình Khách hàng thân thiết tại FPT Shop</h3>

                <p>Khách hàng khi mua hàng tại hệ sinh thái FPT Retail sẽ được tích lũy điểm và đổi thành ưu đãi.</p>

                <h5>1. Tổng quan</h5>
                <p>"Chương trình khách hàng thân thiết” là chương trình ưu đãi dành riêng cho Khách hàng thân thiết của chuỗi cửa hàng trực thuộc Công ty cổ phần bán lẻ kỹ thuật số FPT (FPT Retail) bao gồm:</p>
                <ul>
                    <li>Chuỗi cửa hàng FPT Shop</li>
                    <li>Chuỗi cửa hàng thương hiệu (F.Studio, S.Studio, Garmin...)</li>
                    <li>Công ty Cổ phần Dược phẩm FPT Long Châu</li>
                    <li>Tiêm chủng Long Châu</li>
                </ul>

                <h5>2. Đối tượng áp dụng</h5>
                <p>Chỉ áp dụng cho các khách hàng cá nhân, không áp dụng cho khách hàng bán buôn hoặc mua số lượng lớn phục vụ cho doanh nghiệp hoặc đơn hàng nằm trong chương trình ưu đãi dành riêng cho đối tác/dự án/xuất hoá đơn công ty.</p>

                <h5>3. Phạm vị áp dụng</h5>
                <p>Áp dụng cho khách hàng mua hàng trực tiếp tại hệ thống cửa hàng hoặc trên các kênh bán hàng trực tuyến chính thức của chuỗi cửa hàng trực thuộc Công ty cổ phần bán lẻ kỹ thuật số FPT bao gồm:</p>
                <ul>
                    <li>Chuỗi cửa hàng FPT Shop</li>
                    <li>Chuỗi cửa hàng thương hiệu (F.Studio, S.Studio, Garmin...)</li>
                    <li>Công ty Cổ phần Dược phẩm FPT Long Châu</li>
                    <li>Tiêm chủng Long Châu</li>
                </ul>

                <h5>4. Thời gian diễn ra Chương trình</h5>
                <p>Từ ngày 05/01/2024*<br>
                <em>(*) có thể thay đổi và sẽ cập nhật khi đang diễn ra chương trình</em></p>

                <h5>5. Chi tiết cách thức và thể lệ tham gia chương trình tại FPT Shop và chuỗi cửa hàng thương hiệu (F.Studio, S.Studio, Garmin...) như sau</h5>
                
                <h6>5.1. Thể lệ</h6>
                <ul>
                    <li>Điểm thưởng được tích lũy dựa trên giá trị hóa đơn hàng hóa/dịch vụ của hệ thống bán lẻ FPT Shop (không bao gồm các dịch vụ thu hộ, dịch vụ FPT Shop bán hàng thay cho đối tác không ghi nhận doanh thu trực tiếp FPT Shop, đơn hàng nằm trong chương trình ưu đãi dành riêng cho đối tác/dự án/xuất hoá đơn công ty) và từ hệ thống nhà thuốc FPT Long Châu cùng Tiêm chủng Long Châu.</li>
                    <li>Cứ mỗi 4.000 đồng trên hóa đơn thanh toán, khách hàng sẽ được tích 01 điểm thưởng. Số điểm thưởng được tích sẽ dựa vào giá trị cuối cùng của hóa đơn khách hàng thanh toán. Ví dụ: Giá trị đơn hàng là 500.000 đồng, khách hàng có áp dụng mã khuyến mãi 100.000 đồng. Giá trị hóa đơn khách hàng cần thanh toán là 400.000 đồng và khách hàng sẽ được tích 100 điểm.</li>
                    <li>Từ ngày 01/11/2024, khách hàng có thể quy đổi điểm thưởng thành ưu đãi giảm giá với 2 mức giá trị đơn hàng như sau:<br>
                    - Đối với những đơn hàng dưới 1.000.000 đồng, khách hàng sẽ được giảm tối đa 200.000 đồng (tương đương với mức 20.000 điểm).<br>
                    - Đối với những đơn hàng từ 1.000.000 đồng trở lên, khách hàng có thể quy đổi điểm thưởng với mức tối đa 20% giá trị đơn hàng.</li>
                    <li>Khách hàng cần đổi tối thiểu từ 50 điểm để có thể quy đổi thành Voucher, 1 điểm thưởng = 10 đồng</li>
                </ul>
                <p><strong>** Ưu đãi được nhận khi tích đủ điểm</strong><br>
                Khách hàng khi tích đủ mức điểm sẽ đổi được suất mua đặc quyền với giá 1.000 đồng theo 4 mốc điểm 1.000/3.000/8.000/15.000 <a href="#">tại đây</a>.<br>
                Mỗi suất mua đặc quyền có hạn sử dụng 30 ngày kể từ ngày đổi.<br>
                Lưu ý: Điểm đã đổi thành suất mua đặc quyền khi hết hạn thì không hoàn lại.</p>

                <h6>5.2. Cách thức</h6>
                <ul>
                    <li><strong>Bước 1:</strong> Mua hàng tại chuỗi cửa hàng FPT Shop hoặc chuỗi cửa hàng thương hiệu (F.Studio, S.Studio, Garmin...). Mỗi lần mua hàng với hóa đơn từ 4.000 đồng, khách hàng tích lũy được 01 điểm thưởng tương ứng.</li>
                    <li><strong>Bước 2:</strong> Tìm kiếm từ khóa “FPT Shop” trên ứng dụng Zalo của điện thoại hoặc quét mã QR để theo dõi tài khoản Zalo chính thức của FPT Shop.</li>
                    <br>
                    <li><strong>Bước 3:</strong> Nhấn quan tâm hoặc kết bạn trên ứng dụng Zalo của điện thoại</li>
                    <br>
                    <li><strong>Bước 4:</strong> Bấm tiếp tục ngay ở khung chat Zalo và nhập số điện thoại để đăng ký thành công khách hàng thân thiết của FPT Shop</li>
                    <br>
                    <li><strong>Bước 5:</strong> Sau khi kết bạn thành công, Khách hàng có thể đổi điểm thưởng thành ưu đãi giảm giá khi mua hàng trực tiếp trên website Fptshop.com.vn hoặc hệ thống cửa hàng FPT Shop trên toàn quốc.</li>
                </ul>

                <p class="fw-bold mt-3 text-dark">Trường hợp 1: Khách hàng mua trên website</p>
                <ul>
                    <li style="list-style-type: none; padding-left: 0;">* Khách hàng chọn sản phẩm và nhấn “mua ngay” hoặc thêm vào giỏ hàng.</li>
                    <li style="list-style-type: none; padding-left: 0;">* Tại màn hình giỏ hàng, bật toggle đổi điểm.</li>
                    <br>
                    <li style="list-style-type: none; padding-left: 0;">* Điểm được đổi thành ưu đãi giảm giá sẽ được cộng dồn vào tổng khuyến mại.</li>
                    <li style="list-style-type: none; padding-left: 0;">* Xác nhận đơn hàng và thanh toán để hoàn tất đặt đơn.</li>
                </ul>

                <p class="fw-bold mt-3 text-dark">Trường hợp 2: Khách hàng mua hàng qua tổng đài hoặc mua hàng trực tiếp tại Shop:</p>
                <p>Quý khách liên hệ nhân viên shop hoặc nhân viên tư vấn để được hỗ trợ trực tiếp.<br>
                Chi tiết cách thức và thể lệ tham gia chương trình tại nhà thuốc Long Châu và Trung tâm tiêm chủng Long Châu tham khảo tại <a href="#">tại đây</a></p>

                <h5>6. Các quy định khác</h5>
                
                <h6>6.1. Quy định về số dư điểm/Hết hạn điểm</h6>
                <ul>
                    <li>Khi điểm thưởng được sử dụng để đổi điểm thưởng thành ưu đãi thì số điểm thưởng có thời gian hết hạn gần nhất sẽ được tự động ưu tiên dùng trước để bảo toàn lợi ích cho khách hàng.</li>
                    <li>Khách hàng vui lòng kiểm tra thời hạn sử dụng của điểm thưởng để mau chóng sử dụng, tránh trường hợp điểm thưởng hết hạn.</li>
                    <li>Điểm thưởng có hạn sử dụng trong vòng 12 tháng kể từ lúc tích điểm và hết hạn vào ngày cuối cùng của tháng.<br>
                    Ví dụ: Điểm thưởng được tích vào ngày 24/09/2023 sẽ hết hạn vào ngày 30/09/2024.</li>
                    <li>Điểm thưởng được tích tại mỗi thời điểm khác nhau sẽ có thời hạn sử dụng khác nhau.</li>
                </ul>

                <h6>6.2. Quy định về khấu trừ/Hủy điểm</h6>
                <ul>
                    <li>Sau khi khách hàng tiến hành đổi điểm thưởng thành ưu đãi, FPT Shop sẽ khấu trừ các điểm thưởng đã được tích trong hệ thống.</li>
                    <li>Các trường hợp trả sản phẩm sau khi đã được tích điểm, với mỗi giá trị trả là 4.000 đồng, khách hàng sẽ bị giảm 01 điểm thưởng trong hệ thống.</li>
                </ul>

                <h6>6.3. Các quy định khác</h6>
                <ul>
                    <li>Khi tham gia chương trình, khách hàng hiểu rằng phía FPT Shop có quyền quyết định, hạn chế, tạm ngưng, thu hồi, thay đổi các quy định liên quan của một phần hoặc toàn bộ Chương trình hoặc chấm dứt Chương trình theo quy định của pháp luật.</li>
                    <li>Việc kết thúc chương trình sẽ có hiệu lực trong ngày ghi trong thông báo và khách hàng phải sử dụng điểm đã tích để đổi quà tặng trong thời hạn này (nếu đủ điểm đổi quà tặng). Sau thời gian này, toàn bộ điểm tích lũy chưa đổi quà tặng sẽ không được giải quyết.</li>
                    <li>Thể lệ và thời gian diễn ra chương trình có thể được thay đổi mà không cần thông báo trước.</li>
                    <li>Tất cả các thắc mắc và khiếu nại về chương trình, vui lòng liên hệ với chúng tôi qua hotline: <strong>18006601</strong> (miễn phí).</li>
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