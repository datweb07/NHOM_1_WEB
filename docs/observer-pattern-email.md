# Observer Pattern - Email Notification System

## Tổng Quan

Hệ thống email notification sử dụng **Observer Pattern** để tách biệt logic gửi email khỏi business logic thanh toán. Thay vì viết code gửi mail rải rác ở từng phương thức thanh toán, tất cả logic email được tập trung vào `EmailObserver`.

## Kiến Trúc

```
┌─────────────────────┐
│ ThanhToanController │
│  (Subject)          │
└──────────┬──────────┘
           │ notify()
           ▼
┌─────────────────────┐
│   EventManager      │
│  (Mediator)         │
└──────────┬──────────┘
           │ update()
           ├──────────────────┬──────────────────┐
           ▼                  ▼                  ▼
┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐
│  OrderObserver   │ │  EmailObserver   │ │ Future Observers │
│  (Notification)  │ │  (Email)         │ │  (SMS, Push...)  │
└──────────────────┘ └──────────────────┘ └──────────────────┘
```

## Các Sự Kiện (Events)

### 1. ORDER_PLACED
**Thời điểm:** Ngay khi bấm "Đặt hàng" thành công (DB đã lưu đơn)

**Mục đích:** Gửi mail xác nhận đơn hàng

**Áp dụng cho:** Tất cả phương thức thanh toán (COD, VNPay, PayPal, VietQR)

**Template:** "Chúng tôi đã nhận được đơn hàng #ABC của bạn"

**Trigger location:** `ThanhToanController::taoThanhToan()`

```php
$eventManager->notify('ORDER_PLACED', [
    'order_id' => $donHangId,
    'user_id' => $userId,
    'total_amount' => $tongThanhToan,
    'timestamp' => date('Y-m-d H:i:s')
]);
```

### 2. PAYMENT_SUCCESS
**Thời điểm:** Khi nhận Callback/IPN báo thành công từ cổng thanh toán

**Mục đích:** Gửi mail xác nhận thanh toán thành công

**Áp dụng cho:** VNPay, PayPal (online payment gateways)

**Template:** "Giao dịch qua [VNPay/PayPal] thành công"

**Trigger location:** `CallbackHandler::handleSuccessfulPayment()`

```php
$eventManager->notify('PAYMENT_SUCCESS', [
    'order_id' => $donHangId,
    'payment_method' => 'VNPay',
    'transaction_id' => $gatewayTransactionId,
    'timestamp' => date('Y-m-d H:i:s')
]);
```

### 3. PAYMENT_RECEIVED
**Thời điểm:** Khi Admin xác nhận đã nhận tiền (COD) hoặc hệ thống xác nhận (VietQR)

**Mục đích:** Gửi mail xác nhận đã nhận tiền & bắt đầu xử lý đơn

**Áp dụng cho:** COD, VietQR, Chuyển khoản

**Template:** "Đã nhận được tiền, đơn hàng đang được đóng gói"

**Trigger location:** Admin panel hoặc VietQR callback

```php
$eventManager->notify('PAYMENT_RECEIVED', [
    'order_id' => $donHangId,
    'timestamp' => date('Y-m-d H:i:s')
]);
```

## Cấu Trúc Code

### EmailObserver.php
```php
class EmailObserver implements ObserverInterface
{
    private MailerService $mailService;

    public function update(string $eventType, array $data): void
    {
        switch ($eventType) {
            case 'ORDER_PLACED':
                $this->sendOrderConfirmation($data);
                break;
            case 'PAYMENT_SUCCESS':
                $this->sendPaymentSuccessNotification($data);
                break;
            case 'PAYMENT_RECEIVED':
                $this->sendPaymentReceivedNotification($data);
                break;
        }
    }
}
```

### MailerService.php
```php
class MailerService
{
    public function sendOrderConfirmation(array $emailData): bool { }
    public function sendPaymentSuccess(array $emailData): bool { }
    public function sendPaymentReceived(array $emailData): bool { }
}
```

## Flow Diagram

### COD Payment Flow
```
User đặt hàng
    ↓
ThanhToanController::taoThanhToan()
    ↓
Lưu đơn hàng vào DB
    ↓
Trigger: ORDER_PLACED ──→ EmailObserver ──→ Gửi mail xác nhận đơn
    ↓
Redirect đến trang success
    ↓
Admin xác nhận nhận tiền
    ↓
Trigger: PAYMENT_RECEIVED ──→ EmailObserver ──→ Gửi mail đã nhận tiền
```

