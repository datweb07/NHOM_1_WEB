# Implementation Plan: Admin Management Interface

## Overview

This implementation plan breaks down the admin management interface into discrete coding tasks. The system extends the existing PHP MVC architecture with comprehensive CRUD operations for orders, products, categories, promotions, users, and reviews. All tasks build incrementally on the existing infrastructure including AdminMiddleware, database schema, and admin layout components.

## Tasks

- [x] 1. Set up core infrastructure and file upload handling
  - Create `app/core/FileUpload.php` class with image validation, unique filename generation, and upload methods
  - Implement validation for JPEG, PNG, GIF, WEBP formats with 5MB size limit
  - Add MIME type validation in addition to extension checking
  - Create upload directories: `public/uploads/products/`, `public/uploads/receipts/`, `public/uploads/avatars/`
  - _Requirements: 16.1, 16.2, 16.3, 16.4, 16.5, 16.6_

- [x] 2. Implement Dashboard module
  - [x] 2.1 Create DashboardController with statistics queries
    - Implement `index()` method to query pending orders, active users, available products, pending payments
    - Calculate monthly revenue and order counts
    - Load dashboard view with statistics data
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.6_
  
  - [x] 2.2 Update dashboard view with statistics cards
    - Modify `app/views/admin/dashboard/index.php` to display statistics
    - Add navigation cards linking to all management modules
    - Display monthly metrics
    - _Requirements: 2.5_

- [x] 3. Enhance Order Management module
  - [x] 3.1 Add search and filtering methods to DonHang model
    - Implement `timKiem()` method for searching by ma_don_hang or customer name
    - Implement `layTheoKhoangNgay()` for date range filtering
    - Implement `layTheoPhuongThuc()` for payment method filtering
    - Implement `demDonHang()` for pagination counts
    - _Requirements: 3.4, 22.2_
  
  - [x] 3.2 Enhance DonHangController with search and pagination
    - Update `index()` method to support search, status filtering, date range, and payment method filters
    - Implement pagination with 20 records per page
    - Preserve filter selections in query parameters
    - _Requirements: 3.1, 3.2, 3.3, 3.5, 22.2, 22.5, 23.1, 23.2, 23.3, 23.4_
  
  - [x] 3.3 Add order status validation logic
    - Implement status transition validation in DonHangController
    - Enforce workflow: CHO_DUYET → DA_XAC_NHAN → DANG_GIAO → DA_GIAO → HOAN_THANH
    - Allow DA_HUY from any status, TRA_HANG from DA_GIAO
    - Update `capNhatTrangThai()` method with validation
    - _Requirements: 4.2, 4.3, 4.4, 4.5, 4.6_
  
  - [x] 3.4 Integrate stock management with order status updates
    - Reduce so_luong_ton when order transitions to DA_XAC_NHAN
    - Restore so_luong_ton when order transitions to DA_HUY or TRA_HANG
    - Validate sufficient stock before confirming orders
    - Update variant trang_thai based on stock levels
    - _Requirements: 30.1, 30.2, 30.3, 30.4, 30.5_

- [x] 4. Implement Payment Approval module
  - [x] 4.1 Add payment approval methods to ThanhToan model
    - Implement `layDanhSachChoDuyet()` with pagination
    - Implement `duyetThanhToan()` to approve payments
    - Implement `tuChoiThanhToan()` to reject payments
    - Implement `demChoDuyet()` for dashboard statistics
    - _Requirements: 5.6_
  
  - [x] 4.2 Create ThanhToanController
    - Implement `index()` method to list pending payments
    - Implement `detail($id)` method to display payment with receipt image
    - Implement `approve($id)` method to approve payment
    - Implement `reject($id)` method to reject payment
    - Record nguoi_duyet_id and ngay_duyet on approval/rejection
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 28.1, 28.2, 28.3, 28.4, 28.5_
  
  - [x] 4.3 Create payment approval views
    - Create `app/views/admin/thanh_toan/index.php` for pending payments list
    - Create `app/views/admin/thanh_toan/detail.php` for payment detail with receipt image display
    - Add approval and rejection buttons with confirmation dialogs
    - _Requirements: 5.1, 5.5_
  
  - [x] 4.4 Add payment routes to admin router
    - Add GET `/admin/thanh-toan` route
    - Add GET `/admin/thanh-toan/chi-tiet` route
    - Add POST `/admin/thanh-toan/duyet` route
    - Add POST `/admin/thanh-toan/tu-choi` route
    - _Requirements: 5.2, 5.3_

