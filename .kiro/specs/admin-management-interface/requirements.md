# Requirements Document

## Introduction

This document specifies the requirements for a comprehensive admin management interface system for FPT Shop, a PHP-based e-commerce platform. The admin interface provides centralized management capabilities for orders, products, categories, promotions, users, and reviews. The system builds upon existing infrastructure including admin layout components (header, sidebar, footer, breadcrumb), database schema, and middleware authentication.

## Glossary

- **Admin_System**: The complete administrative interface for managing the e-commerce platform
- **Dashboard**: The central hub displaying overview statistics and quick access to management modules
- **Order_Manager**: The subsystem responsible for viewing and updating order status
- **Payment_Approver**: The subsystem responsible for reviewing and approving payment receipts
- **Category_Manager**: The subsystem responsible for CRUD operations on product categories
- **Product_Manager**: The subsystem responsible for CRUD operations on products and variants
- **Image_Manager**: The subsystem responsible for uploading and organizing product images
- **Promotion_Manager**: The subsystem responsible for managing promotions and discount codes
- **User_Manager**: The subsystem responsible for managing user accounts and permissions
- **Review_Moderator**: The subsystem responsible for approving and moderating product reviews
- **Admin_User**: A user with loai_tai_khoan = 'ADMIN' who has access to the admin interface
- **Order_Status**: One of CHO_DUYET, DA_XAC_NHAN, DANG_GIAO, DA_GIAO, HOAN_THANH, DA_HUY, TRA_HANG
- **Payment_Status**: One of CHO_DUYET, THANH_CONG, THAT_BAI, HOAN_TIEN
- **Product_Variant**: A specific configuration of a product (phien_ban_san_pham) with attributes like color, storage, RAM
- **Receipt_Image**: The anh_bien_lai field in thanh_toan table containing payment proof

## Requirements

### Requirement 1: Admin Authentication and Authorization

**User Story:** As an admin user, I want secure access to the admin interface, so that only authorized personnel can manage the e-commerce platform.

#### Acceptance Criteria

1. THE Admin_System SHALL restrict all admin routes to users with loai_tai_khoan = 'ADMIN'
2. WHEN an unauthenticated user attempts to access an admin route, THE Admin_System SHALL redirect to the admin login page
3. WHEN a user with loai_tai_khoan = 'MEMBER' attempts to access an admin route, THE Admin_System SHALL redirect to the client homepage with an error message
4. THE Admin_System SHALL use AdminMiddleware for all admin route protection
5. WHEN an Admin_User logs out, THE Admin_System SHALL clear the session and redirect to the admin login page

### Requirement 2: Dashboard Overview

**User Story:** As an admin user, I want a dashboard with key metrics and quick access links, so that I can monitor the platform at a glance.

#### Acceptance Criteria

1. THE Dashboard SHALL display the total count of orders with trang_thai = 'CHO_DUYET'
2. THE Dashboard SHALL display the total count of users with loai_tai_khoan = 'MEMBER'
3. THE Dashboard SHALL display the total count of products with trang_thai = 'CON_BAN'
4. THE Dashboard SHALL display the total count of payments with trang_thai_duyet = 'CHO_DUYET'
5. THE Dashboard SHALL provide navigation links to all management modules
6. THE Dashboard SHALL display statistics for the current month by default

### Requirement 3: Order Listing and Filtering

**User Story:** As an admin user, I want to view and filter orders, so that I can track order fulfillment.

#### Acceptance Criteria

1. THE Order_Manager SHALL display all orders from the don_hang table ordered by ngay_tao DESC
2. THE Order_Manager SHALL display ma_don_hang, customer name, tong_thanh_toan, trang_thai, and ngay_tao for each order
3. WHEN an admin user selects a status filter, THE Order_Manager SHALL display only orders matching that Order_Status
4. THE Order_Manager SHALL provide a search function that filters by ma_don_hang or customer name
5. THE Order_Manager SHALL display pagination when the order count exceeds 20 records per page

### Requirement 4: Order Status Management

**User Story:** As an admin user, I want to update order status through a defined workflow, so that orders progress correctly through fulfillment stages.

#### Acceptance Criteria

