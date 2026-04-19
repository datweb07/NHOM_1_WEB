> Đọc bằng ngôn ngữ khác: [English](README.md)

# Website Thương mại Điện tử - FPT Shop

<img width="960" height="313" alt="fpt-shop-banner" src="https://github.com/datweb07/NHOM_1_WEB/blob/main/images/fpt-shop-banner.png" />

## Mô tả Dự án

Dự án website thương mại điện tử FPT Shop là một nền tảng mua sắm trực tuyến chuyên cung cấp các sản phẩm công nghệ, điện thoại di động, laptop, máy tính bảng và phụ kiện điện tử. Website mang đến trải nghiệm mua sắm hiện đại, tiện lợi với giao diện thân thiện và dễ sử dụng cho người dùng.

## Các Tính năng Chính

### Tính năng cho Khách hàng (Client)
- **Danh mục sản phẩm** — Duyệt sản phẩm với hình ảnh, thông số, giá và biến thể (màu sắc, dung lượng, RAM)
- **Chi tiết sản phẩm** — Bảng thông số đầy đủ, thư viện ảnh, chọn biến thể, sản phẩm liên quan
- **Giỏ hàng** — Thêm, cập nhật số lượng, xóa sản phẩm, lưu trữ qua session
- **Tìm kiếm** — Tìm kiếm XML thời gian thực với lịch sử tìm kiếm và từ khóa phổ biến
- **Yêu thích** — Lưu sản phẩm để mua sau, toggle từ card sản phẩm
- **Thanh toán** — Quy trình nhiều bước với chọn địa chỉ và phương thức thanh toán
- **Phương thức thanh toán** — VNPay (thẻ), PayPal (quốc tế), VietQR (QR chuyển khoản), COD (tiền mặt)
- **Quản lý đơn hàng** — Xem lịch sử, theo dõi trạng thái, hủy đơn đang chờ
- **Đánh giá sản phẩm** — Hệ thống sao + bình luận, mỗi sản phẩm đã mua được đánh giá một lần
- **Khuyến mãi** — Xem khuyến mãi đang hoạt động, áp dụng mã giảm giá/voucher khi thanh toán
- **Xác thực người dùng** — Đăng ký, đăng nhập, xác minh email, quên/đặt lại mật khẩu
- **Đăng nhập Google** — Đăng nhập một chạm qua Google OAuth (Supabase Auth)
- **Quản lý hồ sơ** — Cập nhật tên, SĐT, ngày sinh, giới tính, ảnh đại diện (upload Cloudinary)
- **Sổ địa chỉ** — Thêm/sửa/xóa nhiều địa chỉ giao hàng, đặt mặc định
- **Thiết kế đáp ứng** — Giao diện mobile-first, hoạt động trên mọi kích thước màn hình

### Tính năng cho Quản trị viên (Admin)
- **Dashboard** — Biểu đồ doanh thu, thống kê đơn hàng, sản phẩm bán chạy, hoạt động gần đây
- **Quản lý sản phẩm** — CRUD đầy đủ cho sản phẩm, biến thể, hình ảnh (Cloudinary), thông số kỹ thuật
- **Quản lý danh mục** — Danh mục phân cấp với cờ nổi bật/đề xuất
- **Quản lý đơn hàng** — Xem tất cả đơn, cập nhật trạng thái, xem chi tiết
- **Quản lý người dùng** — Xem tài khoản khách hàng, chi tiết tài khoản
- **Quản lý khuyến mãi** — Tạo/sửa/xóa chiến dịch khuyến mãi với liên kết sản phẩm
- **Quản lý mã giảm giá** — Tạo và quản lý mã voucher/coupon với giới hạn sử dụng
- **Quản lý banner** — Banner chính và banner quảng cáo với toggle hiển thị
- **Quản lý đánh giá** — Xem và kiểm duyệt đánh giá sản phẩm
- **Xác minh thanh toán** — Phê duyệt hoặc từ chối xác nhận thanh toán thủ công (VietQR/COD)
- **Sức khỏe cổng thanh toán** — Theo dõi tỷ lệ thành công/thất bại theo từng cổng
- **Hệ thống thông báo** — Thông báo admin thời gian thực cho đơn hàng và thanh toán mới
- **Quản lý hoàn tiền** — Xử lý hoàn tiền cho VNPay (mock sandbox) và PayPal

## Công nghệ Sử dụng

<img alt="fpt-shop-banner" src="https://github.com/datweb07/NHOM_1_WEB/blob/main/images/technology.png" />

