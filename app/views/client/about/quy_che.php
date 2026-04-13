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
    .content-section h4,
    .content-section h5,
    .content-section h6,
    .content-section p,
    .content-section ul li,
    .content-section .table {
        transition: font-size 0.3s ease-in-out;
    }

    .content-section h3 {
        font-weight: bold;
        font-size: 24px;
        color: #212529;
        margin-bottom: 25px;
        text-transform: uppercase;
    }

    .content-section h4 {
        font-weight: bold;
        font-size: 18px;
        margin-top: 30px;
        margin-bottom: 15px;
        text-transform: uppercase;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 8px;
    }

    .content-section h5 {
        font-weight: bold;
        margin-top: 25px;
        margin-bottom: 12px;
        font-size: 16px;
        color: #212529;
    }
    
    .content-section h6 {
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 10px;
        font-size: 15px;
        color: #212529;
    }

    .content-section p {
        text-align: justify;
        color: #495057;
        line-height: 1.6;
        margin-bottom: 15px;
        font-size: 14.5px;
    }

    .content-section ul {
        list-style-type: none;
        padding-left: 0;
        margin-bottom: 15px;
    }

    .content-section ul li {
        margin-bottom: 8px;
        color: #495057;
        text-align: justify;
        line-height: 1.6;
        font-size: 14.5px;
        position: relative;
        padding-left: 15px;
    }

    .content-section ul li::before {
        content: "-";
        color: #cb1c22;
        font-weight: bold;
        position: absolute;
        left: 0;
        top: 0;
    }
    
    .content-section ul.no-bullet li::before {
        content: "";
    }
    
    .content-section ul.plus-bullet li::before {
        content: "+";
        color: #212529;
    }

    .content-section .table-container {
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        margin-bottom: 25px;
    }

    .content-section .table {
        margin-bottom: 0;
        font-size: 14.5px;
    }

    .content-section .table th {
        background-color: #f8f9fa;
        color: #212529;
        font-weight: bold;
        vertical-align: middle;
        border-bottom: 2px solid #dee2e6;
        text-align: center;
    }

    .content-section .table td {
        color: #495057;
        vertical-align: top;
    }

    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h4 { font-size: 20px; }
    .content-section.large-text h5 { font-size: 18px; }
    .content-section.large-text h6 { font-size: 17px; }
    .content-section.large-text p,
    .content-section.large-text ul li,
    .content-section.large-text .table {
        font-size: 16.5px;
        line-height: 1.7;
    }