1. WHEN an admin user views an order detail, THE Order_Manager SHALL display all order information including chi_tiet_don records
2. THE Order_Manager SHALL allow status transitions only in this sequence: CHO_DUYET → DA_XAC_NHAN → DANG_GIAO → DA_GIAO → HOAN_THANH
3. THE Order_Manager SHALL allow transition from any status to DA_HUY
4. THE Order_Manager SHALL allow transition from DA_GIAO to TRA_HANG
5. WHEN an admin user updates order status, THE Order_Manager SHALL update the ngay_cap_nhat field to the current timestamp
6. IF an invalid status transition is attempted, THEN THE Order_Manager SHALL reject the update and display an error message

### Requirement 5: Payment Receipt Review

**User Story:** As an admin user, I want to review payment receipts and approve transactions, so that I can verify customer payments.

#### Acceptance Criteria

1. WHEN an admin user views an order with phuong_thuc = 'CHUYEN_KHOAN', THE Payment_Approver SHALL display the Receipt_Image if anh_bien_lai is not NULL
2. THE Payment_Approver SHALL allow admin users to update trang_thai_duyet to THANH_CONG, THAT_BAI, or HOAN_TIEN
3. WHEN an admin user approves or rejects a payment, THE Payment_Approver SHALL record the Admin_User id in nguoi_duyet_id
4. WHEN an admin user approves or rejects a payment, THE Payment_Approver SHALL update ngay_duyet to the current timestamp
5. THE Payment_Approver SHALL allow admin users to add notes in ghi_chu_duyet field
6. THE Payment_Approver SHALL display all payments with trang_thai_duyet = 'CHO_DUYET' in a pending payments list

### Requirement 6: Category Management

**User Story:** As an admin user, I want to create, edit, and organize product categories, so that products are properly classified.

#### Acceptance Criteria

1. THE Category_Manager SHALL allow admin users to create categories with ten, slug, icon_url, danh_muc_cha_id, and thu_tu fields
2. THE Category_Manager SHALL validate that slug contains only lowercase letters, numbers, and hyphens
3. THE Category_Manager SHALL validate that slug is unique across all categories
4. WHEN a category is created without a slug, THE Category_Manager SHALL generate a slug from the ten field
5. THE Category_Manager SHALL allow admin users to set danh_muc_cha_id to create hierarchical categories
6. THE Category_Manager SHALL prevent a category from being its own parent
7. THE Category_Manager SHALL allow admin users to toggle trang_thai between 0 (hidden) and 1 (visible)
8. THE Category_Manager SHALL display categories in a hierarchical tree structure ordered by thu_tu

### Requirement 7: Product Management

**User Story:** As an admin user, I want to create and edit products with full details, so that customers can browse accurate product information.

#### Acceptance Criteria

1. THE Product_Manager SHALL allow admin users to create products with ten_san_pham, slug, hang_san_xuat, mo_ta, danh_muc_id, and trang_thai fields
2. THE Product_Manager SHALL validate that slug is unique across all products
3. THE Product_Manager SHALL allow admin users to set noi_bat = 1 to feature products on the homepage
4. THE Product_Manager SHALL calculate gia_hien_thi as the minimum gia_ban from associated Product_Variant records
5. THE Product_Manager SHALL allow admin users to assign products to categories via danh_muc_id
6. THE Product_Manager SHALL allow admin users to update trang_thai to CON_BAN, NGUNG_BAN, SAP_RA_MAT, or HET_HANG
7. WHEN a product trang_thai is set to NGUNG_BAN, THE Product_Manager SHALL update all associated Product_Variant records to trang_thai = 'NGUNG_BAN'

### Requirement 8: Product Variant Management

**User Story:** As an admin user, I want to manage product variants with different configurations, so that customers can select specific product options.

#### Acceptance Criteria