| Tầng | Công nghệ |
|---|---|
| Backend | PHP 8.x, kiến trúc MVC, router tùy chỉnh dựa trên file |
| Frontend | HTML5, CSS3, JavaScript ES6+, Bootstrap 5, Font Awesome 6 |
| Cơ sở dữ liệu | MySQL 8.x (utf8mb4) |
| Lưu trữ ảnh | Cloudinary PHP SDK |
| Email | PHPMailer (bundled), Gmail SMTP |
| Xác thực | PHP Sessions + Supabase Auth (Google OAuth) |
| Thanh toán | VNPay, PayPal REST API v2, VietQR, COD |
| Bảo mật | Google reCAPTCHA v2 |
| Cache | Redis (predis) |
| Cấu hình môi trường | vlucas/phpdotenv |

## Cài đặt & Thiết lập

### Yêu cầu Hệ thống
- **PHP**: >= 8.0
- **MySQL**: >= 8.0
- **Composer**: Phiên bản mới nhất
- **Máy chủ Web (Web Server)**: Apache/Nginx hoặc máy chủ tích hợp sẵn của PHP

### Bước 1: Sao chép Kho lưu trữ (Clone Repository)
```bash
git clone [https://github.com/datweb07/NHOM_1_WEB.git](https://github.com/datweb07/NHOM_1_WEB.git)
cd NHOM_1_WEB
````

### Bước 2: Cài đặt các Thư viện phụ thuộc

  - Cài đặt Composer tại [liên kết này](https://getcomposer.org/download/)


```bash
composer install
```

### Bước 3: Cấu hình Biến môi trường

1.  Sao chép tệp môi trường mẫu:


```bash
cp .env.example .env
```

2.  Chỉnh sửa tệp `.env` với các cấu hình của bạn:


```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:3000

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=db_fpt
DB_USERNAME=root
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_DB=0

CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls

RECAPTCHA_SITE_KEY=your_recaptcha_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key

SUPABASE_URL=https://your-project-id.supabase.co
SUPABASE_ANON_KEY=your_supabase_anon_key
SUPABASE_JWT_SECRET=your_supabase_jwt_secret

VNPAY_TMN_CODE=your_tmn_code
VNPAY_HASH_SECRET=your_hash_secret
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html

VIETQR_BANK_ID=VCB
VIETQR_ACCOUNT_NO=your_account_number
VIETQR_ACCOUNT_NAME=TEN TAI KHOAN
VIETQR_TEMPLATE=compact2

PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_SECRET=your_paypal_secret
PAYPAL_MODE=sandbox
```

### Bước 4: Thiết lập Cơ sở dữ liệu

1.  Tạo một cơ sở dữ liệu mới trong MySQL:


```sql
CREATE DATABASE db_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2.  Nhập (Import) lược đồ cơ sở dữ liệu:


```bash
mysql -u root -p db_web < database/db_web.sql
```

Hoặc sử dụng phpMyAdmin để import tệp `database/db_web.sql`

### Bước 5: Cấu hình Cloudinary (Lưu trữ Hình ảnh)

