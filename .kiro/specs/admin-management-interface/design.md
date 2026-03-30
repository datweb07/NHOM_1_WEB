# Design Document: Admin Management Interface

## Overview

The admin management interface provides a comprehensive web-based control panel for FPT Shop administrators to manage all aspects of the e-commerce platform. Built on the existing PHP MVC architecture, this system extends the current admin infrastructure with complete CRUD operations for orders, products, categories, promotions, users, and reviews.

The design leverages the existing database schema, AdminMiddleware authentication, and admin layout components (header, sidebar, footer, breadcrumb) to create a cohesive administrative experience. All admin routes are protected and require `loai_tai_khoan = 'ADMIN'` authentication.

### Key Design Principles

- **Consistency**: All controllers follow the same structural pattern for predictable maintenance
- **Security**: Input validation, SQL injection prevention, and role-based access control throughout
- **Usability**: Clear feedback messages, form validation with error preservation, and intuitive navigation
- **Maintainability**: Separation of concerns between controllers, models, and views
- **Scalability**: Pagination, filtering, and search capabilities for large datasets

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                     Admin Interface Layer                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │Dashboard │  │ Orders   │  │ Products │  │  Users   │   │
│  │Controller│  │Controller│  │Controller│  │Controller│   │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘   │
└───────┼─────────────┼─────────────┼─────────────┼──────────┘
        │             │             │             │
        └─────────────┴─────────────┴─────────────┘
                      │
        ┌─────────────┴─────────────┐
        │   AdminMiddleware Layer    │
        │  (Authentication & Auth)   │
        └─────────────┬───────────────┘
                      │
        ┌─────────────┴─────────────┐
        │      Model Layer           │
        │  ┌──────────┐ ┌─────────┐ │
        │  │BaseModel │ │ Entity  │ │
        │  │          │ │ Models  │ │
        │  └────┬─────┘ └────┬────┘ │
        └───────┼────────────┼───────┘
                │            │
        ┌───────┴────────────┴───────┐
        │      Database Layer         │
        │         (MySQL)             │
        └─────────────────────────────┘
```

### Request Flow

1. **Request Reception**: User accesses admin route (e.g., `/admin/don-hang`)
2. **Middleware Check**: AdminMiddleware validates session and role
3. **Controller Processing**: Appropriate controller method handles request
4. **Model Interaction**: Controller calls model methods for data operations
5. **View Rendering**: Controller loads view template with data
6. **Response**: HTML page rendered to user


## Components and Interfaces

### Controller Structure

All admin controllers follow a consistent pattern:

```php
class [Entity]Controller
{
    private $[entity]Model;
    
    public function __construct() {
        // Load model
    }
    
    public function index(): void {
        // List view with filtering/pagination
    }
    
    public function create(array $old = [], array $errors = []): void {
        // Show create form
    }
    
    public function store(): void {
        // Process create form submission
    }
    
    public function edit($id, array $old = [], array $errors = []): void {
        // Show edit form
    }
    
    public function update($id): void {
        // Process edit form submission
    }
    
    public function delete($id): void {
        // Soft delete or status change
    }
    
    private function validatePayload(array $input, int $editingId = 0): array {
        // Validation logic
        // Returns [$payload, $errors, $old]
    }
}
```

### Controller Specifications

#### 1. DashboardController

**Location**: `app/controllers/admin/DashboardController.php`

**Methods**:
- `index()`: Display dashboard with statistics

**Responsibilities**:
- Query counts for pending orders, active users, available products, pending payments
- Calculate monthly statistics
- Provide navigation links to all management modules

**Data Requirements**:
```php
[
    'pendingOrders' => int,      // COUNT WHERE trang_thai = 'CHO_DUYET'
    'totalUsers' => int,         // COUNT WHERE loai_tai_khoan = 'MEMBER'
    'activeProducts' => int,     // COUNT WHERE trang_thai = 'CON_BAN'
    'pendingPayments' => int,    // COUNT WHERE trang_thai_duyet = 'CHO_DUYET'
    'monthlyRevenue' => float,   // SUM(tong_thanh_toan) for current month
    'monthlyOrders' => int       // COUNT for current month
]
```


#### 2. DonHangController (Enhanced)

**Location**: `app/controllers/admin/DonHangController.php`

**Current Methods** (already implemented):
- `index()`: List orders with status filtering
- `detail($id)`: View order details
- `capNhatTrangThai($id)`: Update order status

**Enhancement Requirements**:
- Add search functionality by `ma_don_hang` or customer name
- Add pagination (20 records per page)
- Add date range filtering
- Add payment method filtering
- Integrate with ThanhToanController for payment approval workflow

**Status Transition Logic**:
```
CHO_DUYET → DA_XAC_NHAN → DANG_GIAO → DA_GIAO → HOAN_THANH
         ↘ DA_HUY
