# Header Dynamic Categories - Hướng dẫn

## Tổng quan

Đã thay thế danh mục hardcode trong header bằng dữ liệu động từ database. Giờ đây, khi admin thêm/sửa/xóa danh mục trong admin panel, header sẽ tự động cập nhật mà không cần sửa code.

## Những gì đã thay đổi

### 1. Tạo HeaderHelper (`app/core/HeaderHelper.php`)

Helper class chứa các method để load dữ liệu cho header:

```php
HeaderHelper::layDanhMucCha(10);        // Lấy 10 danh mục cha
HeaderHelper::layDanhMucCon($parentId); // Lấy danh mục con
HeaderHelper::layIconClass($name);      // Map tên → icon class
```

### 2. Cập nhật Header View (`app/views/client/layouts/header.php`)

**Trước (Hardcode):**
```php
<li class="nav-item">
    <a class="nav-link" href="/san-pham">
        <i class="fa fa-mobile"></i> Điện thoại
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="/san-pham">
        <i class="fa fa-laptop"></i> Laptop
    </a>
</li>
<!-- ... nhiều item hardcode khác -->
```

**Sau (Dynamic):**
```php
<?php foreach ($danhMucCha as $category): 
    $iconClass = HeaderHelper::layIconClass($category['ten']);
    $categoryUrl = '/san-pham?danh_muc_id=' . $category['id'];
?>
<li class="nav-item">
    <a class="nav-link" href="<?= htmlspecialchars($categoryUrl) ?>">
        <i class="<?= htmlspecialchars($iconClass) ?>"></i> 
        <?= htmlspecialchars($category['ten']) ?>
    </a>
</li>
<?php endforeach; ?>
```

## Cấu trúc Database

### Bảng `danh_muc`

```sql
CREATE TABLE `danh_muc` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `ten` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255),
    `icon_url` VARCHAR(500),
    `danh_muc_cha_id` INT DEFAULT NULL,
    `thu_tu` INT DEFAULT 0,
    `trang_thai` TINYINT(1) DEFAULT 1,
    `is_noi_bat` TINYINT(1) DEFAULT 0,
    `is_goi_y` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`)
);
```

### Các trường quan trọng:

- `ten`: Tên danh mục hiển thị
- `slug`: URL-friendly name
- `icon_url`: URL icon (nếu có)
- `danh_muc_cha_id`: ID danh mục cha (NULL = danh mục gốc)
- `thu_tu`: Thứ tự hiển thị (số nhỏ hiển thị trước)
- `trang_thai`: 1 = hiển thị, 0 = ẩn
- `is_noi_bat`: 1 = danh mục nổi bật
- `is_goi_y`: 1 = danh mục gợi ý

## Cách sử dụng

### Thêm danh mục mới

1. Vào Admin Panel → Danh mục → Thêm mới
2. Nhập thông tin:
   - Tên: "Tai nghe"
   - Slug: "tai-nghe"
   - Thứ tự: 5
   - Trạng thái: Hiển thị
3. Lưu lại

Header sẽ tự động hiển thị danh mục mới!

### Thay đổi thứ tự danh mục

1. Vào Admin Panel → Danh mục
2. Sửa trường "Thứ tự" của các danh mục
3. Lưu lại

Danh mục sẽ được sắp xếp lại theo thứ tự mới.

### Ẩn danh mục

1. Vào Admin Panel → Danh mục
2. Chọn danh mục cần ẩn
3. Đổi "Trạng thái" thành "Ẩn"
4. Lưu lại

Danh mục sẽ biến mất khỏi header.

## Icon Mapping

HeaderHelper tự động map tên danh mục sang icon FontAwesome:

| Tên danh mục | Icon Class |
|--------------|------------|
| Điện thoại | `fa fa-mobile` |
| Laptop | `fa fa-laptop` |
| Máy tính bảng | `fa fa-tablet` |
| Apple | `fa-brands fa-apple` |
| PC-Linh kiện | `fa fa-desktop` |
| Phụ kiện | `fa fa-headphones` |
| Máy cũ giá rẻ | `fa fa-rotate-right` |
| Hàng gia dụng | `fa fa-house-laptop` |
| Sim & Thẻ cào | `fa fa-sd-card` |
| Khuyến mãi | `fa fa-certificate` |

### Thêm icon mới

Mở `app/core/HeaderHelper.php` và thêm vào array `$iconMap`:

```php
$iconMap = [
    // ... existing icons
    'Tai nghe' => 'fa fa-headphones',
    'Camera' => 'fa fa-camera',
];
```

## URL Structure

Danh mục sẽ link đến trang sản phẩm với filter:

```
/san-pham?danh_muc_id=1
/san-pham?danh_muc_id=2
```

Trang `/san-pham` sẽ nhận `danh_muc_id` và filter sản phẩm theo danh mục đó.

## Performance

### Caching (Tương lai)

Để tối ưu performance, có thể cache danh mục:

```php
// app/core/HeaderHelper.php
public static function layDanhMucCha(int $limit = 10): array
{
    $cacheKey = 'header_categories_' . $limit;
    
    // Check cache
    if ($cached = Cache::get($cacheKey)) {
        return $cached;
    }
    
    // Fetch from DB
    $categories = /* ... query ... */;
    
    // Store in cache for 1 hour
    Cache::set($cacheKey, $categories, 3600);
    
    return $categories;
}
```

## Troubleshooting

### Danh mục không hiển thị

**Nguyên nhân:**
- `trang_thai = 0` (đã ẩn)
- `danh_muc_cha_id` không NULL (là danh mục con)

**Giải pháp:**
```sql
-- Kiểm tra danh mục
SELECT id, ten, trang_thai, danh_muc_cha_id 
FROM danh_muc 
WHERE ten = 'Tên danh mục';

