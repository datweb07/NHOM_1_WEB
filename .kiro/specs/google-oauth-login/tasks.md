# Implementation Plan: Google OAuth Login

## Overview

Triển khai tính năng đăng nhập Google OAuth thông qua Supabase Auth API cho ứng dụng PHP. Hệ thống cho phép người dùng đăng nhập bằng tài khoản Google, tự động tạo tài khoản mới nếu chưa tồn tại, và tích hợp với hệ thống session hiện tại.

## Tasks

- [x] 1. Cập nhật database schema cho OAuth support
  - Chạy ALTER TABLE để thêm supabase_id, auth_provider
  - Modify mat_khau cho phép NULL
  - Thêm UNIQUE constraints cho supabase_id và email
  - Verify migration thành công bằng DESCRIBE nguoi_dung
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 10.6_

- [x] 2. Setup environment variables và dependencies
  - Thêm SUPABASE_URL, SUPABASE_JWT_SECRET, APP_URL vào file .env
  - Cập nhật .env.example với các biến môi trường mẫu
  - Cài đặt Firebase JWT library (firebase/php-jwt) nếu chưa có
  - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [x] 3. Cập nhật SupabaseAuthService với OAuth methods
  - [x] 3.1 Implement getGoogleLoginUrl() method
    - Đọc SUPABASE_URL và APP_URL từ environment
    - Tạo redirect URL trỏ đến callback.php
    - Return OAuth authorization URL với provider=google
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6_

  - [x] 3.2 Implement verifyUserToken() method
    - Đọc SUPABASE_JWT_SECRET từ environment
    - Sử dụng Firebase JWT library để decode token với HS256
    - Return user data object nếu token hợp lệ
    - Return null và log error nếu token invalid/expired
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

  - [ ]* 3.3 Write unit tests for SupabaseAuthService
    - Test getGoogleLoginUrl() returns correct URL format
    - Test verifyUserToken() with valid token
    - Test verifyUserToken() with invalid token
    - Test verifyUserToken() with expired token

- [x] 4. Checkpoint - Verify SupabaseAuthService methods
  - Ensure all tests pass, ask the user if questions arise.

- [x] 5. Tạo callback handler
  - [x] 5.1 Create callback.php file
    - Tạo file tại /app/views/client/auth/callback.php
    - Hiển thị loading indicator
    - Sử dụng JavaScript để đọc access_token từ window.location.hash
    - Parse URL fragment parameters
    - _Requirements: 4.1, 4.2, 4.3_

  - [x] 5.2 Implement error handling trong callback
    - Kiểm tra error parameter từ Supabase
    - Redirect về login với error message nếu có lỗi
    - Redirect về login nếu không có token
    - _Requirements: 4.6, 8.1, 8.2_

  - [x] 5.3 Implement token forwarding
    - Gửi access_token đến process_login.php bằng fetch POST
    - Handle response và redirect theo kết quả
    - Handle network errors
    - _Requirements: 4.4, 4.5, 8.3_