DA_GIAO → TRA_HANG
```

#### 3. ThanhToanController (New)

**Location**: `app/controllers/admin/ThanhToanController.php`

**Methods**:
- `index()`: List pending payments (trang_thai_duyet = 'CHO_DUYET')
- `detail($id)`: View payment details with receipt image
- `approve($id)`: Approve payment (POST)
- `reject($id)`: Reject payment (POST)

**Approval Workflow**:
```php
public function approve($id): void {
    // Validate admin session
    // Load payment record
    // Update trang_thai_duyet = 'THANH_CONG'
    // Set nguoi_duyet_id = current admin ID
    // Set ngay_duyet = current timestamp
    // Optional: add ghi_chu_duyet
    // Redirect with success message
}
```

**Data Structure**:
```php
[
    'payment' => [
        'id' => int,
        'don_hang_id' => int,
        'ma_don_hang' => string,
        'phuong_thuc' => string,
        'so_tien' => float,
        'anh_bien_lai' => string|null,
        'trang_thai_duyet' => string,
        'ngay_thanh_toan' => string,
        'customer_name' => string,
        'customer_email' => string
    ]
]
```


#### 4. SanPhamController (Enhanced)

**Location**: `app/controllers/admin/SanPhamController.php`

**Current Methods**:
- `index()`: List products with search and filtering
- `xoa($id)`: Soft delete (set trang_thai = 'NGUNG_BAN')
- `moBan($id)`: Restore product

**Enhancement Requirements**:
- Add `create()` and `store()` methods for product creation
- Add `edit($id)` and `update($id)` methods for product editing
- Add variant management methods
- Add image upload handling
- Add technical specifications management

**New Methods**:

```php
public function create(array $old = [], array $errors = []): void {
    // Load categories for dropdown
    // Display create form
}

public function store(): void {
    // Validate input
    // Generate slug if not provided
    // Create product record
    // Redirect to variant creation or product list
}

public function edit($id, array $old = [], array $errors = []): void {
    // Load product by ID
    // Load categories
    // Load existing variants
    // Load existing images
    // Load technical specifications
    // Display edit form
}

public function update($id): void {
    // Validate input
    // Update product record
    // Handle cascade updates to variants if trang_thai changes
    // Redirect with success message
}

public function uploadImage($id): void {
    // Validate file upload
    // Check file type and size
    // Generate unique filename
    // Move file to storage directory
    // Create hinh_anh_san_pham record
    // Handle la_anh_chinh flag
}

