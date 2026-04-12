# Refund Model Documentation

## Overview

The Refund model manages payment refund records for the e-commerce application. It supports refunds for VNPay, Momo, and COD payment methods.

## Database Schema

The `refund` table stores refund information:

```sql
CREATE TABLE `refund` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `thanh_toan_id` INT NOT NULL COMMENT 'ID giao dịch thanh toán gốc',
    `gateway_refund_id` VARCHAR(255) COMMENT 'ID hoàn tiền từ cổng thanh toán',
    `amount` DECIMAL(15,2) NOT NULL COMMENT 'Số tiền hoàn',
    `status` ENUM('PENDING', 'COMPLETED', 'FAILED') DEFAULT 'PENDING',
    `reason` TEXT COMMENT 'Lý do hoàn tiền',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `completed_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`thanh_toan_id`) REFERENCES `thanh_toan` (`id`) ON DELETE CASCADE
);
```

## Model Methods

### createRefund($thanhToanId, $amount, $reason)

Creates a new refund record with PENDING status.

**Parameters:**
- `$thanhToanId` (int): Payment transaction ID
- `$amount` (float): Refund amount
- `$reason` (string): Refund reason

**Returns:** Refund ID on success, false on failure

**Example:**
```php
$refundModel = new Refund();
$refundId = $refundModel->createRefund(123, 500000, 'Hoan tien don hang #DH001');
```

### updateRefundStatus($id, $status, $gatewayRefundId = null)

Updates the status of a refund record.

**Parameters:**
- `$id` (int): Refund ID
- `$status` (string): New status (PENDING, COMPLETED, FAILED)
- `$gatewayRefundId` (string|null): Gateway refund transaction ID (optional)

**Returns:** True on success, false on failure

**Example:**
```php
$refundModel->updateRefundStatus(1, 'COMPLETED', 'VNP_REFUND_123456');
```

### findByThanhToanId($thanhToanId)

Retrieves all refunds for a specific payment transaction.

**Parameters:**
- `$thanhToanId` (int): Payment transaction ID

**Returns:** Array of refund records

**Example:**
```php
$refunds = $refundModel->findByThanhToanId(123);
foreach ($refunds as $refund) {
    echo $refund['status'];
}
```

## Refund Flow

### 1. COD Orders
For COD orders, refunds are marked as COMPLETED immediately without gateway processing:

```php
$refundId = $refundModel->createRefund($thanhToanId, $amount, $reason);
$refundModel->updateRefundStatus($refundId, 'COMPLETED', null);
```

### 2. VNPay Orders
For VNPay orders, the refund is initiated through the VNPay API:

```php
$gateway = new VNPayGateway();
$result = $gateway->initiateRefund($transactionId, $amount, $reason);

if ($result['success']) {
    $refundModel->updateRefundStatus($refundId, 'COMPLETED', $result['refund_id']);
} else {
    $refundModel->updateRefundStatus($refundId, 'FAILED', null);
}
```

### 3. Momo Orders
For Momo orders, the refund is initiated through the Momo API:

```php
$gateway = new MomoGateway();
$result = $gateway->initiateRefund($transactionId, $amount, $reason);

if ($result['success']) {
    $refundModel->updateRefundStatus($refundId, 'COMPLETED', $result['refund_id']);
} else {
    $refundModel->updateRefundStatus($refundId, 'FAILED', null);
}
```

## Admin Interface

The refund functionality is accessible from the order detail page in the admin panel:

- **URL:** `/admin/don-hang/chi-tiet?id={order_id}`
- **Action:** POST to `/admin/don-hang/hoan-tien?id={order_id}`

### Refund Eligibility

An order is eligible for refund if:
1. Payment status is THANH_CONG (successful)
2. No existing refund records exist
3. Payment method is not COD (or COD orders are cancelled without gateway processing)

### UI Features

- Display payment information and refund history
- Show refund status badges (PENDING, COMPLETED, FAILED)
- Initiate refund button with confirmation dialog
- Display gateway refund IDs for tracking

## Requirements Mapping

- **12.1**: Display refund options based on payment method
- **12.2**: VNPay refund API integration
- **12.3**: Momo refund API integration
- **12.4**: COD cancellation without refund processing
- **12.5**: Create refund record with pending status
- **12.6**: Update refund status when callback received
- **12.7**: Store refund transaction ID from gateway

## Error Handling

The refund system handles various error scenarios:

- **no_payment**: Order has no payment information
- **already_refunded**: Order already has a refund record
- **refund_failed**: Gateway API call failed or returned error
- **invalid_id**: Invalid order ID provided

## Security Considerations

1. Refund actions require admin authentication
2. Confirmation dialog prevents accidental refunds
3. Gateway signatures are verified for API calls
4. Refund amounts are validated against original payment amounts
5. All refund actions are logged with timestamps

## Testing

To test the refund functionality:

1. Create a test order with online payment (VNPay or Momo)
2. Complete the payment successfully
3. Navigate to order detail page in admin
4. Click "Khởi tạo hoàn tiền" button
5. Verify refund record is created with PENDING status
6. Check gateway response and verify status update

## Migration

To apply the refund table schema:

```bash
mysql -u username -p database_name < database/migrations/add_refund_table.sql
```

Or execute the SQL directly in your database management tool.