1. THE Product_Manager SHALL allow admin users to create Product_Variant records with sku, ten_phien_ban, mau_sac, dung_luong, ram, gia_ban, gia_goc, and so_luong_ton fields
2. THE Product_Manager SHALL validate that sku is unique across all variants
3. THE Product_Manager SHALL validate that gia_ban is greater than 0
4. THE Product_Manager SHALL validate that gia_goc is NULL or greater than or equal to gia_ban
5. THE Product_Manager SHALL validate that so_luong_ton is greater than or equal to 0
6. WHEN so_luong_ton reaches 0, THE Product_Manager SHALL automatically update Product_Variant trang_thai to 'HET_HANG'
7. WHEN so_luong_ton is increased above 0 for a variant with trang_thai = 'HET_HANG', THE Product_Manager SHALL update trang_thai to 'CON_HANG'

### Requirement 9: Product Image Management

**User Story:** As an admin user, I want to upload and organize product images, so that products are displayed with proper visual content.

#### Acceptance Criteria

1. THE Image_Manager SHALL allow admin users to upload images to the hinh_anh_san_pham table
2. THE Image_Manager SHALL validate that uploaded files are image formats (JPEG, PNG, GIF, WEBP)
3. THE Image_Manager SHALL validate that uploaded files do not exceed 5MB in size
4. THE Image_Manager SHALL allow admin users to set la_anh_chinh = 1 for one image per product
5. WHEN an admin user sets a new image as la_anh_chinh = 1, THE Image_Manager SHALL set la_anh_chinh = 0 for all other images of that product
6. THE Image_Manager SHALL allow admin users to associate images with specific Product_Variant records via phien_ban_id
7. THE Image_Manager SHALL allow admin users to set thu_tu to control image display order
8. THE Image_Manager SHALL store uploaded images in a designated directory and save the file path in url_anh

### Requirement 10: Technical Specifications Management

**User Story:** As an admin user, I want to add technical specifications to products, so that customers can view detailed product attributes.

#### Acceptance Criteria

1. THE Product_Manager SHALL allow admin users to add key-value pairs to thong_so_ky_thuat table for each product
2. THE Product_Manager SHALL allow admin users to specify ten_thong_so (specification name) and gia_tri (specification value)
3. THE Product_Manager SHALL allow admin users to set thu_tu to control specification display order
4. THE Product_Manager SHALL allow admin users to edit existing specifications
5. THE Product_Manager SHALL allow admin users to delete specifications

### Requirement 11: Promotion Management

**User Story:** As an admin user, I want to create and manage promotions, so that I can offer discounts on products.

#### Acceptance Criteria

1. THE Promotion_Manager SHALL allow admin users to create promotions with ten_chuong_trinh, loai_giam, gia_tri_giam, giam_toi_da, ngay_bat_dau, and ngay_ket_thuc fields
2. THE Promotion_Manager SHALL validate that loai_giam is either 'PHAN_TRAM' or 'SO_TIEN'
3. WHEN loai_giam = 'PHAN_TRAM', THE Promotion_Manager SHALL validate that gia_tri_giam is between 0 and 100
4. WHEN loai_giam = 'PHAN_TRAM', THE Promotion_Manager SHALL require giam_toi_da to be specified
5. THE Promotion_Manager SHALL validate that ngay_bat_dau is before ngay_ket_thuc
6. THE Promotion_Manager SHALL allow admin users to link products to promotions via san_pham_khuyen_mai table
7. WHEN the current date exceeds ngay_ket_thuc, THE Promotion_Manager SHALL automatically update trang_thai to 'DA_HET_HAN'

### Requirement 12: Discount Code Management

**User Story:** As an admin user, I want to create and manage discount codes, so that customers can apply vouchers to their orders.

#### Acceptance Criteria

1. THE Promotion_Manager SHALL allow admin users to create discount codes with ma_code, mo_ta, loai_giam, gia_tri_giam, don_toi_thieu, gioi_han_su_dung, ngay_bat_dau, and ngay_ket_thuc fields
2. THE Promotion_Manager SHALL validate that ma_code is unique across all discount codes
3. THE Promotion_Manager SHALL validate that ma_code contains only uppercase letters and numbers
4. THE Promotion_Manager SHALL validate that ngay_bat_dau is before ngay_ket_thuc
5. WHEN gioi_han_su_dung is not NULL and so_luot_da_dung reaches gioi_han_su_dung, THE Promotion_Manager SHALL update trang_thai to 'HET_LUOT'
6. THE Promotion_Manager SHALL allow admin users to set gioi_han_su_dung to NULL for unlimited usage
7. THE Promotion_Manager SHALL display so_luot_da_dung and remaining usage count for each discount code