public function deleteImage($imageId): void {
    // Load image record
    // Delete physical file
    // Delete database record
}
```

**Validation Rules**:
- `ten_san_pham`: Required, max 255 characters
- `slug`: Unique, lowercase, alphanumeric with hyphens only
- `hang_san_xuat`: Optional, max 100 characters
- `mo_ta`: Optional, text
- `danh_muc_id`: Must exist in danh_muc table
- `trang_thai`: Must be one of: CON_BAN, NGUNG_BAN, SAP_RA_MAT, HET_HANG


#### 5. DanhMucController (Complete)

**Location**: `app/controllers/admin/DanhMucController.php`

**Status**: Already fully implemented

**Methods**:
- `index()`: List categories with hierarchical display
- `create()`: Show create form
- `store()`: Process create form
- `edit($id)`: Show edit form
- `update($id)`: Process edit form
- `xoa($id)`: Hide category (trang_thai = 0)
- `hien($id)`: Show category (trang_thai = 1)

**Features**:
- Hierarchical category support via `danh_muc_cha_id`
- Automatic slug generation from `ten`
- Slug uniqueness validation
- Parent-child relationship validation (prevent self-reference)
- Display order control via `thu_tu`

#### 6. KhuyenMaiController (New)

**Location**: `app/controllers/admin/KhuyenMaiController.php`

**Methods**:
- `index()`: List promotions with status filtering
- `create()`: Show promotion create form
- `store()`: Process promotion creation
- `edit($id)`: Show promotion edit form
- `update($id)`: Process promotion update
- `delete($id)`: Delete promotion
- `linkProducts($id)`: Show product linking interface
- `saveProductLinks($id)`: Save product-promotion relationships

**Validation Rules**:
- `ten_chuong_trinh`: Required, max 255 characters
- `loai_giam`: Required, must be 'PHAN_TRAM' or 'SO_TIEN'
- `gia_tri_giam`: Required, must be > 0
- If `loai_giam = 'PHAN_TRAM'`: `gia_tri_giam` must be 0-100, `giam_toi_da` required
- `ngay_bat_dau`: Required, must be valid datetime
- `ngay_ket_thuc`: Required, must be after `ngay_bat_dau`

**Product Linking Logic**:
```php
public function saveProductLinks($id): void {
    // Get promotion
    // Get selected product IDs from POST
    // Delete existing links: DELETE FROM san_pham_khuyen_mai WHERE khuyen_mai_id = $id
    // Insert new links for each selected product
    // Redirect with success message
}
```


#### 7. MaGiamGiaController (New)

**Location**: `app/controllers/admin/MaGiamGiaController.php`

**Methods**:
- `index()`: List discount codes with filtering
- `create()`: Show discount code create form
- `store()`: Process discount code creation
- `edit($id)`: Show discount code edit form
- `update($id)`: Process discount code update
- `delete($id)`: Delete discount code

**Validation Rules**:
- `ma_code`: Required, unique, uppercase letters and numbers only, max 50 characters
- `loai_giam`: Required, must be 'PHAN_TRAM' or 'SO_TIEN'
- `gia_tri_giam`: Required, must be > 0
- If `loai_giam = 'PHAN_TRAM'`: `gia_tri_giam` must be 0-100
- `don_toi_thieu`: Optional, must be >= 0
- `gioi_han_su_dung`: Optional (NULL = unlimited), must be > 0 if set
- `ngay_bat_dau`: Required, must be valid datetime
- `ngay_ket_thuc`: Required, must be after `ngay_bat_dau`

**Code Generation Helper**:
```php
private function generateCode(int $length = 8): string {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}
```

#### 8. NguoiDungController (New)

**Location**: `app/controllers/admin/NguoiDungController.php`

**Methods**:
- `index()`: List users with filtering and search
- `detail($id)`: View user details
- `block($id)`: Block user (trang_thai = 'BLOCKED')
- `unblock($id)`: Unblock user (trang_thai = 'ACTIVE')
- `bulkUpdateStatus()`: Bulk status update

**Filtering Options**:
- By `loai_tai_khoan`: ADMIN, MEMBER, or all
- By `trang_thai`: ACTIVE, BLOCKED, UNVERIFIED, or all
- By registration date range
- Search by email, ho_ten, or sdt

**Pagination**: 20 records per page

**Security Note**: Prevent admins from blocking themselves


#### 9. DanhGiaController (New)

**Location**: `app/controllers/admin/DanhGiaController.php`

**Methods**:
- `index()`: List reviews with filtering
- `detail($id)`: View review details
- `delete($id)`: Delete inappropriate review

**Filtering Options**:
- By `so_sao`: 1-5 stars or all
- By `san_pham_id`: specific product or all
- Search by review content or user name

**Display Information**:
```php
[
    'review' => [
        'id' => int,
        'nguoi_dung_id' => int,
        'user_name' => string,
        'user_email' => string,
        'san_pham_id' => int,
        'product_name' => string,
        'so_sao' => int,
        'noi_dung' => string,
        'ngay_viet' => string
    ]
]
```

**Pagination**: 20 records per page

### View Template Structure

All admin views follow this structure:

```php
<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="app-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/sidebar.php'; ?>
    
    <main class="app-main">
        <?php require_once dirname(__DIR__) . '/layouts/breadcrumb.php'; ?>
        
        <div class="app-content">
            <div class="container-fluid">
                <!-- Page-specific content -->
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <!-- Main content area -->
            </div>
        </div>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
```


### View Templates by Module

#### Dashboard Views
- `app/views/admin/dashboard/index.php`: Main dashboard with statistics cards

#### Order Views
- `app/views/admin/don_hang/index.php`: Order listing (exists)
- `app/views/admin/don_hang/detail.php`: Order detail (exists)

#### Payment Views (New)
- `app/views/admin/thanh_toan/index.php`: Pending payments list
- `app/views/admin/thanh_toan/detail.php`: Payment detail with receipt image

#### Product Views
- `app/views/admin/san_pham/index.php`: Product listing (exists)
- `app/views/admin/san_pham/create.php`: Product create form (exists)
- `app/views/admin/san_pham/edit.php`: Product edit form (exists)
- `app/views/admin/san_pham/variants.php`: Variant management (new)
- `app/views/admin/san_pham/images.php`: Image gallery management (new)

#### Category Views
- `app/views/admin/danh_muc/index.php`: Category listing (exists)
- `app/views/admin/danh_muc/create.php`: Category create form (exists)
- `app/views/admin/danh_muc/edit.php`: Category edit form (exists)

#### Promotion Views (New)
- `app/views/admin/khuyen_mai/index.php`: Promotion listing
- `app/views/admin/khuyen_mai/create.php`: Promotion create form
- `app/views/admin/khuyen_mai/edit.php`: Promotion edit form
- `app/views/admin/khuyen_mai/link_products.php`: Product linking interface

#### Discount Code Views (New)
- `app/views/admin/ma_giam_gia/index.php`: Discount code listing
- `app/views/admin/ma_giam_gia/create.php`: Discount code create form
- `app/views/admin/ma_giam_gia/edit.php`: Discount code edit form

#### User Views (New)
- `app/views/admin/nguoi_dung/index.php`: User listing (exists but empty)
- `app/views/admin/nguoi_dung/detail.php`: User detail view

#### Review Views (New)
- `app/views/admin/danh_gia/index.php`: Review listing
- `app/views/admin/danh_gia/detail.php`: Review detail view


## Data Models

### Model Enhancements

#### DonHang Model (Enhanced)

**New Methods**:
```php
public function timKiem(string $keyword, ?string $trangThai = null, int $limit = 20, int $offset = 0): array {
    // Search by ma_don_hang or customer name
    // Filter by trang_thai if provided
    // Apply pagination
}