1.  Đăng ký tài khoản miễn phí tại [Cloudinary](https://cloudinary.com/)
2.  Lấy thông tin xác thực (credentials) từ bảng điều khiển của bạn.
3.  Cập nhật tệp `.env` với thông tin xác thực Cloudinary của bạn.

### Bước 6: Cấu hình Dịch vụ Email 

Đối với Gmail SMTP:

1.  Bật xác thực 2 bước (2FA) trên tài khoản Google của bạn.
2.  Tạo Mật khẩu Ứng dụng (App Password): [Google App Passwords](https://myaccount.google.com/apppasswords)
3.  Điền mật khẩu vừa tạo vào phần `MAIL_PASSWORD` trong tệp `.env`

### Bước 7: Cấu hình Dịch vụ Login Google
Cấu hình Supabase và Google đám mây (Supabase & Google Cloud)

- Thiết lập Supabase:

1. Truy cập vào trang chủ [Supabase](https://supabase.com/), tạo tài khoản miễn phí và tạo một project mới
2. Sao chép lại URL project vừa mới tạo
3. Ở thanh điều hướng bên trái, truy cập vào **Authentication** → **Sign In/Providers**
4. Ở phần **Auth Providers** bấm chọn Google, thiết lập Google Cloud Console để điền các thông tin cần thiết

- Thiết lập Google Cloud Console:
1. Truy cập vào trang chủ [Google Cloud Console](https://console.cloud.google.com/?hl=vi)
2. Ở góc trái cạnh logo Goole Cloud, chọn cửa sổ và chọn **New project**
3. Ở **Prject name**, nhập tên dự án dễ nhớ vào, ví dụ `FPT-SHOP`, sau đó nhấn **Create**
4. Tiếp theo, bấm chọn vào dấu 3 gạch, chọn **APIs & Services** → **OAuth consent screen**
5. Ở **Overview** → **Google Auth Platform not configured yet**, chọn **Get started**
6. Ở **App Information**, điền **App name** dễ nhớ (có thể điền `FPT-SHOP`), **User support email** chọn email hiện tại đang login, rồi nhấn Next
7. Ở phần **Audience**, chọn **External**, rồi nhấn Next
8. Ở **Contact Infomation** nhập mail hiện tại đang login, nhấn Next rồi nhấn **Create**

- Tạo Client ID:
1. Ở menu bên trái, chọn **APIs & Services** → **Credentials**
2. Trên cùng chọn **Create credentials** → **OAuth client ID**
3. **Application type** chọn **Web application**, ở Name đặt tên dễ nhớ (ví dụ `Supabase Auth Client`)
4. Ở phần **Authorized redirect URIs**, nhấn **Add URL**, sau đó quay lại *Thiết lập Supabase* ở bước 4, copy đường dẫn của phần **Callback URL (for OAuth)** rồi quay lại paste vào URLs 1, sau đó nhấn **Create**
5. Ngay sau khi nhấn tạo, Google sẽ hiển thị một bảng popup chứa 2 chuỗi mã: **Client ID** và **Client Secret**, copy 2 chuỗi này, quay trở lại **(Authentication → Providers → Google)**, bật Enable Sign in with Google, dán 2 chuỗi này vào các ô tương ứng và nhấn Save

- Khai báo URL cho Ứng dụng Web:
1. Quay lại Supabase, vào **Authentication → URL Configuration**
2. **Site URL**: Nhập ``http://localhost:3000`` (development) hoặc ``https://yourdomain.com`` (production)
3. **Redirect URLs**: Thêm chính xác đường dẫn file xử lý callback trên hệ thống PHP của bạn. 
Ví dụ: ``http://localhost:3000/app/views/client/auth/callback.php``
4. Cập nhật tệp `.env` với thông tin xác thực Cloudinary của bạn

### Bước 8: Google reCAPTCHA v2

1. Truy cập https://www.google.com/recaptcha/admin/create.
2. Ở **Nhãn**, nhập tên (ví dụ: `FPT Shop`).
3. Ở **Loại reCAPTCHA**, chọn **reCAPTCHA v2** → "Tôi không phải robot" Checkbox.
4. Ở **Tên miền**, thêm `localhost` (cho development) và tên miền production của bạn.
5. Nhấn **Gửi** — bạn sẽ nhận được **Khóa trang web (Site Key)** và **Khóa bí mật (Secret Key)**.
6. Sao chép vào `.env`:
   ```env
   RECAPTCHA_SITE_KEY=6Lc...your_site_key
   RECAPTCHA_SECRET_KEY=6Lc...your_secret_key
   ```
   
### Bước 9: VNPay (Thanh toán thẻ — Sandbox)

1. Truy cập https://sandbox.vnpayment.vn/devreg/ và đăng ký tài khoản merchant sandbox.
2. Sau khi đăng ký, đăng nhập vào cổng sandbox.
3. Vào **Thông tin tích hợp** để tìm:
   - **TMN Code** (`vnp_TmnCode`) — mã terminal merchant của bạn
   - **Hash Secret** (`vnp_HashSecret`) — khóa ký của bạn
4. Sao chép vào `.env`:
   ```env
   VNPAY_TMN_CODE=XXXXXXXX
   VNPAY_HASH_SECRET=your_hash_secret_here
   VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
   ```
5. Để test, dùng số thẻ test của VNPay tại: https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html hoặc số tài khoản đã được cấp trong email.

> **Lưu ý**: VNPay sandbox không hỗ trợ hoàn tiền thực. Dự án này sử dụng **mock hoàn tiền** — ghi log thao tác mà không gọi API VNPay thực.

### Bước 10: PayPal (Thanh toán quốc tế — Sandbox)

1. Truy cập https://developer.paypal.com/ và đăng nhập bằng tài khoản PayPal.
2. Vào **Apps & Credentials** → đảm bảo đang ở chế độ **Sandbox**.
3. Nhấn **Create App** → đặt tên `FPT Shop` → **Create App**.
4. Bạn sẽ thấy **Client ID** và **Secret key 1** — sao chép:
   ```env
   PAYPAL_CLIENT_ID=AXxx...your_client_id
   PAYPAL_SECRET=EXxx...your_secret
   PAYPAL_MODE=sandbox
   ```
5. Để test, dùng tài khoản buyer sandbox từ **Testing Tools** → **Sandbox Accounts** → Chọn tài khoản có `Type` là Personal, trong đó sẽ có `Email` và `Password` dùng để test thanh toán.
6. Quy đổi tiền tệ: Số tiền VND chia cho 25.000 để ra USD (ví dụ: 500.000 VND → $20.00 USD).

> Khi lên production, đổi `PAYPAL_MODE=live` và thay bằng credentials thực.

### Bước 11: VietQR (QR chuyển khoản ngân hàng)

1. Điền thông tin ngân hàng vào `.env`:
   ```env
   VIETQR_BANK_ID=VCB          # Mã ngân hàng (VCB = Vietcombank, TCB = Techcombank, v.v.)
   VIETQR_ACCOUNT_NO=1234567890
   VIETQR_ACCOUNT_NAME=NGUYEN VAN A
   VIETQR_TEMPLATE=compact2
   ```
2. Tìm mã ngân hàng của bạn tại: https://www.vietqr.io/danh-sach-ngan-hang
3. Xác nhận thanh toán là **thủ công** — admin phải xác minh và phê duyệt giao dịch.

### Bước 12: Chạy Máy chủ Phát triển

Từ thư mục gốc của dự án:

```bash
php -S localhost:3000 router.php
```

### Bước 13: Truy cập Ứng dụng

  - **Dành cho Khách hàng (Client)**: http://localhost:3000
  - **Trang Quản trị (Admin Panel)**: http://localhost:3000/admin/auth/login

### Thông tin Đăng nhập Quản trị viên Mặc định

Sau khi import cơ sở dữ liệu, bạn có thể đăng nhập bằng:

  - **Email**: admin@fptshop.com
  - **Mật khẩu**: admin

## Đội ngũ Phát triển

| Thành viên                                                                        | Vai trò     |
| ------------------------------------------------------------------------------ | ----------- |
| Trương Thành Đạt ([datweb07](https://github.com/datweb07))                     | Trưởng nhóm |
| Phan Khắc Anh Tuấn ([KhacTuan1224](https://github.com/KhacTuan1224))           | Thành viên  |
| Nguyễn Phương Chinh ([chinhngprit](https://github.com/chinhngprit))            | Thành viên  |
| Nguyễn Tấn Khiêm ([nguyentankhiem1610](https://github.com/nguyentankhiem1610)) | Thành viên  |

## Đóng góp

Vui lòng đọc tệp [CONTRIBUTING.md](https://www.google.com/search?q=CONTRIBUTING.md) để biết chi tiết về quy tắc ứng xử của chúng tôi cũng như quy trình gửi pull request.

## Giấy phép

Dự án này được phát hành theo **Giấy phép MIT**. Xem tệp [LICENSE.md](LICENSE.md) để biết thêm chi tiết.

### Giấy phép Thư viện Bên thứ ba

Dự án này sử dụng nhiều thư viện và dịch vụ của bên thứ ba. Để biết thông tin chi tiết về tất cả các thư viện phụ thuộc, giấy phép của chúng và các yêu cầu tuân thủ, vui lòng xem [THIRD-PARTY-NOTICES.md](THIRD-PARTY-NOTICES.md).

## Theo dõi dự án môn học tại [liên kết này](https://docs.google.com/document/d/1SXeumwh1u8Yp0dC2vJMpMznbU5E-hHp4QlYRMehpj54/edit?fbclid=IwY2xjawP7fhlleHRuA2FlbQIxMQBzcnRjBmFwcF9pZAEwAAEedb2YK7uGIXycjsky8VB1DFG-L3-gWnW-waFfYHy-auBXTEFJHKVo2hiwIss_aem_jiqtsPn96N6dYubaf0h3ow&tab=t.n8hb9b8xnj2z)

## Theo dõi tài liệu của nhóm tại [liên kết này](https://docs.google.com/document/d/1JKrh4aKDL6bRvAVQPyokfoLd3LKVeL6jLs0IW6hdVk4/edit?usp=sharing)