</style>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/ho-tro">Hỗ trợ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Quy chế hoạt động</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'quy-che'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>QUY CHẾ HOẠT ĐỘNG WEBSITE CUNG CẤP DỊCH VỤ TMĐT FPTSHOP.COM.VN</h3>

                <h4>I. Nguyên tắc chung</h4>
                <ul>
                    <li>Website thương mại điện tử FPTshop.com.vn do Công ty Cổ Phần Bán lẻ kỹ thuật số FPT (“Công ty”) thực hiện hoạt động và vận hành. Đối tượng phục vụ là tất cả khách hàng trên 63 tỉnh thành Việt Nam có nhu cầu mua hàng nhưng không có thời gian đến shop hoặc đặt trước để khi đến shop là đảm bảo có hàng.</li>
                    <li>Sản phẩm được kinh doanh tại www.FPTshop.com.vn phải đáp ứng đầy đủ các quy định của pháp luật, không bán hàng nhái, hàng không rõ nguồn gốc, hàng xách tay.</li>
                    <li>Hoạt động mua bán tại FPTshop.com.vn phải được thực hiện công khai, minh bạch, đảm bảo quyền lợi của người tiêu dùng.</li>
                </ul>

                <h4>II. Quy định chung</h4>
                <p class="fw-bold mb-1">Tên Miền website Thương mại Điện tử:</p>
                <p>Website thương mại điện tử FPTshop.com.vn do Công ty Cổ phần bán lẻ kỹ thuật số phát triển với tên miền giao dịch là: www.FPTshop.com.vn (sau đây gọi tắt là: “FPTshop.com.vn”)</p>
                
                <p class="fw-bold mb-1">Định nghĩa chung:</p>
                <p><strong>Người bán</strong> là Công ty Cổ phần Bán lẻ kỹ thuật số FPT<br>
                <strong>Người mua</strong> là công dân Việt Nam trên khắp 63 tỉnh thành. Người mua có quyền đăng ký tài khoản hoặc không cần đăng ký để thực hiện giao dịch.<br>
                <strong>Thành viên</strong> là bao gồm cả người mua và người tham khảo thông tin, thảo luận tại website.</p>
                <p>Nội dung bản Quy chế này tuân thủ theo các quy định hiện hành của Việt Nam. Thành viên khi tham gia website TMĐT FPTshop.com.vn phải tự tìm hiểu trách nhiệm pháp lý của mình đối với luật pháp hiện hành của Việt Nam và cam kết thực hiện đúng những nội dung trong Quy chế này.</p>

                <h4>III. Quy trình giao dịch</h4>
                <p class="fw-bold mb-1">Dành cho người mua hàng tại website TMĐT FPTshop.com.vn</p>
                <p><strong>Bước 1:</strong> Tìm kiếm và chọn sản phẩm cần mua.<br>
                <strong>Bước 2:</strong> Xem giá và thông tin chi tiết sản phẩm đó, nếu quý khách đồng ý muốn đặt hàng, quý khách ấn vào 1 trong 3 nút mua hàng:</p>
                <ul class="plus-bullet">
                    <li>Mua ngay</li>
                    <li>Trả góp 0%</li>
                    <li>Trả góp qua thẻ</li>
                </ul>
                <p><strong>Bước 3:</strong> Quý khách điền đầy đủ thông tin theo mua hàng theo mẫu:</p>
                <ul class="plus-bullet">
                    <li>Họ tên; Số điện thoại; Email</li>
                    <li>Chọn phương thức nhận hàng: Nhận hàng tại cửa hàng hoặc giao hàng tận nơi</li>
                    <li>Chọn phương thức thanh toán: Trả tiền mặt; Thẻ ATM; Thẻ Quốc tế (Visa, MasterCard); Trả góp. Quý khách hàng đang sử dụng thẻ ATM nội địa hoặc thẻ tín dụng Visa, Master quý khách có thể thanh toán đơn hàng bằng cách click chọn vào hình thức thanh toán tương ứng và làm theo hướng dẫn của ngân hàng.</li>
                </ul>
                <p><strong>Bước 4:</strong> Sau khi đã nhập đầy đủ thông tin, quý khách click “Đặt hàng” để hoàn tất đặt hàng<br>
                <strong>Bước 5:</strong> Sau khi nhận đơn hàng của người mua, FPTshop.com.vn sẽ liên lạc với khách hàng qua thông tin số điện thoại quý khách hàng cung cấp từ số điện thoại 028 7300 6601 để xác thực thông tin đơn hàng.<br>
                <strong>Bước 6:</strong> FPTshop.com.vn giao hàng tận nhà đến cho khách hàng hoặc khách hàng đến trực tiếp các cửa hàng trên toàn quốc để nhận hàng.</p>

                <p class="fw-bold mb-1">Dành cho bên bán hàng là FPTshop.com.vn</p>
                <ul>
                    <li>Chuẩn bị sản xuất nội dung gồm: hình ảnh sản phẩm chụp thực tế hoặc hình ảnh do hãng sản xuất cung cấp, bài viết giới thiệu, thông tin cấu hình sản phẩm.</li>
                    <li>Nhập liệu bằng công cụ quản lý riêng dành cho nhân viên FPTshop.com.vn</li>
                    <li>Định dạng hình ảnh sử dụng trên website: jpg, png.</li>
                </ul>

                <p class="fw-bold mb-1">Quy trình giao nhận vận chuyển</p>
                <ul>
                    <li>FPTshop.com.vn thực hiện giao hàng trên toàn quốc. Khi nhận đơn hàng từ người mua và sau khi đã xác nhận thông tin mua hàng qua điện thoại, FPTshop.com.vn sẽ tiến hành giao hàng theo yêu cầu của quý khách hàng.</li>
                    <li>Giữ hàng tại các cửa hàng của FPTShop trên toàn quốc và người mua sẽ đến trực tiếp cửa hàng kiểm tra và nhận hàng.</li>
                    <li>Giao hàng tận nơi trên toàn bộ 63 tỉnh thành</li>
                    <li>FPT Shop nhận giao đơn hàng có thời gian hẹn giao tại nhà trước 21h00 đối với Điện thoại, Máy tính bảng và trước 20h00 đối với Máy tính xách tay.</li>
                    <li>Miễn phí giao hàng trong bán kính 20km có đặt shop.</li>
                    <li>Với khoảng cách lớn hơn 20km, nhân viên FPTShop sẽ tư vấn chi tiết về cách thức giao nhận thuận tiện nhất</li>
                    <li>Với những đơn hàng giao tại nhà và có giá trị từ 50 triệu đồng trở lên, Quý khách vui lòng thanh toán trước 100% giá trị đơn hàng.</li>
                </ul>

                <p class="fw-bold mb-2">Quy trình bảo hành/đổi trả sản phẩm</p>

                <h5>I. CHÍNH SÁCH ĐỔI TRẢ SẢN PHẨM MỚI: APPLE, ĐTDĐ, MTB, MTXT, SMARTWATCH, MÀN HÌNH LCD SAMSUNG</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;"></th>
                                <th>THÁNG 1</th>
                                <th>THÁNG 2-12</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Sản phẩm lỗi do nhà sản xuất</td>
                                <td>
                                    <strong>1 ĐỔI 1</strong><br>
                                    sản phẩm cùng model, cùng màu, cùng dung lượng. <br>
                                    Trong tình huống sản phẩm đổi hết hàng, khách hàng có thể đổi sang một sản phẩm khác tương đương hoặc cao hơn về giá trị so với sản phẩm lỗi.<br><br>
                                    Hoặc<br>
                                    Khách hàng muốn trả sản phẩm: FPTShop sẽ kiểm tra tình trạng máy và thông báo đến Khách hàng về giá trị thu lại sản phẩm ngay tại cửa hàng.
                                </td>
                                <td>
                                    <strong>GỬI MÁY ĐI BẢO HÀNH THEO QUI ĐỊNH CỦA HÃNG</strong><br><br>
                                    Hoặc<br>
                                    Khách hàng muốn đổi sang sản phẩm khác hoặc trả sản phẩm: FPTShop sẽ kiểm tra tình trạng máy và thông báo đến Khách hàng về giá trị thu lại sản phẩm ngay tại cửa hàng.
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sản phẩm không lỗi (đổi trả theo nhu cầu của khách hàng)</td>
                                <td colspan="2">Khách hàng muốn đổi sang sản phẩm khác hoặc trả sản phẩm: FPTShop sẽ kiểm tra tình trạng máy và thông báo đến Khách hàng về giá trị thu lại sản phẩm ngay tại cửa hàng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sản phẩm lỗi do người sử dụng</td>
                                <td colspan="2">
                                    <strong>Không áp dụng đổi trả</strong> với sản phẩm:<br>
                                    - Máy không còn giữ nguyên 100% hình dạng ban đầu, bao gồm: có dấu hiệu va chạm mạnh, cấn móp, bị vào nước...<br>
                                    - Không đủ điều kiện bảo hành theo chính sách của hãng.<br>
                                    Trong trường hợp này, FPT Shop hỗ trợ chuyển TTBH và khách hàng chịu phí sửa chữa.<br>
                                    <strong>Phí đổi trả khác nếu có:</strong> FPTShop sẽ kiểm tra tình trạng máy và thông báo đến khách hàng về mức phí phải thu ngay tại cửa hàng.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p><strong>Lưu ý với sản phẩm mua trả góp:</strong><br>
                Trong 14 ngày đầu tiên: khách hàng huỷ hợp đồng và không phải chịu bất kỳ khoản chi phí trả góp nào<br>
                Sau 14 ngày: khách hàng phải thanh lý hợp đồng và chịu phí theo từng công ty trả góp</p>

                <h5>II. CHÍNH SÁCH ĐỔI TRẢ SẢN PHẨM ĐÃ QUA SỬ DỤNG: ĐTDĐ, MTB, MTXT SMARTWATCH (Bao gồm APPLE)</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;"></th>
                                <th>THÁNG 1</th>
                                <th>THÁNG 2-12</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Sản phẩm lỗi do nhà sản xuất</td>
                                <td>
                                    Miễn phí đổi sản phẩm tương đương: cùng model, cùng dung lượng, cùng thời gian bảo hành…<br>
                                    Trường hợp không có sản phẩm tương đương, FPT Shop hoàn lại tiền 100%
                                </td>
                                <td>FPT Shop gửi máy đi bảo hành theo chính sách của hãng hoặc bảo hành của FPTShop.com.vn</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sản phẩm không lỗi</td>
                                <td colspan="2" class="text-center">Không áp dụng đổi trả</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sản phẩm lỗi do người sử dụng</td>
                                <td colspan="2">
                                    <strong>Không áp dụng đổi trả</strong> với sản phẩm:<br>
                                    - Máy không còn giữ nguyên 100% hình dạng ban đầu, bao gồm: có dấu hiệu va chạm mạnh, cấn móp, bị vào nước...<br>
                                    - Không đủ điều kiện bảo hành theo chính sách của hãng.<br>
                                    Trong trường hợp này, FPT Shop hỗ trợ chuyển TTBH và khách hàng chịu phí sửa chữa.<br>
                                    <strong>Phí đổi trả khác nếu có:</strong> FPTShop sẽ kiểm tra tình trạng máy và thông báo đến khách hàng về mức phí phải thu ngay tại cửa hàng
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5>III. CHÍNH SÁCH ĐỔI TRẢ ĐỒNG HỒ THỜI TRANG, VÒNG ĐEO TAY THÔNG MINH</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;"></th>
                                <th style="width: 35%;">THÁNG 1</th>
                                <th>TỪ THÁNG THỨ 2 ĐẾN THỜI HẠN BẢO HÀNH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Sản phẩm lỗi do NSX (Bao gồm các lỗi kĩ thuật liên quan đến bộ máy, không bao gồm pin)</td>
                                <td>
                                    Miễn phí 1 đổi 1 sản phẩm cùng model<br>
                                    Hoặc<br>
                                    - KH được đổi sang sản phẩm khác model bằng hoặc cao tiền hơn, thanh toán thêm phần chênh lệch, và phí đổi trả tính như sau:<br>
                                    + Nếu sản phẩm KH đã mua hết hàng (trên toàn hệ thống): Không mất phí đổi trả<br>
                                    + Nếu sản phẩm KH đã mua còn hàng: FPTShop sẽ kiểm tra tình trạng máy và thông báo đến Khách hàng về giá trị thu lại sản phẩm ngay tại cửa hàng.
                                </td>
                                <td>
                                    Gửi bảo hành theo chính sách hãng<br>
                                    - Trường hợp không sửa chữa được hoặc bảo hành trễ hẹn so với cam kết (tối đa 30 ngày): KH được đổi sản phẩm mới, thanh toán thêm phần chênh lệch. Sản phẩm cũ được FPTShop sẽ kiểm tra tình trạng máy và thông báo đến Khách hàng về giá trị thu lại sản phẩm ngay tại cửa hàng.
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sản phẩm không lỗi</td>
                                <td colspan="2" class="text-center">Không áp dụng đổi trả</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sản phẩm lỗi do người sử dụng</td>
                                <td colspan="2">FPTShop hỗ trợ chuyển TTBH hãng sửa chữa (có tính phí).</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p><strong>Lưu ý:</strong><br>
                - Đồng hồ thời trang chỉ được hưởng chính sách bảo hành, đổi trả khi còn phiếu bảo hành<br>
                - Phí đổi trả khác nếu có: FPTShop sẽ kiểm tra tình trạng máy và thông báo đến khách hàng về mức phí phải thu ngay tại cửa hàng<br>
                - Đối với vòng đeo tay: khi trả hoặc đổi sản phẩm khác, khách hàng phải trả lại sạc, nếu không còn sạc FPTShop chỉ tiếp nhận bảo hành</p>

                <p class="fw-bold mb-2">Thời hạn bảo hành đồng hồ thời trang</p>
                <div class="table-container">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Hãng</th>
                                <th>Bảo hành máy</th>
                                <th>Bảo hành pin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="text-start">Bulova</td><td>36 tháng</td><td>Trọn đời</td></tr>
                            <tr><td class="text-start">Ferrari, Tommy Hilfiger, Lascote, Daniel Klein. Free look, Festina, Candino</td><td>24 tháng</td><td>Trọn đời</td></tr>
                            <tr><td class="text-start">Kitten-Kid, Nakzen, Rossini, SKmei, Sinobi, SK, Elle</td><td>12 tháng</td><td>Trọn đời</td></tr>
                            <tr><td class="text-start">Casio G-shock, Casio Baby-G, Casio Cover</td><td>60 tháng</td><td>60 tháng</td></tr>
                            <tr><td class="text-start">Casio Genaral, Casico Edifice</td><td>12 tháng</td><td>18 tháng</td></tr>
                            <tr><td class="text-start">Citizen</td><td>12 tháng</td><td>12 tháng</td></tr>
                            <tr><td class="text-start">Orient</td><td>12 tháng</td><td>Không bảo hành</td></tr>
                        </tbody>
                    </table>
                </div>

                <h5>IV. CHÍNH SÁCH ĐỔI TRẢ MÁY IN</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;"></th>
                                <th>THÁNG 1</th>
                                <th>TỪ THÁNG THỨ 2 ĐẾN THỜI HẠN BẢO HÀNH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Sản phẩm lỗi do NSX</td>
                                <td>1 đổi 1 sản phẩm cùng mã (Chỉ đổi máy, không đổi hộp mực)</td>
                                <td>Gửi bảo hành theo chính sách hãng</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sản phẩm không lỗi</td>
                                <td colspan="2" class="text-center">Không áp dụng đổi trả</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sản phẩm lỗi do người sử dụng</td>
                                <td colspan="2">
                                    Không áp dụng đổi trả, bảo hành, FPT Shop hỗ trợ chuyển TTBH Hãng sửa chữa (có tính phí), bao gồm:<br>
                                    + Vỡ, nứt thân máy, trầy xước, gãy chốt hộp mực, khách hàng tự mở máy để sửa chữa<br>
                                    + Máy không giữ nguyên 100% hình dạng ban đầu
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p><strong>Lưu ý:</strong><br>
                Khi đổi máy in, KH phải trả cả vỏ hộp. Nếu thiếu vở hộp, chỉ áp dụng bảo hành, không đổi máy mới.<br>
                Phí đổi trả khác nếu có: FPTShop sẽ kiểm tra tình trạng máy và thông báo đến khách hàng về mức phí phải thu ngay tại cửa hàng</p>

                <h5>V. CHÍNH SÁCH BẢO HÀNH PHỤ KIỆN</h5>
                <p><strong>BẢO HÀNH 1 NĂM 1 ĐỔI 1</strong><br>
                Thẻ nhớ, USB, Chuột, Cáp, Sạc, Sạc dự phòng, Bàn phím, Đế tản nhiệt, Tai nghe (trừ Tai nghe JBL giá dưới 2.5 triệu), Thiết bị mạng, Ổ cứng, Loa (trừ Loa Harman Kardon), Loa Kéo-Karaoke, Loa JBL (trừ Loa JBL Studio BAR mã 00441172) Bộ phát wifi không dây, Đèn LED để bàn đa năng, Cân điện tử.</p>

                <p><strong>BẢO HÀNH 1 NĂM CHÍNH HÃNG</strong><br>
                Phụ kiện nhập khẩu chính hãng Apple, Loa JBL Studio BAR (mã 00441172) và loa Harman Kardon</p>

                <p><strong>BẢO HÀNH 6 THÁNG 1 ĐỔI 1</strong><br>
                Mic hát karaoke, Quạt cầm tay, Tai nghe JBL giá dưới 2.5 triệu</p>

                <p><strong>BẢO HÀNH 15 NGÀY 1 ĐỔI 1</strong><br>
                Bao da, và Ốp lưng có giá từ 50.000 VNĐ (trừ Bao da, Ốp lưng chính hãng Samsung)</p>

                <p><strong>DÁN LẦN ĐẦU MUA NGUYÊN GIÁ, TỪ LẦN THỨ 2:</strong><br>
                Dán lại với giá 25.000VNĐ/mặt: Đối với MDMH lẻ và combo miếng dán 2 mặt (dành cho ĐTDĐ)<br>
                GIÁ GIẢM 30%: Đối với MDMH lẻ và combo miếng dán 2 mặt (dành cho MTB, MTXT), MDMH Kính cường lực</p>

                <p><strong>KHÔNG ÁP DỤNG BẢO HÀNH</strong><br>
                Ba lô, Túi xách, Túi chống sốc, Túi chống nước, Gậy chụp hình, Tay cầm chơi game, Bao da và ốp lưng nhập khẩu chính hãng Samsung</p>

                <p><strong>Lưu ý:</strong><br>
                MDMH: không giới hạn số lần dán, KHÔNG giới hạn số tháng áp dụng. Chỉ cần đúng imei máy đã sử dụng miếng dán ở lần mua đầu tiên.<br>
                Quạt cầm tay, Tai nghe JBL, Loa JBL: không bảo hành phụ kiện kèm theo.</p>

                <p><strong>Điều kiện bảo hành:</strong><br>
                Sản phẩm bảo hành bị lỗi kỹ thuật (không bao gồm lỗi thẩm mỹ, lỗi do người sử dụng)<br>
                Đối với phụ kiện ốp lưng, bao da mua kèm máy (Điện thoại di động/ Máy tính bảng): FPT Shop hỗ trợ nhập trả lại phụ kiện trong trường hợp khách hàng trả hàng do lỗi NSX.<br>
                Miếng dán màn hình mua kèm máy: trong trường hợp khách hàng đổi máy do lỗi NSX, FPT Shop đổi miếng dán mới cho Khách hàng.<br>
                Combo phụ kiện: FPT Shop bảo hành cho sản phẩm bị lỗi theo chính sách phụ kiện mua lẻ<br>
                Sản phẩm có thời gian bảo hành dài hơn 12 tháng theo chính sách của nhà sản xuất, kể từ tháng 13 khách hàng bảo hành sản phẩm trực tiếp tại trung tâm bảo hành Hãng<br>
                Đối với PK chính hãng Apple: Khách hàng mang sản phẩm đến tất cả các cửa hàng FPTShop/F.Studio by FPT trên toàn quốc để được hỗ trợ chuyển tới Trung tâm bảo hành uỷ quyền của Apple. Việc xác định tình trạng lỗi có thuộc diện bảo hành hay không sẽ do Apple quyết định.</p>

                <h5>VI. CHÍNH SÁCH ĐỔI TRẢ DỊCH VỤ</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 30%;">ĐẦU THU KỸ THUẬT SỐ FPT PLAYBOX</td>
                                <td>
                                    <strong>30 ngày đầu tiên:</strong> Miễn phí 1 đổi 1 khi sản phẩm lỗi NSX.<br>
                                    <strong>Tháng thứ 2-12:</strong> FPT Shop hỗ trợ chuyển trung tâm bảo hành NSX.
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Phần mềm dịch vụ<br>(không bao gồm các phần mềm Microsoft)</td>
                                <td>FPT Shop hoàn lại tiền 100% trong 31 ngày đầu nếu khách hàng không còn nhu cầu sử dụng sản phẩm.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>Phí đổi trả nếu có:<br>
                Mất vỏ hộp thu phí 2% giá trên hóa đơn<br>
                Mất phụ kiện đi kèm thu phí 5% trên giá hoá đơn cho mỗi phụ kiện mất.</p>

                <p class="fw-bold mb-1 mt-4">Đối với giao dịch của FPTshop.com.vn</p>
                <p>FPTshop.com.vn tiếp nhận khiếu nại qua các hình thức sau:</p>
                <ul>
                    <li>Tại website liên hệ, bình luận khách hàng</li>
                    <li>Qua tổng đài giải quyết khiếu nại: 1800 6616</li>
                    <li>Email: fptshop@fpt.com.vn</li>
                    <li>Trực tiếp tại các cửa hàng FPTShop</li>
                    <li>Tại văn phòng công ty: Lầu 3, 261 Khánh Hội, P2, Q4, TP. Hồ Chí Minh</li>
                </ul>

                <h4>IV. Quy trình thanh toán</h4>
                <p>Người mua và bên bán có thể tham khảo các phương thức thanh toán sau đây và lựa chọn áp dụng phương thức phù hợp:</p>
                
                <p class="fw-bold mb-1">Cách 1: Thanh toán trực tiếp (người mua nhận hàng tại địa chỉ bên bán):</p>
                <ul>
                    <li>Bước 1: Người mua tìm hiểu thông tin về sản phẩm, dịch vụ được đăng tin.</li>
                    <li>Bước 2: Người mua đến địa chỉ bán hàng là các cửa hàng FPTShop</li>
                    <li>Bước 3: Người mua thanh toán bằng tiền mặt, thẻ ATM nội địa hoặc thẻ tín dụng và nhận hàng.</li>
                </ul>

                <p class="fw-bold mb-1 mt-3">Cách 2: Thanh toán sau (COD – giao hàng và thu tiền tận nơi):</p>
                <ul>
                    <li>Bước 1: Người mua tìm hiểu thông tin về sản phẩm, dịch vụ được đăng tin.</li>
                    <li>Bước 2: Người mua xác thực đơn hàng (điện thoại, tin nhắn, email).</li>
                    <li>Bước 3: Người bán xác nhận thông tin Người mua.</li>
                    <li>Bước 4: Người bán chuyển hàng.</li>
                    <li>Bước 5: Người mua nhận hàng và thanh toán bằng tiền mặt, thẻ ATM nội địa hoặc thẻ tín dụng.</li>
                </ul>

                <p class="fw-bold mb-1 mt-3">Cách 3: Thanh toán online qua thẻ tín dụng, chuyển khoản:</p>
                <ul>
                    <li>Bước 1: Người mua tìm hiểu thông tin về sản phẩm, dịch vụ được đăng tin.</li>
                    <li>Bước 2: Người mua xác thực đơn hàng (điện thoại, tin nhắn, email).</li>
                    <li>Bước 3: Người bán xác nhận thông tin Người mua.</li>
                    <li>Bước 4: Người mua thanh toán.</li>
                    <li>Bước 5: Người bán chuyển hàng.</li>
                    <li>Bước 6: Người mua nhận hàng.</li>
                </ul>

                <h4>V. Đảm bảo an toàn giao dịch</h4>
                <ul>
                    <li>Ban quản lý đã sử dụng các dịch vụ để bảo vệ thông tin về nội dung mà người bán đăng sản phẩm trên FPTshop.com.vn. Để đảm bảo các giao dịch được tiến hành thành công, hạn chế tối đa rủi ro có thể phát sinh.</li>
                    <li>Người mua nên cung cấp thông tin đầy đủ (tên, địa chỉ, số điện thoại, email) khi tham gia mua hàng của FPTshop.com.vn để FPTshop.com.vn có thể liên hệ nhanh lại với người mua trong trường hợp xảy ra lỗi.</li>
                    <li>Trong trường hợp giao dịch nhận hàng tại nhà của người mua, thì người mua chỉ nên thanh toán sau khi đã kiểm tra hàng hoá chi tiết và hài lòng với sản phẩm.</li>
                    <li>Khi thanh toán trực tuyến bằng thẻ ATM nội địa, Visa, Master người mua nên tự mình thực hiện và không được để lộ thông tin thẻ. FPTshop.com.vn không lưu trữ thông tin thẻ của người mua sau khi thanh toán, mà thông qua hệ thống của ngân hàng liên kết. Nên tuyệt đối bảo mật thông tin thẻ cho khách hàng.</li>
                    <li>Trong trường lỗi xảy ra trong quá trình thanh toán trực tuyến, Công ty bán lẻ kỹ thuật số FPT sẽ là đơn vị giải quyết cho khách hàng trong vòng 1 giờ làm việc từ khi tiếp nhận thông tin từ người thực hiện giao dịch.</li>
                </ul>

                <h4>VI. Bảo vệ thông tin cá nhân khách hàng</h4>
                <p>FPTshop.com.vn cam kết sẽ bảo mật những thông tin mang tính riêng tư của bạn. Bạn vui lòng đọc bản “Chính sách bảo mật” dưới đây để hiểu hơn những cam kết mà chúng tôi thực hiện, nhằm tôn trọng và bảo vệ quyền lợi của người truy cập:</p>
                
                <p class="fw-bold mb-1">1. Mục đích và phạm vi thu thập</p>
                <ul>
                    <li>Để truy cập và sử dụng một số dịch vụ tại FPTshop.com.vn, bạn có thể sẽ được yêu cầu đăng ký với chúng tôi thông tin cá nhân (Email, Họ tên, Số ĐT liên lạc…). Mọi thông tin khai báo phải đảm bảo tính chính xác và hợp pháp. FPTshop.com.vn không chịu mọi trách nhiệm liên quan đến pháp luật của thông tin khai báo. </li>
                    <li>Chúng tôi cũng có thể thu thập thông tin về số lần viếng thăm, bao gồm số trang bạn xem, số links (liên kết) bạn click và những thông tin khác liên quan đến việc kết nối đến site Fptshop.com.vn. Chúng tôi cũng thu thập các thông tin mà trình duyệt Web (Browser) bạn sử dụng mỗi khi truy cập vào FPTshop.com.vn, bao gồm: địa chỉ IP, loại Browser, ngôn ngữ sử dụng, thời gian và những địa chỉ mà Browser truy xuất đến.</li>
                </ul>

                <p class="fw-bold mb-1">2 Phạm vi sử dụng thông tin</p>
                <ul>
                    <li>FPTshop.com.vn thu thập và sử dụng thông tin cá nhân bạn với mục đích phù hợp và hoàn toàn tuân thủ nội dung của “Chính sách bảo mật” này. Khi cần thiết, chúng tôi có thể sử dụng những thông tin này để liên hệ trực tiếp với bạn dưới các hình thức như: gởi thư ngỏ, đơn đặt hàng, thư cảm ơn, sms, thông tin về kỹ thuật và bảo mật…</li>
                    <li>Bằng việc bấm vào nút "Đặt mua trả góp", Khách hàng đồng ý cho Home Credit được thu thập, sử dụng cũng như chia sẻ cho bên thứ ba các thông tin liên quan đến khách hàng và/hoặc thông tin liên quan đến (các) khoản vay của khách hàng tại Home Credit hoặc các tổ chức khác.</li>
                </ul>

                <p class="fw-bold mb-1">3. Thời gian lưu trữ thông tin</p>
                <p>Dữ liệu cá nhân của Thành viên sẽ được lưu trữ cho đến khi có yêu cầu hủy bỏ hoặc tự thành viên đăng nhập và thực hiện hủy bỏ. Còn lại trong mọi trường hợp thông tin cá nhân thành viên sẽ được bảo mật trên máy chủ của FPTshop.com.vn.</p>

                <p class="fw-bold mb-1">4. Địa chỉ của đơn vị thu thập và quản lý thông tin cá nhân</p>
                <p>Công ty Cổ phần bán lẻ kỹ thuật FPT<br>
                - Địa chỉ đăng ký kinh doanh: 261 – 263 Khánh Hội, P2, Q4, TP. Hồ Chí Minh<br>
                - Văn phòng: Lầu 3, 261 – 263 Khánh Hội, P2, Q4, TP. Hồ Chí Minh<br>
                - Điện thoại văn phòng: 028 730 23456</p>

                <p class="fw-bold mb-1">5. Phương tiện và công cụ để người dùng tiếp cận và chỉnh sửa dữ liệu cá nhân</p>
                <p>Hiện website chưa triển khai trang quản lý thông tin cá nhân của thành viên, vì thế việc tiếp cận và chỉnh sửa dữ liệu cá nhân dựa vào yêu cầu của khách hàng bằng cách hình thức sau:</p>
                <ul>
                    <li>Gọi điện thoại đến tổng đài chăm sóc khách hàng 1800 6616, bằng nghiệp vụ chuyên môn xác định thông tin cá nhân và nhân viên tổng đài sẽ hỗ trợ chỉnh sửa thay người dùng</li>
                    <li>Để lại bình luận hoặc gửi góp ý trực tiếp từ website www.fptshop.com.vn, quản trị viên kiểm tra thông tin và liên lạc lại với người dùng để xác nhận thông tin 1 lần nữa và quản trị viên chỉnh sửa thông tin cho người dùng.</li>
                </ul>

                <p class="fw-bold mb-1">6. Cam kết bảo mật thông tin cá nhân khách hàng</p>
                <ul>
                    <li>Thông tin cá nhân của thành viên trên FPTshop.com.vn được FPTshop.com.vn cam kết bảo mật tuyệt đối theo chính sách bảo vệ thông tin cá nhân của FPTshop.com.vn. Việc thu thập và sử dụng thông tin của mỗi thành viên chỉ được thực hiện khi có sự đồng ý của khách hàng đó trừ những trường hợp pháp luật có quy định khác.</li>
                    <li>Không sử dụng, không chuyển giao, cung cấp hay tiết lộ cho bên thứ 3 nào về thông tin cá nhân của thành viên khi không có sự cho phép đồng ý từ thành viên.</li>
                    <li>Trong trường hợp máy chủ lưu trữ thông tin bị hacker tấn công dẫn đến mất mát dữ liệu cá nhân thành viên, FPTshop.com.vn sẽ có trách nhiệm thông báo vụ việc cho cơ quan chức năng điều tra xử lý kịp thời và thông báo cho thành viên được biết.</li>
                    <li>Bảo mật tuyệt đối mọi thông tin giao dịch trực tuyến của Thành viên bao gồm thông tin hóa đơn kế toán chứng từ số hóa tại khu vực dữ liệu trung tâm an toàn cấp 1 của Fptshop.com.vn.</li>
                    <li>Ban quản lý Fptshop.com.vn yêu cầu các cá nhân khi đăng ký/mua hàng là thành viên, phải cung cấp đầy đủ thông tin cá nhân có liên quan như: Họ và tên, địa chỉ liên lạc, email, số chứng minh nhân dân, điện thoại, số tài khoản, số thẻ thanh toán …., và chịu trách nhiệm về tính pháp lý của những thông tin trên. Ban quản lý Fptshop.com.vn không chịu trách nhiệm cũng như không giải quyết mọi khiếu nại có liên quan đến quyền lợi của Thành viên đó nếu xét thấy tất cả thông tin cá nhân của thành viên đó cung cấp khi đăng ký ban đầu là không chính xác</li>
                </ul>

                <h4>VII. Quản lý thông tin xấu</h4>
                <p class="fw-bold mb-1">Quy định thành viên</p>
                <ul>
                    <li>Thành viên sẽ tự chịu trách nhiệm về bảo mật và lưu giữ mọi hoạt động sử dụng dịch vụ dưới tên đăng ký, mật khẩu của mình. Thành viên có trách nhiệm thông báo kịp thời cho website TMĐT FPTshop.com.vn về những hành vi sử dụng trái phép, lạm dụng, vi phạm bảo mật, lưu giữ tên đăng ký và mật khẩu của bên thứ ba để có biện pháp giải quyết phù hợp.</li>
                    <li>Thành viên không được thay đổi, chỉnh sửa, gán gép, copy, truyền bá, phân phối, cung cấp và tạo những công cụ tương tự của dịch vụ do website TMĐT FPTshop.com.vn cung cấp cho một bên thứ ba nếu không được sự đồng ý của website TMĐT FPTshop.com.vn trong bản Quy chế này.</li>
                    <li>Thành viên không được hành động gây mất uy tín của website MTĐT FPTshop.com.vn dưới mọi hình thức như gây mất đoàn kết giữa các thành viên bằng cách sử dụng tên đăng ký thứ hai, thông qua một bên thứ ba hoặc tuyên truyền, phổ biến những thông tin không có lợi cho uy tín của website TMĐTFPTshop.com.vn.</li>
                </ul>

                <h4>VIII. Trách nhiệm trong trường hợp phát sinh lỗi kỹ thuật</h4>
                <ul>
                    <li>Website TMĐT FPTshop.com.vn cam kết nỗ lực đảm bảo sự an toàn và ổn định của toàn bộ hệ thống kỹ thuật. Tuy nhiên, trong trường hợp xảy ra sự cố do lỗi của FPTshop.com.vn, FPTshop.com.vn sẽ ngay lập tức áp dụng các biện pháp để đảm bảo quyền lợi cho người mua hàng.</li>
                    <li>Khi thực hiện các giao dịch trên website, bắt buộc các thành viên phải thực hiện đúng theo các quy trình hướng dẫn.</li>
                    <li>Ban quản lý website TMĐT FPTshop.com.vn cam kết cung cấp chất lượng dịch vụ tốt nhất cho các thành viên tham gia giao dịch. Trường hợp phát sinh lỗi kỹ thuật, lỗi phần mềm hoặc các lỗi khách quan khác dẫn đến thành viên không thể tham gia giao dịch được thì các thành viên thông báo cho Ban quản lý website TMĐT qua địa chỉ email fptshop@fpt.com.vn hoặc qua điện thoại 1800 6616 (từ 8:00 – 22:00 hằng ngày) chúng tôi sẽ khắc phục lỗi trong thời gian sớm nhất, tạo điều kiện cho các thành viên tham gia website TMĐT FPTshop.com.vn.</li>
                    <li>Tuy nhiên, Ban quản lý website TMĐT FPTshop.com.vn sẽ không chịu trách nhiệm giải quyết trong trường hợp thông báo của các thành viên không đến được Ban quản lý, phát sinh từ lỗi kỹ thuật, lỗi đường truyền, phần mềm hoặc các lỗi khác không do Ban quản lý gây ra.</li>
                </ul>

                <h4>IX. Quyền và nghĩa vụ của Ban quản lý website TMĐT FPTshop.com.vn</h4>
                <p class="fw-bold mb-1">1. Quyền của Ban quản lý FPTshop.com.vn:</p>
                <ul>
                    <li>Website TMĐT FPTshop.com.vn sẽ tiến hành cung cấp các dịch vụ, sản phẩm cho khách hàng sau khi đã hoàn thành các thủ tục và các điều kiện bắt buộc mà nêu ra. </li>
                    <li>FPTshop.com.vn sẽ tiến hành xây dựng các chính sách dịch vụ trên Trang web. Các chính sách này sẽ được công bố trên FPTshop.com.vn. </li>
                    <li>Trong trường hợp có cơ sở để chứng minh thành viên cung cấp thông tin cho Sàn giao dịch điện tử FPTshop.com.vn không chính xác, sai lệch, không đầy đủ hoặc có dấu hiệu vi phạm pháp luật hay thuần phong mỹ tục Việt Nam thì Sàn giao dịch điện tử FPTshop.com.vn có quyền từ chối, tạm ngừng hoặc chấm dứt quyền sử dụng dịch vụ của thành viên. </li>
                    <li>Website TMĐT FPTshop.com.vn có thể chấm dứt quyền thành viên và quyền sử dụng một hoặc tất cả các dịch vụ của thành viên trong trường hợp thành viên vi phạm các Quy chế của Website TMĐT FPTshop.com.vn, hoặc có những hành vi ảnh hưởng đến hoạt động kinh doanh trên Website TMĐT FPTshop.com.vn. </li>
                    <li>Website TMĐT FPTshop.com.vn có thể chấm dứt ngay quyền sử dụng dịch vụ và quyền thành viên của thành viên nếu Website TMĐT FPTshop.com.vn phát hiện thành viên đã phá sản, bị kết án hoặc đang trong thời gian thụ án, trong trường hợp thành viên tiếp tục hoạt động có thể gây cho Website TMĐT FPTshop.com.vn trách nhiệm pháp lý, có những hoạt động lừa đảo, giả mạo, gây rối loạn thị trường, gây mất đoàn kết đối với các thành viên khác của Website TMĐT FPTshop.com.vn, hoạt  động vi phạm pháp luật hiện hành của Việt Nam. </li>
                    <li>Trong trường hợp chấm dứt quyền thành viên và quyền sử dụng dịch vụ thì tất cả các chứng nhận, các quyền của thành viên được cấp sẽ mặc nhiên hết giá trị và bị chấm dứt.</li>
                    <li>Website TMĐT FPTshop.com.vn giữ bản quyền sử dụng dịch vụ và các nội dung trên Website TMĐT FPTshop.com.vn theo các quy định pháp luật về bảo hộ sở hữu trí tuệ tại Việt Nam. Tất cả các biểu tượng, nội dung theo các ngôn ngữ khác nhau đều thuộc quyền sở hữu của Website TMĐT FPTshop.com.vn. Nghiêm cấm mọi hành vi sao chép, sử dụng và phổ biến bất hợp pháp các quyền sở hữu trên. </li>
                    <li>Website TMĐT FPTshop.com.vn giữ quyền được thay đổi bảng, biểu giá dịch vụ và phương thức thanh toán trong thời gian cung cấp dịch vụ cho thành viên theo nhu cầu và điều kiện khả năng của Website TMĐT FPTshop.com.vn và sẽ báo trước cho thành viên thời hạn là một (01) tháng.</li>
                </ul>

                <p class="fw-bold mb-1">2. Nghĩa vụ của Ban quản lý FPTSHOP.COM.VN</p>
                <ul>
                    <li>Website TMĐT FPTshop.com.vn chịu trách nhiệm xây dựng dịch vụ bao gồm một số công việc chính như: nghiên cứu, thiết kế, mua sắm các thiết bị phần cứng và phần mềm, kết nối Internet, xây dựng chính sách phục vụ cho hoạt động Website TMĐT FPTshop.com.vn trong điều kiện và phạm vi cho phép. </li>
                    <li>Website TMĐT FPTshop.com.vn sẽ tiến hành triển khai và hợp tác với các đối tác trong việc xây dựng hệ thống các dịch vụ, các công cụ tiện ích phục vụ cho việc giao dịch của các thành viên tham gia và người sử dụng trên Website TMĐT FPTshop.com.vn</li>
                    <li>Website TMĐT FPTshop.com.vn chịu trách nhiệm xây dựng, bổ sung hệ thống các kiến thức, thông tin về: nghiệp vụ ngoại thương, thương mại điện tử, hệ thống văn bản pháp luật thương mại trong nước và quốc tế, thị trường nước ngoài, cũng như các tin tức có liên quan đến hoạt động của Website TMĐT FPTshop.com.vn. </li>
                    <li>Website TMĐT FPTshop.com.vn sẽ tiến hành các hoạt động xúc tiến, quảng bá Website TMĐT FPTshop.com.vn ra thị trường nước ngoài trong phạm vi và điều kiện cho phép, góp phần mở rộng, kết nối đáp ứng các nhu cầu tìm kiếm bạn hàng và phát triển thị trường nước ngoài của các thành viên tham gia Website TMĐT FPTshop.com.vn. </li>
                    <li>Website TMĐT FPTshop.com.vn sẽ cố gắng đến mức cao nhất trong phạm vi và điều kiện có thể để duy trì hoạt động bình thường của Website TMĐT FPTshop.com.vn và khắc phục các sự cố như: sự cố kỹ thuật về máy móc, lỗi phần mềm, hệ thống đường truyền internet, nhân sự, các biến động xã hội, thiên tai, mất điện, các quyết định của cơ quan nhà nước hay một tổ chức liên quan thứ ba. Tuy nhiên nếu những sự cố trên xảy ra nằm ngoài khả năng kiểm soát, là những trường hợp bất khả kháng mà gây thiệt hại cho thành viên thì Website TMĐT FPTshop.com.vn không phải chịu trách nhiệm liên đới.</li>
                </ul>
                <p>Website TMĐT FPTshop.com.vn phải có trách nhiệm:</p>
                <ul>
                    <li>Xây dựng và thực hiện cơ chế để đảm bảo việc đăng thông tin trên Website TMĐT FPTshop.com.vn được thực hiện chính xác. </li>
                    <li>Không đăng tải những thông tin bán hàng hóa, dịch vụ thuộc danh mục hàng hóa, dịch vụ cấm kinh doanh theo quy định của pháp luật và hàng hóa hạn chế kinh doanh theo quy định tại Thông tư 47/2014/TT-BCT.</li>
                </ul>

                <h4>X. Quyền và trách nhiệm thành viên tham gia website TMĐTFPTshop.com.vn</h4>
                <p class="fw-bold mb-1">1. Quyền của Thành viên Website TMĐT FPTshop.com.vn</p>
                <ul>
                    <li>Khi đăng ký trở thành thành viên của FPTshop.com.vn và đượcFPTshop.com.vn đồng ý, thành viên sẽ được tham gia thảo luận, đánh giá sản phẩm, mua hàng tại FPTshop.com.vn .</li>
                    <li>Thành viên có quyền đóng góp ý kiến cho Website TMĐT FPTshop.com.vn trong quá trình hoạt động. Các kiến nghị được gửi trực tiếp bằng thư, fax hoặc email đến cho Website TMĐT FPTshop.com.vn.</li>
                </ul>
                
                <p class="fw-bold mb-1">Nghĩa vụ của Thành viên Website TMĐT FPTshop.com.vn</p>
                <ul>
                    <li>Thành viên sẽ tự chịu trách nhiệm về bảo mật và lưu giữ và mọi hoạt động sử dụng dịch vụ dưới tên đăng ký, mật khẩu và hộp thư điện tử của mình.</li>
                    <li>Thành viên cam kết những thông tin cung cấp cho Website TMĐT FPTshop.com.vn và những thông tin đang tải lên Website TMĐT FPTshop.com.vn là chính xác.</li>
                    <li>Thành viên cam kết không được thay đổi, chỉnh sửa, sao chép, truyền bá, phân phối, cung cấp và tạo những công cụ tương tự của dịch vụ do Website TMĐT FPTshop.com.vn cung cấp cho một bên thứ ba nếu không được sự đồng ý của Website TMĐT FPTshop.com.vn trong Quy định này.</li>
                    <li>Thành viên không được hành động gây mất uy tín của Website TMĐT FPTshop.com.vn dưới mọi hình thức như gây mất đoàn kết giữa các thành viên bằng cách sử dụng tên đăng ký thứ hai, thông qua một bên thứ ba hoặc tuyên truyền, phổ biến những thông tin không có lợi cho uy tín của Website TMĐT FPTshop.com.vn.</li>
                </ul>

                <h4>XI. Điều khoản áp dụng</h4>
                <ul>
                    <li>Mọi tranh chấp phát sinh giữa Website TMĐT FPTshop.com.vn và thành viên sẽ được giải quyết trên cơ sở thương lượng. Trường hợp không đạt được thỏa thuận như mong muốn, một trong hai bên có quyền  đưa vụ việc ra Tòa án nhân dân có thẩm quyền tại Thành phố Hồ Chí Minh để giải quyết.</li>
                    <li>Quy chế của Website TMĐT FPTshop.com.vn chính thức có hiệu lực thi hành kể từ ngày ký Quyết định ban hành kèm theo Quy chế này. Website TMĐT FPTshop.com.vn có quyền và có thể thay đổi Quy chế này bằng cách thông báo lên Website TMĐT FPTshop.com.vn cho các thành viên biết. Quy chế sửa đổi có hiệu lực kể từ ngày Quyết định về việc sửa đổi Quy chế có hiệu lực. Việc thành viên tiếp tục sử dụng dịch vụ sau khi Quy chế sửa đổi được công bố và thực thi đồng nghĩa với việc họ đã chấp nhận Quy chế sửa đổi này.</li>
                </ul>

                <h4>XII. Điều khoản cam kết</h4>
                <p>Địa chỉ liên lạc chính thức của Website TMĐT FPTshop.com.vn:</p>
                <ul>
                    <li>Website TMĐT FPTshop.com.vn</li>
                    <li>Công ty/Tổ chức : Công ty Cổ Phần Bán lẻ kỹ thuật số FPT</li>
                    <li>Địa chỉ: 261 – 263 Khánh Hội, P2, Q4, TP. Hồ Chí Minh</li>
                    <li>Văn phòng: Lầu 3, 261 – 263 Khánh Hội, P2, Q4, TP. Hồ Chí Minh</li>
                    <li>Tel: 028 730 23456      Email: fptshop@fpt.com.vn</li>
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