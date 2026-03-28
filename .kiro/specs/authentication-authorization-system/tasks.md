# Implementation Plan: Authentication Authorization System

## Overview

Triển khai hệ thống xác thực và phân quyền cho ứng dụng PHP thuần với hai luồng riêng biệt: khách hàng (MEMBER) và quản trị viên (ADMIN). Sử dụng session-based authentication với simulated logic (không cần database), middleware pattern cho authorization, và Bootstrap 5 cho UI.

## Tasks

- [x] 1. Tạo Client Authentication Views
  - [x] 1.1 Tạo view đăng nhập khách hàng (app/views/client/auth/login.php)
    - Tạo form với email và password fields
    - Sử dụng Bootstrap 5 CDN
    - Thêm link đến trang đăng ký
    - Form action POST đến client auth controller
    - _Requirements: 1.1, 1.2, 9.1, 9.4, 9.5_
  
  - [x] 1.2 Tạo view đăng ký khách hàng (app/views/client/auth/register.php)
    - Tạo form với email, password, và name fields
    - Sử dụng Bootstrap 5 CDN
    - Thêm link đến trang đăng nhập
    - Form action POST đến client auth controller
    - _Requirements: 1.3, 1.4, 9.2, 9.4, 9.5_

- [x] 2. Tạo Admin Authentication View
  - [x] 2.1 Tạo view đăng nhập admin (app/views/admin/auth/login.php)
    - Tạo form với email và password fields
    - Hiển thị title "Cổng quản trị nội bộ"
    - Sử dụng Bootstrap 5 CDN
    - KHÔNG có link đăng ký
    - Form action POST đến admin auth controller
    - _Requirements: 2.1, 2.2, 2.3, 9.3, 9.4, 9.5_

- [x] 3. Implement Client AuthController
  - [x] 3.1 Tạo AuthController cho client (app/controllers/client/AuthController.php)
    - Implement static method login($email, $password)
    - Implement static method register($email, $password, $name)
    - Implement static method logout()
    - Validate email format và non-empty password/name
    - Sử dụng simulated authentication logic
    - Set session data với role MEMBER
    - Redirect đến /client/profile sau login/register thành công
    - _Requirements: 1.5, 1.6, 1.7, 1.8, 5.1, 5.2, 6.1, 6.3, 7.3, 7.4, 7.5, 8.1, 8.2, 8.3, 8.4, 8.5_
  
  - [ ]* 3.2 Write property test cho MEMBER authentication role
    - **Property 1: MEMBER Authentication Sets Correct Role**
    - **Validates: Requirements 1.5, 1.6**
  
  - [ ]* 3.3 Write property test cho MEMBER login redirect
    - **Property 3: MEMBER Login Redirect**
    - **Validates: Requirements 1.7, 6.1**
  
  - [ ]* 3.4 Write property test cho MEMBER registration redirect
    - **Property 4: MEMBER Registration Redirect**
    - **Validates: Requirements 1.8, 6.3**
  
  - [ ]* 3.5 Write property test cho complete session data storage
    - **Property 12: Complete Session Data Storage**
    - **Validates: Requirements 5.1, 5.2, 8.5**
  
  - [ ]* 3.6 Write property test cho login input validation
    - **Property 15: Login Input Validation**
    - **Validates: Requirements 8.3**
  
  - [ ]* 3.7 Write property test cho registration input validation
    - **Property 16: Registration Input Validation**
    - **Validates: Requirements 8.4**
  
  - [ ]* 3.8 Write unit tests cho AuthController
    - Test empty password rejection
    - Test invalid email format rejection
    - Test empty name rejection (registration)
    - Test logout clears session
    - _Requirements: 5.3, 8.3, 8.4_

- [x] 4. Implement Admin AuthController
  - [x] 4.1 Tạo AuthController cho admin (app/controllers/admin/AuthController.php)
    - Implement static method login($email, $password)
    - Implement static method logout()
    - Validate email format và non-empty password
    - Sử dụng simulated authentication logic
    - Set session data với role ADMIN
    - Redirect đến /admin/dashboard sau login thành công
    - _Requirements: 2.4, 2.5, 5.1, 5.2, 6.2, 7.3, 7.4, 7.5, 8.1, 8.2, 8.3, 8.5_
  
  - [ ]* 4.2 Write property test cho ADMIN authentication role
    - **Property 2: ADMIN Authentication Sets Correct Role**
    - **Validates: Requirements 2.4**
  
  - [ ]* 4.3 Write property test cho ADMIN login redirect
    - **Property 5: ADMIN Login Redirect**
    - **Validates: Requirements 2.5, 6.2**
  
  - [ ]* 4.4 Write unit tests cho Admin AuthController
    - Test empty password rejection
    - Test invalid email format rejection
    - Test logout clears session
    - _Requirements: 5.3, 8.3_