- [x] 5. Checkpoint - Verify order and payment modules
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Enhance Product Management module
  - [x] 6.1 Add product creation methods to SanPhamController
    - Implement `create()` method to display product create form
    - Load categories for dropdown selection
    - Implement `store()` method to process form submission
    - Generate slug from ten_san_pham if not provided
    - Validate slug uniqueness
    - _Requirements: 7.1, 7.2, 6.2, 6.3, 6.4_
  
  - [x] 6.2 Add product editing methods to SanPhamController
    - Implement `edit($id)` method to display product edit form
    - Load product, categories, variants, images, and specifications
    - Implement `update($id)` method to process form updates
    - Cascade trang_thai updates to variants when product is set to NGUNG_BAN
    - _Requirements: 7.1, 7.2, 7.4, 7.5, 7.6, 7.7_
  
  - [x] 6.3 Implement product validation logic
    - Create `validatePayload()` method in SanPhamController
    - Validate ten_san_pham, slug, hang_san_xuat, danh_muc_id, trang_thai
    - Validate slug format (lowercase, alphanumeric, hyphens only)
    - Check slug uniqueness excluding current product when editing
    - _Requirements: 7.1, 7.2, 15.1, 15.2, 15.3, 15.4, 24.1, 24.2, 24.3_
  
  - [x] 6.4 Update product views with create and edit forms
    - Update `app/views/admin/san_pham/create.php` with complete form
    - Update `app/views/admin/san_pham/edit.php` with complete form
    - Add category dropdown, status selection, noi_bat checkbox
    - Display validation errors and preserve form values
    - _Requirements: 7.1, 7.3, 7.6, 24.2, 24.3, 24.4_

- [x] 7. Implement Product Variant Management
  - [x] 7.1 Create PhienBanSanPham model with variant methods
    - Implement `layTheoSanPham()` to get all variants for a product
    - Implement `capNhatSoLuongTon()` to update stock and auto-update trang_thai
    - Implement `kiemTraSKU()` for SKU uniqueness validation
    - _Requirements: 8.1, 8.2, 8.6, 8.7_
  
  - [x] 7.2 Add variant management methods to SanPhamController
    - Implement variant creation within product edit page
    - Implement variant update functionality
    - Implement variant deletion
    - Validate SKU uniqueness, gia_ban > 0, gia_goc >= gia_ban, so_luong_ton >= 0
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7_
  
  - [x] 7.3 Create variant management view
    - Create `app/views/admin/san_pham/variants.php` or integrate into edit view
    - Display variant list with SKU, color, storage, RAM, price, stock
    - Add forms for creating and editing variants
    - _Requirements: 8.1, 30.4_

- [x] 8. Implement Product Image Management
  - [x] 8.1 Create HinhAnhSanPham model with image methods
    - Implement `layTheoSanPham()` to get all images ordered by thu_tu
    - Implement `datAnhChinh()` to set main image and unset others
    - Implement `xoaVaXoaFile()` to delete image record and physical file
    - _Requirements: 9.1, 9.4, 9.5_
  
  - [x] 8.2 Add image upload methods to SanPhamController
    - Implement `uploadImage($id)` method using FileUpload class
    - Validate image format and size
    - Handle la_anh_chinh flag (unset others if setting new main image)
    - Associate images with specific variants via phien_ban_id
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.8_
  
  - [x] 8.3 Add image deletion method to SanPhamController
    - Implement `deleteImage($imageId)` method
    - Delete physical file from server
    - Delete database record
    - _Requirements: 27.3, 27.4_
  
  - [x] 8.4 Create image gallery management view
    - Create `app/views/admin/san_pham/images.php` or integrate into edit view
    - Display image thumbnails with thu_tu, la_anh_chinh indicator
    - Add upload form with alt_text, thu_tu, la_anh_chinh fields
    - Add delete buttons for each image
    - _Requirements: 9.7, 27.1, 27.2, 27.5, 27.6_
  
  - [x] 8.5 Add image management routes
    - Add POST `/admin/san-pham/upload-anh` route
    - Add POST `/admin/san-pham/xoa-anh` route
    - _Requirements: 9.1, 27.3_