-- Hiển thị danh mục
UPDATE danh_muc SET trang_thai = 1 WHERE id = [ID];
```

### Icon không hiển thị đúng

**Nguyên nhân:**
- Tên danh mục không khớp với `$iconMap`

**Giải pháp:**
- Thêm mapping mới vào `HeaderHelper::layIconClass()`
- Hoặc đổi tên danh mục cho khớp với mapping có sẵn

### Thứ tự không đúng

**Nguyên nhân:**
- Trường `thu_tu` chưa được set đúng

**Giải pháp:**
```sql
-- Cập nhật thứ tự
UPDATE danh_muc SET thu_tu = 1 WHERE ten = 'Điện thoại';
UPDATE danh_muc SET thu_tu = 2 WHERE ten = 'Laptop';
UPDATE danh_muc SET thu_tu = 3 WHERE ten = 'Máy tính bảng';
```

## Mở rộng

### Thêm Mega Menu động

Hiện tại mega menu vẫn đang bị comment. Để thêm mega menu động:

1. Tạo method trong HeaderHelper:
```php
public static function layMegaMenuData(int $categoryId): array
{
    // Lấy danh mục con
    $subCategories = self::layDanhMucCon($categoryId);
    
    // Lấy sản phẩm bán chạy
    $hotProducts = /* ... query ... */;
    
    // Lấy banner
    $banner = /* ... query ... */;
    
    return [
        'subCategories' => $subCategories,
        'hotProducts' => $hotProducts,
        'banner' => $banner
    ];
}
```

2. Cập nhật header.php:
```php
<?php 
$megaMenuData = HeaderHelper::layMegaMenuData($category['id']);
?>
<div class="mega-menu">
    <!-- Render mega menu từ $megaMenuData -->
</div>
```

### Thêm breadcrumb động

```php
public static function layBreadcrumb(int $categoryId): array
{
    $breadcrumb = [];
    $currentId = $categoryId;
    
    while ($currentId !== null) {
        $category = /* ... get category ... */;
        array_unshift($breadcrumb, $category);
        $currentId = $category['danh_muc_cha_id'];
    }
    
    return $breadcrumb;
}
```

## Best Practices

1. **Luôn set `thu_tu`**: Để kiểm soát thứ tự hiển thị
2. **Sử dụng `slug`**: Để tạo URL thân thiện SEO
3. **Kiểm tra `trang_thai`**: Trước khi hiển thị
4. **Escape output**: Sử dụng `htmlspecialchars()` để tránh XSS
5. **Cache khi cần**: Nếu traffic cao

## Kết luận

Header giờ đây hoàn toàn động và dễ dàng quản lý qua Admin Panel. Không cần sửa code khi thêm/sửa/xóa danh mục!

**Lợi ích:**
- ✅ Dễ dàng quản lý danh mục
- ✅ Không cần sửa code
- ✅ Tự động cập nhật
- ✅ Hỗ trợ phân cấp danh mục
- ✅ Linh hoạt mở rộng
