# Tài liệu Yêu cầu - Chức năng Quên Mật khẩu

## Giới thiệu

Chức năng quên mật khẩu cho phép khách hàng đặt lại mật khẩu của họ khi không thể đăng nhập vào tài khoản. Hệ thống sẽ gửi email chứa link đặt lại mật khẩu có thời hạn, đảm bảo tính bảo mật và trải nghiệm người dùng mượt mà.

## Thuật ngữ

- **Password_Reset_System**: Hệ thống xử lý quên mật khẩu
- **User**: Khách hàng đã đăng ký tài khoản
- **Reset_Token**: Mã token ngẫu nhiên 64 ký tự hex để xác thực yêu cầu đặt lại mật khẩu
- **Reset_Link**: Đường dẫn URL chứa Reset_Token gửi qua email
- **Token_Expiry**: Thời gian hết hạn của Reset_Token (24 giờ)
- **Email_Service**: Dịch vụ gửi email sử dụng PHPMailer
- **Database**: Cơ sở dữ liệu MySQL chứa bảng nguoi_dung

## Yêu cầu

### Yêu cầu 1: Hiển thị giao diện yêu cầu đặt lại mật khẩu

**User Story:** Là một User, tôi muốn truy cập trang quên mật khẩu từ trang đăng nhập, để có thể bắt đầu quy trình đặt lại mật khẩu.

#### Tiêu chí chấp nhận

1. THE Password_Reset_System SHALL hiển thị link "Quên mật khẩu?" trên trang đăng nhập
2. WHEN User nhấn vào link "Quên mật khẩu?", THE Password_Reset_System SHALL chuyển hướng đến trang yêu cầu đặt lại mật khẩu
3. THE Password_Reset_System SHALL hiển thị form nhập email trên trang yêu cầu đặt lại mật khẩu
4. THE Password_Reset_System SHALL hiển thị nút "Gửi link đặt lại mật khẩu" trên form

### Yêu cầu 2: Xử lý yêu cầu đặt lại mật khẩu

**User Story:** Là một User, tôi muốn nhập email của mình và nhận được link đặt lại mật khẩu, để có thể tạo mật khẩu mới.

#### Tiêu chí chấp nhận

1. WHEN User gửi form với email hợp lệ, THE Password_Reset_System SHALL kiểm tra định dạng email
2. IF email không hợp lệ, THEN THE Password_Reset_System SHALL hiển thị thông báo lỗi "Email không hợp lệ"
3. WHEN email hợp lệ được gửi, THE Password_Reset_System SHALL kiểm tra email có tồn tại trong Database
4. IF email không tồn tại trong Database, THEN THE Password_Reset_System SHALL hiển thị thông báo "Nếu email tồn tại, bạn sẽ nhận được link đặt lại mật khẩu"
5. WHEN email tồn tại trong Database, THE Password_Reset_System SHALL tạo Reset_Token ngẫu nhiên 64 ký tự hex
6. WHEN Reset_Token được tạo, THE Password_Reset_System SHALL lưu Reset_Token vào cột forget_token trong bảng nguoi_dung
7. WHEN Reset_Token được lưu, THE Password_Reset_System SHALL lưu thời gian tạo token vào cột forget_token_created_at
8. WHEN Reset_Token và thời gian được lưu thành công, THE Password_Reset_System SHALL tạo Reset_Link chứa Reset_Token
9. WHEN Reset_Link được tạo, THE Password_Reset_System SHALL gửi email chứa Reset_Link đến địa chỉ email của User thông qua Email_Service
10. WHEN email được gửi thành công, THE Password_Reset_System SHALL hiển thị thông báo "Nếu email tồn tại, bạn sẽ nhận được link đặt lại mật khẩu"

### Yêu cầu 3: Xác thực link đặt lại mật khẩu

**User Story:** Là một User, tôi muốn nhấn vào link trong email để truy cập trang đặt lại mật khẩu, để có thể nhập mật khẩu mới.

#### Tiêu chí chấp nhận

1. WHEN User truy cập Reset_Link, THE Password_Reset_System SHALL trích xuất Reset_Token từ URL
2. WHEN Reset_Token được trích xuất, THE Password_Reset_System SHALL kiểm tra Reset_Token có tồn tại trong Database
3. IF Reset_Token không tồn tại trong Database, THEN THE Password_Reset_System SHALL hiển thị thông báo lỗi "Link đặt lại mật khẩu không hợp lệ"
4. WHEN Reset_Token tồn tại, THE Password_Reset_System SHALL kiểm tra thời gian tạo token
5. IF thời gian hiện tại vượt quá Token_Expiry kể từ forget_token_created_at, THEN THE Password_Reset_System SHALL hiển thị thông báo lỗi "Link đặt lại mật khẩu đã hết hạn"
6. WHEN Reset_Token hợp lệ và chưa hết hạn, THE Password_Reset_System SHALL hiển thị form nhập mật khẩu mới

### Yêu cầu 4: Đặt lại mật khẩu

**User Story:** Là một User, tôi muốn nhập mật khẩu mới và xác nhận mật khẩu, để có thể cập nhật mật khẩu tài khoản của mình.

#### Tiêu chí chấp nhận

