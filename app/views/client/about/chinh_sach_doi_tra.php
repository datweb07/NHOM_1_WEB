<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .support-sidebar .list-group-item {
        border: none;
        padding: 12px 15px;
        font-size: 14.5px;
        color: #333;
        border-radius: 6px !important;
        margin-bottom: 4px;
        background-color: transparent;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    .support-sidebar .list-group-item:hover {
        color: #cb1c22;
        background-color: #f8f9fa;
    }
    .support-sidebar .list-group-item.active {
        background-color: #fce8e9;
        color: #cb1c22;
        border-left: 4px solid #cb1c22;
        border-radius: 4px 6px 6px 4px !important;
    }
    .support-sidebar .menu-header {
        background-color: #f8f9fa;
        color: #212529;
        font-weight: bold;
        text-transform: uppercase;
        border-radius: 6px !important;
    }
    .content-section h3 {
        color: #212529;
        margin-bottom: 1.5rem;
    }
    .content-section h5 {
        font-weight: bold;
        margin-top: 1.8rem;
        margin-bottom: 1rem;
        font-size: 1.15rem;
        color: #cb1c22;
        text-transform: uppercase;
    }
    .content-section h6 {
        font-weight: bold;
        margin-top: 1.5rem;
        margin-bottom: 0.8rem;
        font-size: 1rem;
        color: #212529;
    }
    .content-section p {
        text-align: justify;
        color: #495057;
        line-height: 1.6;
        margin-bottom: 1rem;
        font-size: 15px;
    }
    .content-section ul {
        padding-left: 1.2rem;
        margin-bottom: 1.5rem;
    }
    .content-section ul li {
        margin-bottom: 0.6rem;
        color: #495057;
        text-align: justify;
        line-height: 1.6;
        font-size: 15px;
    }
    .policy-table th {
        background-color: #f8f9fa;
        text-align: center;
        vertical-align: middle;
        font-size: 14.5px;
    }
    .policy-table td {
        vertical-align: top;
        font-size: 14.5px;
        color: #495057;
    }
    .breadcrumb-item a {
        color: #cb1c22;
        text-decoration: none;
    }
</style>

<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#">Hỗ trợ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách đổi trả</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Menu bên trái -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="list-group support-sidebar sticky-top" style="top: 20px;">
                <div class="list-group-item menu-header disabled mb-2">Danh mục chính sách</div>
                <a href="#" class="list-group-item list-group-item-action">Câu hỏi thường gặp</a>
                <a href="/gioi-thieu" class="list-group-item list-group-item-action">Giới thiệu về FPT Shop</a>
                <a href="#" class="list-group-item list-group-item-action">Đại lý uỷ quyền và TTBH uỷ quyền của Apple</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách mạng di động FPT</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách gói cước di động FPT</a>
                <a href="#" class="list-group-item list-group-item-action">Danh sách điểm cung cấp dịch vụ viễn thông FPT</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách giao hàng & lắp đặt</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách giao hàng & lắp đặt Điện máy, Gia dụng</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách giao hàng & lắp đặt Điện máy chỉ bán online</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách Chương trình Khách hàng thân thiết tại FPT Shop</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách khui hộp sản phẩm</a>
                <a href="#" class="list-group-item list-group-item-action">Hướng dẫn mua hàng và thanh toán online</a>
                <a href="/gioi-thieu-may-doi-tra" class="list-group-item list-group-item-action">Giới thiệu máy đổi trả</a>
                <a href="#" class="list-group-item list-group-item-action active" aria-current="true">Chính sách đổi trả</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách bảo mật dữ liệu cá nhân khách hàng</a>
                <a href="/quy-che-hoat-dong" class="list-group-item list-group-item-action">Quy chế hoạt động</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách bảo mật</a>
                <a href="#" class="list-group-item list-group-item-action">Quy định hỗ trợ kỹ thuật và sao lưu dữ liệu</a>
                <a href="/chinh-sach-bao-hanh" class="list-group-item list-group-item-action">Chính sách bảo hành</a>
                <a href="#" class="list-group-item list-group-item-action">Chính sách trả góp</a>
            </div>
        </div>

        <!-- Nội dung chính bên phải -->
        <div class="col-lg-9 col-md-8">
            <div class="content-section bg-white p-4 rounded shadow-sm border">
                <h3 class="fw-bold mb-4 text-center">Chính sách đổi trả</h3>
                
                <h5>I. QUY ĐỊNH CHUNG</h5>
                <div class="table-responsive">
                    <table class="table table-bordered policy-table">
                        <thead>
                            <tr>
                                <th width="10%">STT</th>
                                <th width="25%">Hạng mục</th>
                                <th width="65%">Nội dung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td><strong>Đủ điều kiện đổi trả</strong></td>
                                <td>
                                    Sản phẩm chưa sử dụng còn giữ nguyên 100% hình dạng ban đầu hoặc đã sử dụng, nhưng đảm bảo:
                                    <ul class="mb-0 mt-2">
                                        <li>Màn hình không trầy xước</li>
                                        <li>Đủ điều kiện bảo hành theo chính sách của hãng, không có các tình trạng bất thường về chức năng và ngoại quan, ví dụ như: mất/chập chờn nguồn, treo đơ, cấn móp, sứt mẻ, nứt, vỡ, đọng nước/hơi ẩm, có mùi khét, …</li>
                                        <li>Tài khoản: Máy đã đã được đăng xuất khỏi tất cả các tài khoản như: iCloud, Google Account, Mi Account…</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td><strong>Đủ điều kiện bảo hành</strong></td>
                                <td>Sản phẩm đủ điều kiện bảo hành theo chính sách của Hãng công bố và được kết luận bởi nhà sản xuất hoặc trung tâm bảo hành chính hãng/đối tác uỷ quyền.</td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td><strong>Không đủ điều kiện bảo hành hãng</strong></td>
                                <td>Sản phẩm nằm ngoài chính sách bảo hành được công bố bởi Hãng và được Trung tâm bảo hành chính hãng hoặc đối tác uỷ quyền kiểm tra, kết luận.</td>
                            </tr>
                            <tr>
                                <td class="text-center">4</td>
                                <td><strong>Phí phát sinh trong quá trình đổi trả</strong></td>
                                <td>
                                    FPT Shop sẽ kiểm tra tình trạng máy và thông báo đến KH về mức phí phải thu ngay tại cửa hàng. Bao gồm:
                                    <ul class="mb-0 mt-2">
                                        <li>Phí khấu hao</li>
                                        <li>Phí vỏ hộp</li>
                                        <li>Phí phụ kiện</li>
                                        <li>Phí trầy xước</li>
                                        <li>Phí hóa đơn công ty nếu không có biên bản điều chỉnh (Đổi trả hàng trong 30 ngày)</li>
                                        <li>Số tiền tương đương giá trị quà tặng khuyến mãi đi kèm nếu không được hoàn trả</li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="fw-bold text-dark mt-4 mb-2">Lưu ý:</p>
                <ul>
                    <li>Trường hợp sản phẩm có hạn bảo hành hãng trên 365 ngày, từ ngày thứ 366 FPT Shop hỗ trợ gửi máy đi bảo hành và không áp dụng đổi trả theo nhu cầu hoặc bảo hành đổi mới tại FPT Shop.</li>
                    <li>Đối với sản phẩm trả góp qua Nhà trả góp: Khách hàng phải thực hiện Hủy hợp đồng hoặc Tất toán hợp đồng trả góp trước khi đổi trả sản phẩm tại FPT Shop.</li>
                    <li>Đối với phụ kiện ốp lưng, bao da mua kèm máy (Điện thoại di động/ Máy tính bảng): FPT Shop hỗ trợ nhập trả lại phụ kiện trong trường hợp khách hàng trả hàng do lỗi NSX.</li>
                    <li>Miếng dán màn hình mua kèm máy: trong trường hợp khách hàng đổi máy do lỗi NSX, FPT Shop đổi miếng dán mới cho Khách hàng.</li>
                    <li>Phụ kiện kèm máy/sản phẩm chính không áp dụng đổi trả, chỉ áp dụng bảo hành hãng (nếu có).</li>
                </ul>

                <h5>II. CÁC CHÍNH SÁCH ĐỔI TRẢ</h5>
                
                <h6>2.1. Chính sách đổi trả sản phẩm ICT các hãng: Điện thoại, Máy tính bảng, Máy tính xách tay, PC đồng bộ, PC AIO, Đồng hồ thông minh, Vòng đeo tay thông minh, Màn hình</h6>
                
                <p class="fw-bold mb-2">2.1.1. Sản phẩm mới</p>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered policy-table">
                        <thead>
                            <tr>
                                <th width="20%">Trường hợp</th>
                                <th width="20%">Thời gian<br><small class="fw-normal">(tính từ ngày xuất hoá đơn)</small></th>
                                <th width="35%">Chính sách đổi trả</th>
                                <th width="25%">Phí khấu hao khi trả hàng<br><small class="fw-normal">(dựa trên giá trị sản phẩm trên đơn hàng)</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2"><strong>Sản phẩm lỗi nhà sản xuất</strong></td>
                                <td>0 – 30 ngày</td>
                                <td>
                                    <p class="mb-2"><strong class="text-danger">1 ĐỔI 1</strong> sản phẩm chính (cùng model, cùng màu, cùng dung lượng).<br>
                                    Nếu sản phẩm đổi hết hàng, khách hàng có thể đổi sang một sản phẩm khác tương đương hoặc cao hơn về giá trị so với sản phẩm lỗi.</p>
                                    <p class="mb-0 border-top pt-2">Khách hàng muốn trả sản phẩm: FPT Shop sẽ kiểm tra tình trạng máy và thông báo đến KH về giá trị thu lại sản phẩm theo quy định.</p>
                                </td>
                                <td>
                                    <p class="mb-2">0% (Khi đổi sản phẩm)</p>
                                    <p class="mb-0 border-top pt-2">30% trong tháng đầu tiên, mỗi tháng tiếp theo tính thêm 5%/Tháng</p>
                                </td>
                            </tr>
                            <tr>
                                <td>31 – 365 ngày</td>
                                <td>
                                    <strong>GỬI MÁY ĐI BẢO HÀNH THEO QUY ĐỊNH CỦA HÃNG</strong><br>
                                    Hoặc<br>
                                    KH muốn đổi sang sản phẩm khác hoặc trả sản phẩm: FPT Shop sẽ kiểm tra tình trạng máy và thông báo đến KH về giá trị thu lại sản phẩm theo quy định (áp dụng đổi/trả nhu cầu tính phí theo quy định).
                                </td>
                                <td>30% trong tháng đầu tiên, mỗi tháng tiếp theo tính thêm 5%/Tháng</td>
                            </tr>
                            <tr>
                                <td><strong>Đổi trả theo nhu cầu</strong></td>
                                <td>0 – 365 ngày</td>
                                <td>Khách hàng muốn đổi sang sản phẩm khác hoặc trả sản phẩm: FPT Shop sẽ kiểm tra tình trạng máy và thông báo đến Khách hàng về giá trị thu lại sản phẩm theo quy định.</td>
                                <td>30% trong tháng đầu tiên, mỗi tháng tiếp theo tính thêm 5%/Tháng</td>
                            </tr>
                            <tr>
                                <td><strong>Lỗi do người dùng</strong></td>
                                <td>0 – 365 ngày</td>
                                <td colspan="2">FPT Shop hỗ trợ gửi máy đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-light p-3 rounded mb-4 border">
                    <p class="fw-bold text-dark mb-2">Phụ phí đổi trả khác nếu có (dựa trên giá trị sản phẩm trên đơn hàng):</p>
                    <ul class="mb-0">
                        <li><strong>Phí trầy xước:</strong> 
                            <ul class="mt-1 mb-2">
                                <li>Trầy xước mức độ 1 (Xước nhẹ, nhỏ (&lt;=0,5cm), ít (&lt;=2 điểm) tại vị trí khuất (đáy/cạnh laptop, xước dăm viền điện thoại)): 0%</li>
                                <li>Trầy xước mức độ 2 (Xước &gt;0,5cm hoặc &gt;=3 điểm tại vị trí khuất hoặc có xước tại vị trí dễ nhìn thấy (bàn phím laptop, mặt lưng điện thoại, …)): 10% (ngoại trừ phụ kiện, dịch vụ, Điện máy, sản phẩm thiết bị dịch vụ)</li>
                                <li>Trầy xước mức độ 3 (Xước màn hình): Không áp dụng đổi trả</li>
                            </ul>
                        </li>
                        <li><strong>Phí vỏ hộp:</strong> 2%</li>
                        <li><strong>Phí phụ kiện:</strong> 5% mỗi món</li>
                        <li><strong>Phí hóa đơn công ty</strong> nếu không có biên bản điều chỉnh: 10% (Trả hàng trong 30 ngày).</li>
                        <li>Số tiền tương đương giá trị quà tặng khuyến mãi đi kèm nếu không được hoàn trả.</li>
                    </ul>
                </div>

                <p class="fw-bold mb-2 mt-4">2.1.2. Sản phẩm cũ</p>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered policy-table">
                        <thead>
                            <tr>
                                <th width="25%">Trường hợp</th>
                                <th width="25%">Thời gian<br><small class="fw-normal">(tính từ ngày xuất hoá đơn)</small></th>
                                <th width="50%">Chính sách đổi trả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2"><strong>Sản phẩm lỗi nhà sản xuất</strong></td>
                                <td>0 – 30 ngày</td>
                                <td><strong class="text-danger">1 ĐỔI 1</strong> sản phẩm chính tương đương (cùng model, cùng dung lượng, cùng thời gian bảo hành).<br>Nếu không có sản phẩm tương đương, FPT Shop hoàn lại tiền 100% giá trị sản phẩm (áp dụng các phí khác nếu có tương tự máy mới).</td>
                            </tr>
                            <tr>
                                <td>Thời gian bảo hành còn lại</td>
                                <td>FPT Shop hỗ trợ gửi máy đi bảo hành theo chính sách hãng/FPT Shop.</td>
                            </tr>
                            <tr>
                                <td><strong>Đổi trả theo nhu cầu</strong></td>
                                <td>—</td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td><strong>Lỗi do người dùng</strong></td>
                                <td>—</td>
                                <td>FPT Shop hỗ trợ gửi máy đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6>2.2. Chính sách đổi trả thiết bị gia dụng</h6>
                <p class="fw-bold mb-2">2.2.1. Gia dụng có điện có chính sách bảo hành tại Trạm: bao gồm bếp gas (trừ Kangaroo, Gold sun), Camera</p>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered policy-table">
                        <thead>
                            <tr>
                                <th width="25%">Trường hợp</th>
                                <th width="25%">Thời gian<br><small class="fw-normal">(tính từ ngày xuất hoá đơn)</small></th>
                                <th width="50%">Chính sách đổi trả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2"><strong>Lỗi nhà sản xuất</strong></td>
                                <td>0 - 30 ngày</td>
                                <td>“Hư gì đổi nấy” - <strong class="text-danger">1 ĐỔI 1</strong> đối với bộ phận lỗi (cùng model, cùng màu, cùng cấu hình)</td>
                            </tr>
                            <tr>
                                <td>Từ ngày 31 đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ gửi sản phẩm đi bảo hành theo chính sách hãng.</td>
                            </tr>
                            <tr>
                                <td><strong>Đổi trả theo nhu cầu</strong></td>
                                <td>—</td>
                                <td>Không áp dụng đổi trả hàng.</td>
                            </tr>
                            <tr>
                                <td><strong>Lỗi người dùng</strong></td>
                                <td>Đến khi hết hạn bảo hành</td>
                                <td>FPT Shop hỗ trợ gửi sản phẩm đi sửa chữa, khách hàng trả phí sửa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>