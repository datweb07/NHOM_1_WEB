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

.content-section h3 {
    font-weight: bold;
    font-size: 24px;
    color: #212529;
    margin-bottom: 25px;
}

.faq-item {
    border-bottom: 1px solid #f0f0f0;
}

.faq-button {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: none;
    border: none;
    padding: 18px 0;
    text-align: left;
    font-size: 15.5px;
    font-weight: 600;
    color: #333;
    transition: color 0.2s ease;
}

.faq-button:focus {
    outline: none;
}

.faq-icon-wrap {
    width: 24px;
    height: 24px;
    background-color: #f5f5f5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    font-size: 18px;
    flex-shrink: 0;
    margin-left: 15px;
    transition: all 0.3s ease;
}

.faq-button.collapsed .faq-icon-wrap::before {
    content: "+";
    font-weight: 400;
}

.faq-button:not(.collapsed) .faq-icon-wrap::before {
    content: "-";
    font-weight: 400;
}

.faq-body {
    padding: 0 0 15px 15px;
    border-left: 3px solid #cb1c22;
    margin-bottom: 20px;
    font-size: 14.5px;
    color: #495057;
    line-height: 1.6;
    transition: font-size 0.3s ease-in-out;
}

.faq-body p {
    margin-bottom: 10px;
    text-align: justify;
}

.faq-body p:last-child {
    margin-bottom: 0;
}

.faq-body a {
    color: #0056b3;
    text-decoration: none;
    font-weight: 500;
}

.faq-body a:hover {
    text-decoration: underline;
}

.btn-view-more {
    display: block;
    width: fit-content;
    margin: 25px auto;
    padding: 8px 30px;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    transition: all 0.2s ease;
}

.btn-view-more:hover {
    background-color: #eeededff;
}

.content-section.large-text .faq-button {
    font-size: 17.5px;
}