public function layTheoKhoangNgay(string $from, string $to, ?string $trangThai = null): array {
    // Filter orders by date range
}

public function layTheoPhuongThuc(string $phuongThuc): array {
    // Filter orders by payment method
}

public function demDonHang(?string $trangThai = null): int {
    // Count orders for pagination
}
```

#### ThanhToan Model (New Methods)

**Location**: `app/models/entities/ThanhToan.php`

**New Methods**:
```php
public function layDanhSachChoDuyet(int $limit = 20, int $offset = 0): array {
    // Get payments with trang_thai_duyet = 'CHO_DUYET'
    // Join with don_hang and nguoi_dung for display
    // Apply pagination
}

public function duyetThanhToan(int $id, int $nguoiDuyetId, ?string $ghiChu = null): int {
    // Update trang_thai_duyet = 'THANH_CONG'
    // Set nguoi_duyet_id and ngay_duyet
    // Optional ghi_chu_duyet
}

public function tuChoiThanhToan(int $id, int $nguoiDuyetId, ?string $ghiChu = null): int {
    // Update trang_thai_duyet = 'THAT_BAI'
    // Set nguoi_duyet_id and ngay_duyet
    // Optional ghi_chu_duyet
}

public function demChoDuyet(): int {
    // Count pending payments for dashboard
}
```


#### PhienBanSanPham Model (New)

**Location**: `app/models/entities/PhienBanSanPham.php`

**Methods**:
```php
public function layTheoSanPham(int $sanPhamId): array {
    // Get all variants for a product
}

public function capNhatSoLuongTon(int $id, int $soLuong): int {
    // Update so_luong_ton
    // Auto-update trang_thai based on stock level
}

public function kiemTraSKU(string $sku, int $excludeId = 0): bool {
    // Check if SKU exists (for uniqueness validation)
}
```

#### HinhAnhSanPham Model (New)

**Location**: `app/models/entities/HinhAnhSanPham.php`

**Methods**:
```php
public function layTheoSanPham(int $sanPhamId): array {
    // Get all images for a product ordered by thu_tu
}

public function datAnhChinh(int $id, int $sanPhamId): int {
    // Set la_anh_chinh = 0 for all images of product
    // Set la_anh_chinh = 1 for specified image
}

public function xoaVaXoaFile(int $id): bool {
    // Get image record
    // Delete physical file
    // Delete database record
}
```

#### ThongSoKyThuat Model (New)

**Location**: `app/models/entities/ThongSoKyThuat.php`

**Methods**:
```php
public function layTheoSanPham(int $sanPhamId): array {
    // Get all specifications for a product ordered by thu_tu
}

public function capNhatHoacTao(int $sanPhamId, array $specifications): void {
    // Delete existing specifications
    // Insert new specifications
}
```


#### KhuyenMai Model (New)

**Location**: `app/models/entities/KhuyenMai.php`

**Methods**:
```php
public function layDanhSach(?string $trangThai = null, int $limit = 20, int $offset = 0): array {
    // List promotions with optional status filter
}

public function layDanhSachSanPhamLienKet(int $khuyenMaiId): array {
    // Get products linked to promotion via san_pham_khuyen_mai
}

public function xoaLienKetSanPham(int $khuyenMaiId): int {
    // Delete all product links for promotion
}

public function themLienKetSanPham(int $khuyenMaiId, array $sanPhamIds): int {
    // Insert product-promotion links
}

public function capNhatTrangThaiHetHan(): int {
    // Auto-update trang_thai = 'DA_HET_HAN' where ngay_ket_thuc < NOW()
}
```

#### MaGiamGia Model (New)

**Location**: `app/models/entities/MaGiamGia.php`

**Methods**:
```php
public function layDanhSach(?string $trangThai = null, int $limit = 20, int $offset = 0): array {
    // List discount codes with optional status filter
}

public function kiemTraMaCode(string $maCode, int $excludeId = 0): bool {
    // Check if code exists (for uniqueness validation)
}

public function tangSoLuotDung(int $id): int {
    // Increment so_luot_da_dung
    // Auto-update trang_thai if limit reached
}

public function capNhatTrangThaiHetHan(): int {
    // Auto-update trang_thai = 'DA_HET_HAN' where ngay_ket_thuc < NOW()
}

