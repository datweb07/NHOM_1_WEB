# Kế hoạch Triển khai: Chức năng Quên Mật khẩu

## Tổng quan

Triển khai chức năng quên mật khẩu cho phép người dùng đặt lại mật khẩu thông qua email verification. Hệ thống sẽ tạo reset token có thời hạn 24 giờ, gửi email chứa link đặt lại mật khẩu, và cho phép người dùng cập nhật mật khẩu mới.

## Nhiệm vụ

- [x] 1. Cập nhật database schema
  - Thêm 2 cột mới vào bảng nguoi_dung: forget_token và forget_token_created_at
  - Thêm index cho cột forget_token để tối ưu truy vấn
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [ ] 2. Triển khai model methods trong KhachHang
  - [x] 2.1 Implement method tao_reset_token()
    - Kiểm tra email có tồn tại trong database
    - Tạo reset token 64 ký tự hex bằng bin2hex(random_bytes(32))
    - Lưu token và timestamp vào database
    - Return token nếu email tồn tại, null nếu không
    - _Requirements: 2.3, 2.5, 2.6, 2.7_
  
  - [ ]* 2.2 Write property test for tao_reset_token()
    - **Property 6: Token generation format**
    - **Property 7: Token and timestamp persistence**
    - **Validates: Requirements 2.5, 2.6, 2.7**
  
  - [x] 2.3 Implement method xac_thuc_reset_token()
    - Trích xuất và escape token parameter
    - Query database để tìm token
    - Kiểm tra token có tồn tại
    - Kiểm tra thời gian hết hạn (24 giờ)
    - Return user data nếu hợp lệ, false nếu không
    - _Requirements: 3.1, 3.2, 3.4, 3.5_
  
  - [ ]* 2.4 Write property test for xac_thuc_reset_token()
    - **Property 12: Token existence validation**
    - **Property 14: Token expiry check**
    - **Validates: Requirements 3.2, 3.4, 3.5**
  
  - [x] 2.5 Implement method dat_lai_mat_khau()
    - Validate password không rỗng và >= 6 ký tự
    - Hash password bằng SHA-1
    - Update mat_khau trong database
    - Xóa forget_token và forget_token_created_at (set NULL)
    - Return true nếu thành công
    - _Requirements: 4.3, 4.5, 4.9, 4.10, 4.11, 4.12_
  
  - [ ]* 2.6 Write property test for dat_lai_mat_khau()
    - **Property 19: Password hashing with SHA-1**
    - **Property 21: Token invalidation after use**
    - **Validates: Requirements 4.9, 4.11, 4.12**

- [x] 3. Checkpoint - Kiểm tra model methods
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 4. Triển khai controller methods trong AuthController
  - [x] 4.1 Implement method requestPasswordReset()
    - Validate email format
    - Gọi KhachHang->tao_reset_token()
    - Tạo reset URL với token
    - Gửi email chứa reset link
    - Redirect đến check-email page với message nhất quán
    - _Requirements: 2.1, 2.2, 2.8, 2.9, 2.10_
  
  - [ ]* 4.2 Write unit tests for requestPasswordReset()
    - Test với email hợp lệ
    - Test với email không hợp lệ
    - Test với email không tồn tại
    - Test email service failure
    - _Requirements: 2.1, 2.2, 2.4, 2.10_
  
  - [x] 4.3 Implement method verifyResetToken()
    - Trích xuất token từ URL query parameter
    - Gọi KhachHang->xac_thuc_reset_token()
    - Nếu token hợp lệ: hiển thị form reset password
    - Nếu token không hợp lệ: redirect với error message
    - Nếu token hết hạn: redirect với error message và link request lại
    - _Requirements: 3.1, 3.2, 3.3, 3.5, 3.6, 7.6_
  
  - [ ]* 4.4 Write unit tests for verifyResetToken()
    - Test với token hợp lệ
    - Test với token không tồn tại
    - Test với token đã hết hạn
    - Test với token rỗng
    - _Requirements: 3.2, 3.3, 3.5_
  
  - [x] 4.5 Implement method resetPassword()
    - Validate password mới không rỗng
    - Validate password >= 6 ký tự
    - Validate password và confirm password khớp nhau
    - Gọi KhachHang->dat_lai_mat_khau()
    - Redirect đến reset-success page nếu thành công
    - Display error message nếu thất bại
    - _Requirements: 4.3, 4.4, 4.5, 4.6, 4.7, 4.8, 4.13_
  
  - [ ]* 4.6 Write unit tests for resetPassword()
    - Test với password hợp lệ
    - Test với password rỗng
    - Test với password < 6 ký tự
    - Test với password không khớp
    - _Requirements: 4.3, 4.5, 4.7_

