# Khắc phục lỗi: "Unexpected token '<', "<!DOCTYPE "... is not valid JSON"

## Mô tả lỗi

Khi gọi API AJAX, trình duyệt báo lỗi:
```
SyntaxError: Unexpected token '<', "<!DOCTYPE "... is not valid JSON
```

## Nguyên nhân

Server PHP không trả về JSON như mong đợi, mà trả về một trang HTML (thường là trang 404 hoặc trang chủ). Điều này xảy ra khi:

1. **Routing không nhận diện được đường dẫn API**
2. **URL API không đúng với cấu trúc routing**
3. **Middleware chặn request trước khi đến API endpoint**

## Cách khắc phục

### Bước 1: Kiểm tra cấu trúc routing

Dự án này sử dụng routing tập trung qua `public/index.php`:

```php
if ($path === 'admin' || strpos($path, 'admin/') === 0) {
    adminRoute($requestUri);  // Xử lý các route admin
} else {
    clientRoute($requestUri); // Xử lý các route client
}
```

**Quy tắc**: 
- URL bắt đầu bằng `/admin/` → Đi vào `adminRoute()`
- URL khác → Đi vào `clientRoute()`

### Bước 2: Đảm bảo URL API có prefix đúng

**❌ SAI** (không có prefix admin):
```javascript
fetch(`/api/get-category-attributes?category=${categoryName}`)
```
→ Routing sẽ gọi `clientRoute()` → Không tìm thấy → Trả về HTML 404

**✅ ĐÚNG** (có prefix admin):
```javascript
fetch(`/admin/api/get-category-attributes?category=${categoryName}`)
```
→ Routing sẽ gọi `adminRoute()` → Tìm thấy route → Trả về JSON

### Bước 3: Kiểm tra route đã được khai báo

Trong file `app/routes/admin/admin.php`, đảm bảo có:

```php
if ($path === 'admin/api/get-category-attributes' && $method === 'GET') {
    $sanPhamController->getCategoryAttributes();
    return;
}
```

**Lưu ý**: `$path` đã được trim `/` ở đầu và cuối, nên:
- URL: `/admin/api/get-category-attributes`
- `$path`: `admin/api/get-category-attributes`

### Bước 4: Kiểm tra middleware

Nếu API vẫn không hoạt động, kiểm tra xem có middleware nào chặn request không:

```php
// Trong adminRoute(), đảm bảo API routes được xử lý TRƯỚC middleware
if ($path === 'admin/api/get-category-attributes' && $method === 'GET') {
    // Không cần kiểm tra auth cho API này
    $sanPhamController->getCategoryAttributes();
    return;
}

// Các route khác mới kiểm tra auth
require_once dirname(__DIR__, 2) . '/middleware/AdminMiddleware.php';
AdminMiddleware::checkAdmin();
```

## Cách debug

### 1. Kiểm tra URL thực tế được gọi

Mở DevTools → Network tab → Xem request:
- **URL**: Phải là `/admin/api/get-category-attributes?category=...`
- **Status**: Nếu 404 → Route chưa được khai báo
- **Response**: Nếu là HTML → Routing sai

### 2. Test API trực tiếp

Mở trình duyệt và truy cập:
```
http://localhost/admin/api/get-category-attributes?category=Điện%20Thoại
```

**Kết quả mong đợi**:
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
    }
  ]
}
```

**Nếu thấy HTML** → Route chưa hoạt động

### 3. Thêm log để debug

Trong `app/routes/admin/admin.php`, thêm log:

```php
// Thêm ở đầu hàm adminRoute()
error_log("Admin Route - Path: " . $path);
error_log("Admin Route - Method: " . $method);

if ($path === 'admin/api/get-category-attributes' && $method === 'GET') {
    error_log("API Route matched!");
    $sanPhamController->getCategoryAttributes();
    return;
}
```

Xem log trong file error log của PHP để biết routing có nhận diện được không.

## Các lỗi thường gặp khác

### Lỗi 1: CORS (Cross-Origin Resource Sharing)

**Triệu chứng**: Console báo lỗi CORS

**Giải pháp**: Thêm header trong API endpoint:
```php
public function getCategoryAttributes(): void
{
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');
    // ...
}
```

### Lỗi 2: Session/Auth blocking API

**Triệu chứng**: API redirect về trang login

**Giải pháp**: Xử lý API routes trước khi kiểm tra auth:
```php
// Xử lý API trước
if (strpos($path, 'admin/api/') === 0) {
    // Handle API routes without auth check
}

// Sau đó mới check auth cho các route khác
AdminMiddleware::checkAdmin();
```

### Lỗi 3: PHP syntax error trong API endpoint

**Triệu chứng**: Response là HTML error page

**Giải pháp**: 
1. Kiểm tra syntax trong `getCategoryAttributes()`
2. Bật error reporting:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Checklist khắc phục

- [ ] URL API có prefix `/admin/` chưa?
- [ ] Route đã được khai báo trong `admin.php` chưa?
- [ ] `$path` trong route có khớp với URL không? (nhớ trim `/`)
- [ ] Method có đúng không? (GET/POST)
- [ ] API endpoint có `exit;` ở cuối không?
- [ ] Header `Content-Type: application/json` đã được set chưa?
- [ ] Có middleware nào chặn request không?
- [ ] Test trực tiếp URL API trong trình duyệt

## Kết luận

Lỗi "Unexpected token '<'" thường do routing không nhận diện được API endpoint. Giải pháp chính là:

1. **Thêm prefix `/admin/` vào URL API**
2. **Khai báo route đúng trong `admin.php`**
3. **Đảm bảo không có middleware chặn**

Sau khi sửa, API sẽ trả về JSON đúng như mong đợi.
