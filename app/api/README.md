# API Folder

Folder này chứa các API endpoints được gọi bởi AJAX từ frontend.

## Cấu trúc

```
app/api/
├── CategoryAttributesApi.php    # API lấy thuộc tính động theo danh mục
└── README.md                     # File này
```

## Quy tắc

### 1. Naming Convention

- Tên file: `{Feature}Api.php` (PascalCase + Api suffix)
- Tên class: Trùng với tên file
- Ví dụ: `CategoryAttributesApi.php` → `class CategoryAttributesApi`

### 2. Response Format

Tất cả API phải trả về JSON với format chuẩn:

```json
{
  "success": true,
  "data": [...],
  "message": "Optional message"
}
```

### 3. Error Handling

```json
{
  "success": false,
  "data": [],
  "message": "Error description"
}
```

### 4. Headers

Luôn set header JSON:

```php
header('Content-Type: application/json; charset=utf-8');
```

### 5. Security

- Luôn validate input
- Sử dụng `addslashes()` hoặc prepared statements để chống SQL Injection
- Kiểm tra quyền truy cập nếu cần (authentication/authorization)

## Cách sử dụng

### Từ Controller

```php
public function someApiMethod(): void
{
    require_once dirname(__DIR__, 2) . '/api/YourApi.php';
    // API file sẽ tự xử lý request và trả về JSON response
}
```

### Từ AJAX (Frontend)

```javascript
fetch('/admin/san-pham/api/category-attributes?category=Điện Thoại')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data.data);
        } else {
            console.error(data.message);
        }
    });
```

## Ví dụ: CategoryAttributesApi

### Request

```
GET /admin/san-pham/api/category-attributes?category=Điện Thoại
```

### Response

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

## Lợi ích của cấu trúc này

1. **Separation of Concerns**: Tách biệt logic API khỏi Controller
2. **Reusability**: API có thể được gọi từ nhiều nơi
3. **Maintainability**: Dễ dàng tìm và sửa lỗi
4. **Testability**: Dễ dàng test riêng từng API
5. **Scalability**: Dễ dàng thêm API mới

## Best Practices

### ✅ DO

- Validate tất cả input
- Trả về response format chuẩn
- Log errors khi cần
- Sử dụng HTTP status codes phù hợp (nếu framework hỗ trợ)
- Document API endpoints

### ❌ DON'T

- Không hardcode dữ liệu trong API
- Không trả về thông tin nhạy cảm (passwords, tokens)
- Không bỏ qua validation
- Không để lộ stack trace ra ngoài production

## Thêm API mới

### Bước 1: Tạo file API

```php
<?php
// app/api/YourFeatureApi.php

require_once dirname(__DIR__) . '/models/BaseModel.php';

class YourFeatureApi
{
    private $baseModel;

    public function __construct()
    {
        $this->baseModel = new BaseModel('your_table');
    }

    public function getData(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        // Your logic here
        
        $this->sendResponse(true, $data);
    }

    private function sendResponse(bool $success, array $data, ?string $message = null): void
    {
        $response = [
            'success' => $success,
            'data' => $data
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$api = new YourFeatureApi();
$api->getData();
```

### Bước 2: Thêm method trong Controller

```php
public function yourApiMethod(): void
{
    require_once dirname(__DIR__, 2) . '/api/YourFeatureApi.php';
}
```

### Bước 3: Thêm route (nếu cần)

```php
// app/routes/admin/admin.php
$router->get('/admin/your-feature/api/endpoint', 'YourController@yourApiMethod');
```

## Troubleshooting

### Lỗi: "Class not found"

**Nguyên nhân**: Đường dẫn require_once sai

**Giải pháp**: Kiểm tra lại đường dẫn relative path

```php
// Từ Controller (app/controllers/admin/)
require_once dirname(__DIR__, 2) . '/api/YourApi.php';

// Từ API (app/api/)
require_once dirname(__DIR__) . '/models/BaseModel.php';
```

### Lỗi: "Headers already sent"

**Nguyên nhân**: Có output trước khi gọi `header()`

**Giải pháp**: 
- Xóa bỏ mọi `echo`, `print_r`, `var_dump` trước `header()`
- Kiểm tra không có whitespace/BOM ở đầu file PHP

### Lỗi: JSON parse error

**Nguyên nhân**: Response không phải JSON hợp lệ

**Giải pháp**:
- Kiểm tra `json_encode()` có lỗi không
- Sử dụng `JSON_UNESCAPED_UNICODE` để hỗ trợ tiếng Việt
- Test response bằng Postman/curl

## Tài liệu tham khảo

- [PHP JSON Functions](https://www.php.net/manual/en/ref.json.php)
- [RESTful API Best Practices](https://restfulapi.net/)
- [HTTP Status Codes](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status)