- [x] 9. Implement Technical Specifications Management
  - [x] 9.1 Create ThongSoKyThuat model with specification methods
    - Implement `layTheoSanPham()` to get specifications ordered by thu_tu
    - Implement `capNhatHoacTao()` to replace all specifications for a product
    - _Requirements: 10.1, 10.2_
  
  - [x] 9.2 Add specification management to SanPhamController
    - Add specification handling in product edit workflow
    - Process specification array from form submission
    - Call `capNhatHoacTao()` to update specifications
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_
  
  - [x] 9.3 Add specification management to product edit view
    - Add dynamic specification input fields in edit form
    - Allow adding/removing specification rows with JavaScript
    - Include ten_thong_so, gia_tri, thu_tu fields
    - _Requirements: 10.1, 10.2, 10.3_

- [x] 10. Checkpoint - Verify product, variant, image, and specification modules
  - Ensure all tests pass, ask the user if questions arise.

- [x] 11. Implement Promotion Management module
  - [x] 11.1 Create KhuyenMai model with promotion methods
    - Implement `layDanhSach()` with status filtering and pagination
    - Implement `layDanhSachSanPhamLienKet()` to get linked products
    - Implement `xoaLienKetSanPham()` to delete all product links
    - Implement `themLienKetSanPham()` to insert product-promotion links
    - Implement `capNhatTrangThaiHetHan()` to auto-update expired promotions
    - _Requirements: 11.6, 11.7, 19.3, 19.4, 19.6_
  
  - [x] 11.2 Create KhuyenMaiController
    - Implement `index()` method with status filtering
    - Implement `create()` and `store()` methods
    - Implement `edit($id)` and `update($id)` methods
    - Implement `delete($id)` method
    - Implement `linkProducts($id)` to display product linking interface
    - Implement `saveProductLinks($id)` to save product selections
    - _Requirements: 11.1, 11.6, 19.1, 19.2, 19.3, 19.4, 19.5_
  
  - [x] 11.3 Implement promotion validation logic
    - Validate ten_chuong_trinh, loai_giam, gia_tri_giam, ngay_bat_dau, ngay_ket_thuc
    - Validate gia_tri_giam is 0-100 when loai_giam = PHAN_TRAM
    - Require giam_toi_da when loai_giam = PHAN_TRAM
    - Validate ngay_bat_dau < ngay_ket_thuc
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_
  
  - [x] 11.4 Create promotion views
    - Create `app/views/admin/khuyen_mai/index.php` for promotion listing
    - Create `app/views/admin/khuyen_mai/create.php` for promotion create form
    - Create `app/views/admin/khuyen_mai/edit.php` for promotion edit form
    - Create `app/views/admin/khuyen_mai/link_products.php` for product linking interface
    - _Requirements: 11.1, 19.1, 19.5_
  
  - [x] 11.5 Add promotion routes
    - Add GET `/admin/khuyen-mai` route
    - Add GET/POST `/admin/khuyen-mai/them` routes
    - Add GET/POST `/admin/khuyen-mai/sua` routes
    - Add POST `/admin/khuyen-mai/xoa` route
    - Add GET/POST `/admin/khuyen-mai/lien-ket-san-pham` routes
    - _Requirements: 11.1, 19.2_

