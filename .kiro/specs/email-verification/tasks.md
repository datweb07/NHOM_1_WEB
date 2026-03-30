# Implementation Plan: Email Verification

## Overview

Tính năng xác thực email đảm bảo người dùng đăng ký với địa chỉ email hợp lệ. Hệ thống sẽ tạo tài khoản với trạng thái UNVERIFIED, gửi email chứa link xác thực, và chỉ kích hoạt tài khoản khi người dùng nhấn vào link đó. Sau khi xác thực thành công, người dùng sẽ được tự động đăng nhập.

Database đã có sẵn cột `verification_token` và enum `trang_thai` với giá trị 'UNVERIFIED', 'ACTIVE', 'BLOCKED'.

## Tasks

- [ ] 1. Implement chức năng đăng ký với email verification
  - [ ] 1.1 Cập nhật method `dang_ky()` trong KhachHang model
    - Kiểm tra email đã tồn tại (return null nếu có)
    - Sinh verification token 64 ký tự hex bằng `bin2hex(random_bytes(32))`
    - Hash mật khẩu bằng SHA-1
    - Tạo tài khoản với `trang_thai = 'UNVERIFIED'` và `loai_tai_khoan = 'MEMBER'`
    - Lưu verification_token vào database
    - Return array chứa `{id, token}`
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 6.3_

  - [ ]* 1.2 Write property test for token generation
    - **Property 2: Token có định dạng 64 ký tự hexadecimal**
    - **Property 3: Token được lưu vào database**
    - **Property 22: Token uniqueness**
    - **Validates: Requirements 1.2, 1.3, 6.3**

  - [ ]* 1.3 Write property test for account creation
    - **Property 1: Tài khoản mới có trạng thái UNVERIFIED**
    - **Property 4: Mật khẩu được hash bằng SHA-1**
    - **Property 5: Email phải unique**
    - **Property 28: New accounts have MEMBER role**
    - **Validates: Requirements 1.1, 1.4, 1.5, 8.3**

- [ ] 2. Implement email service để gửi verification email
  - [ ] 2.1 Cập nhật method `register()` trong AuthController
    - Validate input: email format, password không rỗng, name không rỗng
    - Gọi `KhachHang::dang_ky()` để tạo tài khoản
    - Xây dựng verification URL với format: `{base_url}/client/auth/verify-email?token={token}`
    - Tạo nội dung email HTML với tên người dùng và verification link
    - Bao gồm thông báo link hết hạn sau 24 giờ
    - Gọi `sendMail()` để gửi email với subject "Xác thực tài khoản FPT Shop của bạn"
    - Redirect đến `/client/auth/check-email` nếu thành công
    - Redirect đến `/client/auth/register?error=mail_failed` nếu thất bại
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

  - [ ]* 2.2 Write property test for email sending
    - **Property 6: Email xác thực được gửi sau đăng ký thành công**
    - **Property 7: Email chứa verification link đúng format**
    - **Property 8: Email có format HTML và subject đúng**
    - **Property 9: Email chứa tên người dùng**
    - **Property 10: Email chứa thông báo hết hạn 24 giờ**
    - **Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5**

  - [ ]* 2.3 Write unit tests for validation errors
    - Test invalid email format → error 'invalid_email'
    - Test empty password → error 'empty_password'
    - Test empty name → error 'empty_name'
    - Test email exists → error 'email_exists'
    - _Requirements: 7.4, 7.5_

- [ ] 3. Checkpoint - Đảm bảo đăng ký và gửi email hoạt động
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 4. Implement xác thực email qua token
  - [ ] 4.1 Implement method `xac_thuc_email()` trong KhachHang model
    - Escape token bằng `mysqli_real_escape_string()` để ngăn SQL injection
    - Query tìm user với `verification_token = token` và `trang_thai = 'UNVERIFIED'`
    - Nếu tìm thấy: update `trang_thai = 'ACTIVE'`, xóa token (set ''), update `ngay_cap_nhat`
    - Load user data vào object properties
    - Return true nếu thành công, false nếu không tìm thấy
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 7.1, 7.3_

  - [ ]* 4.2 Write property test for email verification
    - **Property 13: Token hợp lệ tìm được tài khoản UNVERIFIED**
    - **Property 14: Xác thực chuyển trạng thái sang ACTIVE**
    - **Property 15: Token được xóa sau xác thực**
    - **Property 16: Timestamp được cập nhật sau xác thực**
    - **Property 23: SQL injection prevention**
    - **Property 25: Token reuse prevention**
    - **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 7.1, 7.3**

  - [ ] 4.3 Implement method `verifyEmail()` trong AuthController
    - Validate token không rỗng (redirect với error 'invalid_token' nếu rỗng)
    - Gọi `KhachHang::xac_thuc_email()`
    - Nếu thất bại: redirect đến `/client/auth/verify-failed`
    - Nếu thành công: tạo session với user data, redirect đến `/client/auth/verified`
    - _Requirements: 3.5, 3.6, 3.7, 3.8, 8.1, 8.2, 8.4_

  - [ ]* 4.4 Write property test for auto-login after verification
    - **Property 17: Session được tạo sau xác thực thành công**
    - **Property 29: Auto-login redirect với session**
    - **Validates: Requirements 3.5, 8.1, 8.2, 8.4**

  - [ ]* 4.5 Write unit tests for verification error cases
    - Test empty token → redirect với error 'invalid_token'
    - Test invalid token → redirect đến verify-failed
    - Test token đã sử dụng → redirect đến verify-failed
    - _Requirements: 3.7, 3.8, 7.3_

