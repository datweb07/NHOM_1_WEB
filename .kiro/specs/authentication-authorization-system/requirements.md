# Requirements Document

## Introduction

Hệ thống xác thực và phân quyền cho ứng dụng PHP thuần sử dụng OOP và $_SESSION. Hệ thống cung cấp các giao diện đăng nhập/đăng ký riêng biệt cho khách hàng và quản trị viên, với middleware bảo vệ các trang theo vai trò người dùng.

## Glossary

- **Authentication_System**: Hệ thống xác thực và phân quyền
- **Client_Login_View**: Giao diện đăng nhập cho khách hàng (views/client/auth/login.php)
- **Client_Register_View**: Giao diện đăng ký cho khách hàng (views/client/auth/register.php)
- **Admin_Login_View**: Giao diện đăng nhập cho quản trị viên (views/admin/auth/login.php)
- **AuthMiddleware**: Middleware kiểm tra quyền truy cập cho khách hàng
- **AdminMiddleware**: Middleware kiểm tra quyền truy cập cho quản trị viên
- **Session**: Đối tượng quản lý phiên làm việc ($_SESSION)
- **MEMBER**: Vai trò khách hàng (loai_tai_khoan = 'MEMBER')
- **ADMIN**: Vai trò quản trị viên (loai_tai_khoan = 'ADMIN')
- **Guest**: Người dùng chưa đăng nhập (không có session)
- **Client_Profile_Page**: Trang profile khách hàng (views/client/khach_hang/profile.php)
- **Admin_Dashboard_Page**: Trang dashboard quản trị (views/admin/dashboard/index.php)

## Requirements

### Requirement 1: Client Authentication Views

**User Story:** Là một khách hàng, tôi muốn có giao diện đăng nhập và đăng ký, để tôi có thể truy cập vào hệ thống.

#### Acceptance Criteria

1. THE Client_Login_View SHALL render a login form with email and password fields using Bootstrap 5 CDN
2. THE Client_Login_View SHALL include a link to the Client_Register_View
3. THE Client_Register_View SHALL render a registration form with email, password, and name fields using Bootstrap 5 CDN
4. THE Client_Register_View SHALL include a link to the Client_Login_View
5. WHEN a user successfully logs in as MEMBER, THE Authentication_System SHALL set $_SESSION['loai_tai_khoan'] to 'MEMBER'
6. WHEN a user successfully registers as MEMBER, THE Authentication_System SHALL set $_SESSION['loai_tai_khoan'] to 'MEMBER'
7. WHEN a MEMBER login succeeds, THE Authentication_System SHALL redirect to Client_Profile_Page
8. WHEN a MEMBER registration succeeds, THE Authentication_System SHALL redirect to Client_Profile_Page

### Requirement 2: Admin Authentication View

**User Story:** Là một quản trị viên, tôi muốn có giao diện đăng nhập riêng biệt, để tôi có thể truy cập vào cổng quản trị nội bộ.

#### Acceptance Criteria

1. THE Admin_Login_View SHALL render a login form with email and password fields using Bootstrap 5 CDN
2. THE Admin_Login_View SHALL display the title "Cổng quản trị nội bộ"
3. THE Admin_Login_View SHALL NOT include a registration link
4. WHEN an admin successfully logs in, THE Authentication_System SHALL set $_SESSION['loai_tai_khoan'] to 'ADMIN'
5. WHEN an ADMIN login succeeds, THE Authentication_System SHALL redirect to Admin_Dashboard_Page

### Requirement 3: Client Access Control Middleware

**User Story:** Là một nhà phát triển, tôi muốn middleware kiểm tra quyền truy cập cho trang khách hàng, để đảm bảo chỉ MEMBER mới truy cập được.

#### Acceptance Criteria

1. THE AuthMiddleware SHALL provide a static method checkMember()
2. WHEN a Guest attempts to access a protected client page, THE AuthMiddleware SHALL redirect to Client_Login_View
3. WHEN an ADMIN attempts to access a protected client page, THE AuthMiddleware SHALL redirect to Client_Login_View
4. WHEN a MEMBER attempts to access a protected client page, THE AuthMiddleware SHALL allow access

### Requirement 4: Admin Access Control Middleware

**User Story:** Là một nhà phát triển, tôi muốn middleware kiểm tra quyền truy cập cho trang quản trị, để đảm bảo chỉ ADMIN mới truy cập được.

