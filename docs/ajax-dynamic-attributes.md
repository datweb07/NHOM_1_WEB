# Hướng dẫn: Hệ thống Thuộc tính Động với AJAX

## Tổng quan

Hệ thống này cho phép quản lý thuộc tính sản phẩm động dựa trên danh mục, sử dụng AJAX để tải cấu hình từ server thay vì hardcode trong JavaScript.

## Kiến trúc

### 1. Backend (PHP)

**File**: `app/controllers/admin/SanPhamController.php`

**Method**: `getCategoryAttributes()`

```php
public function getCategoryAttributes(): void
{
    header('Content-Type: application/json; charset=utf-8');
    
    $categoryName = $_GET['category'] ?? '';
    $attributes = [];
    
    // Logic phân loại thuộc tính theo danh mục
    // Trong thực tế, query từ database
    
    echo json_encode([
        'success' => true,
        'data' => $attributes
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
```

**Cấu trúc dữ liệu trả về**:

```json
{
  "success": true,
  "data": [
    {
      "name": "RAM",
      "label": "Dung lượng RAM",
      "placeholder": "VD: 8GB",
      "type": "text",
      "col": 6
    },
    {
      "name": "Dung_luong",
      "label": "Bộ nhớ trong",
      "placeholder": "VD: 256GB",
      "type": "text",
      "col": 6
    }
  ]
}
```

### 2. Frontend (JavaScript)

**File**: `app/views/admin/san_pham/variants.php`

**Function**: `renderDynamicInputsAJAX()`

```javascript
async function renderDynamicInputsAJAX(categoryName, containerId, existingData = null) {
    const container = document.getElementById(containerId);
    
    // Hiển thị loading
    container.innerHTML = '<div class="spinner-border"></div>';
    
    try {
        // Gọi API
        const response = await fetch(`/admin/api/get-category-attributes?category=${encodeURIComponent(categoryName)}`);
        const result = await response.json();
        
        // Render form từ JSON
        const attributes = result.data;
        let html = '';
        
        attributes.forEach(attr => {
            html += `
                <div class="col-md-${attr.col}">
                    <div class="mb-3">
                        <label class="form-label">${attr.label}</label>
                        <input type="${attr.type}" 
                               name="thuoc_tinh[${attr.name}]" 
                               class="form-control" 
                               placeholder="${attr.placeholder}" 
                               value="${existingData?.[attr.name] || ''}">
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
        
    } catch (error) {
        console.error("Lỗi AJAX:", error);
        container.innerHTML = '<div class="text-danger">Không thể kết nối đến máy chủ.</div>';
    }
}
```

### 3. Routing

**File**: `app/routes/admin/admin.php`

```php
if ($path === 'admin/api/get-category-attributes' && $method === 'GET') {
    $sanPhamController->getCategoryAttributes();
    return;
}
```

**Lưu ý**: URL API phải có prefix `admin/` để routing nhận diện đúng.

## Cấu hình Thuộc tính theo Danh mục

### Nhóm 1: Thiết bị điện toán
- **Danh mục**: Điện Thoại, Laptop, Máy tính bảng, PC - Máy tính để bàn, Máy Mac
- **Thuộc tính**:
  - RAM (Dung lượng RAM)
  - Dung_luong (Bộ nhớ trong)

### Nhóm 2: Màn hình & Tivi
- **Danh mục**: Tivi, Màn hình
- **Thuộc tính**:
  - Kich_thuoc (Kích thước màn hình)
  - Do_phan_giai (Độ phân giải)

### Nhóm 3: Điện lạnh & Gia dụng lớn
- **Danh mục**: Máy lạnh - Điều hòa, Máy giặt, Tủ lạnh, Máy lọc nước
- **Thuộc tính**:
  - Cong_suat_Dung_tich (Công suất / Dung tích / Khối lượng)

### Nhóm 4: Đồng hồ thông minh
- **Danh mục**: Đồng hồ thông minh
- **Thuộc tính**:
  - Kich_thuoc_mat (Kích thước mặt)
  - Chat_lieu_day (Chất liệu dây)

## Luồng hoạt động

### 1. Khi tải trang (Form Thêm)

```
User truy cập trang → DOMContentLoaded event → 
renderDynamicInputsAJAX(productCategory, 'dynamic-attributes-container') →
Fetch API → Server trả JSON → Render form
```

### 2. Khi sửa phiên bản (Modal)

```
User click nút Sửa → editVariant(variant) →
Parse JSON thuộc tính cũ →
renderDynamicInputsAJAX(productCategory, 'edit-dynamic-attributes-container', thuocTinhData) →
Fetch API → Server trả JSON → Render form với dữ liệu cũ
```

### 3. Khi submit form

```
User submit form → PHP nhận $_POST['thuoc_tinh'] →
Lọc bỏ giá trị rỗng → json_encode() →
Lưu vào database (cột thuoc_tinh_bien_the)
```

## Nâng cấp lên Database

Hiện tại cấu hình thuộc tính được hardcode trong PHP. Để nâng cấp lên Enterprise-level:

### Bước 1: Tạo bảng `thuoc_tinh_danh_muc`

```sql
CREATE TABLE `thuoc_tinh_danh_muc` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `danh_muc_id` INT NOT NULL,
  `ten_thuoc_tinh` VARCHAR(100) NOT NULL COMMENT 'Tên key (VD: RAM, Dung_luong)',
  `nhan_hien_thi` VARCHAR(255) NOT NULL COMMENT 'Label hiển thị',
  `placeholder` VARCHAR(255) DEFAULT NULL,
  `loai_input` ENUM('text', 'number', 'select') DEFAULT 'text',
  `chieu_rong_cot` TINYINT DEFAULT 6 COMMENT 'Bootstrap col-md-*',
  `thu_tu` INT DEFAULT 0,
  `trang_thai` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Bước 2: Cập nhật method `getCategoryAttributes()`

