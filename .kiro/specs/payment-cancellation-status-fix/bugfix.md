# Bugfix Requirements Document

## Introduction

This bugfix addresses an issue where the system incorrectly handles payment cancellations at the VNPay sandbox gateway. When a user cancels a payment transaction (e.g., by canceling during OTP input), the system receives a valid callback with response code '24' (customer canceled transaction) but fails to update the order and payment statuses appropriately. This results in orders remaining in "CHỜ DUYỆT" (Pending Approval) state even though the payment was explicitly canceled by the user.

**Impact:**
- **Severity**: HIGH
- **User Impact**: Users who cancel payments see incorrect order status, causing confusion about whether their order is active
- **Business Impact**: Admins cannot distinguish between genuinely pending orders and canceled orders, leading to operational inefficiencies
- **Data Integrity**: Order and payment statuses are inconsistent with actual payment results

**Affected Components:**
- VNPay payment gateway callback handling
- Momo payment gateway callback handling (potentially affected by same issue)
- Order status management
- Payment status management
- Gateway health metrics dashboard

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN a user cancels a payment at VNPay gateway (response code '24') THEN the system receives the callback with valid signature but leaves payment status as "CHỜ DUYỆT" (Pending Approval)

1.2 WHEN a user cancels a payment at VNPay gateway (response code '24') THEN the system receives the callback with valid signature but leaves order status as "CHỜ DUYỆT" (Pending Approval)

1.3 WHEN VNPay callback is received with response code '24' (customer canceled) THEN the Gateway Health dashboard incorrectly records the transaction as "SUCCESS" instead of "FAILURE"

1.4 WHEN a user cancels a payment at VNPay gateway (response code '24') THEN the product inventory is not restored, leaving items incorrectly reserved

1.5 WHEN admin views the order list THEN canceled orders appear in "Pending" state alongside genuinely pending orders, making them indistinguishable

1.6 WHEN client views their order history THEN canceled orders appear as "Pending" instead of "Canceled", causing confusion about order status

1.7 WHEN a payment fails with any non-'00' response code at VNPay THEN the Gateway Health dashboard may incorrectly record it as "SUCCESS" if signature validation passed

1.8 WHEN a payment fails with any non-'0' result code at Momo THEN the Gateway Health dashboard may incorrectly record it as "SUCCESS" if signature validation passed

### Expected Behavior (Correct)

2.1 WHEN a user cancels a payment at VNPay gateway (response code '24') THEN the system SHALL update the payment status to "THẤT BẠI" (Failed)

2.2 WHEN a user cancels a payment at VNPay gateway (response code '24') THEN the system SHALL update the order status to "ĐÃ HỦY" (Canceled)

2.3 WHEN VNPay callback is received with response code '24' (customer canceled) THEN the Gateway Health dashboard SHALL record the transaction as "FAILURE"

2.4 WHEN a user cancels a payment at VNPay gateway (response code '24') THEN the system SHALL restore the product inventory by adding back the reserved quantities

2.5 WHEN admin views the order list THEN canceled orders SHALL appear with status "ĐÃ HỦY" (Canceled), clearly distinguishable from pending orders

2.6 WHEN client views their order history THEN canceled orders SHALL appear with status "ĐÃ HỦY" (Canceled), clearly indicating the payment was not completed

2.7 WHEN a payment fails with any non-'00' response code at VNPay THEN the Gateway Health dashboard SHALL record it as "FAILURE" regardless of signature validation status

2.8 WHEN a payment fails with any non-'0' result code at Momo THEN the Gateway Health dashboard SHALL record it as "FAILURE" regardless of signature validation status

### Unchanged Behavior (Regression Prevention)

3.1 WHEN a payment succeeds at VNPay gateway (response code '00') THEN the system SHALL CONTINUE TO update payment status to "THANH_CONG" (Success)

3.2 WHEN a payment succeeds at VNPay gateway (response code '00') THEN the system SHALL CONTINUE TO update order status to "DA_XAC_NHAN" (Confirmed)

3.3 WHEN a payment succeeds at VNPay gateway (response code '00') THEN the Gateway Health dashboard SHALL CONTINUE TO record the transaction as "SUCCESS"

3.4 WHEN a payment succeeds at VNPay gateway (response code '00') THEN the system SHALL CONTINUE TO NOT restore inventory (items remain sold)

3.5 WHEN VNPay callback has invalid signature THEN the system SHALL CONTINUE TO reject the callback and record security violation

3.6 WHEN Momo callback has invalid signature THEN the system SHALL CONTINUE TO reject the callback and record security violation

3.7 WHEN a payment succeeds at Momo gateway (result code '0') THEN the system SHALL CONTINUE TO update payment status to "THANH_CONG" (Success)

3.8 WHEN a payment succeeds at Momo gateway (result code '0') THEN the system SHALL CONTINUE TO update order status to "DA_XAC_NHAN" (Confirmed)

3.9 WHEN a payment succeeds at Momo gateway (result code '0') THEN the Gateway Health dashboard SHALL CONTINUE TO record the transaction as "SUCCESS"

3.10 WHEN a payment fails with any non-'00' response code at VNPay (except '24') THEN the system SHALL CONTINUE TO call handleFailedPayment() which updates payment status to "THẤT BẠI" and restores inventory

3.11 WHEN a payment fails with any non-'0' result code at Momo THEN the system SHALL CONTINUE TO call handleFailedPayment() which updates payment status to "THẤT BẠI" and restores inventory

3.12 WHEN handleFailedPayment() is called THEN the system SHALL CONTINUE TO restore product inventory correctly

3.13 WHEN a transaction expires (timeout) THEN the system SHALL CONTINUE TO update order status to "ĐÃ HỦY" and restore inventory via handleExpiredTransaction()
