# Requirements Document

## Introduction

Tính năng đăng nhập Google OAuth thông qua Supabase cho phép người dùng đăng nhập vào ứng dụng PHP web bằng tài khoản Google của họ. Hệ thống sử dụng Supabase Auth API làm trung gian xác thực, Firebase JWT library để giải mã token, và JavaScript để xử lý URL fragment chứa access token.

## Glossary

- **SupabaseAuthService**: Service PHP xử lý tương tác với Supabase Auth API
- **Google_OAuth_Provider**: Nhà cung cấp xác thực Google thông qua Supabase
- **Access_Token**: JWT token được Supabase trả về sau khi xác thực thành công
- **Callback_Handler**: Script PHP xử lý callback từ Supabase sau khi đăng nhập
- **Session_Manager**: Hệ thống quản lý session người dùng (App\Core\Session)
- **Login_Button**: Nút đăng nhập Google trên giao diện người dùng
- **User_Profile**: Thông tin người dùng bao gồm email, name, avatar
- **Supabase_ID**: UUID duy nhất từ Supabase để định danh người dùng (trường sub trong JWT)
- **Auth_Provider**: Nguồn tạo tài khoản (LOCAL, GOOGLE, FACEBOOK)

## Requirements

### Requirement 1: Tạo URL chuyển hướng đến Google OAuth

**User Story:** Là một người dùng, tôi muốn nhấn nút "Đăng nhập với Google" để được chuyển đến trang đăng nhập Google, để tôi có thể xác thực bằng tài khoản Google của mình.

#### Acceptance Criteria

1. THE SupabaseAuthService SHALL cung cấp method getGoogleLoginUrl() trả về URL chuyển hướng đến Google OAuth
2. THE getGoogleLoginUrl() SHALL sử dụng SUPABASE_URL từ biến môi trường để tạo base URL
3. THE getGoogleLoginUrl() SHALL sử dụng APP_URL từ biến môi trường để tạo redirect URL
4. THE getGoogleLoginUrl() SHALL tạo URL theo format: {SUPABASE_URL}/auth/v1/authorize?provider=google&redirect_to={encoded_callback_url}
5. THE getGoogleLoginUrl() SHALL encode redirect URL bằng urlencode()
6. THE redirect URL SHALL trỏ đến /app/views/client/auth/callback.php

### Requirement 2: Giải mã và xác thực JWT Token

**User Story:** Là một hệ thống backend, tôi cần giải mã JWT token từ Supabase để lấy thông tin người dùng, để tôi có thể tạo session cho người dùng.

#### Acceptance Criteria

1. THE SupabaseAuthService SHALL cung cấp method verifyUserToken($jwtToken) để giải mã token
2. THE verifyUserToken() SHALL sử dụng SUPABASE_JWT_SECRET từ biến môi trường làm secret key
3. THE verifyUserToken() SHALL sử dụng Firebase JWT library với thuật toán HS256
4. WHEN token hợp lệ, THE verifyUserToken() SHALL trả về object chứa thông tin User
5. IF token không hợp lệ hoặc hết hạn, THEN THE verifyUserToken() SHALL trả về null
6. IF xảy ra exception khi giải mã, THEN THE verifyUserToken() SHALL log error và trả về null

### Requirement 3: Hiển thị nút đăng nhập Google

**User Story:** Là một người dùng, tôi muốn thấy nút "Đăng nhập với Google" trên trang login, để tôi có thể chọn phương thức đăng nhập phù hợp.

#### Acceptance Criteria

1. THE Login_Button SHALL được hiển thị trên trang /client/auth/login
2. THE Login_Button SHALL là thẻ <a> với href từ SupabaseAuthService::getGoogleLoginUrl()
3. THE Login_Button SHALL có giao diện phù hợp với thiết kế hiện tại của trang login
4. THE Login_Button SHALL được đặt sau form đăng nhập thông thường
5. THE Login_Button SHALL có text "Đăng nhập với Google" hoặc tương tự

### Requirement 4: Xử lý callback từ Supabase

**User Story:** Là một hệ thống, tôi cần nhận và xử lý callback từ Supabase sau khi người dùng đăng nhập Google thành công, để tôi có thể lấy access token.

#### Acceptance Criteria