- [x] 12. Implement Discount Code Management module
  - [x] 12.1 Create MaGiamGia model with discount code methods
    - Implement `layDanhSach()` with status filtering and pagination
    - Implement `kiemTraMaCode()` for code uniqueness validation
    - Implement `tangSoLuotDung()` to increment usage and update status
    - Implement `capNhatTrangThaiHetHan()` to auto-update expired codes
    - Implement `capNhatTrangThaiHetLuot()` to auto-update codes at usage limit
    - _Requirements: 12.2, 12.5, 12.7_
  
  - [x] 12.2 Create MaGiamGiaController
    - Implement `index()` method with status filtering
    - Implement `create()` and `store()` methods
    - Implement `edit($id)` and `update($id)` methods
    - Implement `delete($id)` method
    - Add code generation helper method
    - _Requirements: 12.1, 12.6_
  
  - [x] 12.3 Implement discount code validation logic
    - Validate ma_code uniqueness and format (uppercase letters and numbers only)
    - Validate loai_giam, gia_tri_giam, don_toi_thieu, gioi_han_su_dung
    - Validate gia_tri_giam is 0-100 when loai_giam = PHAN_TRAM
    - Validate ngay_bat_dau < ngay_ket_thuc
    - _Requirements: 12.1, 12.2, 12.3, 12.4_
  
  - [x] 12.4 Create discount code views
    - Create `app/views/admin/ma_giam_gia/index.php` for code listing
    - Create `app/views/admin/ma_giam_gia/create.php` for code create form
    - Create `app/views/admin/ma_giam_gia/edit.php` for code edit form
    - Display so_luot_da_dung and remaining usage count
    - _Requirements: 12.1, 12.7_
  
  - [x] 12.5 Add discount code routes
    - Add GET `/admin/ma-giam-gia` route
    - Add GET/POST `/admin/ma-giam-gia/them` routes
    - Add GET/POST `/admin/ma-giam-gia/sua` routes
    - Add POST `/admin/ma-giam-gia/xoa` route
    - _Requirements: 12.1_

- [x] 13. Checkpoint - Verify promotion and discount code modules
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 14. Implement User Management module
  - [ ] 14.1 Add user management methods to NguoiDung model
    - Implement `layDanhSach()` with loai_tai_khoan and trang_thai filtering
    - Implement `timKiem()` for searching by email, ho_ten, or sdt
    - Implement `layTheoKhoangNgay()` for registration date filtering
    - Implement `chanNguoiDung()` to set trang_thai = BLOCKED
    - Implement `moChanNguoiDung()` to set trang_thai = ACTIVE
    - Implement `demNguoiDung()` for pagination counts
    - _Requirements: 13.1, 13.6, 22.3_
  
  - [ ] 14.2 Create NguoiDungController
    - Implement `index()` method with filtering by loai_tai_khoan, trang_thai, date range
    - Implement search functionality
    - Implement pagination with 20 records per page
    - Implement `detail($id)` method to view user details
    - Implement `block($id)` method with self-blocking prevention
    - Implement `unblock($id)` method
    - Implement `bulkUpdateStatus()` for bulk operations
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7, 20.3, 20.4, 22.3, 22.5_
  
  - [ ] 14.3 Create user management views
    - Update `app/views/admin/nguoi_dung/index.php` with user listing table
    - Create `app/views/admin/nguoi_dung/detail.php` for user detail view
    - Add filtering controls for loai_tai_khoan, trang_thai, date range
    - Add search input for email, ho_ten, sdt
    - Add block/unblock buttons
    - Add bulk selection checkboxes
    - _Requirements: 13.1, 13.2, 13.3, 13.6, 20.3, 22.3_
  
  - [ ] 14.4 Add user management routes
    - Update GET `/admin/nguoi-dung` route
    - Add GET `/admin/nguoi-dung/chi-tiet` route
    - Add POST `/admin/nguoi-dung/chan` route
    - Add POST `/admin/nguoi-dung/mo-chan` route
    - Add POST `/admin/nguoi-dung/cap-nhat-hang-loat` route
    - _Requirements: 13.4, 13.5, 20.3_

- [ ] 15. Implement Review Moderation module
  - [ ] 15.1 Add review moderation methods to DanhGia model
    - Implement `layDanhSach()` with so_sao and san_pham_id filtering
    - Implement `timKiem()` for searching by review content or user name
    - Implement `demDanhGia()` for pagination counts
    - Join with nguoi_dung and san_pham tables for display
    - _Requirements: 14.1, 14.2, 14.3, 22.4_
  
  - [ ] 15.2 Create DanhGiaController
    - Implement `index()` method with filtering by so_sao and san_pham_id
    - Implement search functionality
    - Implement pagination with 20 records per page
    - Implement `detail($id)` method to view review details
    - Implement `delete($id)` method to remove inappropriate reviews
    - Order reviews by ngay_viet DESC
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5, 22.4_
  
  - [ ] 15.3 Create review moderation views
    - Create `app/views/admin/danh_gia/index.php` for review listing
    - Create `app/views/admin/danh_gia/detail.php` for review detail view
    - Display user name, product name, so_sao, noi_dung, ngay_viet
    - Add filtering controls for so_sao and san_pham_id
    - Add search input
    - Add delete buttons with confirmation
    - _Requirements: 14.1, 14.2, 14.3_
  
  - [ ] 15.4 Add review moderation routes
    - Add GET `/admin/danh-gia` route
    - Add GET `/admin/danh-gia/chi-tiet` route
    - Add POST `/admin/danh-gia/xoa` route
    - _Requirements: 14.4_