public function capNhatTrangThaiHetLuot(): int {
    // Auto-update trang_thai = 'HET_LUOT' where so_luot_da_dung >= gioi_han_su_dung
}
```


#### NguoiDung Model (Enhanced)

**Location**: `app/models/abstract/NguoiDung.php` or create `app/models/entities/NguoiDung.php`

**New Methods**:
```php
public function layDanhSach(?string $loaiTaiKhoan = null, ?string $trangThai = null, int $limit = 20, int $offset = 0): array {
    // List users with filtering
}

public function timKiem(string $keyword, int $limit = 20, int $offset = 0): array {
    // Search by email, ho_ten, or sdt
}

public function layTheoKhoangNgay(string $from, string $to): array {
    // Filter users by registration date range
}

public function chanNguoiDung(int $id): int {
    // Update trang_thai = 'BLOCKED'
}

public function moChanNguoiDung(int $id): int {
    // Update trang_thai = 'ACTIVE'
}

public function demNguoiDung(?string $loaiTaiKhoan = null, ?string $trangThai = null): int {
    // Count users for pagination and dashboard
}
```

#### DanhGia Model (Enhanced)

**Location**: `app/models/entities/DanhGia.php`

**New Methods**:
```php
public function layDanhSach(?int $soSao = null, ?int $sanPhamId = null, int $limit = 20, int $offset = 0): array {
    // List reviews with filtering
    // Join with nguoi_dung and san_pham for display
}

public function timKiem(string $keyword, int $limit = 20, int $offset = 0): array {
    // Search by review content or user name
}

public function demDanhGia(?int $soSao = null, ?int $sanPhamId = null): int {
    // Count reviews for pagination
}
```


## Route Definitions

### Admin Routes Structure

**Location**: `app/routes/admin/admin.php`

```php
// Dashboard
$router->get('/admin', 'admin/DashboardController@index');
$router->get('/admin/dashboard', 'admin/DashboardController@index');

// Orders
$router->get('/admin/don-hang', 'admin/DonHangController@index');
$router->get('/admin/don-hang/chi-tiet', 'admin/DonHangController@detail');
$router->post('/admin/don-hang/cap-nhat-trang-thai', 'admin/DonHangController@capNhatTrangThai');

// Payments
$router->get('/admin/thanh-toan', 'admin/ThanhToanController@index');
$router->get('/admin/thanh-toan/chi-tiet', 'admin/ThanhToanController@detail');
$router->post('/admin/thanh-toan/duyet', 'admin/ThanhToanController@approve');
$router->post('/admin/thanh-toan/tu-choi', 'admin/ThanhToanController@reject');

// Products
$router->get('/admin/san-pham', 'admin/SanPhamController@index');
$router->get('/admin/san-pham/them', 'admin/SanPhamController@create');
$router->post('/admin/san-pham/them', 'admin/SanPhamController@store');
$router->get('/admin/san-pham/sua', 'admin/SanPhamController@edit');
$router->post('/admin/san-pham/sua', 'admin/SanPhamController@update');
$router->post('/admin/san-pham/xoa', 'admin/SanPhamController@xoa');
$router->post('/admin/san-pham/mo-ban', 'admin/SanPhamController@moBan');
$router->post('/admin/san-pham/upload-anh', 'admin/SanPhamController@uploadImage');
$router->post('/admin/san-pham/xoa-anh', 'admin/SanPhamController@deleteImage');

// Categories
$router->get('/admin/danh-muc', 'admin/DanhMucController@index');
$router->get('/admin/danh-muc/them', 'admin/DanhMucController@create');
$router->post('/admin/danh-muc/them', 'admin/DanhMucController@store');
$router->get('/admin/danh-muc/sua', 'admin/DanhMucController@edit');
$router->post('/admin/danh-muc/sua', 'admin/DanhMucController@update');
$router->post('/admin/danh-muc/xoa', 'admin/DanhMucController@xoa');
$router->post('/admin/danh-muc/hien', 'admin/DanhMucController@hien');

// Promotions
$router->get('/admin/khuyen-mai', 'admin/KhuyenMaiController@index');
$router->get('/admin/khuyen-mai/them', 'admin/KhuyenMaiController@create');
$router->post('/admin/khuyen-mai/them', 'admin/KhuyenMaiController@store');
$router->get('/admin/khuyen-mai/sua', 'admin/KhuyenMaiController@edit');
$router->post('/admin/khuyen-mai/sua', 'admin/KhuyenMaiController@update');
$router->post('/admin/khuyen-mai/xoa', 'admin/KhuyenMaiController@delete');
$router->get('/admin/khuyen-mai/lien-ket-san-pham', 'admin/KhuyenMaiController@linkProducts');
$router->post('/admin/khuyen-mai/lien-ket-san-pham', 'admin/KhuyenMaiController@saveProductLinks');