### VNPay Payment Flow
```
User đặt hàng
    ↓
ThanhToanController::taoThanhToan()
    ↓
Lưu đơn hàng vào DB
    ↓
Trigger: ORDER_PLACED ──→ EmailObserver ──→ Gửi mail xác nhận đơn
    ↓
Redirect đến VNPay
    ↓
User thanh toán thành công
    ↓
VNPay gửi callback
    ↓
CallbackHandler::handleSuccessfulPayment()
    ↓
Trigger: PAYMENT_SUCCESS ──→ EmailObserver ──→ Gửi mail thanh toán thành công
```

## Ưu Điểm

### 1. Clean Code
- Code xử lý VNPay/PayPal không cần biết hàm `sendMail()` trông như thế nào
- Chỉ cần "bắn" event là xong

### 2. Đồng Bộ
- Tất cả phương thức thanh toán đều trigger `ORDER_PLACED`
- Đảm bảo không bao giờ quên gửi mail xác nhận đơn hàng

### 3. Dễ Bảo Trì
- Muốn đổi nhà cung cấp Email (Gmail → SendGrid)?
- Chỉ cần sửa 1 chỗ trong `MailerService`

### 4. Dễ Mở Rộng
- Muốn thêm SMS notification?
- Tạo `SmsObserver` và attach vào `EventManager`
- Không cần sửa code thanh toán

### 5. Testable
- Có thể test `EmailObserver` độc lập
- Mock `MailerService` để test logic mà không gửi email thật

## Cách Thêm Observer Mới

### Ví dụ: SMS Observer

**Bước 1:** Tạo `SmsObserver.php`
```php
class SmsObserver implements ObserverInterface
{
    private SmsService $smsService;

    public function update(string $eventType, array $data): void
    {
        switch ($eventType) {
            case 'ORDER_PLACED':
                $this->sendOrderSms($data);
                break;
            case 'PAYMENT_SUCCESS':
                $this->sendPaymentSms($data);
                break;
        }
    }
}
```

**Bước 2:** Attach vào EventManager
```php
$smsObserver = new SmsObserver($smsService);
$eventManager->attach($smsObserver);
```

**Bước 3:** Done! Không cần sửa code thanh toán

## Testing

### Test EmailObserver
```php
// Mock MailerService
$mailerMock = $this->createMock(MailerService::class);
$mailerMock->expects($this->once())
    ->method('sendOrderConfirmation')
    ->with($this->arrayHasKey('order_id'));

// Test observer
$observer = new EmailObserver($mailerMock);
$observer->update('ORDER_PLACED', ['order_id' => 123]);
```

### Test Integration
```php
// Create order
$orderId = $this->createTestOrder();

// Check email was sent
$this->assertEmailSent('customer@example.com');
$this->assertEmailContains("Đơn hàng #$orderId");
```

## Troubleshooting

### Email không được gửi

**Kiểm tra:**
1. Event có được trigger không? → Check error logs
2. Observer có được attach không? → Check EventManager
3. SMTP config đúng chưa? → Check `.env`

**Debug:**
```php
// Thêm logging vào EmailObserver
error_log("EmailObserver: Received event $eventType");
```

### Email bị gửi nhiều lần

**Nguyên nhân:** Observer được attach nhiều lần

**Giải pháp:** Đảm bảo chỉ attach 1 lần trong lifecycle

### Email gửi chậm

**Nguyên nhân:** SMTP blocking

**Giải pháp:** Sử dụng queue system (Redis, RabbitMQ)

## Best Practices

### ✅ DO
- Log tất cả events để debug
- Catch exceptions trong Observer (không để crash order flow)
- Sử dụng email templates
- Test email templates trước khi deploy

### ❌ DON'T
- Không throw exception trong Observer
- Không gửi email đồng bộ nếu có nhiều observers
- Không hardcode email content
- Không quên handle email failure gracefully

## Future Enhancements

1. **Queue System:** Gửi email async qua Redis Queue
2. **Email Templates:** Sử dụng template engine (Twig, Blade)
3. **A/B Testing:** Test different email templates
4. **Analytics:** Track email open rate, click rate
5. **Retry Logic:** Retry failed emails
6. **Unsubscribe:** Allow users to opt-out

## References

- [Observer Pattern - Refactoring Guru](https://refactoring.guru/design-patterns/observer)
- [PHPMailer Documentation](https://github.com/PHPMailer/PHPMailer)
- [Event-Driven Architecture](https://martinfowler.com/articles/201701-event-driven.html)
