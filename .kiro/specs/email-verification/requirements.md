# Tài liệu Yêu cầu - Xác thực Email khi Đăng ký

## Giới thiệu

Tính năng xác thực email đảm bảo rằng người dùng đăng ký tài khoản với địa chỉ email hợp lệ và có quyền truy cập vào email đó. Khi người dùng đăng ký, hệ thống sẽ gửi một email chứa link xác thực. Tài khoản chỉ được kích hoạt sau khi người dùng nhấn vào link này.

## Bảng thuật ngữ

- **Registration_System**: Hệ thống xử lý đăng ký tài khoản người dùng
- **Email_Service**: Dịch vụ gửi email (PHPMailer)
- **Verification_Token**: Chuỗi ký tự ngẫu nhiên duy nhất dùng để xác thực email
- **User_Account**: Tài khoản người dùng trong bảng nguoi_dung
- **Account_Status**: Trạng thái tài khoản (UNVERIFIED, ACTIVE, BLOCKED)
- **Verification_Link**: Đường link chứa token được gửi qua email
- **Database**: Cơ sở dữ liệu MySQL chứa thông tin người dùng

## Yêu cầu

### Yêu cầu 1: Tạo tài khoản với trạng thái chờ xác thực

**User Story:** Là một người dùng mới, tôi muốn đăng ký tài khoản, để có thể sử dụng các dịch vụ của FPT Shop sau khi xác thực email.

#### Tiêu chí chấp nhận

1. WHEN một người dùng gửi form đăng ký với email hợp lệ, THE Registration_System SHALL tạo User_Account với Account_Status là 'UNVERIFIED'
2. WHEN tạo User_Account, THE Registration_System SHALL sinh một Verification_Token ngẫu nhiên có độ dài 64 ký tự hexadecimal
3. WHEN tạo User_Account, THE Registration_System SHALL lưu Verification_Token vào cột verification_token trong Database
4. WHEN tạo User_Account, THE Registration_System SHALL hash mật khẩu bằng thuật toán SHA-1 trước khi lưu vào Database
5. IF email đã tồn tại trong Database, THEN THE Registration_System SHALL từ chối đăng ký và trả về thông báo lỗi 'email_exists'

### Yêu cầu 2: Gửi email xác thực

**User Story:** Là một người dùng mới, tôi muốn nhận email xác thực, để có thể kích hoạt tài khoản của mình.

#### Tiêu chí chấp nhận

1. WHEN User_Account được tạo thành công, THE Email_Service SHALL gửi email xác thực đến địa chỉ email đã đăng ký
2. THE Email_Service SHALL bao gồm Verification_Link trong nội dung email với định dạng: {base_url}/client/auth/verify-email?token={Verification_Token}
3. THE Email_Service SHALL sử dụng định dạng HTML cho nội dung email với tiêu đề "Xác thực tài khoản FPT Shop của bạn"
4. THE Email_Service SHALL bao gồm tên người dùng trong nội dung email
5. THE Email_Service SHALL bao gồm thông báo rằng Verification_Link sẽ hết hạn sau 24 giờ
6. IF Email_Service gửi email thành công, THEN THE Registration_System SHALL chuyển hướng người dùng đến trang thông báo kiểm tra email
7. IF Email_Service gửi email thất bại, THEN THE Registration_System SHALL chuyển hướng người dùng đến trang đăng ký với thông báo lỗi 'mail_failed'

### Yêu cầu 3: Xử lý xác thực email qua token

**User Story:** Là một người dùng mới, tôi muốn nhấn vào link trong email, để kích hoạt tài khoản của mình.

#### Tiêu chí chấp nhận

1. WHEN người dùng truy cập Verification_Link với token hợp lệ, THE Registration_System SHALL tìm User_Account có verification_token khớp và Account_Status là 'UNVERIFIED'
2. WHEN User_Account được tìm thấy, THE Registration_System SHALL cập nhật Account_Status thành 'ACTIVE'
3. WHEN User_Account được tìm thấy, THE Registration_System SHALL xóa Verification_Token khỏi Database bằng cách đặt giá trị rỗng
4. WHEN User_Account được tìm thấy, THE Registration_System SHALL cập nhật trường ngay_cap_nhat với thời gian hiện tại
5. WHEN xác thực thành công, THE Registration_System SHALL tự động đăng nhập người dùng bằng cách tạo session
6. WHEN xác thực thành công, THE Registration_System SHALL chuyển hướng người dùng đến trang thông báo xác thực thành công
7. IF token không tồn tại hoặc User_Account không ở trạng thái 'UNVERIFIED', THEN THE Registration_System SHALL chuyển hướng người dùng đến trang xác thực thất bại
8. IF token trống hoặc không được cung cấp, THEN THE Registration_System SHALL chuyển hướng người dùng đến trang đăng ký với thông báo lỗi 'invalid_token'