#### Acceptance Criteria

1. THE AdminMiddleware SHALL provide a static method checkAdmin()
2. WHEN a Guest attempts to access a protected admin page, THE AdminMiddleware SHALL redirect to Admin_Login_View
3. WHEN a MEMBER attempts to access a protected admin page, THE AdminMiddleware SHALL redirect to the homepage with message "Không có quyền truy cập"
4. WHEN an ADMIN attempts to access a protected admin page, THE AdminMiddleware SHALL allow access

### Requirement 5: Session Management

**User Story:** Là một nhà phát triển, tôi muốn quản lý session một cách nhất quán, để theo dõi trạng thái đăng nhập của người dùng.

#### Acceptance Criteria

1. WHEN a user logs in, THE Authentication_System SHALL store user information in $_SESSION
2. THE Authentication_System SHALL store at minimum: user_id, user_email, user_name, and loai_tai_khoan in $_SESSION
3. WHEN a user logs out, THE Authentication_System SHALL clear all user-related session data
4. THE Session SHALL persist across page requests until logout or session expiration

### Requirement 6: Role-Based Redirection

**User Story:** Là một người dùng, tôi muốn được chuyển hướng đến trang phù hợp với vai trò của mình, để truy cập đúng chức năng.

#### Acceptance Criteria

1. WHEN a MEMBER logs in successfully, THE Authentication_System SHALL redirect to Client_Profile_Page
2. WHEN an ADMIN logs in successfully, THE Authentication_System SHALL redirect to Admin_Dashboard_Page
3. WHEN a MEMBER registers successfully, THE Authentication_System SHALL redirect to Client_Profile_Page
4. WHEN an ADMIN with active session attempts to access client pages, THE AuthMiddleware SHALL require re-authentication as MEMBER

### Requirement 7: OOP Architecture and Code Organization

**User Story:** Là một nhà phát triển, tôi muốn code được tổ chức theo OOP, để dễ bảo trì và mở rộng.

#### Acceptance Criteria

1. THE AuthMiddleware SHALL be implemented as a class with static methods
2. THE AdminMiddleware SHALL be implemented as a class with static methods
3. THE Authentication_System SHALL separate HTML presentation from PHP logic
4. THE Authentication_System SHALL use the existing Session class from app/core/Session.php
5. THE Authentication_System SHALL use the existing LoaiTaiKhoan enum from app/enums/LoaiTaiKhoan.php
6. THE Authentication_System SHALL follow OOP encapsulation principles

### Requirement 8: Simulated Authentication Logic

**User Story:** Là một nhà phát triển, tôi muốn logic xác thực mô phỏng không cần database, để phát triển và test nhanh chóng.

#### Acceptance Criteria

1. THE Authentication_System SHALL simulate successful login without requiring database connection
2. THE Authentication_System SHALL simulate successful registration without requiring database connection
3. WHEN simulating login, THE Authentication_System SHALL accept any valid email format and non-empty password
4. WHEN simulating registration, THE Authentication_System SHALL accept any valid email format, non-empty password, and non-empty name
5. THE Authentication_System SHALL set appropriate session values to simulate authenticated state

### Requirement 9: Bootstrap 5 Integration

**User Story:** Là một người dùng, tôi muốn giao diện đẹp và responsive, để có trải nghiệm tốt trên mọi thiết bị.

#### Acceptance Criteria

1. THE Client_Login_View SHALL load Bootstrap 5 via CDN
2. THE Client_Register_View SHALL load Bootstrap 5 via CDN
3. THE Admin_Login_View SHALL load Bootstrap 5 via CDN
4. THE Authentication_System SHALL use Bootstrap 5 form components and styling classes
5. THE Authentication_System SHALL use Bootstrap 5 responsive grid system

### Requirement 10: Security Messages and User Feedback

**User Story:** Là một người dùng, tôi muốn nhận thông báo rõ ràng, để biết trạng thái của hành động mình thực hiện.

#### Acceptance Criteria

1. WHEN a MEMBER attempts to access admin pages, THE AdminMiddleware SHALL display message "Không có quyền truy cập"
2. WHEN a Guest attempts to access protected pages, THE Authentication_System SHALL redirect without displaying error messages
3. WHEN login succeeds, THE Authentication_System SHALL redirect without displaying success messages
4. WHEN registration succeeds, THE Authentication_System SHALL redirect without displaying success messages