- [x] 6. Tạo process_login handler với logic 3-step lookup
  - [x] 6.1 Create process_login.php file
    - Tạo file tại /app/views/client/auth/process_login.php
    - Validate POST request method
    - Parse JSON input để lấy access_token
    - Return JSON responses
    - _Requirements: 5.1, 5.2_

  - [x] 6.2 Implement token verification và extract data
    - Gọi SupabaseAuthService::verifyUserToken()
    - Extract supabase_id (sub), email, name, avatar_url từ token payload
    - Validate supabase_id và email tồn tại trong token
    - Return error nếu token invalid hoặc thiếu data
    - _Requirements: 5.2, 5.7, 9.1, 9.2_

  - [x] 6.3 Implement Step 1: Tìm user theo supabase_id
    - Query database: SELECT * FROM nguoi_dung WHERE supabase_id = ?
    - Nếu tìm thấy: Cập nhật avatar_url nếu khác, load user data
    - Nếu không tìm thấy: Chuyển sang Step 2
    - _Requirements: 11.1, 11.2, 11.3, 11.10_

  - [x] 6.4 Implement Step 2: Tìm user theo email và liên kết
    - Query database: SELECT * FROM nguoi_dung WHERE email = ?
    - Nếu tìm thấy: UPDATE supabase_id, auth_provider='GOOGLE', avatar_url
    - Load user data sau khi update
    - Nếu không tìm thấy: Chuyển sang Step 3
    - _Requirements: 11.4, 11.5, 11.10_

  - [x] 6.5 Implement Step 3: Tạo user mới
    - INSERT INTO nguoi_dung với supabase_id, auth_provider='GOOGLE'
    - Set email, ho_ten, avatar_url, mat_khau=NULL
    - Set loai_tai_khoan='MEMBER', trang_thai='ACTIVE'
    - Return error nếu create failed
    - _Requirements: 11.6, 11.7, 11.8, 11.9_

  - [x] 6.6 Implement session creation
    - Gọi Session::login() với user data
    - Lưu id, email, ho_ten, loai_tai_khoan, avatar_url vào session
    - Return success response với redirect URL
    - _Requirements: 5.3, 5.4, 5.5, 5.6, 7.1, 7.2_

  - [ ]* 6.7 Write integration tests for process_login
    - Test Step 1: Existing user với supabase_id
    - Test Step 2: Existing user với email (liên kết tài khoản)
    - Test Step 3: New user creation
    - Test với invalid token return error
    - Test session được tạo đúng format

- [x] 7. Checkpoint - Test OAuth flow end-to-end
  - Ensure all tests pass, ask the user if questions arise.

- [x] 8. Tích hợp nút đăng nhập vào login page
  - [x] 8.1 Update login.php view
    - Thêm divider "Hoặc đăng nhập với" sau form đăng nhập
    - Thêm nút "Đăng nhập với Google" với href từ getGoogleLoginUrl()
    - Style nút phù hợp với thiết kế hiện tại
    - Thêm Google icon nếu có
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

  - [x] 8.2 Implement error message display
    - Hiển thị error messages từ query parameters
    - Map error codes sang messages tiếng Việt
    - Style alert phù hợp với thiết kế
    - _Requirements: 8.1, 8.2, 8.3, 8.4_

- [x] 9. Verify session compatibility
  - [x] 9.1 Test Session methods với OAuth login
    - Verify Session::isLoggedIn() returns true
    - Verify Session::getUserEmail(), getUserName(), getUserAvatar() hoạt động
    - Verify Session::logout() xóa OAuth session
    - _Requirements: 7.3, 7.4, 7.5_

  - [ ]* 9.2 Write tests for session compatibility
    - Test session data format giống đăng nhập thông thường
    - Test logout flow xóa session đúng cách

- [x] 10. Implement security measures
  - [x] 10.1 Add security validations
    - Validate JWT signature trong verifyUserToken()
    - Check token expiration time
    - Không lưu access_token vào database hoặc logs
    - _Requirements: 9.1, 9.2, 9.4_

  - [x] 10.2 Add security documentation
    - Document callback URL cần whitelist trong Supabase dashboard
    - Document HTTPS requirement cho production
    - Document SUPABASE_JWT_SECRET không được commit vào git
    - _Requirements: 9.3, 9.5, 9.6_

- [x] 11. Final testing và error handling
  - [x] 11.1 Add comprehensive error logging
    - Log tất cả OAuth errors vào error_log
    - Log token verification failures
    - Log user creation/update failures
    - _Requirements: 8.5_

  - [ ]* 11.2 Manual testing checklist
    - Test complete OAuth flow với Google account
    - Test Step 1: User đã có supabase_id
    - Test Step 2: User có email nhưng chưa có supabase_id (liên kết)
    - Test Step 3: User hoàn toàn mới
    - Test user denial scenario
    - Test network error handling
    - Test invalid/expired token scenarios

- [x] 12. Final checkpoint - Complete integration test
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Task 1 (Database migration) MUST be completed first
- SupabaseAuthService đã tồn tại, chỉ cần cập nhật methods
- Session class đã tồn tại và tương thích với OAuth flow
- KhachHang model đã có methods cần thiết cho user management
- Logic 3-step lookup: supabase_id → email (link) → create new