- [ ] 5. Checkpoint - Đảm bảo toàn bộ verification flow hoạt động
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 6. Cập nhật login để kiểm tra trạng thái ACTIVE
  - [ ] 6.1 Cập nhật method `dang_nhap()` trong KhachHang model
    - Thêm điều kiện `trang_thai = 'ACTIVE'` vào SQL query
    - Chỉ cho phép login nếu account status là ACTIVE
    - _Requirements: 4.1, 4.2, 4.3_

  - [ ]* 6.2 Write property test for login verification check
    - **Property 21: Login kiểm tra trạng thái ACTIVE**
    - **Validates: Requirements 4.1, 4.2, 4.3**

  - [ ]* 6.3 Write unit tests for login with unverified account
    - Test login với UNVERIFIED account → return false
    - Test login với BLOCKED account → return false
    - Test login với ACTIVE account → return true
    - _Requirements: 4.2_

- [ ] 7. Tạo các view pages cho email verification flow
  - [ ] 7.1 Tạo view `app/views/client/auth/check_email.php`
    - Hiển thị thông báo "Kiểm tra email của bạn"
    - Hiển thị địa chỉ email đã đăng ký (từ query parameter)
    - Hướng dẫn người dùng check inbox và spam folder
    - _Requirements: 5.1_

  - [ ] 7.2 Tạo view `app/views/client/auth/verified.php`
    - Hiển thị thông báo "Xác thực thành công"
    - Thông báo tài khoản đã được kích hoạt
    - Link đến trang profile hoặc trang chủ
    - _Requirements: 5.2_

  - [ ] 7.3 Tạo view `app/views/client/auth/verify_failed.php`
    - Hiển thị thông báo "Xác thực thất bại"
    - Giải thích lý do có thể (link hết hạn, đã sử dụng, không hợp lệ)
    - Hướng dẫn thử lại hoặc liên hệ support
    - _Requirements: 5.3_

  - [x] 7.4 Cập nhật view `app/views/client/auth/register.php`
    - Thêm xử lý hiển thị error messages từ query parameters
    - Hiển thị error: invalid_email, empty_password, empty_name, email_exists, registration_failed, mail_failed
    - _Requirements: 5.4_

  - [x] 7.5 Cập nhật view `app/views/client/auth/login.php`
    - Thêm xử lý hiển thị error message 'invalid_credentials'
    - _Requirements: 5.4_

- [x] 8. Cập nhật routes để hỗ trợ email verification
  - [ ] 8.1 Thêm route GET `/client/auth/check-email` trong `app/routes/client/client.php`
    - Load view check_email.php
    - _Requirements: 2.6_

  - [ ] 8.2 Thêm route GET `/client/auth/verify-email` trong `app/routes/client/client.php`
    - Gọi `AuthController::verifyEmail()` với token từ query parameter
    - _Requirements: 3.1_

  - [ ] 8.3 Thêm route GET `/client/auth/verified` trong `app/routes/client/client.php`
    - Load view verified.php
    - _Requirements: 3.6_

  - [ ] 8.4 Thêm route GET `/client/auth/verify-failed` trong `app/routes/client/client.php`
    - Load view verify_failed.php
    - _Requirements: 3.7_

- [ ] 9. Testing và validation
  - [ ]* 9.1 Write property test for input validation
    - **Property 26: Email validation**
    - **Property 27: Required fields validation**
    - **Validates: Requirements 7.4, 7.5**

  - [ ]* 9.2 Write integration tests cho complete flow
    - Test complete registration → email → verification → auto-login flow
    - Test error handling ở mỗi bước
    - Test redirect logic
    - _Requirements: All_

  - [ ]* 9.3 Write property test for redirect behaviors
    - **Property 11: Redirect đến check-email sau gửi mail thành công**
    - **Property 12: Redirect với error khi gửi mail thất bại**
    - **Property 18: Redirect đến verified page sau xác thực thành công**
    - **Property 19: Token invalid redirect đến verify-failed**
    - **Property 20: Empty token redirect với error**
    - **Validates: Requirements 2.6, 2.7, 3.6, 3.7, 3.8**

- [ ] 10. Final checkpoint và documentation
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties (29 properties total)
- Unit tests validate specific examples and edge cases
- Database schema đã có sẵn verification_token và trang_thai với giá trị UNVERIFIED
- Email service (PHPMailer) đã có sẵn trong project, chỉ cần sử dụng function `sendMail()`
- Session management đã có sẵn trong `App\Core\Session`, chỉ cần gọi `Session::login()`
