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

    .content-section h4 {
        font-weight: bold;
        font-size: 18px;
        margin-top: 30px;
        margin-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 8px;
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
        padding-left: 20px;
    }

    .content-section ul li::before {
        content: "-";
        color: #cb1c22;
        font-weight: bold;
        position: absolute;
        left: 0;
        top: 0;
    }

    .content-section strong {
        color: #212529;
    }

    .content-section.large-text h3 { font-size: 28px; }
    .content-section.large-text h4 { font-size: 20px; }
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
            <li class="breadcrumb-item active" aria-current="page">Chính sách bảo mật dữ liệu cá nhân khách hàng</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <?php 
            $active_page = 'bao-mat-du-lieu'; 
            require_once dirname(__DIR__) . '/layouts/sidebar_about.php'; 
            ?>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="content-section" id="policy-content">
                <h3>Chính sách bảo mật dữ liệu cá nhân khách hàng</h3>

                <p>Chính sách bảo mật dữ liệu cá nhân khách hàng (“Chính sách”) này được thực hiện bởi Công ty Cổ phần Bán lẻ Kỹ thuật số FRT (“FRT”, “Công ty”), mô tả các hoạt động liên quan đến việc xử lý dữ liệu cá nhân của Khách hàng để Khách hàng hiểu rõ hơn về mục đích, phạm vi thông tin mà FRT xử lý, các biện pháp FRT áp dụng để bảo vệ thông tin và quyền của Quý Khách hàng đối với các hoạt động này.</p>
                <p>Chính sách này là một phần không thể tách rời của các hợp đồng, thỏa thuận, điều khoản và điều kiện ràng buộc mối quan hệ giữa FRT và Khách hàng.</p>

                <h4>Điều 1. Đối tượng và phạm vi áp dụng</h4>
                <p><strong>1.1.</strong> Chính sách này điều chỉnh cách thức mà FRT xử lý dữ liệu cá nhân của Khách hàng và những người có liên quan đến Khách hàng theo các mối quan hệ do pháp luật yêu cầu phải xử lý dữ liệu hoặc người đồng sử dụng các sản phẩm/ dịch vụ của FRT với khách hàng khi sử dụng hoặc tương tác với trang tin điện tử hoặc/và các sản phẩm/ dịch vụ của FRT.</p>
                <p><strong>1.2.</strong> Để tránh nhầm lẫn, Chính sách này chỉ áp dụng cho các Khách hàng cá nhân. FRT khuyến khích Khách hàng đọc kỹ Chính sách này và thường xuyên kiểm tra trang tin điện tử để cập nhật bất kỳ thay đổi nào mà FRT có thể thực hiện theo các điều khoản của Chính sách.</p>

                <h4>Điều 2. Giải thích từ ngữ</h4>
                <p><strong>2.1.</strong> “Khách hàng” là cá nhân tiếp cận, tìm hiểu, đăng ký, sử dụng hoặc có liên quan trong quy trình hoạt động, cung cấp các sản phẩm, dịch vụ của FRT.</p>
                <p><strong>2.2.</strong> “FRT” là Công ty Cổ phần Bán lẻ Kỹ thuật số FPT, mã số thuế 0311609355, địa chỉ trụ sở chính: 261 - 263 Khánh Hội, Phường Vĩnh Hội, TP. Hồ Chí Minh, Việt Nam.</p>
                <p><strong>2.3.</strong> “Dữ liệu cá nhân” hay “DLCN” là dữ liệu dưới dạng số hoặc thông tin dưới dạng khác xác định hoặc giúp xác định một con người cụ thể, bao gồm dữ liệu cá nhân cơ bản và dữ liệu cá nhân nhạy cảm. Dữ liệu cá nhân sau khi khử nhận dạng không còn là dữ liệu cá nhân.</p>
                <p><strong>2.4.</strong> Tập đoàn FPT gồm Công ty CP FPT và các Công ty con, Công ty liên kết của Công ty CP FPT được công bố tại website fpt.com.</p>
                <p><strong>2.5.</strong> Long Châu là Công ty CP Dược phẩm FPT Long Châu, là Công ty do FRT nắm quyền chi phối.</p>
                <p><strong>2.6.</strong> Bên thứ ba là tổ chức, cá nhân khác ngoài FRT và Khách hàng đã được giải thích theo Chính sách này.</p>
                <p><strong>2.7.</strong> Kênh giao dịch FRT: gồm các kênh giao dịch điện tử (website https://fptshop.com.vn/; ứng dụng FPT Shop; zalo; …) hoặc các kênh giao dịch khác nhằm cung cấp sản phẩm/ dịch vụ hoặc để phục vụ nhu cầu của khách hàng.</p>
                <p><strong>2.8.</strong> Các khái niệm “dữ liệu cá nhân cơ bản”, ”dữ liệu cá nhân nhạy cảm”, “bảo vệ dữ liệu cá nhân”, “xử lý dữ liệu cá nhân” được đề cập trong chính sách này được hiểu theo cách giải thích tại Luật bảo vệ dữ liệu cá nhân số 91/2025/QH15, ngày 26 tháng 6 năm 2025 (Sau đây gọi tắt là Luật BVDLCN) và Nghị định 356/2025/NĐ-CP Quy định chi tiết một số điều và biện pháp thi hành Luật BVDLCN.</p>

                <h4>Điều 3. Mục đích xử lý dữ liệu cá nhân của Khách hàng</h4>
                <p>Trong phạm vi Khách hàng cho phép và/hoặc trong phạm vi pháp luật yêu cầu hoặc cho phép, FRT có thể sử dụng DLCN của Khách hàng cho một hoặc nhiều mục đích dưới đây:</p>
                <p><strong>3.1.</strong> Cho mục đích mua hàng và sử dụng dịch vụ do FRT cung cấp hoặc hợp tác với Đối tác cung cấp cho Khách hàng. Ở mục đích này, việc cung cấp thông tin của Khách hàng là cần thiết để giúp khách hàng mua hàng và sử dụng dịch vụ, chúng tôi sẽ không thể đáp ứng được các yêu cầu của Khách hàng nếu như các thông tin mà chúng tôi đề nghị Khách hàng cung cấp không được thực hiện một cách đầy đủ. Trong một số trường hợp, thông tin cá nhân của Khách hàng mà chúng tôi thu thập cũng nhằm đảm bảo đáp ứng quy định pháp luật liên quan.<br>
                Trong trường hợp Khách hàng đăng ký mở tài khoản khách hàng hoặc cài đặt và sử dụng ứng dụng (app) được phát triển bởi Công ty chúng tôi, thông tin cá nhân Khách hàng cung cấp cũng được sử dụng để (i) Điều chỉnh, cập nhật, bảo mật và cải tiến các sản phẩm, dịch vụ, ứng dụng, thiết bị mà FRT đang cung cấp cho Khách hàng; (ii) Xác minh danh tính và đảm bảo tính bảo mật thông tin cá nhân của Khách hàng; (iii) Ngặn chặn và phòng chống gian lận, đánh cắp danh tính và các hoạt động bất hợp pháp khác; (iv) Để có cơ sở thiết lập, thực thi các quyền hợp pháp hoặc bảo vệ các khiếu nại pháp lý của Khách hàng hoặc bất kỳ cá nhân nào. Các mục đích này có thể bao gồm việc trao đổi dữ liệu với các công ty và tổ chức khác để ngăn chặn và phát hiện gian lận, giảm rủi ro về tín dụng.</p>
                <p><strong>3.2.</strong> Cho mục đích nâng cao trải nghiệm khách hàng: ở mục đích này, thông tin Khách hàng cung cấp giúp cho chúng tôi:<br>
                (a) Thực hiện các hoạt động nhằm chăm sóc khách hàng và thực hiện các chương trình hậu mãi sau bán hàng (bao gồm nhưng không giới hạn ở chương trình Khách hàng thân thiết được triển khai trong nội bộ FRT và/ hoặc Long Châu, Tập đoàn FPT);<br>
                (b) Đáp ứng các yêu cầu dịch vụ và nhu cầu hỗ trợ của Khách hàng;<br>
                (c) Đo lường, phân tích dữ liệu nội bộ và các xử lý khác để cải thiện, nâng cao chất lượng dịch vụ/sản phẩm của Công ty hoặc thực hiện các hoạt động truyền thông tiếp thị;</p>
                <p><strong>3.3.</strong> Tổ chức các hoạt động nghiên cứu thị trường, thăm dò dư luận nhằm cải thiện chất lượng sản phẩm/ dịch vụ hoặc để nghiên cứu phát triển các sản phẩm, dịch vụ mới nhằm đáp ứng tốt hơn nhu cầu của khách hàng. Để thực hiện mục đích này, chúng tôi sẽ triển khai riêng biệt từng chương trình và sẽ đề nghị Khách hàng có ý kiến về việc đồng ý trước khi Khách hàng cung cấp thông tin.</p>
                <p><strong>3.4.</strong> Cho mục đích truyền thông, tiếp thị: Thông báo cho Khách hàng về sản phẩm/ dịch vụ mới hoặc những thay đổi đối với các chính sách, khuyến mại của các sản phẩm, dịch vụ mà Công ty đang cung cấp;</p>
                <p><strong>3.5.</strong> Bất kỳ mục đích nào khác mà FRT thông báo cho Khách hàng, vào thời điểm thu thập dữ liệu cá nhân của Khách hàng hoặc trước khi bắt đầu xử lý liên quan hoặc theo yêu cầu khác hoặc được pháp luật hiện hành cho phép.</p>

                <h4>Điều 4. Bảo mật Dữ liệu cá nhân khách hàng</h4>
                <p><strong>4.1. Nguyên tắc bảo mật:</strong><br>
                (a) Dữ liệu cá nhân của Khách hàng được cam kết bảo mật theo quy định của FRT và quy định của pháp luật. Việc xử lý Dữ liệu cá nhân của mỗi Khách hàng chỉ được thực hiện khi có sự đồng ý của Khách hàng, trừ trường hợp pháp luật có quy định khác.<br>
                (b) FRT không sử dụng, chuyển giao, cung cấp hay chia sẻ cho bên thứ ba nào về Dữ liệu cá nhân của Khách hàng khi không có sự đồng ý của Khách hàng, trừ trường hợp pháp luật có quy định khác.<br>
                (c) FRT sẽ tuân thủ các nguyên tắc bảo mật dữ liệu cá nhân khác theo quy định pháp luật hiện hành.</p>
                <p><strong>4.2. Hậu quả, thiệt hại không mong muốn có thể xảy ra:</strong><br>
                FRT sử dụng nhiều công nghệ bảo mật thông tin khác nhau nhằm bảo vệ Dữ liệu cá nhân của Khách hàng không bị truy lục, sử dụng hoặc chia sẻ ngoài ý muốn. Tuy nhiên, không một dữ liệu nào có thể được bảo mật 100%. Do vậy, FRT cam kết sẽ bảo mật một cách tối đa trong khả năng cho phép Dữ liệu cá nhân của Khách hàng. Một số hậu quả, thiệt hại không mong muốn có thể xảy ra bao gồm nhưng không giới hạn:<br>
                (a) Lỗi phần cứng, phần mềm trong quá trình xử lý dữ liệu làm mất dữ liệu của Khách hàng;<br>
                (b) Lỗ hổng bảo mật nằm ngoài khả năng kiểm soát của FRT, hệ thống có liên quan bị hacker tấn công gây lộ lọt dữ liệu;<br>
                (c) Khách hàng tự làm lộ lọt dữ liệu cá nhân do: bất cẩn hoặc bị lừa đảo truy cập các website/tải các ứng dụng có chứa phần mềm độc hại, vv...</p>
                <p><strong>4.3.</strong> FRT khuyến cáo Khách hàng bảo mật các thông tin liên quan đến mật khẩu đăng nhập vào tài khoản của Khách hàng, mã OTP và không chia sẻ mật khẩu đăng nhập, mã OTP này với bất kỳ người nào khác.</p>
                <p><strong>4.4.</strong> Khách hàng nên bảo quản thiết bị điện tử trong quá trình sử dụng; Khách hàng nên khóa, đăng xuất, hoặc thoát khỏi tài khoản trên website hoặc Ứng dụng của FRT khi không sử dụng.</p>

                <h4>Điều 5. Các loại dữ liệu cá nhân mà FRT xử lý</h4>
                <p>Để FRT có thể cung cấp các sản phẩm, dịch vụ cho Khách hàng và/hoặc xử lý các yêu cầu của Khách hàng, FRT có thể cần phải và/hoặc được yêu cầu phải thu thập dữ liệu cá nhân, bao gồm:<br>
                (a) Dữ liệu cá nhân cơ bản của Khách hàng và các cá nhân có liên quan của Khách hàng được Khách hàng cung cấp khi giao kết, thực hiện hợp đồng, giao dịch với FRT;<br>
                (b) Dữ liệu cá nhân nhạy cảm của Khách hàng và các cá nhân có liên quan của Khách hàng mà dữ liệu đó cần thiết cho việc cung cấp hàng hoá/ dịch vụ hoặc giao dịch giữa FRT và Khách hàng. Trong trường hợp dữ liệu thu thập là DLCN nhạy cảm, FRT sẽ thông báo cho Khách hàng ngay khi thu thập;<br>
                (c) Dữ liệu liên quan đến các trang tin điện tử hoặc ứng dụng: dữ liệu kỹ thuật (như đã nêu ở trên, bao gồm loại thiết bị, hệ điều hành, loại trình duyệt, cài đặt trình duyệt, địa chỉ IP, cài đặt ngôn ngữ, ngày và giờ kết nối với trang tin điện tử, thống kê sử dụng ứng dụng, cài đặt ứng dụng, ngày và giờ kết nối với ứng dụng, dữ liệu vị trí và thông tin liên lạc kỹ thuật khác); chi tiết đăng nhập bảo mật; dữ liệu sử dụng;<br>
                (d) Dữ liệu tiếp thị: các mối quan tâm đối với quảng cáo; dữ liệu cookie; dữ liệu clickstream; lịch sử duyệt web; phản ứng với tiếp thị trực tiếp; và lựa chọn không tham gia tiếp thị trực tiếp.</p>

                <h4>Điều 6. Cách thức thu thập dữ liệu cá nhân</h4>
                <p>FRT thực hiện thu thập dữ liệu cá nhân từ Khách hàng theo các phương thức sau:</p>
                <p><strong>6.1. Trực tiếp từ Khách hàng bằng các phương tiện khác nhau:</strong><br>
                (a) Khi Khách hàng gửi yêu cầu đăng ký hoặc điền thông tin vào bất kỳ biểu mẫu nào khác liên quan tới các sản phẩm và dịch vụ của FRT, đối tác của FRT;<br>
                (b) Khi Khách hàng tương tác với nhân viên dịch vụ khách hàng của Công ty, ví dụ như thông qua các cuộc gọi điện thoại, thư từ, gặp mặt trực tiếp, gửi thư điện tử hoặc tương tác trên mạng xã hội;<br>
                (c) Khi Khách hàng sử dụng một số dịch vụ của FRT, ví dụ như các trang web và ứng dụng bao gồm việc thiết lập các tài khoản trực tuyến với FRT;<br>
                (d) Khi Khách hàng được liên hệ và phản hồi lại các đại diện tiếp thị và các nhân viên dịch vụ khách hàng của FRT;<br>
                (e) Khi Khách hàng gửi thông tin cá nhân của mình cho Công ty vì bất kỳ lý do nào khác, bao gồm cả khi Khách hàng đăng ký sử dụng thử miễn phí bất kỳ sản phẩm và dịch vụ nào hoặc khi Khách hàng thể hiện quan tâm đến bất kỳ sản phẩm và dịch vụ nào của Công ty.<br>
                (f) Khi Khách hàng, mua hoặc sử dụng các dịch vụ của bên thứ ba thông qua FRT hoặc tại các điểm giao dịch, cơ sở kinh doanh của FRT;</p>
                <p><strong>6.2. Từ các bên thứ ba khác:</strong><br>
                (a) Nếu Khách hàng tương tác với nội dung hoặc quảng cáo của bên thứ ba trên trang tin điện tử hoặc trong ứng dụng, Công ty có thể nhận được thông tin cá nhân của Khách hàng từ bên thứ ba có liên quan, theo chính sách bảo mật hiện hành hợp pháp của bên thứ ba đó.<br>
                (b) Nếu Khách hàng chọn thanh toán điện tử trực tiếp tới FRT hoặc thông qua trang tin điện tử hoặc ứng dụng, FRT có thể nhận được dữ liệu cá nhân của Khách hàng từ các bên thứ ba, chẳng hạn như nhà cung cấp dịch vụ thanh toán, cho mục đích thanh toán đó.<br>
                (c) Để tuân thủ các nghĩa vụ của mình theo luật hiện hành, FRT có thể tiếp nhận dữ liệu cá nhân về Khách hàng từ các cơ quan pháp luật và cơ quan công quy.<br>
                (d) có thể tiếp nhận được dữ liệu cá nhân về Khách hàng từ các nguồn công khai (như danh bạ điện thoại, thông tin quảng cáo/tờ rơi, các thông tin được công khai trên các trang tin điện tử, v.v.).<br>
                (e) Bất cứ khi nào thu thập dữ liệu cá nhân như vậy, FRT sẽ đảm bảo việc nhận dữ liệu từ các bên thứ ba có liên quan theo những cách hợp pháp, đồng thời yêu cầu các bên thứ ba đó chịu trách nhiệm tuân thủ quy định của pháp luật về bảo vệ dữ liệu cá nhân.</p>

                <h4>Điều 7. Tổ chức được xử lý dữ liệu cá nhân</h4>
                <p><strong>7.1.</strong> Công ty Cổ phần Bán lẻ Kỹ thuật số FPT.</p>
                <p><strong>7.2.</strong> FRT sẽ thực hiện việc chia sẻ hoặc cùng xử lý dữ liệu cá nhân với các tổ chức, cá nhân sau:<br>
                (a) Tập đoàn FPT, Long Châu.<br>
                (b) Các nhà thầu, đại lý, đối tác, các nhà cung cấp dịch vụ vận hành của FRT.<br>
                (c) Các tổ chức liên quan tới việc thực hiện các chương trình khuyến mại của FRT.<br>
                (d) Các cố vấn chuyên nghiệp của FRT như kiểm toán, luật sư,… theo quy định của pháp luật.<br>
                (e) Tòa án, các cơ quan nhà nước có thẩm quyền phù hợp với quy định của pháp luật và/hoặc khi được yêu cầu và pháp luật cho phép.</p>
                <p><strong>7.3.</strong> FRT nỗ lực để khử nhận dạng đối với dữ liệu của Khách hàng và cam kết việc chia sẻ hoặc cùng xử lý dữ liệu cá nhân chỉ thực hiện trong trường hợp cần thiết để thực hiện các Mục Đích Xử Lý được nêu tại Chính sách này hoặc theo quy định của pháp luật. Các tổ chức, cá nhân nhận được dữ liệu cá nhân của Khách hàng sẽ phải tuân thủ theo nội dung quy định tại Chính sách này và quy định của pháp luật về bảo vệ dữ liệu cá nhân liên quan.<br>
                Mặc dù FRT sẽ thực hiện mọi nỗ lực để đảm bảo rằng các thông tin Khách hàng được ẩn danh/mã hóa, nhưng không thể loại trừ hoàn toàn rủi ro các dữ liệu này có thể bị tiết lộ trong những trường hợp bất khả kháng.</p>
                <p><strong>7.4.</strong> Trong trường hợp có sự tham gia của các tổ chức xử lý dữ liệu cá nhân khác được nêu tại Điều này, FRT thực hiện trên cơ sở sự đồng ý của Khách hàng hoặc sẽ thông báo cho Khách hàng trước khi FRT thực hiện.</p>

                <h4>Điều 8. Xử lý dữ liệu cá nhân trong một số trường hợp đặc biệt</h4>
                <p>FRT đảm bảo thực hiện xử lý dữ liệu cá nhân của Khách hàng đáp ứng đầy đủ các yêu cầu của Pháp luật trong các trường hợp đặc biệt nêu sau:</p>
                <p><strong>8.1.</strong> Đoạn phim của máy quay giám sát (CCTV) có hoặc không tích hợp tính năng ghi âm, thiết bị ghi âm, trong trường hợp cụ thể, cũng có thể được sử dụng cho các mục đích sau đây:<br>
                (a) Cho các mục đích đảm bảo chất lượng;<br>
                (b) Cho mục đích an ninh công cộng và an toàn lao động;<br>
                (c) Phát hiện và ngăn chặn việc sử dụng đáng ngờ, không phù hợp hoặc không được phép của các tiện ích, sản phẩm, dịch vụ và/hoặc cơ sở của Công ty;<br>
                (d) Phát hiện và ngăn chặn hành vi phạm tội; và/hoặc<br>
                (e) Tiến hành điều tra các sự cố.</p>
                <p><strong>8.2.</strong> FRT luôn tôn trọng và bảo vệ dữ liệu cá nhân của trẻ em. Ngoài các biện pháp bảo vệ dữ liệu cá nhân được quy định theo pháp luật, trước khi xử lý dữ liệu cá nhân của trẻ em, Công ty sẽ thực hiện xác minh tuổi của trẻ em và yêu cầu sự đồng ý của (i) trẻ em và/hoặc (ii) cha, mẹ hoặc người giám hộ của trẻ em theo quy định của pháp luật.</p>
                <p><strong>8.3.</strong> Bên cạnh tuân thủ theo các quy định pháp luật có liên quan khác, đối với việc xử lý dữ liệu cá nhân liên quan đến dữ liệu cá nhân của người bị tuyên bố mất tích/ người đã chết, Công ty sẽ phải được sự đồng ý của một trong số những người có liên quan theo quy định của pháp luật hiện hành.</p>

                <h4>Điều 9. Quyền và nghĩa vụ của Khách hàng liên quan đến dữ liệu cá nhân cung cấp cho FRT</h4>
                <p><strong>9.1. Quyền của khách hàng</strong><br>
                (a) Khách hàng có quyền được biết về hoạt động xử lý dữ liệu cá nhân của mình, trừ trường hợp pháp luật có quy định khác.<br>
                (b) Khách hàng được đồng ý hoặc không đồng ý cho phép xử lý dữ liệu cá nhân của mình, trừ trường hợp luật có quy định khác.<br>
                (c) Khách hàng được quyền truy cập để xem, chỉnh sửa hoặc yêu cầu chỉnh sửa Dữ liệu cá nhân của mình bằng văn bản gửi đến FRT, trừ trường hợp luật có quy định khác.<br>
                (d) Khách hàng có quyền rút lại sự đồng ý của mình bằng văn bản gửi đến FRT, trừ trường hợp pháp luật có quy định khác. Việc rút lại sự đồng ý không ảnh hưởng đến tính hợp pháp của việc xử lý dữ liệu đã được Khách hàng đồng ý với FRT trước khi rút lại sự đồng ý.<br>
                (e) Khách hàng được quyền xóa hoặc yêu cầu xóa dữ liệu cá nhân của mình bằng văn bản gửi đến FRT, trừ trường hợp luật có quy định khác.<br>
                (f) Khách hàng được quyền yêu cầu hạn chế xử lý Dữ liệu cá nhân của mình bằng văn bản gửi đến , trừ trường hợp luật có quy định khác.<br>
                (g) Khách hàng được quyền yêu cầu FRT cung cấp cho bản thân Dữ liệu cá nhân của mình bằng văn bản gửi đến FRT, trừ trường hợp luật có quy định khác.<br>
                (h) Khách hàng được quyền phản đối FRT, Tổ Chức Được Xử Lý Dữ Liệu Cá Nhân quy định tại Chính sách này xử lý dữ liệu cá nhân của mình bằng văn bản gửi đến FRT nhằm ngăn chặn hoặc hạn chế việc tiết lộ DLCN hoặc sử dụng DLCN cho mục đích quảng cáo, tiếp thị, trừ trường hợp pháp luật có quy định khác.<br>
                (i) Khách hàng có quyền khiếu nại, tố cáo hoặc khởi kiện theo quy định của pháp luật.<br>
                (j) Khách hàng có quyền yêu cầu bồi thường đối với thiệt hại thực tế theo quy định của pháp luật nếu FRT có hành vi vi phạm quy định về bảo vệ Dữ liệu cá nhân của mình, trừ trường hợp các bên có thỏa thuận khác hoặc luật có quy định khác.<br>
                (k) Khách hàng có quyền tự bảo vệ theo quy định của Bộ luật Dân sự, luật khác có liên quan, hoặc yêu cầu cơ quan, tổ chức có thẩm quyền thực hiện các phương thức bảo vệ quyền dân sự theo quy định tại Điều 11 Bộ luật Dân sự.<br>
                (l) Các quyền khác theo quy định của pháp luật hiện hành.<br>
                (m) FRT đảm bảo thực hiện các yêu cầu rút lại sự đồng ý, hạn chế xử lý dữ liệu cá nhân, phản đối xử lý dữ liệu cá nhân, xem, chỉnh sửa, yêu cầu chỉnh sửa, xoá, thực hiện các biện pháp, giải pháp bảo vệ dữ liệu cá nhân trong thời hạn quy định pháp luật có hiệu lực tại thời điểm Khách hàng yêu cầu.</p>
                <p><strong>9.2. Nghĩa vụ của Khách hàng</strong><br>
                (a) Tuân thủ các quy định của pháp luật, quy định, hướng dẫn của FRT liên quan đến xử lý Dữ liệu cá nhân của Khách hàng.<br>
                (b) Cung cấp đầy đủ, trung thực, chính xác Dữ liệu cá nhân, các thông tin khác theo yêu cầu của FRT khi đăng ký và sử dụng dịch vụ của FRT và khi có thay đổi về các thông tin này. FRT sẽ tiến hành bảo mật Dữ liệu cá nhân của Khách hàng căn cứ trên thông tin Khách hàng đã đăng ký, do đó nếu có bất kỳ thông tin sai lệch nào FRT sẽ không chịu trách nhiệm trong trường hợp thông tin đó làm ảnh hưởng hoặc hạn chế quyền lợi của Khách hàng. Trường hợp không thông báo, nếu có phát sinh rủi ro, tổn thất thì Khách hàng chịu trách nhiệm về những sai sót hay hành vi lợi dụng, lừa đảo khi sử dụng dịch vụ do lỗi của mình hoặc do không cung cấp đúng, đầy đủ, chính xác, kịp thời sự thay đổi thông tin; bao gồm cả thiệt hại về tài chính, chi phí phát sinh do thông tin cung cấp sai hoặc không thống nhất.<br>
                (c) Phối hợp với FRT, cơ quan nhà nước có thẩm quyền hoặc bên thứ ba trong trường hợp phát sinh các vấn đề ảnh hưởng đến tính bảo mật Dữ liệu cá nhân của Khách hàng.<br>
                (d) Tự bảo vệ dữ liệu cá nhân của mình; chủ động áp dụng các biện pháp nhằm bảo vệ Dữ liệu cá nhân của mình trong quá trình sử dụng dịch vụ của FRT; thông báo kịp thời cho FRT khi phát hiện thấy có sai sót, nhầm lẫn về Dữ liệu cá nhân của mình hoặc nghi ngờ Dữ liệu cá nhân của mình đang bị xâm phạm.<br>
                (e) Tự chịu trách nhiệm đối với những thông tin, dữ liệu, chấp thuận mà mình tạo lập, cung cấp trên môi trường mạng; tự chịu trách nhiệm trong trường hợp dữ liệu cá nhân bị rò rỉ, xâm phạm do lỗi của mình.<br>
                (f) Thường xuyên cập nhật các Quy định, Chính sách của FRT trong từng thời kỳ được thông báo tới Khách hàng hoặc đăng tải trên các website và hoặc các kênh giao dịch khác của FRT từng thời kỳ. Thực hiện các hành động theo hướng dẫn của FRT để thể hiện rõ việc chấp thuận hoặc không chấp thuận đối với các mục đích xử lý Dữ liệu cá nhân mà FRT thông báo tới Khách hàng trong từng thời kỳ.<br>
                (g) Tôn trọng, bảo vệ dữ liệu cá nhân của người khác.<br>
                (h) Các trách nhiệm khác theo quy định của pháp luật.</p>

                <h4>Điều 10. Lưu trữ dữ liệu</h4>
                <p>FRT cam kết sẽ chỉ lưu trữ dữ liệu cá nhân của Khách hàng trong trường hợp liên quan đến các mục đích được nêu trong Chính sách này. Ngoài việc lưu trữ dữ liệu cá nhân của Khách hàng trong thời gian pháp luật quy định, FRT có thể cần lưu trữ dữ liệu cá nhân của Khách hàng trong thời hạn tối đa không quá 10 năm kể từ ngày Khách hàng thực hiện các giao dịch cuối cùng với FRT hoặc thời hạn ngắn hơn được thông báo tới Khách hàng khi thực hiện thu thập dữ liệu.</p>

                <h4>Điều 11. Cách thức xử lý dữ liệu</h4>
                <p>FRT áp dụng một hoặc nhiều hoạt động tác động tới dữ liệu cá nhân như: thu thập, ghi, phân tích, xác nhận, lưu trữ, chỉnh sửa, công khai, kết hợp, truy cập, truy xuất, thu hồi, mã hóa, giải mã, sao chép, chia sẻ, truyền đưa, cung cấp, chuyển giao, xóa, hủy dữ liệu cá nhân hoặc các hành động khác có liên quan.</p>

                <h4>Điều 12. Cookies</h4>
                <p><strong>12.1.</strong> Khi Khách hàng sử dụng hoặc truy cập các website, trang tin trực tuyến (sau đây gọi chung là “trang tin điện tử”) của FRT, FRT có thể đặt một hoặc nhiều cookie trên thiết bị của Khách hàng. “Cookie” là một tệp nhỏ được đặt trên thiết bị của Khách hàng khi Khách hàng truy cập một trang tin điện tử, nó ghi lại thông tin về thiết bị, trình duyệt của Khách hàng và trong một số trường hợp, sở thích và thói quen duyệt tin điện tử của Khách hàng. FRT có thể sử dụng thông tin này để nhận diện Khách hàng khi Khách hàng quay lại các trang tin điện tử của FRT, để cung cấp các dịch vụ được cá nhân hóa trên các trang tin điện tử của FRT, để biên soạn số liệu phân tích nhằm hiểu rõ hơn về hoạt động của trang tin điện tử và để cải thiện các trang tin điện tử của FRT. Khách hàng có thể sử dụng cài đặt trình duyệt của mình để xóa hoặc chặn cookie trên thiết bị của mình. Tuy nhiên, nếu Khách hàng quyết định không chấp nhận hoặc chặn cookie từ các trang tin điện tử của FRT, Khách hàng có thể không tận dụng hết tất cả các tính năng của các trang tin điện tử của FRT.</p>
                <p><strong>12.2.</strong> FRT có thể xử lý thông tin cá nhân của Khách hàng thông qua công nghệ cookie, theo các quy định của Điều khoản này. FRT cũng có thể sử dụng biện pháp tiếp thị lại để phân phát quảng cáo cho những cá nhân mà FRT biết trước đây đã truy cập trang tin điện tử của mình.</p>
                <p><strong>12.3.</strong> Trong phạm vi các bên thứ ba đã gán nội dung lên trên các trang tin điện tử của FRT (ví dụ: các tính năng truyền thông xã hội), các bên thứ ba đó có thể thu thập thông tin cá nhân của Khách hàng (ví dụ: dữ liệu cookie) nếu Khách hàng chọn tương tác với nội dung của bên thứ ba đó hoặc sử dụng các dịch vụ của bên thứ ba.</p>

                <h4>Điều 13: Xử lý dữ liệu cá nhân có yếu tố nước ngoài</h4>
                <p>Nhằm thực hiện mục đích xử lý dữ liệu cá nhân tại Chính sách này, FRT có thể phải cung cấp/ chia sẻ dữ liệu cá nhân của Khách hàng đến các bên thứ ba liên quan của FRT và các bên thứ ba này có thể là tại Việt Nam hoặc bất cứ địa điểm nào khác nằm ngoài lãnh thổ Việt Nam.</p>
                <p><strong>13.1.</strong> Khi thực hiện việc cung cấp/ chia sẻ dữ liệu cá nhân ra nước ngoài, FRT sẽ yêu cầu bên tiếp nhận đảm bảo rằng dữ liệu cá nhân của Khách hàng được chuyển giao cho họ sẽ bảo mật và an toàn. FRT đảm bảo tuân thủ các nghĩa vụ pháp lý và quy định liên quan đến việc chuyển giao dữ liệu cá nhân của Khách hàng.</p>
                <p><strong>13.2.</strong> Khách hàng tại Liên Minh Châu Âu (EU): Dữ liệu cá nhân của Khách hàng có thể được truy cập, chuyển giao và/ hoặc lưu trữ bên ngoài Khu vực Kinh tế Châu Âu (EEA), bao gồm cả các quốc gia có thể có mức độ bảo vệ dữ liệu thấp hơn theo luật bảo vệ dữ liệu của EU, phải tuân thủ các quy tắc cụ thể khi chuyển Dữ liệu Cá nhân từ bên trong EEA ra bên ngoài EEA. Khi đó, FRT sẽ sử dụng các biện pháp bảo vệ thích hợp để bảo vệ mọi Dữ liệu Cá nhân được chuyển giao.</p>

                <h4>Điều 14. Thông tin liên hệ xử lý dữ liệu cá nhân</h4>
                <p>Trường hợp Khách hàng có bất kỳ câu hỏi nào liên quan đến Chính sách này hoặc thực hiện các quyền của chủ thể dữ liệu hoặc xử lý dữ liệu cá nhân của Khách hàng, Khách hàng hàng có thể sử dụng các hình thức liên hệ nêu sau:<br>
                (1) Gửi thư về Công ty theo địa chỉ: Trung tâm trải nghiệm khách hàng; địa chỉ: Toà nhà The 678, số 67 Hoàng Văn Thái, Phường Tân Mỹ, TP. Hồ Chí Minh<br>
                (2) Gửi email về hòm thư điện tử: fptshop@fpt.com<br>
                (3) Hotline: 18006616 (Nhánh 3)</p>

                <h4>Điều 15. Điều khoản chung</h4>
                <p><strong>15.1.</strong> Chính sách này có hiệu lực từ ngày 01/01/2026. Khách hàng hiểu và đồng ý rằng, Chính sách này có thể được sửa đổi theo từng thời kỳ và được đăng tải công khai trên website, ứng dụng của FRT. Trường hợp Khách hàng không đồng ý với một phần hoặc toàn bộ nội dung Chính sách sau khi cập nhật, Khách hàng có thể gửi yêu cầu thực hiện quyền rút lại sự đồng ý.</p>
                <p><strong>15.2.</strong> Khách hàng cam kết thực hiện nghiêm túc các quy định tại Chính sách này. Các vấn đề chưa được quy định, các Bên thống nhất thực hiện theo quy định của pháp luật, hướng dẫn của cơ quan Nhà nước có thẩm quyền và/hoặc các sửa đổi, bổ sung Chính sách này được FRT đăng tải công khai trong từng thời kỳ.</p>
                <p><strong>15.3.</strong> Khách hàng có thể thấy quảng cáo hoặc nội dung khác trên bất kỳ trang tin điện tử, ứng dụng hoặc thiết bị nào có thể liên kết đến các trang tin điện tử hoặc dịch vụ của các đối tác, nhà quảng cáo, nhà tài trợ hoặc các bên thứ ba khác. FRT không kiểm soát nội dung hoặc các liên kết xuất hiện trên các trang tin điện tử hoặc dịch vụ của bên thứ ba và không chịu trách nhiệm hoặc/và trách nhiệm pháp lý đối với các hoạt động được sử dụng bởi các trang tin điện tử hoặc dịch vụ của bên thứ ba được liên kết đến hoặc từ bất kỳ trang tin điện tử, ứng dụng hoặc thiết bị nào. Các trang tin điện tử và dịch vụ này có thể tuân theo các chính sách bảo mật và điều khoản sử dụng của riêng của bên thứ ba.</p>
                <p><strong>15.4.</strong> Chính sách này được giao kết trên cơ sở thiện chí giữa FRT và Khách hàng. Trong quá trình thực hiện nếu phát sinh tranh chấp, các Bên sẽ chủ động giải quyết thông qua thương lượng, hòa giải. Trường hợp hòa giải không thành, tranh chấp sẽ được đưa ra Tòa án nhân dân có thẩm quyền để giải quyết theo quy định của pháp luật.</p>
                <p><strong>15.5.</strong> Khách hàng đã đọc kỹ, hiểu rõ các quyền và nghĩa vụ và đồng ý với toàn bộ nội dung của bản Chính sách bảo vệ dữ liệu cá nhân này.</p>
                <p><strong>15.6.</strong> Khi nhận được yêu cầu thực hiện quyền của Khách hàng theo quy định, FRT sẽ thực hiện các bước cần thiết để xác nhận, nhận dạng người yêu cầu trước khi triển khai các quyền mà người yêu cầu muốn áp dụng. Trong trường hợp cần thiết, nhằm xác minh danh tính và đảm bảo tính bảo mật của dữ liệu cá nhân của Khách hàng, FRT có thể thực hiện khớp dữ liệu cá nhân cung cấp bởi người yêu cầu khi gửi yêu cầu thực hiện quyền với dữ liệu mà FRT đã và đang lưu trữ. Trường hợp FRT thực hiện việc xóa, hủy, hay hạn chế sử dụng dữ liệu theo yêu cầu của Khách hàng, các quyền lợi của Khách hàng theo hợp đồng, thỏa thuận dịch vụ ký với FRT mà đòi hỏi phải sử dụng dữ liệu cá nhân nói trên, có thể bị gián đoạn, thay đổi, hoặc chấm dứt.</p>

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