- [ ] 5. Checkpoint - Verify authentication controllers
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Implement AuthMiddleware (Client Authorization)
  - [x] 6.1 Tạo AuthMiddleware class (app/middleware/AuthMiddleware.php)
    - Implement static method checkMember()
    - Check session role === 'MEMBER'
    - Guest → redirect to /client/login
    - ADMIN → redirect to /client/login
    - MEMBER → allow access
    - Sử dụng Session class và LoaiTaiKhoan enum
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 6.4, 7.1, 7.4, 7.5, 7.6, 10.2_
  
  - [ ]* 6.2 Write property test cho guest access denied
    - **Property 6: Guest Access to Client Pages Denied**
    - **Validates: Requirements 3.2**
  
  - [ ]* 6.3 Write property test cho admin access to client pages denied
    - **Property 7: Admin Access to Client Pages Denied**
    - **Validates: Requirements 3.3, 6.4**
  
  - [ ]* 6.4 Write property test cho member access allowed
    - **Property 8: MEMBER Access to Client Pages Allowed**
    - **Validates: Requirements 3.4**
  
  - [ ]* 6.5 Write unit tests cho AuthMiddleware
    - Test redirect URLs are correct
    - Test no error messages displayed
    - _Requirements: 10.2_

- [x] 7. Implement AdminMiddleware (Admin Authorization)
  - [x] 7.1 Tạo AdminMiddleware class (app/middleware/AdminMiddleware.php)
    - Implement static method checkAdmin()
    - Check session role === 'ADMIN'
    - Guest → redirect to /admin/login
    - MEMBER → redirect to homepage với message "Không có quyền truy cập"
    - ADMIN → allow access
    - Sử dụng Session class và LoaiTaiKhoan enum
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 7.2, 7.4, 7.5, 7.6, 10.1, 10.2_
  
  - [ ]* 7.2 Write property test cho guest access to admin pages denied
    - **Property 9: Guest Access to Admin Pages Denied**
    - **Validates: Requirements 4.2**
  
  - [ ]* 7.3 Write property test cho member access to admin pages denied
    - **Property 10: MEMBER Access to Admin Pages Denied**
    - **Validates: Requirements 4.3, 10.1**
  
  - [ ]* 7.4 Write property test cho admin access allowed
    - **Property 11: ADMIN Access to Admin Pages Allowed**
    - **Validates: Requirements 4.4**
  
  - [ ]* 7.5 Write unit tests cho AdminMiddleware
    - Test redirect URLs are correct
    - Test error message "Không có quyền truy cập" displayed
    - _Requirements: 10.1_

- [ ] 8. Checkpoint - Verify middleware authorization
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 9. Implement Session Management Tests
  - [ ]* 9.1 Write property test cho logout clears session
    - **Property 13: Logout Clears Session**
    - **Validates: Requirements 5.3**
  
  - [ ]* 9.2 Write property test cho session persistence
    - **Property 14: Session Persistence**
    - **Validates: Requirements 5.4**
  
  - [ ]* 9.3 Write integration tests cho complete authentication flows
    - Test complete client login flow
    - Test complete client registration flow
    - Test complete admin login flow
    - Test complete logout flow
    - Test cross-role access scenarios
    - _Requirements: 1.7, 1.8, 2.5, 5.3, 6.1, 6.2, 6.3, 6.4_

- [ ] 10. Wire authentication views to controllers
  - [ ] 10.1 Update client login view form action
    - Set form action to POST handler trong AuthController
    - Ensure proper error handling
    - _Requirements: 1.1, 1.7_
  
  - [ ] 10.2 Update client register view form action
    - Set form action to POST handler trong AuthController
    - Ensure proper error handling
    - _Requirements: 1.3, 1.8_
  
  - [ ] 10.3 Update admin login view form action
    - Set form action to POST handler trong Admin AuthController
    - Ensure proper error handling
    - _Requirements: 2.1, 2.5_

- [ ] 11. Apply middleware to existing protected pages
  - [ ] 11.1 Add AuthMiddleware::checkMember() to client protected pages
    - Apply to app/views/client/khach_hang/profile.php
    - Apply to app/views/client/khach_hang/history.php
    - Apply to app/views/client/gio_hang/index.php
    - Apply to app/views/client/thanh_toan/checkout.php
    - _Requirements: 3.1, 3.2, 3.3, 3.4_
  
  - [ ] 11.2 Add AdminMiddleware::checkAdmin() to admin protected pages
    - Apply to app/views/admin/dashboard/index.php
    - Apply to all admin CRUD pages (danh_muc, san_pham, don_hang, nguoi_dung)
    - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [ ] 12. Final checkpoint - Integration verification
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Simulated authentication không cần database connection
- Sử dụng existing Session class và LoaiTaiKhoan enum
- Property tests require Pest PHP with pest-plugin-faker
- All views use Bootstrap 5 CDN for styling
- Middleware sử dụng static methods pattern
- Session data persists across page requests