- [x] 5. Checkpoint - Kiểm tra controller methods
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 6. Tạo email template
  - [x] 6.1 Create file app/views/emails/password_reset.php
    - Tạo HTML email template với styling
    - Hiển thị tên người dùng (personalization)
    - Hiển thị nút "Đặt lại mật khẩu" với reset link
    - Hiển thị reset link dạng text (fallback)
    - Hiển thị thông tin token hết hạn sau 24 giờ
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_
  
  - [ ]* 6.2 Write property test for email template
    - **Property 23: Email HTML format and subject**
    - **Property 24: Email personalization**
    - **Property 25: Email reset link content**
    - **Property 26: Email expiry information**
    - **Validates: Requirements 6.1, 6.2, 6.3, 6.5**

- [ ] 7. Tạo view files
  - [x] 7.1 Create app/views/client/auth/forgot_password.php
    - Form nhập email
    - Nút submit "Gửi link đặt lại mật khẩu"
    - Display error messages nếu có
    - _Requirements: 1.3, 1.4_
  
  - [x] 7.2 Create app/views/client/auth/check_email.php
    - Hiển thị message: "Nếu email tồn tại, bạn sẽ nhận được link đặt lại mật khẩu"
    - _Requirements: 2.10_
  
  - [x] 7.3 Create app/views/client/auth/reset_password.php
    - Form nhập mật khẩu mới và xác nhận mật khẩu
    - Hidden input chứa token
    - Nút submit "Đặt lại mật khẩu"
    - Display error messages nếu có
    - _Requirements: 3.6, 4.1, 4.2_
  
  - [x] 7.4 Create app/views/client/auth/reset_success.php
    - Hiển thị message "Đặt lại mật khẩu thành công"
    - Auto redirect đến login page sau 3 giây
    - _Requirements: 4.13, 4.14_
  
  - [ ]* 7.5 Write integration tests for views
    - Test form rendering
    - Test error message display
    - Test success message display
    - _Requirements: 1.3, 1.4, 3.6, 4.13_

- [ ] 8. Thêm link "Quên mật khẩu?" vào trang login
  - [x] 8.1 Update app/views/client/auth/login.php
    - Thêm link "Quên mật khẩu?" dưới form login
    - Link navigate đến /client/auth/forgot-password
    - _Requirements: 1.1, 1.2_

- [ ] 9. Cấu hình routes
  - [x] 9.1 Update app/routes/client/client.php
    - Thêm route GET /client/auth/forgot-password (hiển thị form)
    - Thêm route POST /client/auth/forgot-password (xử lý request)
    - Thêm route GET /client/auth/check-email (hiển thị message)
    - Thêm route GET /client/auth/reset-password (verify token và hiển thị form)
    - Thêm route POST /client/auth/reset-password (xử lý reset)
    - Thêm route GET /client/auth/reset-success (hiển thị success)
    - _Requirements: 1.2, 2.1, 2.10, 3.1, 4.1, 4.13_

- [x] 10. Checkpoint - Integration testing
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 11. Tích hợp email service
  - [x] 11.1 Update AuthController->requestPasswordReset()
    - Sử dụng sendMail() function để gửi email
    - Load email template từ password_reset.php
    - Pass variables: userName, resetLink, expiryHours
    - Handle email service errors
    - _Requirements: 2.9, 6.6, 6.7_
  
  - [ ]* 11.2 Write unit tests for email integration
    - Test email sending success
    - Test email sending failure
    - Test email content generation
    - _Requirements: 2.9, 6.7_

- [ ] 12. Security enhancements
  - [x] 12.1 Add SQL injection prevention
    - Sử dụng mysqli_real_escape_string cho token parameter
    - Validate token format (64 hex chars) trước khi query
    - _Requirements: 7.4, 7.5_
  
  - [x] 12.2 Add email enumeration prevention
    - Ensure consistent response message
    - Return same message cho email exists và not exists
    - _Requirements: 7.1_
  
  - [ ]* 12.3 Write security tests
    - Test SQL injection attempts
    - Test email enumeration prevention
    - Test token format validation
    - _Requirements: 7.1, 7.5_

- [ ] 13. Error handling và validation
  - [x] 13.1 Add comprehensive error handling
    - Validate email format trong controller
    - Validate password requirements trong controller
    - Handle database errors gracefully
    - Display user-friendly error messages
    - _Requirements: 2.1, 2.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8_
  
  - [ ]* 13.2 Write validation tests
    - Test email format validation
    - Test password validation rules
    - Test error message display
    - _Requirements: 2.1, 2.2, 4.3, 4.5, 4.7_

- [x] 14. Final checkpoint - End-to-end testing
  - Test complete password reset flow từ forgot password đến reset success
  - Test với multiple scenarios: valid token, expired token, invalid token
  - Test email sending và receiving
  - Ensure all tests pass, ask the user if questions arise.

## Ghi chú

- Tasks đánh dấu `*` là optional và có thể bỏ qua để triển khai nhanh hơn
- Mỗi task tham chiếu đến requirements cụ thể để đảm bảo traceability
- Checkpoints đảm bảo validation từng bước
- Property tests validate universal correctness properties
- Unit tests validate specific examples và edge cases
- Cần chạy database migration trước khi bắt đầu implementation