- [ ] 16. Update sidebar navigation
  - Update `app/views/admin/layouts/sidebar.php` with all module links
  - Add Dashboard, Orders, Payments, Products, Categories, Promotions, Discount Codes, Users, Reviews menu items
  - Organize related functions into collapsible menu groups
  - Implement active menu highlighting based on current route
  - _Requirements: 17.1, 17.2, 17.3, 17.5_

- [ ] 17. Implement breadcrumb navigation
  - Update `app/views/admin/layouts/breadcrumb.php` to display dynamic breadcrumbs
  - Generate breadcrumb hierarchy based on current route
  - Make all breadcrumb items except current page clickable
  - _Requirements: 18.1, 18.2, 18.3, 18.4_

- [ ] 18. Add bulk operations functionality
  - Implement bulk status toggle for categories in DanhMucController
  - Implement bulk status update for products in SanPhamController
  - Implement bulk status update for users in NguoiDungController (already added in task 14.2)
  - Display success count and failed records for bulk operations
  - _Requirements: 20.1, 20.2, 20.3, 20.4, 20.5_

- [ ] 19. Implement data integrity constraints
  - Add category deletion prevention when products exist
  - Add product deletion prevention when orders exist
  - Implement transaction rollback on constraint violations
  - Display user-friendly error messages for constraint violations
  - _Requirements: 21.1, 21.2, 21.3, 21.4, 21.5_

- [ ] 20. Add comprehensive search and filtering
  - Implement product filtering by danh_muc_id, hang_san_xuat, trang_thai, price range (enhance existing)
  - Implement order filtering by trang_thai, date range, payment method (already added in task 3.2)
  - Implement user filtering by loai_tai_khoan, trang_thai, date range (already added in task 14.2)
  - Implement promotion filtering by trang_thai and date range
  - Preserve filter selections in query parameters across pagination
  - Display filtered result counts
  - _Requirements: 22.1, 22.2, 22.3, 22.4, 22.5, 22.6_

- [ ] 21. Enhance data tables with sorting
  - Add sortable column headers to all listing tables
  - Implement sort by clicking column headers
  - Indicate current sort column and direction with visual indicators
  - Preserve sort state across pagination
  - _Requirements: 23.5, 23.6_

- [ ] 22. Implement session timeout handling
  - Update Session class with 2-hour timeout configuration
  - Add last_activity tracking
  - Implement automatic logout on timeout
  - Redirect to login page with timeout message
  - _Requirements: 25.1, 25.2, 25.3, 25.4, 25.5_

- [ ] 23. Add audit trail timestamps
  - Ensure ngay_tao is set on record creation in all models
  - Ensure ngay_cap_nhat is updated on record modification in all models
  - Display timestamps in detail views with consistent format (YYYY-MM-DD HH:MM:SS)
  - Use server timezone for all timestamp operations
  - _Requirements: 26.1, 26.2, 26.3, 26.4, 26.5_

- [ ] 24. Implement hierarchical category display
  - Update category listing to display parent categories before children
  - Add visual indentation for child categories
  - Display full category path in breadcrumb format
  - Filter parent options in edit form to exclude self and descendants
  - Allow category movement by changing danh_muc_cha_id
  - _Requirements: 29.1, 29.2, 29.3, 29.4, 29.5_

- [ ] 25. Final checkpoint - Integration testing
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- All tasks reference specific requirements for traceability
- The implementation uses PHP with the existing MVC architecture
- All admin routes are protected by AdminMiddleware
- File uploads use the FileUpload helper class for security and validation
- All forms include CSRF protection and input sanitization
- Pagination is set to 20 records per page across all modules
- Error messages are user-friendly and preserve form state on validation failure
- Database operations use the existing BaseModel and entity model patterns