.content-section.large-text .faq-body {
    font-size: 16.5px;
    line-height: 1.7;
}
</style>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Câu hỏi thường gặp</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php
            $active_page = 'cau-hoi-thuong-gap';
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php';
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Câu hỏi thường gặp</h3>

                <div class="faq-accordion" id="faqContainer">

                    <div class="faq-item">
                        <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq1" aria-expanded="false">
                            1. Mua sản phẩm FPT Shop được bảo hành như thế nào?
                            <span class="faq-icon-wrap"></span>
                        </button>
                        <div id="faq1" class="collapse">
                            <div class="faq-body">
                                <p>Để đảm bảo quyền lợi của Quý khách hàng khi mua sản phẩm tại các cửa hàng thuộc hệ
                                    thống cửa hàng FPT Shop. Chúng tôi cam kết tất cả các sản phẩm được tuân theo các
                                    điều khoản bảo hành của sản phẩm tại thời điểm xuất hóa đơn cho Quý khách hàng. Các
                                    sản phẩm điện thoại sẽ có chính sách bảo hành khác nhau tùy thuộc vào hãng sản xuất.
                                    Khách hàng có thể bảo hành máy tại các cửa hàng FPT Shop trên toàn quốc cũng như các
                                    trung tâm bảo hành chính hãng sản phẩm.</p>
                                <p>Khách hàng có thể truy cập các đường dẫn sau để tìm kiếm địa chỉ trung tâm bảo hoặc
                                    cửa hàng FPT Shop gần nhất và tham khảo chính sách bảo hành:</p>
                                <p>Chính sách bảo hành: Quý khách vui lòng <a href="#">Xem tại đây</a>.</p>
                                <p>Cửa hàng FPT Shop gần nhất: Quý khách vui lòng <a href="#">Xem tại đây</a>.</p>
                                <p>Để tra cứu thông tin máy gửi bảo hành, Quý khách hàng vui lòng tra cứu tại <a
                                        href="#">Trang kiểm tra bảo hành</a>.</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq2" aria-expanded="false">
                            2. Mua sản phẩm tại FPT Shop có được đổi trả không? Nếu được thì phí đổi trả sẽ được tính
                            như thế nào?
                            <span class="faq-icon-wrap"></span>
                        </button>
                        <div id="faq2" class="collapse">
                            <div class="faq-body">
                                <p>Đối với các sản phẩm ĐTDĐ, MTB, MTXT, SMARTWATCH (Áp dụng bao gồm các sản phẩm
                                    Apple), sản phẩm cùng model, cùng màu, cùng dung lượng. Trong tình huống sản phẩm
                                    đổi hết hàng, khách hàng có thể đổi sang một sản phẩm khác tương đương hoặc cao hơn
                                    về giá trị so với sản phẩm lỗi. Trường hợp khách hàng muốn trả sản phẩm: FPTShop sẽ
                                    kiểm tra tình trạng máy và thông báo đến Khách hàng về giá trị thu lại sản phẩm ngay
                                    tại cửa hàng.</p>
                                <p>Để biết thêm thông tin chi tiết, Quý khách hàng có thể tra cứ phí đổi trả chi tiết <a
                                        href="#">Tại đây</a>.</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq3" aria-expanded="false">
                            3. FPT Shop có chính sách giao hàng tận nhà không? Nếu giao hàng tại nhà mà không ưng sản
                            phẩm có được trả lại không?
                            <span class="faq-icon-wrap"></span>
                        </button>
                        <div id="faq3" class="collapse">
                            <div class="faq-body">
                                <p>FPT Shop cam kết giao hàng toàn bộ 63 tỉnh thành, FPT Shop nhận giao đơn hàng có thời
                                    gian hẹn giao tại nhà trước 20h00. Miễn phí giao hàng với các đơn hàng trong bán
                                    kính 20km có đặt shop (Với đơn hàng có giá trị nhỏ hơn 100.000đ sẽ thu phí 10.000đ)
                                    nhân viên FPT Shop sẽ tư vấn chi tiết về cách thức giao nhận thuận tiện nhất.</p>
                                <p>Nếu Quý khách hàng không ưng ý với sản phẩm khi nhận hàng, Quý khách có thể từ chối
                                    mua hàng mà không mất bất cứ chi phí nào. Để biết thêm thông tin, Quý khách có thể
                                    tham khảo Chính sách giao hàng <a href="#">Tại đây</a>.</p>
                                <p><strong>Lưu ý:</strong><br>
                                    Đối với các sản phẩm còn nguyên seal, khách hàng muốn bóc seal sẽ phải thanh toán
                                    100% giá trị sản phẩm. Nếu không ưng, FPT Shop sẽ hỗ trợ đổi sản phẩm cho khách hàng
                                    theo chính sách đổi trả.<br>
                                    Đối với các sản phẩm không seal, Quý khách hàng có thể kiểm tra máy mà không phải
                                    chịu bất cứ chi phí nào nếu không mua.</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq4" aria-expanded="false">
                            4. Làm thế nào để kiểm tra được tình trạng máy đã gửi đi bảo hành tại FPT Shop?
                            <span class="faq-icon-wrap"></span>
                        </button>
                        <div id="faq4" class="collapse">
                            <div class="faq-body">
                                <p>Để tra cứu thông tin máy gửi bảo hành, Quý khách hàng có thể truy cập <a href="#">Tại
                                        đây</a>.</p>
                                <p>→ Chọn mục "Tra cứu thông tin máy".</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq5" aria-expanded="false">
                            5. Muốn kiểm tra sản phẩm đã mua từ FPT Shop có chính hãng của Apple thì xem như thế nào?
                            <span class="faq-icon-wrap"></span>
                        </button>
                        <div id="faq5" class="collapse">
                            <div class="faq-body">
                                <p>Để tra cứu thông tin sản phẩm chính hãng của Apple, Quý khách hàng có thể truy cập <a
                                        href="#">Tại đây</a>.</p>
                                <p>→ Nhập số sê-ri của thiết bị.</p>
                            </div>
                        </div>
                    </div>

                    <div id="hiddenFaqItems" style="display: none;">

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq6" aria-expanded="false">
                                6. Cần hướng dẫn cách sử dụng về sản phẩm thì liên hệ hoặc xem ở đâu được?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq6" class="collapse">
                                <div class="faq-body">
                                    <p>Quý khách có thể tham khảo sách hướng dẫn sử dụng kèm theo sản phẩm hoặc gọi vào
                                        tổng đài 1800.6601 nhánh số 2 để gặp điện thoại viên hướng dẫn thêm.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq7" aria-expanded="false">
                                7. Muốn xem giá thay thế linh kiện cho sản phẩm đã mua tại FPT Shop thì xem ở đâu?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq7" class="collapse">
                                <div class="faq-body">
                                    <p>Quý khách tham khảo bảng giá sửa chữa <a href="#">Tại đây</a>.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq8" aria-expanded="false">
                                8. Đặt đơn hàng thành công và muốn theo dõi tiến độ đơn hàng đã được đi giao chưa thì
                                xem ở đâu?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq8" class="collapse">
                                <div class="faq-body">
                                    <p>Để tra cứu thông tin đơn hàng đã đặt thành công và tiến độ xử lý đơn hàng, Quý
                                        khách hàng có thể truy cập <a href="#">Tại đây</a>.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq9" aria-expanded="false">
                                9. Sản phẩm mới mua về bị lỗi không sử dụng được thì liên hệ ai để xử lý nhanh mà không
                                bị mất thời gian di chuyển nhiều lần?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq9" class="collapse">
                                <div class="faq-body">
                                    <p>Khách hàng có thể mang máy đến tại các cửa hàng FPT Shop trên toàn quốc cũng như
                                        các trung tâm bảo hành chính hãng sản phẩm nơi gần nhà khách hàng nhất.</p>
                                    <p>Khách hàng có thể truy cập <a href="#">Tại đây</a> để tìm kiếm địa chỉ cửa hàng
                                        FPT Shop gần nhất.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq10" aria-expanded="false">
                                10. Làm thế nào để tra cứu về hóa đơn đã mua hàng tại FPT Shop?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq10" class="collapse">
                                <div class="faq-body">
                                    <p>Quý khách thực hiện tra cứu <a href="#">Tại đây</a>.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq11" aria-expanded="false">
                                11. Cần hướng dẫn vấn đề điều chỉnh hoặc xuất lại hóa đơn do bị sai thông tin khách
                                hàng?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq11" class="collapse">
                                <div class="faq-body">
                                    <p>Quý khách vui lòng liên hệ tổng đài 1800.6616 để gặp điện thoại viên tư vấn hỗ
                                        trợ hoặc Quý khách tham khảo qua hướng dẫn <a href="#">Tại đây</a> nếu liên quan
                                        đến hóa đơn công ty.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq12" aria-expanded="false">
                                12. Điện thoại mua tại FPT Shop bị lỗi và gửi đi bảo hành nhưng muốn mượn một máy khác
                                để dùng trong thời gian chờ bảo hành thì có được không?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq12" class="collapse">
                                <div class="faq-body">
                                    <p>FPT Shop sẽ hỗ trợ cho khách hàng mượn điện thoại khác sử dụng theo quy định của
                                        công ty, mời Quý khách liên hệ tại cửa hàng FPT Shop nơi khách hàng gửi máy đi
                                        bảo hành để được tư vấn hỗ trợ.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq13" aria-expanded="false">
                                13. Muốn thanh toán tiền thu hộ qua kênh online thì thực hiện bằng cách nào?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq13" class="collapse">
                                <div class="faq-body">
                                    <p>Quý khách thực hiện truy cập <a href="#">Tại đây</a> vào đường dẫn sau để thực
                                        hiện thanh toán.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq14" aria-expanded="false">
                                14. Cần tra cứu điểm mua hàng tại FPT Shop đã tích điểm được bao nhiêu thì xem ở đâu?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq14" class="collapse">
                                <div class="faq-body">
                                    <p>Quý khách thực hiện tra cứu <a href="#">Tại đây</a> và đăng nhập số điện thoại
                                        mua hàng của Quý khách.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq15" aria-expanded="false">
                                15. Muốn cập nhật máy Apple có thời gian bảo hành không đúng hoặc chưa kích hoạt bảo
                                hành thì làm thế nào?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq15" class="collapse">
                                <div class="faq-body">
                                    <p>Quý khách vui lòng chờ thêm và kiểm tra lại thông tin bảo hành sau 72h kể từ khi
                                        kích hoạt máy (không tính lễ, Tết, Thứ 7, CN). Nếu sau thời gian này vẫn chưa
                                        cập nhật thời gian bảo hành thì Quý khách vui lòng liên hệ tổng đài 1800.6616 để
                                        gặp tổng đài viên hỗ trợ.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq16" aria-expanded="false">
                                16. Cách tra cứu về thông tin trúng thưởng của FPT Shop khi tham gia các chương trình
                                mini game?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq16" class="collapse">
                                <div class="faq-body">
                                    <p>Quý khách thực hiện tra cứu <a href="#">Tại đây</a>.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq17" aria-expanded="false">
                                17. Máy gửi đi sửa dịch vụ và đã nhận thông tin báo phí nhưng muốn thanh toán phí online
                                thì thực hiện bằng cách nào?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq17" class="collapse">
                                <div class="faq-body">
                                    <p>Quý khách vui lòng liên hệ tổng đài 1800.6616 để gặp điện thoại viên tư vấn hỗ
                                        trợ.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq18" aria-expanded="false">
                                18. Phụ kiện nhập khẩu Apple đã hết hạn bảo hành và muốn gửi sửa chữa dịch vụ tại FPT
                                Shop thì có được không?
                                <span class="faq-icon-wrap"></span>
                            </button>
                            <div id="faq18" class="collapse">
                                <div class="faq-body">
                                    <p>Đối với Phụ kiện nhập khẩu nếu Quý khách có nhu cầu gửi hãng để làm dịch vụ FPT
                                        Shop tiếp nhận sản phẩm gửi về TTBH kiểm tra, có chi phí báo lại Quý khách sau.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div> <button class="btn-view-more" id="btnViewMore">Xem thêm</button>

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


    const btnViewMore = document.getElementById('btnViewMore');
    const hiddenItems = document.getElementById('hiddenFaqItems');

    if (btnViewMore && hiddenItems) {
        btnViewMore.addEventListener('click', function() {
            if (hiddenItems.style.display === 'none') {

                hiddenItems.style.display = 'block';
                btnViewMore.innerText = 'Thu gọn';
            } else {

                hiddenItems.style.display = 'none';
                btnViewMore.innerText = 'Xem thêm';

                document.getElementById('faq5').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    }
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>