// Discount Codes
$router->get('/admin/ma-giam-gia', 'admin/MaGiamGiaController@index');
$router->get('/admin/ma-giam-gia/them', 'admin/MaGiamGiaController@create');
$router->post('/admin/ma-giam-gia/them', 'admin/MaGiamGiaController@store');
$router->get('/admin/ma-giam-gia/sua', 'admin/MaGiamGiaController@edit');
$router->post('/admin/ma-giam-gia/sua', 'admin/MaGiamGiaController@update');
$router->post('/admin/ma-giam-gia/xoa', 'admin/MaGiamGiaController@delete');

// Users
$router->get('/admin/nguoi-dung', 'admin/NguoiDungController@index');
$router->get('/admin/nguoi-dung/chi-tiet', 'admin/NguoiDungController@detail');
$router->post('/admin/nguoi-dung/chan', 'admin/NguoiDungController@block');
$router->post('/admin/nguoi-dung/mo-chan', 'admin/NguoiDungController@unblock');
$router->post('/admin/nguoi-dung/cap-nhat-hang-loat', 'admin/NguoiDungController@bulkUpdateStatus');

// Reviews
$router->get('/admin/danh-gia', 'admin/DanhGiaController@index');
$router->get('/admin/danh-gia/chi-tiet', 'admin/DanhGiaController@detail');
$router->post('/admin/danh-gia/xoa', 'admin/DanhGiaController@delete');
```

### Middleware Integration

All admin routes must be protected by AdminMiddleware:

```php
// In router.php or admin route file
AdminMiddleware::checkAdmin();
```

This should be called at the beginning of the admin route file to protect all routes.


## File Upload Handling

### Upload Configuration

**Storage Directories**:
- Product images: `public/uploads/products/`
- Payment receipts: `public/uploads/receipts/`
- User avatars: `public/uploads/avatars/`

**Validation Rules**:
- Allowed image types: JPEG, PNG, GIF, WEBP
- Maximum file size: 5MB
- MIME type validation in addition to extension check

### Upload Helper Class

**Location**: `app/core/FileUpload.php`

```php
class FileUpload
{
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_FILE_SIZE = 5242880; // 5MB in bytes
    