### Requirement 13: User Account Management

**User Story:** As an admin user, I want to view and manage user accounts, so that I can control platform access.

#### Acceptance Criteria

1. THE User_Manager SHALL display all users from nguoi_dung table with email, ho_ten, sdt, loai_tai_khoan, and trang_thai fields
2. THE User_Manager SHALL allow admin users to filter users by loai_tai_khoan (ADMIN, MEMBER)
3. THE User_Manager SHALL allow admin users to filter users by trang_thai (ACTIVE, BLOCKED, UNVERIFIED)
4. THE User_Manager SHALL allow admin users to update user trang_thai from ACTIVE to BLOCKED
5. THE User_Manager SHALL allow admin users to update user trang_thai from BLOCKED to ACTIVE
6. THE User_Manager SHALL provide a search function that filters by email, ho_ten, or sdt
7. THE User_Manager SHALL display pagination when user count exceeds 20 records per page

### Requirement 14: Review Moderation

**User Story:** As an admin user, I want to review and moderate product reviews, so that I can maintain content quality.

#### Acceptance Criteria

1. THE Review_Moderator SHALL display all reviews from danh_gia table with nguoi_dung name, san_pham name, so_sao, noi_dung, and ngay_viet
2. THE Review_Moderator SHALL allow admin users to filter reviews by so_sao rating (1-5)
3. THE Review_Moderator SHALL allow admin users to filter reviews by san_pham_id
4. THE Review_Moderator SHALL allow admin users to delete inappropriate reviews
5. THE Review_Moderator SHALL display reviews ordered by ngay_viet DESC

### Requirement 15: Data Validation and Error Handling

**User Story:** As an admin user, I want proper validation and error messages, so that I can correct mistakes when managing data.

#### Acceptance Criteria

1. WHEN an admin user submits a form with missing required fields, THE Admin_System SHALL display field-specific error messages
2. WHEN an admin user submits a form with invalid data types, THE Admin_System SHALL display validation error messages
3. WHEN a database constraint violation occurs, THE Admin_System SHALL display a user-friendly error message
4. THE Admin_System SHALL preserve form input values when validation fails
5. WHEN an operation succeeds, THE Admin_System SHALL display a success message
6. THE Admin_System SHALL sanitize all user input to prevent SQL injection attacks

### Requirement 16: File Upload Management

**User Story:** As an admin user, I want to upload images and documents securely, so that I can add visual content to products and verify payments.

#### Acceptance Criteria

1. THE Admin_System SHALL validate uploaded file types against an allowed list (JPEG, PNG, GIF, WEBP for images)
2. THE Admin_System SHALL validate that uploaded files do not exceed 5MB for images
3. THE Admin_System SHALL generate unique filenames to prevent file overwrites
4. THE Admin_System SHALL store uploaded files in organized directories by type (products, receipts)
5. WHEN a file upload fails, THE Admin_System SHALL display an error message with the failure reason
6. THE Admin_System SHALL validate file MIME types in addition to file extensions

### Requirement 17: Sidebar Navigation

**User Story:** As an admin user, I want a sidebar menu with all management modules, so that I can navigate the admin interface efficiently.

#### Acceptance Criteria

1. THE Admin_System SHALL display a sidebar menu with links to Dashboard, Order_Manager, Product_Manager, Category_Manager, Promotion_Manager, User_Manager, and Review_Moderator
2. THE Admin_System SHALL highlight the active menu item based on the current route
3. THE Admin_System SHALL organize related functions into collapsible menu groups
4. THE Admin_System SHALL persist sidebar expand/collapse state during navigation
5. THE Admin_System SHALL display the FPT Shop logo and branding in the sidebar header

### Requirement 18: Breadcrumb Navigation

**User Story:** As an admin user, I want breadcrumb navigation, so that I can understand my current location and navigate back easily.

#### Acceptance Criteria