1. THE Callback_Handler SHALL được tạo tại /app/views/client/auth/callback.php
2. WHEN Supabase redirect về callback URL, THE Callback_Handler SHALL nhận access_token từ URL fragment (#)
3. THE Callback_Handler SHALL sử dụng JavaScript để đọc token từ window.location.hash
4. THE Callback_Handler SHALL gửi token đến process_login.php bằng HTTP POST request
5. THE Callback_Handler SHALL hiển thị loading indicator trong khi xử lý
6. IF không có token trong URL fragment, THEN THE Callback_Handler SHALL redirect về trang login với error message

### Requirement 5: Xử lý đăng nhập và tạo session

**User Story:** Là một hệ thống backend, tôi cần xử lý token từ callback và tạo session cho người dùng, để người dùng có thể truy cập các trang yêu cầu đăng nhập.

#### Acceptance Criteria

1. THE system SHALL tạo file process_login.php để xử lý POST request từ callback
2. WHEN nhận được token, THE process_login.php SHALL gọi SupabaseAuthService::verifyUserToken()
3. WHEN token hợp lệ, THE process_login.php SHALL lưu thông tin user vào Session
4. THE process_login.php SHALL lưu email, name (ho_ten), và avatar_url vào Session
5. THE process_login.php SHALL set loai_tai_khoan là 'MEMBER' cho user đăng nhập qua Google
6. WHEN session được tạo thành công, THE process_login.php SHALL redirect đến /client/profile
7. IF token không hợp lệ, THEN THE process_login.php SHALL redirect về trang login với error message
8. IF user chưa tồn tại trong database, THEN THE process_login.php SHALL tự động tạo tài khoản mới

### Requirement 6: Quản lý biến môi trường

**User Story:** Là một developer, tôi cần cấu hình các biến môi trường cần thiết, để hệ thống có thể kết nối với Supabase.

#### Acceptance Criteria

1. THE system SHALL yêu cầu biến môi trường SUPABASE_URL trong file .env
2. THE system SHALL yêu cầu biến môi trường SUPABASE_JWT_SECRET trong file .env
3. THE system SHALL yêu cầu biến môi trường APP_URL trong file .env
4. THE system SHALL cung cấp file .env.example với các biến môi trường mẫu
5. THE EnvSetup::env() SHALL có thể đọc các biến môi trường Supabase
6. IF thiếu biến môi trường bắt buộc, THEN THE system SHALL hiển thị error message rõ ràng

### Requirement 7: Tích hợp với hệ thống Session hiện tại

**User Story:** Là một hệ thống, tôi cần đảm bảo Google OAuth login tương thích với hệ thống session hiện tại, để người dùng có trải nghiệm nhất quán.

#### Acceptance Criteria

1. THE Google OAuth login SHALL sử dụng App\Core\Session::login() để tạo session
2. THE session data format SHALL giống với đăng nhập thông thường (email, ho_ten, loai_tai_khoan, avatar_url)
3. THE Session::isLoggedIn() SHALL trả về true sau khi đăng nhập Google thành công
4. THE Session::getUserEmail(), getUserName(), getUserAvatar() SHALL hoạt động bình thường
5. THE logout flow SHALL xóa session Google OAuth giống như đăng nhập thông thường

### Requirement 8: Xử lý lỗi và edge cases

**User Story:** Là một người dùng, tôi muốn nhận được thông báo lỗi rõ ràng khi đăng nhập thất bại, để tôi biết cách khắc phục.

#### Acceptance Criteria

1. IF người dùng từ chối cấp quyền trên Google, THEN THE system SHALL redirect về login với message "Bạn đã từ chối cấp quyền"
2. IF Supabase trả về error, THEN THE system SHALL log error và hiển thị message "Đăng nhập thất bại, vui lòng thử lại"
3. IF network error xảy ra, THEN THE system SHALL hiển thị message "Lỗi kết nối, vui lòng kiểm tra internet"
4. IF token expired trong quá trình xử lý, THEN THE system SHALL redirect về login với message "Phiên đăng nhập hết hạn"
5. THE system SHALL log tất cả errors vào error_log để debug

### Requirement 9: Bảo mật và validation

**User Story:** Là một hệ thống, tôi cần đảm bảo quá trình đăng nhập Google OAuth an toàn, để bảo vệ thông tin người dùng.

#### Acceptance Criteria

1. THE system SHALL validate JWT token signature trước khi tin tưởng thông tin trong token
2. THE system SHALL kiểm tra token expiration time
3. THE callback URL SHALL được whitelist trong Supabase dashboard
4. THE system SHALL không lưu access_token vào database hoặc log files
5. THE system SHALL sử dụng HTTPS cho production environment
6. THE SUPABASE_JWT_SECRET SHALL không được commit vào git repository

### Requirement 10: Cập nhật database schema cho OAuth

**User Story:** Là một hệ thống, tôi cần database schema hỗ trợ đăng nhập qua nhiều providers, để có thể quản lý người dùng từ nhiều nguồn khác nhau.

#### Acceptance Criteria

1. THE bảng nguoi_dung SHALL có trường mat_khau cho phép NULL
2. THE bảng nguoi_dung SHALL có trường supabase_id (CHAR(36)) để lưu UUID từ Supabase
3. THE bảng nguoi_dung SHALL có trường auth_provider ENUM('LOCAL','GOOGLE','FACEBOOK') với default 'LOCAL'
4. THE supabase_id SHALL có UNIQUE constraint để tránh duplicate
5. THE email SHALL có UNIQUE constraint để tránh duplicate
6. THE system SHALL có index trên supabase_id và email để tăng tốc độ query

### Requirement 11: Tự động tạo/liên kết tài khoản từ Google OAuth

**User Story:** Là một người dùng, tôi muốn tự động có tài khoản hoặc liên kết tài khoản hiện tại khi đăng nhập Google, để tôi không phải đăng ký thủ công.

#### Acceptance Criteria

1. WHEN user đăng nhập Google, THE system SHALL extract supabase_id (sub) từ token
2. THE system SHALL tìm user theo supabase_id trước tiên
3. IF tìm thấy user theo supabase_id, THEN THE system SHALL cho đăng nhập thành công
4. IF không tìm thấy theo supabase_id, THEN THE system SHALL tìm theo email
5. IF tìm thấy user theo email, THEN THE system SHALL cập nhật supabase_id và auth_provider='GOOGLE' để liên kết tài khoản
6. IF không tìm thấy cả supabase_id và email, THEN THE system SHALL tạo user mới với supabase_id, email, ho_ten, avatar_url, auth_provider='GOOGLE'
7. THE system SHALL set mat_khau=NULL cho user tạo từ Google OAuth
8. THE system SHALL set loai_tai_khoan='MEMBER' cho user mới
9. THE system SHALL set trang_thai='ACTIVE' cho user mới (đã verify qua Google)
10. WHEN cập nhật user hiện tại, THE system SHALL cập nhật avatar_url nếu khác với database