    public static function validateImage(array $file): array {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed';
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $errors[] = 'File size exceeds 5MB limit';
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::ALLOWED_IMAGE_TYPES, true)) {
            $errors[] = 'Invalid file type. Only JPEG, PNG, GIF, and WEBP are allowed';
        }
        
        return $errors;
    }
    
    public static function generateUniqueFilename(string $originalName): string {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return "{$timestamp}_{$random}.{$extension}";
    }
    
    public static function uploadImage(array $file, string $directory): ?string {
        // Validate
        $errors = self::validateImage($file);
        if (!empty($errors)) {
            return null;
        }
        
        // Ensure directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Generate unique filename
        $filename = self::generateUniqueFilename($file['name']);
        $destination = $directory . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
        
        return null;
    }
    
    public static function deleteFile(string $path): bool {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
}
```


### Image Upload in Controllers

**Example: Product Image Upload**

```php
public function uploadImage($id): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        header('Location: /admin/san-pham');
        exit;
    }
    
    $id = (int)$id;
    if ($id <= 0) {
        header('Location: /admin/san-pham?error=invalid_id');
        exit;
    }
    
    // Validate product exists
    $product = $this->sanPhamModel->getById($id);
    if (!$product) {
        header('Location: /admin/san-pham?error=not_found');
        exit;
    }
    
    // Validate file upload
    if (!isset($_FILES['image'])) {
        header("Location: /admin/san-pham/sua?id={$id}&error=no_file");
        exit;
    }
    
    require_once dirname(__DIR__, 2) . '/core/FileUpload.php';
    
    $errors = FileUpload::validateImage($_FILES['image']);
    if (!empty($errors)) {
        $errorMsg = urlencode(implode(', ', $errors));
        header("Location: /admin/san-pham/sua?id={$id}&error={$errorMsg}");
        exit;
    }
    
    // Upload file
    $directory = dirname(__DIR__, 3) . '/public/uploads/products/';
    $filename = FileUpload::uploadImage($_FILES['image'], $directory);
    
    if ($filename === null) {
        header("Location: /admin/san-pham/sua?id={$id}&error=upload_failed");
        exit;
    }
    
    // Save to database
    require_once dirname(__DIR__, 2) . '/models/entities/HinhAnhSanPham.php';
    $imageModel = new HinhAnhSanPham();
    
    $imageData = [
        'san_pham_id' => $id,
        'url_anh' => '/uploads/products/' . $filename,
        'alt_text' => addslashes($_POST['alt_text'] ?? ''),
        'la_anh_chinh' => isset($_POST['la_anh_chinh']) ? 1 : 0,
        'thu_tu' => (int)($_POST['thu_tu'] ?? 0)
    ];
    
    // If setting as main image, unset others
    if ($imageData['la_anh_chinh'] === 1) {
        $imageModel->datAnhChinh(0, $id); // Unset all
    }
    
    $imageModel->create($imageData);
    
    header("Location: /admin/san-pham/sua?id={$id}&success=image_uploaded");
    exit;
}
```


## Form Validation Approach

### Validation Pattern

All controllers follow this validation pattern:

```php
private function validatePayload(array $input, int $editingId = 0): array {
    $errors = [];
    $payload = [];
    $old = [];
    
    // Extract and trim inputs
    $field1 = trim((string)($input['field1'] ?? ''));
    $field2 = trim((string)($input['field2'] ?? ''));
    
    // Validate required fields
    if ($field1 === '') {
        $errors['field1'] = 'Field 1 is required';
    }
    
    // Validate format
    if (!preg_match('/pattern/', $field1)) {
        $errors['field1'] = 'Field 1 format is invalid';
    }
    
    // Validate uniqueness
    if ($this->model->exists($field1, $editingId)) {
        $errors['field1'] = 'Field 1 already exists';
    }
    
    // Build payload (escaped for SQL)
    $payload = [
        'field1' => addslashes($field1),
        'field2' => addslashes($field2)
    ];
    
    // Build old values (for form repopulation)
    $old = [
        'field1' => $field1,
        'field2' => $field2
    ];
    
    return [$payload, $errors, $old];
}
```

### Common Validation Rules

**Email Validation**:
```php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format';
}
```

**Phone Number Validation** (Vietnamese format):
```php
if (!preg_match('/^(0|\+84)[0-9]{9,10}$/', $sdt)) {
    $errors['sdt'] = 'Invalid phone number format';
}
```

**Slug Validation**:
```php
if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    $errors['slug'] = 'Slug must contain only lowercase letters, numbers, and hyphens';
}
```

**Date Validation**:
```php
$date = DateTime::createFromFormat('Y-m-d H:i:s', $input);
if (!$date || $date->format('Y-m-d H:i:s') !== $input) {
    $errors['date'] = 'Invalid date format';
}
```

**Date Range Validation**:
```php
if (strtotime($ngayBatDau) >= strtotime($ngayKetThuc)) {
    $errors['ngay_ket_thuc'] = 'End date must be after start date';
}
```

**Numeric Range Validation**:
```php
if ($giaTriGiam < 0 || $giaTriGiam > 100) {
    $errors['gia_tri_giam'] = 'Value must be between 0 and 100';
}
```


### Error Display in Views

**Form Error Display Pattern**:

```php
<div class="form-group">
    <label for="field1">Field Label <span class="text-danger">*</span></label>
    <input 
        type="text" 
        class="form-control <?= isset($errors['field1']) ? 'is-invalid' : '' ?>" 
        id="field1" 
        name="field1" 
        value="<?= htmlspecialchars($old['field1'] ?? '') ?>"
    >
    <?php if (isset($errors['field1'])): ?>
        <div class="invalid-feedback d-block">
            <?= htmlspecialchars($errors['field1']) ?>
        </div>
    <?php endif; ?>
</div>
```

**Success/Error Message Display**:

```php
<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        $messages = [
            'created' => 'Record created successfully',
            'updated' => 'Record updated successfully',
            'deleted' => 'Record deleted successfully',
            'status_updated' => 'Status updated successfully'
        ];
        echo htmlspecialchars($messages[$success] ?? $success);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php
        $messages = [
            'invalid_id' => 'Invalid ID provided',
            'not_found' => 'Record not found',
            'invalid_transition' => 'Invalid status transition',
            'upload_failed' => 'File upload failed'
        ];
        echo htmlspecialchars($messages[$error] ?? $error);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
