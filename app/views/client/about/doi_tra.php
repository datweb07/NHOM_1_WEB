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
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 8px;
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
    font-size: 14.5px;
}

.content-section ul {
    list-style-type: disc;
    padding-left: 20px;
    margin-bottom: 15px;
}

.content-section ul li {
    margin-bottom: 8px;
    color: #495057;
    text-align: justify;
    line-height: 1.6;
    font-size: 14.5px;
}

.content-section .table-container {
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #dee2e6;
    margin-bottom: 25px;
}

.content-section .table {
    margin-bottom: 0;
    font-size: 14px;
}

.content-section .table th {
    background-color: #dee2e6;
    font-weight: bold;
    vertical-align: middle;
    border-bottom: 2px solid #dee2e6;
    text-align: center;
}

.content-section .table td {
    color: #495057;
    vertical-align: middle;
}

.content-section .table ul {
    margin-bottom: 0;
    padding-left: 15px;
}

.content-section .table ul li {
    font-size: 14px;
    margin-bottom: 5px;
}

.note-box {
    background-color: #f8f9fa;
    border-left: 4px solid #cb1c22;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.note-box p {
    margin-bottom: 5px;
}

.content-section.large-text h3 {
    font-size: 28px;
}

.content-section.large-text h4 {
    font-size: 20px;
}

.content-section.large-text h5 {
    font-size: 18px;
}

