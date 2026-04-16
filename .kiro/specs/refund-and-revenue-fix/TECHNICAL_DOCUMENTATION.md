# Technical Documentation: Refund and Revenue Fix

## Overview

This document provides technical details about the refund processing and revenue calculation fix implementation. It is intended for developers and system administrators who need to understand, maintain, or extend the system.

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                     Admin Interface Layer                    │
│  - Payment Detail View (detail.php)                         │
│  - Refund Modal UI                                          │
│  - Dashboard View (index.php)                               │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     Controller Layer                         │
│  - ThanhToanController (refund endpoints)                   │
│  - DashboardController (revenue calculation)                │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     Service Layer                            │
│  - RefundService (refund orchestration)                     │
│  - PaymentService (gateway routing)                         │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     Gateway Layer                            │
│  - VNPayGateway (VNPay API integration)                     │
│  - MomoGateway (Momo API integration)                       │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     Data Layer                               │
│  - Refund Model (refund CRUD)                               │
│  - ThanhToan Model (payment data)                           │
│  - DonHang Model (order data)                               │
│  - TransactionLog Model (audit logging)                     │
└─────────────────────────────────────────────────────────────┘
```

## Database Schema

### Refund Table

```sql
CREATE TABLE `refund` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `thanh_toan_id` INT NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `status` ENUM('PENDING', 'COMPLETED', 'FAILED') DEFAULT 'PENDING',
    `reason` TEXT,
    `gateway_refund_id` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `completed_at` DATETIME DEFAULT NULL,
    `admin_id` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`thanh_toan_id`) REFERENCES `thanh_toan`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `nguoi_dung`(`id`) ON DELETE SET NULL,
    INDEX `idx_refund_thanh_toan` (`thanh_toan_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Performance Indexes

```sql
-- Index for revenue calculation filtering
CREATE INDEX idx_thanh_toan_duyet ON thanh_toan(trang_thai_duyet);

-- Composite index for refund lookups
CREATE INDEX idx_refund_thanh_toan ON refund(thanh_toan_id, status);

-- Composite index for dashboard queries
CREATE INDEX idx_don_hang_revenue ON don_hang(trang_thai, ngay_tao);
```

## API Endpoints

### POST /admin/thanh-toan/refund

Process a refund for a payment.

**Authentication:** Admin only (AdminMiddleware)

**Request Parameters:**
- `id` (query parameter): Payment ID
- `amount` (POST): Refund amount (must match payment amount)
- `reason` (POST): Refund reason (required, min 10 characters)

**Request Example:**
```http
POST /admin/thanh-toan/refund?id=123
Content-Type: application/x-www-form-urlencoded

amount=1500000&reason=Khách+hàng+yêu+cầu+hoàn+tiền+do+sản+phẩm+lỗi
```

**Success Response:**
```
HTTP/1.1 302 Found
Location: /admin/thanh-toan/chi-tiet?id=123&success=refund_completed
```

**Error Responses:**

| Error Code | Redirect | Message |
|------------|----------|---------|
| Payment not found | `/admin/thanh-toan?error=payment_not_found` | Không tìm thấy thông tin thanh toán |
| Not approved | `/admin/thanh-toan/chi-tiet?id=123&error=payment_not_approved` | Chỉ có thể hoàn tiền cho thanh toán đã được duyệt |
| Already refunded | `/admin/thanh-toan/chi-tiet?id=123&error=already_refunded` | Thanh toán này đã được hoàn tiền |
| Gateway failure | `/admin/thanh-toan/chi-tiet?id=123&error=gateway_failure` | [Gateway error message] |

## Service Layer

### RefundService

**Location:** `app/services/refund/RefundService.php`

#### Method: initiateRefund()

```php
/**
 * Initiate a refund for a payment
 * 
 * @param int $thanhToanId Payment ID
 * @param float $amount Refund amount
 * @param string $reason Refund reason
 * @param int $adminId Admin user ID who initiated refund
 * @return array ['success' => bool, 'message' => string, 'refund_id' => int|null]
 */
public function initiateRefund(int $thanhToanId, float $amount, string $reason, int $adminId): array
```

**Workflow:**
1. Validate payment exists and is eligible for refund
2. Create refund record with status PENDING
3. Get payment gateway instance from PaymentService
4. Call gateway's initiateRefund() method
5. Update refund status based on gateway response
6. Log transaction (REFUND_INITIATED, REFUND_COMPLETED, or REFUND_FAILED)
7. Return result array

**Return Values:**
```php
// Success
[
    'success' => true,
    'message' => 'Hoàn tiền thành công',
    'refund_id' => 123,
    'gateway_refund_id' => 'REF_VNP_20240115_001'
]

// Failure
[
    'success' => false,
    'message' => 'Gateway error: Invalid transaction',
    'refund_id' => 123
]
```

#### Method: canRefund()

```php
/**
 * Check if a payment can be refunded
 * 
 * @param array $thanhToan Payment record
 * @return array ['can_refund' => bool, 'reason' => string]
 */
public function canRefund(array $thanhToan): array
```

**Validation Rules:**
1. Payment approval status must be 'THANH_CONG'
2. Payment method must not be 'COD' or 'ZaloPay'
3. No existing COMPLETED refund for this payment

**Return Values:**
```php
// Can refund
['can_refund' => true, 'reason' => '']

// Cannot refund
['can_refund' => false, 'reason' => 'Payment not approved']
['can_refund' => false, 'reason' => 'COD payments cannot be refunded']
['can_refund' => false, 'reason' => 'Payment already refunded']
```

## Payment Gateway Integration

### VNPayGateway

**Method:** `initiateRefund(string $transactionId, float $amount, string $reason): array`

**Implementation Details:**
- Uses VNPay Refund API endpoint
- Requires: TMN_CODE, HASH_SECRET, API_URL from .env
- Request signing: HMAC SHA512
- Timeout: 30 seconds

**Request Format:**
```php
[
    'vnp_RequestId' => uniqid(),
    'vnp_Version' => '2.1.0',
    'vnp_Command' => 'refund',
    'vnp_TmnCode' => $tmnCode,
    'vnp_TransactionType' => '02', // Full refund
    'vnp_TxnRef' => $transactionId,
    'vnp_Amount' => $amount * 100, // Convert to smallest unit
    'vnp_OrderInfo' => $reason,
    'vnp_TransactionDate' => date('YmdHis'),
    'vnp_CreateDate' => date('YmdHis'),
    'vnp_IpAddr' => $_SERVER['REMOTE_ADDR'],
    'vnp_SecureHash' => $secureHash
]
```

**Response Format:**
```php
// Success
[
    'success' => true,
    'refund_id' => 'REF_VNP_20240115_001',
    'message' => 'Refund successful'
]

// Failure
[
    'success' => false,
    'message' => 'Invalid transaction ID',
    'error_code' => '02'
]
```

### MomoGateway

**Method:** `initiateRefund(string $transactionId, float $amount, string $reason): array`

**Implementation Details:**
- Uses Momo Refund API endpoint
- Requires: PARTNER_CODE, ACCESS_KEY, SECRET_KEY, API_URL from .env
- Request signing: HMAC SHA256
- Timeout: 30 seconds

**Request Format:**
```php
[
    'partnerCode' => $partnerCode,
    'orderId' => $transactionId,
    'requestId' => uniqid(),
    'amount' => $amount,
    'transId' => $transactionId,
    'lang' => 'vi',
    'description' => $reason,
    'signature' => $signature
]
```

## Revenue Calculation

### Query Logic

**Location:** `app/controllers/admin/DashboardController.php`

#### Method: calculateMonthlyRevenue()

```php
private function calculateMonthlyRevenue(): float
{
    $sql = "
        SELECT SUM(dh.tong_thanh_toan) as total_revenue
        FROM don_hang dh
        INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
        LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
        WHERE tt.trang_thai_duyet = 'THANH_CONG'
          AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
          AND r.id IS NULL
          AND MONTH(dh.ngay_tao) = MONTH(CURRENT_DATE())
          AND YEAR(dh.ngay_tao) = YEAR(CURRENT_DATE())
    ";
    
    // Execute query and return result
}
```

**Filtering Logic:**
1. **INNER JOIN thanh_toan**: Only include orders with payment records
2. **LEFT JOIN refund**: Check for completed refunds
3. **WHERE tt.trang_thai_duyet = 'THANH_CONG'**: Only approved payments
4. **AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')**: Exclude cancelled/returned orders
5. **AND r.id IS NULL**: Exclude refunded payments
6. **AND MONTH/YEAR filters**: Current month only

#### Method: calculateTotalRevenue()

Same logic as `calculateMonthlyRevenue()` but without month/year filters.

### Performance Optimization

**Index Usage:**
- `idx_thanh_toan_duyet` speeds up payment approval filtering
- `idx_refund_thanh_toan` speeds up refund lookups
- `idx_don_hang_revenue` speeds up order status filtering

**Query Performance:**
- Expected execution time: < 500ms for 100,000 orders
- Uses index scans instead of full table scans
- Efficient LEFT JOIN for refund checks

## Transaction Logging

### Log Entry Format

**Table:** `transaction_log`

**Fields:**
- `thanh_toan_id`: Payment ID
- `action_type`: REFUND_INITIATED, REFUND_COMPLETED, REFUND_FAILED
- `request_data`: JSON with refund details
- `response_data`: JSON with gateway response
- `created_at`: Timestamp

**Example Log Entry:**
```json
{
    "action": "REFUND_INITIATED",
    "refund_id": 123,
    "amount": 1500000,
    "reason": "Khách hàng yêu cầu hoàn tiền",
    "admin_id": 5,
    "timestamp": "2024-01-15 14:30:00"
}
```

## Error Handling

### Error Codes

| Code | Description | User Message |
|------|-------------|--------------|
| PAYMENT_NOT_FOUND | Payment ID doesn't exist | Không tìm thấy thông tin thanh toán |
| PAYMENT_NOT_APPROVED | Payment status != THANH_CONG | Chỉ có thể hoàn tiền cho thanh toán đã được duyệt |
| ALREADY_REFUNDED | Completed refund exists | Thanh toán này đã được hoàn tiền |
| UNSUPPORTED_METHOD | COD or ZaloPay payment | Phương thức thanh toán này không hỗ trợ hoàn tiền |
| GATEWAY_NOT_CONFIGURED | Missing gateway credentials | Cổng thanh toán chưa được cấu hình |
| GATEWAY_ERROR | Gateway API returned error | [Gateway error message] |

### Exception Handling

```php
try {
    $result = $refundService->initiateRefund($thanhToanId, $amount, $reason, $adminId);
} catch (PaymentNotFoundException $e) {
    // Log error and redirect with error message
    error_log("Payment not found: " . $e->getMessage());
    header("Location: /admin/thanh-toan?error=payment_not_found");
    exit;
} catch (GatewayException $e) {
    // Log error and redirect with gateway error
    error_log("Gateway error: " . $e->getMessage());
    header("Location: /admin/thanh-toan/chi-tiet?id=$thanhToanId&error=gateway_failure");
    exit;
} catch (Exception $e) {
    // Log unexpected error
    error_log("Unexpected error: " . $e->getMessage());
    header("Location: /admin/thanh-toan/chi-tiet?id=$thanhToanId&error=system_error");
    exit;
}
```

## Security Considerations

### Authentication & Authorization

1. **Admin Middleware**: All refund endpoints protected by AdminMiddleware
2. **Session Validation**: Admin session checked before processing refund
3. **CSRF Protection**: Forms include CSRF token (if implemented)

### Input Validation

```php
// Validate refund amount
if (!is_numeric($amount) || $amount <= 0) {
    throw new InvalidArgumentException("Invalid refund amount");
}

// Validate refund reason
if (empty($reason) || strlen($reason) < 10) {
    throw new InvalidArgumentException("Refund reason is required (min 10 characters)");
}

// Sanitize inputs
$reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');
```

### SQL Injection Prevention

All database queries use prepared statements:

```php
$stmt = $pdo->prepare("
    SELECT * FROM thanh_toan 
    WHERE id = :id AND trang_thai_duyet = :status
");
$stmt->execute(['id' => $thanhToanId, 'status' => 'THANH_CONG']);
```

### Gateway Security

1. **HTTPS Only**: All gateway API calls use HTTPS
2. **Request Signing**: All requests signed with HMAC
3. **Timeout**: 30-second timeout prevents hanging requests
4. **Credential Storage**: Gateway credentials stored in .env (not in code)

## Configuration

### Environment Variables

```env
# VNPay Configuration
VNPAY_TMN_CODE=your_tmn_code
VNPAY_HASH_SECRET=your_hash_secret
VNPAY_API_URL=https://sandbox.vnpayment.vn/merchant_webapi/api/transaction
VNPAY_REFUND_URL=https://sandbox.vnpayment.vn/merchant_webapi/api/transaction

# Momo Configuration
MOMO_PARTNER_CODE=your_partner_code
MOMO_ACCESS_KEY=your_access_key
MOMO_SECRET_KEY=your_secret_key
MOMO_API_URL=https://test-payment.momo.vn/v2/gateway/api/refund

# Application Settings
APP_ENV=production
APP_DEBUG=false
```

### Production vs Sandbox

**Sandbox (Testing):**
- Use sandbox credentials
- Use sandbox API endpoints
- Test with small amounts

**Production:**
- Use production credentials
- Use production API endpoints
- Monitor all transactions
- Set up alerts for failures

## Monitoring & Maintenance

### Key Metrics to Monitor

1. **Refund Success Rate**: % of successful refunds
2. **Gateway Response Time**: Average time for gateway API calls
3. **Failed Refunds**: Count of failed refunds per day
4. **Revenue Accuracy**: Verify revenue calculations match accounting

### Log Files to Monitor

```bash
# Application logs
tail -f /path/to/app/storage/logs/laravel.log

# Web server logs
tail -f /var/log/nginx/error.log

# Database slow query log
tail -f /var/log/mysql/slow-query.log
```

### Database Maintenance

```sql
-- Check refund statistics
SELECT status, COUNT(*) as count, SUM(amount) as total_amount
FROM refund
GROUP BY status;

-- Find stuck refunds (pending > 1 hour)
SELECT * FROM refund
WHERE status = 'PENDING'
  AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- Check transaction log size
SELECT COUNT(*) FROM transaction_log
WHERE action_type LIKE '%REFUND%';
```

## Troubleshooting

### Common Issues

**Issue: Refund stuck in PENDING status**

**Diagnosis:**
```sql
SELECT r.*, tt.gateway_transaction_id, tt.phuong_thuc
FROM refund r
JOIN thanh_toan tt ON r.thanh_toan_id = tt.id
WHERE r.status = 'PENDING' AND r.created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

**Solution:**
1. Check gateway status manually
2. Update refund status based on gateway response
3. Investigate gateway connectivity issues

**Issue: Revenue calculation incorrect**

**Diagnosis:**
```sql
-- Manual revenue calculation
SELECT SUM(dh.tong_thanh_toan) as manual_revenue
FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.trang_thai_duyet = 'THANH_CONG'
  AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
  AND r.id IS NULL;
```

**Solution:**
1. Verify indexes exist
2. Check for data inconsistencies
3. Clear application cache

## Testing

### Unit Test Examples

```php
// Test RefundService::canRefund()
public function testCanRefundApprovedPayment()
{
    $payment = [
        'id' => 1,
        'trang_thai_duyet' => 'THANH_CONG',
        'phuong_thuc' => 'VNPay'
    ];
    
    $result = $this->refundService->canRefund($payment);
    
    $this->assertTrue($result['can_refund']);
    $this->assertEmpty($result['reason']);
}

// Test revenue calculation
public function testRevenueExcludesRefundedPayments()
{
    // Create test data
    $this->createApprovedPayment(1000000);
    $this->createRefundedPayment(500000);
    
    $revenue = $this->dashboardController->calculateTotalRevenue();
    
    $this->assertEquals(1000000, $revenue);
}
```

## Future Enhancements

### Planned Features

1. **Partial Refunds**: Support refunding partial amounts
2. **Bulk Refunds**: Refund multiple payments at once
3. **Refund Approval Workflow**: Require manager approval for large refunds
4. **Automatic Refunds**: Trigger refunds when order is cancelled
5. **Refund Notifications**: Email/SMS notifications to customers
6. **ZaloPay Support**: Implement when API becomes available

### API Improvements

1. **RESTful API**: Expose refund endpoints as REST API
2. **Webhook Support**: Receive refund status updates from gateways
3. **Rate Limiting**: Prevent abuse of refund endpoints
4. **API Documentation**: OpenAPI/Swagger documentation

## Support & Contact

**Technical Support:** support@yourcompany.com  
**Documentation:** https://docs.yourcompany.com  
**Issue Tracker:** https://github.com/yourcompany/project/issues

## Changelog

**Version 1.0.0** (2024-01-XX)
- Initial implementation of refund workflow
- Revenue calculation fix
- VNPay gateway integration
- Transaction logging
- Admin UI for refund processing