1. THE Admin_System SHALL display breadcrumb navigation on all admin pages except the login page
2. THE Admin_System SHALL include the page hierarchy in breadcrumbs (e.g., Dashboard > Orders > Order Detail)
3. THE Admin_System SHALL make all breadcrumb items except the current page clickable links
4. THE Admin_System SHALL update breadcrumbs dynamically based on the current route

### Requirement 19: Product-Promotion Linking

**User Story:** As an admin user, I want to link products to promotions, so that discounts are applied correctly.

#### Acceptance Criteria

1. WHEN creating or editing a promotion, THE Promotion_Manager SHALL display a list of available products
2. THE Promotion_Manager SHALL allow admin users to select multiple products to link to a promotion
3. THE Promotion_Manager SHALL create records in san_pham_khuyen_mai table for each selected product
4. THE Promotion_Manager SHALL allow admin users to remove product-promotion links
5. THE Promotion_Manager SHALL display currently linked products when editing a promotion
6. WHEN a promotion is deleted, THE Promotion_Manager SHALL remove all associated san_pham_khuyen_mai records

### Requirement 20: Bulk Operations

**User Story:** As an admin user, I want to perform bulk operations on multiple records, so that I can manage data efficiently.

#### Acceptance Criteria

1. THE Category_Manager SHALL allow admin users to select multiple categories and toggle trang_thai in bulk
2. THE Product_Manager SHALL allow admin users to select multiple products and update trang_thai in bulk
3. THE User_Manager SHALL allow admin users to select multiple users and update trang_thai in bulk
4. WHEN a bulk operation is performed, THE Admin_System SHALL display the count of successfully updated records
5. IF any record in a bulk operation fails, THE Admin_System SHALL display which records failed and continue processing remaining records

### Requirement 21: Data Integrity and Constraints

**User Story:** As an admin user, I want the system to enforce data integrity, so that the database remains consistent.

#### Acceptance Criteria

1. WHEN an admin user attempts to delete a category with associated products, THE Category_Manager SHALL prevent deletion and display an error message
2. WHEN an admin user attempts to delete a product with existing orders, THE Product_Manager SHALL prevent deletion and display an error message
3. THE Admin_System SHALL enforce all foreign key constraints defined in the database schema
4. THE Admin_System SHALL enforce all CHECK constraints defined in the database schema
5. WHEN a constraint violation occurs, THE Admin_System SHALL rollback the transaction and display an error message

### Requirement 22: Search and Filtering

**User Story:** As an admin user, I want comprehensive search and filtering capabilities, so that I can find records quickly.

#### Acceptance Criteria

1. THE Product_Manager SHALL allow filtering by danh_muc_id, hang_san_xuat, trang_thai, and price range
2. THE Order_Manager SHALL allow filtering by trang_thai, date range, and payment method
3. THE User_Manager SHALL allow filtering by loai_tai_khoan, trang_thai, and registration date range
4. THE Promotion_Manager SHALL allow filtering by trang_thai and date range
5. THE Admin_System SHALL preserve filter selections when navigating between pages
6. THE Admin_System SHALL display the count of filtered results

### Requirement 23: Responsive Data Tables

**User Story:** As an admin user, I want data displayed in sortable, paginated tables, so that I can browse large datasets efficiently.

#### Acceptance Criteria

1. THE Admin_System SHALL display data in HTML tables with column headers
2. THE Admin_System SHALL implement pagination with configurable page size (default 20 records)
3. THE Admin_System SHALL display page numbers and next/previous navigation controls
4. THE Admin_System SHALL display the total record count and current page range
5. THE Admin_System SHALL allow admin users to sort tables by clicking column headers
6. THE Admin_System SHALL indicate the current sort column and direction

### Requirement 24: Form Validation and User Feedback

**User Story:** As an admin user, I want immediate feedback on form inputs, so that I can correct errors before submission.

#### Acceptance Criteria

1. WHEN an admin user submits a form, THE Admin_System SHALL validate all required fields are not empty
2. WHEN validation fails, THE Admin_System SHALL display error messages adjacent to the relevant form fields
3. THE Admin_System SHALL preserve all form input values when redisplaying a form after validation failure
4. THE Admin_System SHALL display success messages after successful create, update, or delete operations
5. THE Admin_System SHALL display error messages in a consistent format across all admin pages
6. THE Admin_System SHALL use distinct visual styling for success messages (green) and error messages (red)