.content-section.large-text p,
.content-section.large-text ul li,
.content-section.large-text .table {
    font-size: 16px;
    line-height: 1.7;
}
</style>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách đổi trả</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'doi-tra'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách đổi trả</h3>

                <h4>I. QUY ĐỊNH CHUNG</h4>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 8%;">STT</th>
                                <th style="width: 25%;">Hạng mục</th>
                                <th>Nội dung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center fw-bold">1</td>
                                <td class="fw-bold">Đủ điều kiện đổi trả</td>
                                <td>
                                    Sản phẩm chưa sử dụng còn giữ nguyên 100% hình dạng ban đầu hoặc đã sử dụng, nhưng
                                    đảm bảo:
                                    <ul>
                                        <li>Màn hình không trầy xước</li>
                                        <li>Đủ điều kiện bảo hành theo chính sách của hãng, không có các tình trạng bất
                                            thường về chức năng và ngoại quan, ví dụ như: mất/chập chờn nguồn, treo đơ,
                                            cấn móp, sứt mẻ, nứt, vỡ, đọng nước/hơi ẩm, có mùi khét, …</li>
                                        <li>Tài khoản: Máy đã đã được đăng xuất khỏi tất cả các tài khoản như: iCloud,
                                            Google Account, Mi Account… </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center fw-bold">2</td>
                                <td class="fw-bold">Đủ điều kiện bảo hành</td>
                                <td>Sản phẩm đủ điều kiện bảo hành theo chính sách của Hãng công bố và được kết luận bởi
                                    nhà sản xuất hoặc trung tâm bảo hành chính hãng/đối tác uỷ quyền.</td>
                            </tr>
                            <tr>
                                <td class="text-center fw-bold">3</td>
                                <td class="fw-bold">Không đủ điều kiện bảo hành hãng</td>
                                <td>Sản phẩm nằm ngoài chính sách bảo hành được công bố bởi Hãng và được Trung tâm bảo
                                    hành chính hãng hoặc đối tác uỷ quyền kiểm tra, kết luận.</td>
                            </tr>
                            <tr>
                                <td class="text-center fw-bold">4</td>
                                <td class="fw-bold">Phí phát sinh trong quá trình đổi trả</td>
                                <td>
                                    FPT Shop sẽ kiểm tra tình trạng máy và thông báo đến KH về mức phí phải thu ngay tại
                                    cửa hàng. Bao gồm:
                                    <ul>
                                        <li>Phí khấu hao</li>
                                        <li>Phí vỏ hộp</li>
                                        <li>Phí phụ kiện</li>
                                        <li>Phí trầy xước</li>
                                        <li>Phí hóa đơn công ty nếu không có biên bản điều chỉnh (Đổi trả hàng trong 30
                                            ngày)</li>
                                        <li>Số tiền tương đương giá trị quà tặng khuyến mãi đi kèm nếu không được hoàn
                                            trả</li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="note-box">
                    <p class="fw-bold">Lưu ý</p>
                    <ul>
                        <li>Trường hợp sản phẩm có hạn bảo hành hãng trên 365 ngày, từ ngày thứ 366 FPT Shop hỗ trợ gửi
                            máy đi bảo hành và không áp dụng đổi trả theo nhu cầu hoặc bảo hành đổi mới tại FPT Shop.
                        </li>
                        <li>Đối với sản phẩm trả góp qua Nhà trả góp: Khách hàng phải thực hiện Hủy hợp đồng hoặc Tất
                            toán hợp đồng trả góp trước khi đổi trả sản phẩm tại FPT Shop.</li>
                        <li>Đối với phụ kiện ốp lưng, bao da mua kèm máy (Điện thoại di động/ Máy tính bảng): FPT Shop
                            hỗ trợ nhập trả lại phụ kiện trong trường hợp khách hàng trả hàng do lỗi NSX.</li>
                        <li>Miếng dán màn hình mua kèm máy: trong trường hợp khách hàng đổi máy do lỗi NSX, FPT Shop đổi
                            miếng dán mới cho Khách hàng.</li>
                        <li>Phụ kiện kèm máy/sản phẩm chính không áp dụng đổi trả, chỉ áp dụng bảo hành hãng (nếu có).
                        </li>
                    </ul>
                </div>

                <h4>II. CÁC CHÍNH SÁCH ĐỔI TRẢ</h4>

                <h5>2.1. Chính sách đổi trả sản phẩm ICT các hãng: Điện thoại, Máy tính bảng, Máy tính xách tay, PC đồng
                    bộ, PC AIO, Đồng hồ thông minh, Vòng đeo tay thông minh, Màn hình</h5>
                <p class="fw-bold">2.1.1. Sản phẩm mới</p>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Trường hợp</th>
                                <th style="width: 20%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th>Chính sách đổi trả</th>
                                <th style="width: 25%;">Phí khấu hao khi trả hàng (dựa trên giá trị sản phẩm trên đơn
                                    hàng)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Sản phẩm lỗi nhà sản xuất</td>
                                <td class="text-center">0 – 30 ngày</td>
                                <td>
                                    1 ĐỔI 1 sản phẩm chính (cùng model, cùng màu, cùng dung lượng)<br>
                                    Nếu sản phẩm đổi hết hàng, khách hàng có thể đổi sang một sản phẩm khác tương đương
                                    hoặc cao hơn về giá trị so với sản phẩm lỗi.<br><br>
                                    Khách hàng muốn trả sản phẩm: FPT Shop sẽ kiểm tra tình trạng máy và thông báo đến
                                    KH về giá trị thu lại sản phẩm theo quy định.
                                </td>
                                <td class="text-center fw-bold text-danger">
                                    0%<br><br><br><br>
                                    30% trong tháng đầu tiên, mỗi tháng tiếp theo tính thêm 5%/Tháng
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">31 – 365 ngày</td>
                                <td>
                                    GỬI MÁY ĐI BẢO HÀNH THEO QUY ĐỊNH CỦA HÃNG<br>
                                    Hoặc<br>
                                    KH muốn đổi sang sản phẩm khác hoặc trả sản phẩm: FPT Shop sẽ kiểm tra tình trạng
                                    máy và thông báo đến KH về giá trị thu lại sản phẩm theo quy định (áp dụng đổi/trả
                                    nhu cầu tính phí theo quy định).
                                </td>
                                <td class="text-center fw-bold text-danger">30% trong tháng đầu tiên, mỗi tháng tiếp
                                    theo tính thêm 5%/Tháng</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Đổi trả theo nhu cầu</td>
                                <td class="text-center">0 – 365 ngày</td>
                                <td>Khách hàng muốn đổi sang sản phẩm khác hoặc trả sản phẩm: FPT Shop sẽ kiểm tra tình
                                    trạng máy và thông báo đến Khách hàng về giá trị thu lại sản phẩm theo quy định.
                                </td>
                                <td class="text-center fw-bold text-danger">30% trong tháng đầu tiên, mỗi tháng tiếp
                                    theo tính thêm 5%/Tháng</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi do người dùng</td>
                                <td class="text-center">0 – 365 ngày</td>
                                <td colspan="2">FPT Shop hỗ trợ gửi máy đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="fw-bold mb-1">Phụ phí đổi trả khác nếu có (dựa trên giá trị sản phẩm trên đơn hàng)</p>
                <ul>
                    <li><strong>Phí trầy xước:</strong><br>
                        + Trầy xước mức độ 1 (Xước nhẹ, nhỏ (<=0,5cm), ít (<=2 điểm) tại vị trí khuất (đáy/cạnh laptop,
                            xước dăm viền điện thoại): 0%<br>
                            + Trầy xước mức độ 2 (Xước >0,5cm hoặc >=3 điểm tại vị trí khuất hoặc có xước tại vị trí dễ
                            nhìn thấy (bàn phím laptop, mặt lưng điện thoại, …): 10% (ngoại trừ phụ kiện, dịch vụ, Điện
                            máy, sản phẩm thiết bị dịch vụ)<br>
                            + Trầy xước mức độ 3 (Xước màn hình): Không áp dụng đổi trả
                    </li>
                    <li><strong>Phí vỏ hộp:</strong> 2%</li>
                    <li><strong>Phí phụ kiện:</strong> 5% mỗi món</li>
                    <li><strong>Phí hóa đơn công ty nếu không có biên bản điều chỉnh:</strong> 10% (Trả hàng trong 30
                        ngày).</li>
                    <li>Số tiền tương đương giá trị quà tặng khuyến mãi đi kèm nếu không được hoàn trả.</li>
                </ul>

                <p class="fw-bold mt-4">2.1.2. Sản phẩm cũ</p>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th>Chính sách đổi trả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Sản phẩm lỗi nhà sản xuất</td>
                                <td class="text-center">0 – 30 ngày</td>
                                <td>
                                    1 ĐỔI 1 sản phẩm chính tương đương<br>
                                    (cùng model, cùng dung lượng, cùng thời gian bảo hành)<br>
                                    Nếu không có sản phẩm tương đương, FPT Shop hoàn lại tiền 100% giá trị sản phẩm (áp
                                    dụng các phí khác nếu có tương tự máy mới).
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">Thời gian bảo hành còn lại</td>
                                <td>FPT Shop hỗ trợ gửi máy đi bảo hành theo chính sách hãng/FPT Shop.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Đổi trả theo nhu cầu</td>
                                <td class="text-center"></td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi do người dùng</td>
                                <td class="text-center"></td>
                                <td>FPT Shop hỗ trợ gửi máy đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="mt-4">2.2. Chính sách đổi trả thiết bị gia dụng</h5>
                <p class="fw-bold">2.2.1. Gia dụng có điện có chính sách bảo hành tại Trạm: bao gồm bếp gas (trừ
                    Kangaroo, Gold sun), Camera</p>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th>Chính sách đổi trả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Lỗi nhà sản xuất</td>
                                <td class="text-center">0 - 30 ngày</td>
                                <td>“Hư gì đổi nấy” - 1 ĐỔI 1 đối với bộ phận lỗi<br>(cùng model, cùng màu, cùng cấu
                                    hình)</td>
                            </tr>
                            <tr>
                                <td class="text-center">Từ ngày 31 đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ gửi sản phẩm đi bảo hành theo chính sách hãng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Đổi trả theo nhu cầu</td>
                                <td></td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi người dùng</td>
                                <td class="text-center">Đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ gửi sản phẩm đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="fw-bold">2.2.2. Gia dụng có điện có chính sách bảo hành tại Nhà; VD: máy lọc nước, quạt điều
                    hòa trừ Magic, Magic ECO; bếp ga Kangaroo, Goldsun; Bếp từ đa vùng nấu;…</p>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th>Chính sách đổi trả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Lỗi nhà sản xuất</td>
                                <td class="text-center">0 - 30 ngày</td>
                                <td>
                                    “Hư gì đổi nấy” - 1 ĐỔI 1 đối với bộ phận lỗi sau khi có xác nhận của Kỹ thuật viên
                                    kiểm tra tại nhà KH<br>
                                    Lưu ý: Không đổi vật tư tiêu hao (VD lõi lọc của máy lọc nước)
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">Từ ngày 31 đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ liên hệ hãng để bảo hành tại nhà cho khách.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Đổi trả theo nhu cầu</td>
                                <td></td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi người dùng</td>
                                <td class="text-center">Đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ gửi sản phẩm đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="mt-4">2.3. Chính sách đổi trả PC E-POWER, Linh kiện máy tính mua theo combo hoặc mua lẻ</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th>Chính sách đổi trả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Lỗi nhà sản xuất</td>
                                <td class="text-center">0 - 30 ngày</td>
                                <td>“Hư gì đổi nấy” - 1 ĐỔI 1 đối với linh kiện lỗi<br>(cùng cấu hình, chức năng)</td>
                            </tr>
                            <tr>
                                <td class="text-center">Từ ngày 31 đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ gửi máy đi bảo hành theo chính sách hãng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Đổi trả theo nhu cầu</td>
                                <td class="text-center">Từ ngày 0 đến khi hết hạn bảo hành</td>
                                <td>FPT Shop sẽ kiểm tra tình trạng sản phẩm và thông báo đến Khách hàng về giá trị thu
                                    lại sản phẩm ngay tại cửa hàng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi người dùng</td>
                                <td class="text-center">Đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ gửi sản phẩm đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>Phí khấu hao và phụ phí phát sinh tương tự như mục 2.1</p>

                <h5 class="mt-4">2.4. Chính sách đổi trả máy in</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th>Chính sách đổi trả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Lỗi nhà sản xuất</td>
                                <td class="text-center">0 - 30 ngày</td>
                                <td>
                                    1 ĐỔI 1 sản phẩm chính<br>
                                    (cùng model, không bao gồm hộp mực)<br>
                                    Nếu không còn vỏ hộp, FPT Shop chỉ tiếp nhận bảo hành, không áp dụng đổi.
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">Từ ngày 31 đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ gửi máy đi bảo hành theo chính sách hãng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Đổi trả theo nhu cầu</td>
                                <td></td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi người dùng</td>
                                <td class="text-center">Đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ gửi sản phẩm đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p><strong>Lưu ý:</strong><br>Phí đổi trả khác nếu có: FPT Shop sẽ kiểm tra tình trạng máy và thông báo
                    đến khách hàng về mức phí phải thu ngay tại cửa hàng</p>

                <h5 class="mt-4">2.5. Chính sách đổi trả máy chiếu</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th>Chính sách đổi trả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-center">Lỗi nhà sản xuất</td>
                                <td class="text-center">Từ ngày 0 đến khi hết hạn bảo hành</td>
                                <td>
                                    FPT Shop hỗ trợ gửi sản phẩm đi bảo hành theo chính sách hãng.<br>
                                    Đối với sản phẩm máy chiếu hãng Beecube: Hãng bảo hành đổi mới trong 12 tháng, bao
                                    gồm thân máy và phụ kiện (điều khiển, Adapter).
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Đổi trả theo nhu cầu</td>
                                <td></td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi người dùng</td>
                                <td></td>
                                <td>FPT Shop hỗ trợ gửi sản phẩm đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="mt-4">2.6. Chính sách đổi trả điện máy</h5>
                <p class="fw-bold">2.6.1. Chính sách đổi trả máy lạnh, điều hòa</p>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th style="width: 30%;">Chính sách đổi trả</th>
                                <th>Cách thức</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Lỗi nhà sản xuất</td>
                                <td class="text-center">Trong 30 ngày</td>
                                <td>
                                    <strong>Hãng Casper và Midea:</strong> FPT Shop Miễn phí 1 đổi 1 sản phẩm cùng model
                                    đối với sản phẩm có lỗi.<br>
                                    (Chỉ đổi sản phẩm chính, không đổi vỏ hộp, phụ kiện)<br><br>
                                    <strong>Các hãng còn lại:</strong> Hãng bảo hành theo chính sách tại nhà.
                                </td>
                                <td>
                                    KH liên hệ hotline Hãng hoặc hotline FPT Shop để kỹ thuật hãng kiểm tra tại nhà.<br>
                                    FPT Shop thực hiện 1 đổi 1 tại nhà cho KH khi KH cung cấp biên bản xác nhận lỗi của
                                    Hãng (Hiệu lực của biên bản trong vòng 7 ngày kể từ ngày lập).<br><br>
                                    KH liên hệ hotline hãng hoặc hotline FPT Shop để yêu cầu bảo hành/sửa chữa. Hãng sẽ
                                    điều kỹ thuật xuống kiểm tra và xử lý cho KH tại nhà.
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">Ngày thứ 31 – Hết thời gian bảo hành</td>
                                <td colspan="2">Hãng bảo hành theo chính sách tại nhà.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi do người dùng</td>
                                <td class="text-center">Toàn bộ thời gian</td>
                                <td colspan="2">Không áp dụng đổi trả. Hãng sửa chữa dịch vụ (có thu phí)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Nhu cầu (Không lỗi)</td>
                                <td class="text-center">Toàn bộ thời gian</td>
                                <td colspan="2">Không áp dụng đổi trả</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>Thời gian hãng cam kết bảo hành (trừ T7, CN, Lễ, Tết):<br>
                    Đến kiểm tra tại nhà KH: trong 48 đến 72 giờ làm việc kể từ khi nhận được yêu cầu.<br>
                    Bảo hành/sửa chữa: trong 5 ngày làm việc sau khi hoàn tất kiểm tra trực tiếp.</p>

                <p class="fw-bold mt-4">2.6.2. Chính sách đổi trả Tivi</p>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th>Chính sách đổi trả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Lỗi nhà sản xuất</td>
                                <td class="text-center">0 - 30 ngày</td>
                                <td>
                                    1 ĐỔI 1 sản phẩm chính<br>
                                    (cùng model, cùng cấu hình)<br>
                                    Nếu sản phẩm đổi hết hàng, khách hàng có thể đổi sang một sản phẩm khác tương đương
                                    hoặc cao hơn về giá trị so với sản phẩm lỗi.
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">Từ ngày 31 đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ điều động kỹ thuật hãng tới bảo hành tại nhà khách.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Đổi trả theo nhu cầu</td>
                                <td></td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi người dùng</td>
                                <td class="text-center">Đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ điều động kỹ thuật hãng tới sửa chữa tại nhà, khách hàng trả phí
                                    sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>Thời gian hãng cam kết bảo hành (trừ T7, CN, Lễ, Tết):<br>
                    Đến kiểm tra tại nhà KH: trong 48 đến 72 giờ làm việc kể từ khi nhận được yêu cầu.<br>
                    Bảo hành/sửa chữa: trong 5 ngày làm việc sau khi hoàn tất kiểm tra trực tiếp.</p>

                <p class="fw-bold mt-4">2.6.3. Chính sách đổi trả Tủ lạnh, tủ đông, tủ mát.</p>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(từ khi xuất hóa đơn)</th>
                                <th style="width: 25%;">Chính sách</th>
                                <th>Cách thức</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Lỗi NSX</td>
                                <td class="text-center">Trong 30 ngày</td>
                                <td>
                                    <strong>Hãng Casper</strong><br>
                                    FPT Shop miễn phí 1 đổi 1 sản phẩm cùng model đối với sản phẩm có lỗi.<br>
                                    Chỉ đổi sản phẩm chính, không đổi vỏ hộp và phụ kiện<br><br>
                                    <strong>Các hãng còn lại</strong><br>
                                    Không áp dụng 1 đổi 1<br>
                                    Hãng bảo hành theo chính sách tại nhà
                                </td>
                                <td>
                                    KH liên hệ hotline Hãng hoặc hotline FPT Shop để kỹ thuật hãng kiểm tra tại nhà.<br>
                                    FPT Shop thực hiện 1 đổi 1 tại nhà cho KH khi KH cung cấp biên bản xác nhận lỗi của
                                    Hãng (Hiệu lực của biên bản trong vòng 7 ngày kể ngày lập)<br><br>
                                    KH liên hệ hotline Hãng hoặc hotline FPT Shop để yêu cầu bảo hành/ sửa chữa. Hãng sẽ
                                    điều kỹ thuật xuống kiểm tra và xử lý cho KH tại nhà.
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">Ngày thứ 31 – Hết thời gian bảo hành</td>
                                <td colspan="2">Hãng bảo hành theo chính sách tại nhà</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi do người dùng</td>
                                <td class="text-center">Toàn bộ thời gian</td>
                                <td colspan="2">Hãng sửa chữa dịch vụ (có thu phí)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Nhu cầu (Không lỗi)</td>
                                <td class="text-center">Toàn bộ thời gian</td>
                                <td colspan="2">Không áp dụng đổi trả</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>Thời gian hãng cam kết bảo hành (trừ T7, CN, Lễ, Tết):<br>
                    Đến kiểm tra tại nhà KH: trong 48 đến 72 giờ làm việc kể từ khi nhận được yêu cầu.<br>
                    Bảo hành/sửa chữa: trong 5 ngày làm việc sau khi hoàn tất kiểm tra trực tiếp.</p>

                <p class="fw-bold mt-4">2.6.4. Chính sách đổi trả máy giặt – máy sấy</p>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Trường hợp</th>
                                <th style="width: 25%;">Thời gian<br>(từ khi xuất hóa đơn)</th>
                                <th style="width: 25%;">Chính sách</th>
                                <th>Cách thức</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold text-center">Lỗi NSX</td>
                                <td class="text-center">Trong 30 ngày</td>
                                <td>
                                    <strong>Hãng Casper</strong><br>
                                    FPT Shop miễn phí 1 đổi 1 sản phẩm cùng model đối với sản phẩm có lỗi<br>
                                    Chỉ đổi sản phẩm chính, không đổi vỏ hộp, phụ kiện<br><br>
                                    <strong>Các hãng còn lại</strong><br>
                                    Không áp dụng 1 đổi 1<br>
                                    Hãng bảo hành theo chính sách tại nhà
                                </td>
                                <td>
                                    KH liên hệ hotline Hãng hoặc hotline FPT Shop để kỹ thuật hãng kiểm tra tại nhà.<br>
                                    FPT Shop thực hiện 1 đổi 1 tại nhà cho KH khi KH cung cấp biên bản xác nhận lỗi của
                                    Hãng (Hiệu lực của biên bản trong vòng 7 ngày kể ngày lập)<br><br>
                                    KH liên hệ hotline Hãng hoặc hotline FPT Shop để yêu cầu bảo hành/ sửa chữa. Hãng sẽ
                                    điều kỹ thuật xuống kiểm tra và xử lý cho KH tại nhà.
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">Ngày thứ 31 – Hết thời gian bảo hành</td>
                                <td colspan="2">Hãng bảo hành theo chính sách tại nhà</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Lỗi do người dùng</td>
                                <td class="text-center">Toàn bộ thời gian</td>
                                <td colspan="2">Hãng sửa chữa dịch vụ (có thu phí)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-center">Nhu cầu (Không lỗi)</td>
                                <td class="text-center">Toàn bộ thời gian</td>
                                <td colspan="2">Không áp dụng đổi trả</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>Thời gian hãng cam kết bảo hành (SLA hãng; trừ thứ 7, Chủ nhật, Lễ, Tết):<br>
                    Đến kiểm tra tại nhà KH: trong 48 đến 72 giờ làm việc kể từ khi nhận được yêu cầu.<br>
                    Bảo hành/sửa chữa: trong 5 ngày làm việc sau khi hoàn tất kiểm tra trực tiếp.</p>

                <h5 class="mt-4">2.7. Chính sách đổi trả phụ kiện</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Loại sản phẩm</th>
                                <th style="width: 20%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th style="width: 20%;">Trường hợp</th>
                                <th>Nội dung chính sách</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="fw-bold">Thiết bị mạng, Ổ cứng di động, USB, Thẻ nhớ, Sạc, Cáp,
                                    Sạc dự phòng, Chuột, Phụ kiện laptop, Tai nghe, Loa, thiết bị Giải trí (trừ hãng
                                    Marshall, JBL, Harman kardon, SONY), Combo phụ kiện, Sạc, Tai nghe có dây, đế sạc
                                    không dây, Sạc dự phòng thuộc Samsung</td>
                                <td class="text-center">0 - 365 ngày</td>
                                <td class="text-center">Lỗi nhà sản xuất</td>
                                <td>
                                    1 ĐỔI 1 đối với thiết bị lỗi<br>
                                    (cùng mã hoặc cùng nhóm hàng nếu hết hàng cùng mã).<br>
                                    Trường hợp hết mã cùng loại hoặc cùng nhóm hàng để đổi hoặc hết hàng FPT Shop hỗ trợ
                                    đổi sang mã khác theo nhu cầu của KH.
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">0 - 365 ngày</td>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="fw-bold">Tai nghe bluetooth Buds2, Buds2 Pro, Buds live</td>
                                <td rowspan="2" class="text-center">Đến khi hết hạn bảo hành</td>
                                <td class="text-center">Lỗi nhà sản xuất</td>
                                <td>Không đổi trả, FPT Shop hỗ trợ gửi máy đi bảo hành theo chính sách hãng.</td>
                            </tr>
                            <tr>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>Không áp dụng đổi trả.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Máy chơi game Sony PlayStation; PKNK (trừ bao da, ốp lưng, dây đeo
                                    đồng hồ); Tai nghe, loa thuộc hãng Marshall, JBL, Harman Kardon, SONY</td>
                                <td class="text-center">Đến khi hết hạn bảo hành</td>
                                <td class="text-center">Lỗi nhà sản xuất</td>
                                <td>Không đổi trả, FPT Shop hỗ trợ gửi máy đi bảo hành theo chính sách hãng.</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="fw-bold">Phụ kiện cũ Apple, Samsung</td>
                                <td rowspan="2" class="text-center">0 - 180 ngày</td>
                                <td class="text-center">Lỗi nhà sản xuất</td>
                                <td>Không đổi trả, FPT Shop hỗ trợ gửi máy đi bảo hành theo chính sách hãng.</td>
                            </tr>
                            <tr>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>Không áp dụng đổi trả.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Ốp lưng, Bao da, balo,túi xách, túi chống sốc, túi chống nước, gậy
                                    chụp hình, tay cầm chơi game, dây đồng hồ, Miếng dán màn hình lẻ và combo miếng dán
                                    2 mặt (dành cho ĐTDĐ)</td>
                                <td class="text-center">Kể từ khi xuất hoá đơn</td>
                                <td class="text-center">Lỗi nhà sản xuất/Lỗi do Người dùng/Đổi trả theo nhu cầu</td>
                                <td>Không bảo hành, đổi trả</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">MDMH lẻ và combo miếng dán 2 mặt (dành cho MTB, MTXT), MDMH Kính
                                    cường lực</td>
                                <td class="text-center">Kể từ lần mua dán thứ 2</td>
                                <td class="text-center">Theo nhu cầu KH</td>
                                <td>Giảm 30%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="mt-4">2.8. Chính sách đổi trả dịch vụ</h5>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Loại sản phẩm</th>
                                <th style="width: 20%;">Thời gian<br>(tính từ ngày xuất hoá đơn)</th>
                                <th style="width: 20%;">Trường hợp</th>
                                <th>Nội dung chính sách</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="4" class="fw-bold">FPT Play box</td>
                                <td class="text-center">0 - 30 ngày</td>
                                <td class="text-center">Lỗi nhà sản xuất</td>
                                <td>1 ĐỔI 1 sản phẩm chính (cùng model)</td>
                            </tr>
                            <tr>
                                <td class="text-center">31 - 365 ngày</td>
                                <td class="text-center">Lỗi nhà sản xuất</td>
                                <td>FPT Shop hỗ trợ gửi máy đi bảo hành theo chính sách hãng.</td>
                            </tr>
                            <tr>
                                <td class="text-center">0 - 365 ngày</td>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td class="text-center">0 - 365 ngày</td>
                                <td class="text-center">Lỗi do người dùng</td>
                                <td>FPT Shop hỗ trợ gửi máy đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="fw-bold">Phần mềm dịch vụ trừ Microsoft</td>
                                <td class="text-center">0 - 30 ngày</td>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>FPT Shop hoàn lại tiền 100% nếu KH không còn nhu cầu sử dụng</td>
                            </tr>
                            <tr>
                                <td class="text-center">31 - ngày Gói dịch vụ hết hiệu lực</td>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>Không áp dụng đổi trả hàng</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Khoá học</td>
                                <td class="text-center">0 - 24 tiếng</td>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>Không áp dụng đổi trả hàng</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="fw-bold">Thiết bị mạng</td>
                                <td class="text-center">0 - 365 ngày</td>
                                <td class="text-center">Lỗi nhà sản xuất</td>
                                <td>
                                    1 ĐỔI 1 đối với thiết bị lỗi<br>
                                    (cùng mã hoặc cùng nhóm hàng nếu hết hàng cùng mã)<br>
                                    Trường hợp hết mã cùng loại hoặc cùng nhóm hàng để đổi hoặc hết hàng FPT Shop hỗ trợ
                                    đổi sang mã khác theo nhu cầu của KH
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">0 - 365 ngày</td>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Các gói bảo hành Bolttech:<br>- Bảo hành mở rộng<br>- Bảo hành đặc
                                    quyền đổi mới 12 tháng<br>- Bảo hành VIP 1 đổi 1<br>- Bảo hành rơi vỡ vào nước<br>-
                                    Bảo hành đặc quyền thay pin trong 3 năm</td>
                                <td class="text-center">0 - Ngày gói bảo hành hết hiệu lực</td>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>Không áp dụng đổi trả gói dịch vụ</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="fw-bold">Gói bảo hành hãng:<br>- Bảo hành Samsung Care Plus</td>
                                <td class="text-center">0 – 30 ngày</td>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>FPT Shop hỗ trợ trả gói bảo hành nếu KH chưa sử dụng gói</td>
                            </tr>
                            <tr>
                                <td class="text-center">31 - ngày gói bảo hành hết hiệu lực</td>
                                <td class="text-center">Đổi trả theo nhu cầu</td>
                                <td>Không áp dụng đổi trả hàng</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>Phí đổi trả FPT Play box nếu có:<br>
                    - Mất vỏ hộp thu phí 2% giá trên hóa đơn<br>
                    - Mất phụ kiện đi kèm thu phí 5% trên giá hoá đơn cho mỗi phụ kiện mất.</p>

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