```


## Session Management

### Session Structure

The admin system uses PHP sessions managed by the `Session` class:

```php
// Session data structure
$_SESSION = [
    'user_id' => int,           // nguoi_dung.id
    'email' => string,          // nguoi_dung.email
    'ho_ten' => string,         // nguoi_dung.ho_ten
    'loai_tai_khoan' => string, // 'ADMIN' or 'MEMBER'
    'avatar_url' => string|null // nguoi_dung.avatar_url
];
```

### Session Validation

**AdminMiddleware Implementation**:

```php
public static function checkAdmin(): void {
    Session::start();
    
    // Check if logged in
    if (!Session::isLoggedIn()) {
        header('Location: /admin/auth/login');
        exit;
    }
    
    // Check role
    $userRole = Session::getUserRole();
    
    if ($userRole === LoaiTaiKhoan::MEMBER) {
        Session::set('error_message', 'Không có quyền truy cập');
        header('Location: /');
        exit;
    }
    
    if ($userRole !== LoaiTaiKhoan::ADMIN) {
        header('Location: /admin/auth/login');
        exit;
    }
}
```

### Session Timeout

**Configuration**: 2 hours of inactivity

```php
// In Session class
public static function start(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.gc_maxlifetime', 7200); // 2 hours
        session_start();
        
        // Check for timeout
        if (isset($_SESSION['last_activity'])) {
            $inactive = time() - $_SESSION['last_activity'];
            if ($inactive > 7200) {
                self::destroy();
                header('Location: /admin/auth/login?timeout=1');
                exit;
            }
        }
        
        $_SESSION['last_activity'] = time();
    }
}
```

### Logout Functionality

```php
public function logout(): void {
    Session::destroy();
    header('Location: /admin/auth/login?logged_out=1');
    exit;
}
```


## Error Handling

### Error Handling Strategy

**Three-Layer Error Handling**:

1. **Validation Errors**: User input validation errors displayed in forms
2. **Application Errors**: Business logic errors (e.g., invalid state transitions)
3. **System Errors**: Database errors, file system errors, unexpected exceptions

### Validation Error Handling

```php
// In controller
[$payload, $errors, $old] = $this->validatePayload($_POST, $id);

if (!empty($errors)) {
    // Redisplay form with errors and old values
    $this->edit($id, $old, $errors);
    return;
}

// Proceed with update
$this->model->update($id, $payload);
```

### Application Error Handling

```php
// Check business rules
if (!$this->donHangModel->trangThaiHopLe($currentStatus, $newStatus)) {
    header("Location: /admin/don-hang/chi-tiet?id={$id}&error=invalid_transition");
    exit;
}

// Check data integrity
if ($this->danhMucModel->hasProducts($id)) {
    header("Location: /admin/danh-muc?error=has_products");
    exit;
}
```

### System Error Handling

**Database Error Handling**:

```php
// In BaseModel or specific models
public function query($sql) {
    try {
        $result = chayTruyVanTraVeDL($this->link, $sql);
        
        if ($result === false) {
            error_log("Database query failed: " . mysqli_error($this->link));
            throw new Exception("Database query failed");
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    } catch (Exception $e) {
        error_log("Exception in query: " . $e->getMessage());
        throw $e;
    }
}
```

**File Upload Error Handling**:

```php
public static function uploadImage(array $file, string $directory): ?string {
    try {
        // Validation
        $errors = self::validateImage($file);
        if (!empty($errors)) {
            error_log("Image validation failed: " . implode(', ', $errors));
            return null;
        }
        
        // Directory creation
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                error_log("Failed to create directory: {$directory}");
                return null;
            }
        }
        
        // File move
        $filename = self::generateUniqueFilename($file['name']);
        $destination = $directory . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            error_log("Failed to move uploaded file to: {$destination}");
            return null;
        }
        
        return $filename;
    } catch (Exception $e) {
        error_log("Exception in uploadImage: " . $e->getMessage());
        return null;
    }
}
```


### Global Error Handler

**Location**: `app/core/ErrorHandler.php`

```php
class ErrorHandler
{
    public static function handleException(Throwable $e): void {
        error_log("Uncaught exception: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // In production, show generic error page
        if (getenv('APP_ENV') === 'production') {
            http_response_code(500);
            require_once __DIR__ . '/../views/errors/500.php';
            exit;
        }
        
        // In development, show detailed error
        http_response_code(500);
        echo "<h1>Error</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        exit;
    }
    
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        error_log("Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}");
        
        // Convert errors to exceptions for consistent handling
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}

// Register handlers
set_exception_handler([ErrorHandler::class, 'handleException']);
set_error_handler([ErrorHandler::class, 'handleError']);
```

### Error Logging

All errors should be logged to PHP error log:

```php
// Log validation errors
error_log("Validation failed for user {$userId}: " . json_encode($errors));

// Log business logic errors
error_log("Invalid status transition attempted: {$from} -> {$to}");

// Log system errors
error_log("Database connection failed: " . mysqli_connect_error());

// Log security events
error_log("Unauthorized access attempt by user {$userId} to admin area");
```

### User-Friendly Error Messages

**Error Message Mapping**:

```php
// In controller or view
$errorMessages = [
    // Validation errors
    'required' => 'This field is required',
    'invalid_format' => 'Invalid format',
    'already_exists' => 'This value already exists',
    
    // Business logic errors
    'invalid_transition' => 'Invalid status transition',
    'has_dependencies' => 'Cannot delete: record has dependencies',
    'insufficient_stock' => 'Insufficient stock available',
    
    // System errors
    'database_error' => 'A database error occurred. Please try again',
    'upload_failed' => 'File upload failed. Please try again',
    'permission_denied' => 'You do not have permission to perform this action',
    
    // Generic
    'unknown_error' => 'An unexpected error occurred. Please contact support'
];
```