### Requirement 25: Session Management

**User Story:** As an admin user, I want my session to remain active during work, so that I don't lose progress.

#### Acceptance Criteria

1. THE Admin_System SHALL maintain admin user session data including id, email, ho_ten, and loai_tai_khoan
2. THE Admin_System SHALL validate session data on every admin page request
3. WHEN a session expires, THE Admin_System SHALL redirect to the admin login page
4. THE Admin_System SHALL allow admin users to manually log out, which clears the session
5. THE Admin_System SHALL set session timeout to 2 hours of inactivity

### Requirement 26: Audit Trail and Timestamps

**User Story:** As an admin user, I want to track when records are created and modified, so that I can audit changes.

#### Acceptance Criteria

1. WHEN a record is created, THE Admin_System SHALL set ngay_tao to the current timestamp
2. WHEN a record is updated, THE Admin_System SHALL update ngay_cap_nhat to the current timestamp
3. THE Admin_System SHALL display ngay_tao and ngay_cap_nhat in detail views
4. THE Admin_System SHALL display timestamps in a consistent format (YYYY-MM-DD HH:MM:SS)
5. THE Admin_System SHALL use server timezone for all timestamp operations

### Requirement 27: Image Gallery Management

**User Story:** As an admin user, I want to manage multiple images per product, so that customers can view products from different angles.

#### Acceptance Criteria

1. THE Image_Manager SHALL display all images for a product ordered by thu_tu ASC
2. THE Image_Manager SHALL allow admin users to reorder images by updating thu_tu values
3. THE Image_Manager SHALL allow admin users to delete images from hinh_anh_san_pham table
4. WHEN an image is deleted, THE Image_Manager SHALL remove the physical file from the server
5. THE Image_Manager SHALL display image thumbnails in the admin interface
6. THE Image_Manager SHALL allow admin users to set alt_text for accessibility

### Requirement 28: Payment Method Handling

**User Story:** As an admin user, I want to handle different payment methods appropriately, so that transactions are processed correctly.

#### Acceptance Criteria

1. WHEN phuong_thuc = 'COD', THE Payment_Approver SHALL not require receipt approval
2. WHEN phuong_thuc = 'CHUYEN_KHOAN', THE Payment_Approver SHALL require anh_bien_lai to be uploaded
3. WHEN phuong_thuc = 'CHUYEN_KHOAN' and anh_bien_lai is NULL, THE Payment_Approver SHALL display a warning message
4. THE Payment_Approver SHALL display phuong_thuc for each payment record
5. THE Payment_Approver SHALL allow filtering payments by phuong_thuc

### Requirement 29: Hierarchical Category Display

**User Story:** As an admin user, I want to see category hierarchies clearly, so that I can understand the category structure.

#### Acceptance Criteria

1. THE Category_Manager SHALL display parent categories before their children
2. THE Category_Manager SHALL indent child categories visually to show hierarchy depth
3. THE Category_Manager SHALL display the full category path (e.g., Electronics > Phones > iPhone)
4. WHEN editing a category, THE Category_Manager SHALL display only valid parent options (excluding self and descendants)
5. THE Category_Manager SHALL allow admin users to move categories by changing danh_muc_cha_id

### Requirement 30: Stock Management Integration

**User Story:** As an admin user, I want stock levels to update automatically with orders, so that inventory remains accurate.

#### Acceptance Criteria

1. WHEN an order transitions to DA_XAC_NHAN, THE Order_Manager SHALL reduce so_luong_ton for each Product_Variant in the order
2. WHEN an order transitions to DA_HUY or TRA_HANG, THE Order_Manager SHALL restore so_luong_ton for each Product_Variant
3. IF so_luong_ton would become negative, THE Order_Manager SHALL reject the status transition and display an error message
4. THE Product_Manager SHALL display current so_luong_ton for all variants
5. THE Product_Manager SHALL highlight variants with so_luong_ton below 10 as low stock warnings