### Yêu cầu 4: Ngăn chặn đăng nhập với tài khoản chưa xác thực

**User Story:** Là quản trị viên hệ thống, tôi muốn ngăn người dùng chưa xác thực email đăng nhập, để đảm bảo tính bảo mật và chất lượng dữ liệu người dùng.

#### Tiêu chí chấp nhận

1. WHEN người dùng cố gắng đăng nhập, THE Registration_System SHALL kiểm tra Account_Status của User_Account
2. IF Account_Status không phải là 'ACTIVE', THEN THE Registration_System SHALL từ chối đăng nhập và trả về thông báo lỗi 'invalid_credentials'
3. WHEN đăng nhập thành công, THE Registration_System SHALL chỉ cho phép User_Account có Account_Status là 'ACTIVE'

### Yêu cầu 5: Hiển thị giao diện thông báo

**User Story:** Là một người dùng, tôi muốn thấy các thông báo rõ ràng về trạng thái xác thực, để biết các bước tiếp theo cần thực hiện.

#### Tiêu chí chấp nhận

1. WHEN người dùng hoàn tất đăng ký, THE Registration_System SHALL hiển thị trang thông báo "Kiểm tra email của bạn" với địa chỉ email đã đăng ký
2. WHEN người dùng xác thực thành công, THE Registration_System SHALL hiển thị trang "Xác thực thành công" với thông báo tài khoản đã được kích hoạt
3. WHEN xác thực thất bại, THE Registration_System SHALL hiển thị trang "Xác thực thất bại" với thông báo lỗi và hướng dẫn thử lại
4. THE Registration_System SHALL hiển thị thông báo lỗi phù hợp trên trang đăng ký khi có lỗi xảy ra (invalid_email, empty_password, empty_name, email_exists, registration_failed, mail_failed)

### Yêu cầu 6: Cấu trúc cơ sở dữ liệu

**User Story:** Là một nhà phát triển, tôi muốn có cấu trúc database phù hợp, để lưu trữ thông tin xác thực email.

#### Tiêu chí chấp nhận

1. THE Database SHALL có cột verification_token kiểu VARCHAR trong bảng nguoi_dung để lưu Verification_Token (đã có sẵn)
2. THE Database SHALL hỗ trợ giá trị 'UNVERIFIED' trong enum trang_thai của bảng nguoi_dung (đã có sẵn)
3. WHEN Verification_Token được tạo, THE Registration_System SHALL đảm bảo Verification_Token là duy nhất trong Database
4. WHEN User_Account được xác thực, THE Registration_System SHALL xóa Verification_Token khỏi Database

### Yêu cầu 7: Bảo mật và xử lý lỗi

**User Story:** Là quản trị viên hệ thống, tôi muốn hệ thống xử lý các trường hợp lỗi một cách an toàn, để bảo vệ dữ liệu người dùng.

#### Tiêu chí chấp nhận

1. WHEN xử lý Verification_Token từ URL, THE Registration_System SHALL escape token bằng mysqli_real_escape_string để ngăn SQL injection
2. WHEN tạo Verification_Token, THE Registration_System SHALL sử dụng hàm random_bytes() để tạo token ngẫu nhiên an toàn
3. IF người dùng cố gắng sử dụng token đã được sử dụng, THEN THE Registration_System SHALL từ chối xác thực
4. WHEN xử lý dữ liệu đầu vào, THE Registration_System SHALL validate email bằng filter_var với FILTER_VALIDATE_EMAIL
5. WHEN xử lý dữ liệu đầu vào, THE Registration_System SHALL kiểm tra các trường bắt buộc không được rỗng (email, password, name)

### Yêu cầu 8: Tự động đăng nhập sau xác thực

**User Story:** Là một người dùng mới, tôi muốn được tự động đăng nhập sau khi xác thực email, để không phải nhập lại thông tin đăng nhập.

#### Tiêu chí chấp nhận

1. WHEN xác thực email thành công, THE Registration_System SHALL tạo session cho người dùng
2. WHEN tạo session, THE Registration_System SHALL lưu thông tin người dùng (id, email, ho_ten, loai_tai_khoan, avatar_url) vào session
3. WHEN session được tạo, THE Registration_System SHALL đặt loai_tai_khoan là 'MEMBER'
4. WHEN người dùng được đăng nhập tự động, THE Registration_System SHALL chuyển hướng đến trang xác thực thành công với session đã được thiết lập