```php
public function getCategoryAttributes(): void
{
    header('Content-Type: application/json; charset=utf-8');
    
    $categoryName = $_GET['category'] ?? '';
    
    // Lấy danh_muc_id từ tên danh mục
    $sql = "SELECT id FROM danh_muc WHERE ten = '" . addslashes($categoryName) . "' LIMIT 1";
    $result = $this->baseModel->query($sql);
    
    if (empty($result)) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }
    
    $danhMucId = $result[0]['id'];
    
    // Query thuộc tính từ database
    $sql = "SELECT 
                ten_thuoc_tinh as name,
                nhan_hien_thi as label,
                placeholder,
                loai_input as type,
                chieu_rong_cot as col
            FROM thuoc_tinh_danh_muc 
            WHERE danh_muc_id = $danhMucId 
              AND trang_thai = 1
            ORDER BY thu_tu ASC";
    
    $attributes = $this->baseModel->query($sql);
    
    echo json_encode([
        'success' => true,
        'data' => $attributes
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
```

### Bước 3: Tạo giao diện quản lý thuộc tính

Tạo CRUD để Admin có thể:
- Thêm/sửa/xóa thuộc tính cho từng danh mục
- Sắp xếp thứ tự hiển thị
- Bật/tắt thuộc tính

## Ưu điểm của phương pháp AJAX

### 1. Tính linh hoạt
- Dễ dàng thêm/sửa thuộc tính mà không cần sửa code
- Admin có thể tự quản lý cấu hình

### 2. Hiệu suất
- Chỉ tải thuộc tính cần thiết cho danh mục hiện tại
- Giảm kích thước HTML ban đầu

### 3. Bảo trì
- Code gọn gàng, tách biệt logic và dữ liệu
- Dễ mở rộng cho nhiều danh mục mới

### 4. Trải nghiệm người dùng
- Loading state cho biết đang tải dữ liệu
- Error handling rõ ràng khi có lỗi

## Xử lý lỗi

### 1. Lỗi kết nối
```javascript
catch (error) {
    console.error("Lỗi AJAX:", error);
    container.innerHTML = '<div class="text-danger">Không thể kết nối đến máy chủ.</div>';
}
```

### 2. Lỗi server
```javascript
if (!result.success) {
    container.innerHTML = '<div class="text-danger">Lỗi khi tải cấu hình thuộc tính.</div>';
    return;
}
```

### 3. Không có thuộc tính
```javascript
if (attributes.length === 0) {
    html = '<div class="text-muted"><em>Danh mục này không yêu cầu thuộc tính biến thể phụ.</em></div>';
}
```

## Testing

### 1. Test API endpoint
```bash
curl "http://localhost/admin/api/get-category-attributes?category=Điện%20Thoại"
```

### 2. Test trong trình duyệt
1. Mở trang quản lý phiên bản
2. Mở DevTools → Network tab
3. Kiểm tra request đến `/admin/api/get-category-attributes`
4. Xem response JSON

### 3. Test với các danh mục khác nhau
- Điện Thoại → Hiển thị RAM, Dung lượng
- Tivi → Hiển thị Kích thước, Độ phân giải
- Máy lạnh → Hiển thị Công suất

## Tài liệu tham khảo

- [Fetch API - MDN](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)
- [Async/Await - JavaScript.info](https://javascript.info/async-await)
- [JSON.stringify() - MDN](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/stringify)