1. THE Password_Reset_System SHALL hiển thị trường nhập "Mật khẩu mới" trên form đặt lại mật khẩu
2. THE Password_Reset_System SHALL hiển thị trường nhập "Xác nhận mật khẩu mới" trên form đặt lại mật khẩu
3. WHEN User gửi form đặt lại mật khẩu, THE Password_Reset_System SHALL kiểm tra mật khẩu mới không rỗng
4. IF mật khẩu mới rỗng, THEN THE Password_Reset_System SHALL hiển thị thông báo lỗi "Vui lòng nhập mật khẩu mới"
5. WHEN mật khẩu mới không rỗng, THE Password_Reset_System SHALL kiểm tra độ dài mật khẩu mới tối thiểu 6 ký tự
6. IF độ dài mật khẩu mới nhỏ hơn 6 ký tự, THEN THE Password_Reset_System SHALL hiển thị thông báo lỗi "Mật khẩu phải có ít nhất 6 ký tự"
7. WHEN độ dài mật khẩu hợp lệ, THE Password_Reset_System SHALL kiểm tra mật khẩu mới và xác nhận mật khẩu khớp nhau
8. IF mật khẩu mới và xác nhận mật khẩu không khớp, THEN THE Password_Reset_System SHALL hiển thị thông báo lỗi "Mật khẩu xác nhận không khớp"
9. WHEN mật khẩu mới và xác nhận mật khẩu khớp nhau, THE Password_Reset_System SHALL mã hóa mật khẩu mới bằng SHA1
10. WHEN mật khẩu được mã hóa, THE Password_Reset_System SHALL cập nhật cột mat_khau trong Database với mật khẩu đã mã hóa
11. WHEN mật khẩu được cập nhật, THE Password_Reset_System SHALL xóa Reset_Token khỏi cột forget_token
12. WHEN Reset_Token được xóa, THE Password_Reset_System SHALL xóa thời gian tạo token khỏi cột forget_token_created_at
13. WHEN cập nhật thành công, THE Password_Reset_System SHALL hiển thị thông báo "Đặt lại mật khẩu thành công"
14. WHEN thông báo thành công được hiển thị, THE Password_Reset_System SHALL chuyển hướng User đến trang đăng nhập sau 3 giây

### Yêu cầu 5: Cập nhật cấu trúc Database

**User Story:** Là một Developer, tôi muốn thêm các cột cần thiết vào bảng nguoi_dung, để hỗ trợ chức năng quên mật khẩu.

#### Tiêu chí chấp nhận

1. THE Password_Reset_System SHALL thêm cột forget_token kiểu VARCHAR(64) vào bảng nguoi_dung
2. THE Password_Reset_System SHALL cho phép cột forget_token có giá trị NULL
3. THE Password_Reset_System SHALL thêm cột forget_token_created_at kiểu DATETIME vào bảng nguoi_dung
4. THE Password_Reset_System SHALL cho phép cột forget_token_created_at có giá trị NULL
5. THE Password_Reset_System SHALL thêm index trên cột forget_token để tối ưu truy vấn

### Yêu cầu 6: Gửi email đặt lại mật khẩu

**User Story:** Là một User, tôi muốn nhận email chứa link đặt lại mật khẩu, để có thể truy cập trang đặt lại mật khẩu một cách an toàn.

#### Tiêu chí chấp nhận

1. THE Password_Reset_System SHALL tạo nội dung email HTML chứa Reset_Link
2. THE Password_Reset_System SHALL hiển thị tên User trong nội dung email
3. THE Password_Reset_System SHALL hiển thị nút "Đặt lại mật khẩu" trong email với Reset_Link
4. THE Password_Reset_System SHALL hiển thị Reset_Link dạng text trong email cho trường hợp nút không hoạt động
5. THE Password_Reset_System SHALL hiển thị thông tin Token_Expiry trong email
6. WHEN Email_Service gửi email thành công, THE Password_Reset_System SHALL trả về kết quả thành công
7. IF Email_Service gửi email thất bại, THEN THE Password_Reset_System SHALL hiển thị thông báo lỗi "Không thể gửi email, vui lòng thử lại sau"

### Yêu cầu 7: Bảo mật và xử lý lỗi

**User Story:** Là một Developer, tôi muốn đảm bảo chức năng quên mật khẩu an toàn, để bảo vệ tài khoản người dùng khỏi các cuộc tấn công.

#### Tiêu chí chấp nhận

1. THE Password_Reset_System SHALL không tiết lộ thông tin email có tồn tại hay không trong Database
2. WHEN User yêu cầu đặt lại mật khẩu nhiều lần, THE Password_Reset_System SHALL ghi đè Reset_Token cũ bằng Reset_Token mới
3. WHEN Reset_Token được sử dụng để đặt lại mật khẩu thành công, THE Password_Reset_System SHALL vô hiệu hóa Reset_Token
4. THE Password_Reset_System SHALL sử dụng hàm bin2hex và random_bytes để tạo Reset_Token
5. THE Password_Reset_System SHALL sử dụng mysqli_real_escape_string để ngăn chặn SQL injection
6. WHEN User truy cập Reset_Link đã hết hạn, THE Password_Reset_System SHALL hiển thị link "Yêu cầu link mới" dẫn đến trang quên mật khẩu
