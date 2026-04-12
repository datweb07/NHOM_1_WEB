# Requirements Document

## Introduction

This document specifies the requirements for integrating three payment methods into the e-commerce PHP application: Cash on Delivery (COD), VNPay online payment gateway, and Momo e-wallet. The system currently has basic payment infrastructure but lacks complete integration with external payment providers and proper transaction handling.

## Glossary

- **Payment_System**: The payment processing module within the e-commerce application
- **VNPay_Gateway**: The VNPay payment service provider API integration
- **Momo_Gateway**: The Momo e-wallet payment service provider API integration
- **Transaction**: A payment record associated with a specific order
- **Callback_Handler**: The server-side endpoint that receives payment status notifications from payment gateways
- **IPN** (Instant Payment Notification): Asynchronous notification from payment gateway about transaction status
- **Payment_Status**: The current state of a transaction (pending, completed, failed, expired)
- **Order_System**: The order management module that tracks customer orders
- **Hash_Signature**: Cryptographic signature used to verify payment gateway requests
- **Transaction_ID**: Unique identifier for a payment transaction from the payment gateway
- **Redirect_URL**: The URL where users are sent after completing payment on gateway
- **Timeout_Period**: Maximum duration allowed for payment completion before expiration

## Requirements

### Requirement 1: COD Payment Processing

**User Story:** As a customer, I want to select Cash on Delivery as my payment method, so that I can pay when I receive my order.

#### Acceptance Criteria

1. WHEN a customer selects COD payment method, THE Payment_System SHALL create a transaction record with status "pending"
2. WHEN a COD order is created, THE Order_System SHALL set order status to "awaiting_confirmation"
3. THE Payment_System SHALL NOT require prepayment verification for COD transactions
4. WHEN an admin confirms a COD order, THE Order_System SHALL update order status to "confirmed"
5. WHEN delivery is completed for COD order, THE Payment_System SHALL allow manual payment confirmation by admin

### Requirement 2: VNPay Gateway Integration

**User Story:** As a customer, I want to pay using VNPay, so that I can complete my purchase securely online.

#### Acceptance Criteria

1. WHEN a customer selects VNPay payment method, THE VNPay_Gateway SHALL generate a payment URL with order details and Hash_Signature
2. THE VNPay_Gateway SHALL include transaction amount, order ID, return URL, and IPN URL in the payment request
3. WHEN payment URL is generated, THE Payment_System SHALL redirect the customer to VNPay payment page within 2 seconds
4. THE VNPay_Gateway SHALL use HMAC-SHA512 algorithm to generate Hash_Signature for request validation
5. WHEN VNPay payment page loads, THE customer SHALL see order details matching their cart total
6. THE Payment_System SHALL store VNPay configuration (TMN_CODE, HASH_SECRET, API_URL) in environment variables

### Requirement 3: Momo Gateway Integration

**User Story:** As a customer, I want to pay using Momo e-wallet, so that I can use my mobile wallet balance.

#### Acceptance Criteria

1. WHEN a customer selects Momo payment method, THE Momo_Gateway SHALL generate a payment request with order details and Hash_Signature
2. THE Momo_Gateway SHALL include transaction amount, order ID, redirect URL, and IPN URL in the payment request
3. WHEN payment request is created, THE Payment_System SHALL redirect the customer to Momo payment page within 2 seconds
4. THE Momo_Gateway SHALL use HMAC-SHA256 algorithm to generate Hash_Signature for request validation
5. WHEN Momo payment page loads, THE customer SHALL see order details matching their cart total
6. THE Payment_System SHALL store Momo configuration (PARTNER_CODE, ACCESS_KEY, SECRET_KEY, API_URL) in environment variables

### Requirement 4: Payment Callback Processing

**User Story:** As the system, I want to receive payment status notifications from gateways, so that I can update order and transaction status automatically.

#### Acceptance Criteria

1. WHEN VNPay sends a callback request, THE Callback_Handler SHALL verify the Hash_Signature before processing
2. WHEN Momo sends an IPN request, THE Callback_Handler SHALL verify the Hash_Signature before processing
3. IF Hash_Signature verification fails, THEN THE Callback_Handler SHALL reject the request and log the security violation
4. WHEN a valid callback is received with success status, THE Payment_System SHALL update transaction status to "completed"
5. WHEN a valid callback is received with success status, THE Order_System SHALL update order status to "confirmed"
6. WHEN a valid callback is received with failure status, THE Payment_System SHALL update transaction status to "failed"
7. WHEN a valid callback is received with failure status, THE Order_System SHALL restore product inventory quantities
8. THE Callback_Handler SHALL respond to gateway callbacks within 3 seconds to prevent timeout
9. THE Callback_Handler SHALL return HTTP 200 status code with success response to acknowledge receipt

### Requirement 5: Payment Return URL Handling

**User Story:** As a customer, I want to see my payment result after completing payment, so that I know if my order was successful.

#### Acceptance Criteria

1. WHEN a customer returns from VNPay, THE Payment_System SHALL verify the return URL Hash_Signature
2. WHEN a customer returns from Momo, THE Payment_System SHALL verify the return URL Hash_Signature
3. IF return URL signature verification fails, THEN THE Payment_System SHALL display an error message and log the incident
4. WHEN payment is successful, THE Payment_System SHALL display order confirmation page with order details
5. WHEN payment fails, THE Payment_System SHALL display failure reason and option to retry payment
6. WHEN payment is pending, THE Payment_System SHALL display pending status and estimated processing time
7. THE Payment_System SHALL retrieve final transaction status from database rather than relying solely on return URL parameters

### Requirement 6: Transaction Timeout Handling

**User Story:** As the system, I want to handle abandoned payment sessions, so that inventory is not locked indefinitely.

#### Acceptance Criteria

1. WHEN a payment transaction is created, THE Payment_System SHALL set expiration time to 15 minutes from creation
2. WHILE a transaction status is "pending", THE Payment_System SHALL check for timeout on status queries
3. WHEN current time exceeds transaction expiration time, THE Payment_System SHALL update transaction status to "expired"
4. WHEN a transaction expires, THE Order_System SHALL cancel the associated order
5. WHEN a transaction expires, THE Order_System SHALL restore product inventory quantities
6. IF a callback is received for an expired transaction, THEN THE Callback_Handler SHALL reject the callback and return error response

### Requirement 7: Payment Error Handling

**User Story:** As a customer, I want to understand why my payment failed, so that I can take corrective action.

#### Acceptance Criteria

1. WHEN VNPay returns an error code, THE Payment_System SHALL map the error code to a user-friendly message in Vietnamese
2. WHEN Momo returns an error code, THE Payment_System SHALL map the error code to a user-friendly message in Vietnamese
3. IF payment gateway is unreachable, THEN THE Payment_System SHALL display a maintenance message and suggest COD alternative
4. WHEN a payment fails due to insufficient balance, THE Payment_System SHALL display the specific error and suggest alternative payment methods
5. WHEN a payment fails due to network timeout, THE Payment_System SHALL allow the customer to check payment status or retry
6. THE Payment_System SHALL log all payment errors with timestamp, order ID, error code, and error message for debugging

### Requirement 8: Duplicate Transaction Prevention

**User Story:** As the system, I want to prevent duplicate payment processing, so that customers are not charged multiple times.

#### Acceptance Criteria

1. WHEN a callback is received, THE Callback_Handler SHALL check if the Transaction_ID has already been processed
2. IF a Transaction_ID has already been processed, THEN THE Callback_Handler SHALL return success response without updating database
3. THE Payment_System SHALL use database transaction locking when updating payment status to prevent race conditions
4. WHEN multiple callbacks arrive for the same transaction, THE Payment_System SHALL process only the first valid callback
5. THE Payment_System SHALL store gateway Transaction_ID in the transaction record for idempotency checking

### Requirement 9: Payment Gateway Configuration Management

**User Story:** As a system administrator, I want to configure payment gateway credentials securely, so that sensitive data is protected.

#### Acceptance Criteria

1. THE Payment_System SHALL read VNPay credentials from environment variables (VNPAY_TMN_CODE, VNPAY_HASH_SECRET, VNPAY_URL)
2. THE Payment_System SHALL read Momo credentials from environment variables (MOMO_PARTNER_CODE, MOMO_ACCESS_KEY, MOMO_SECRET_KEY, MOMO_URL)
3. THE Payment_System SHALL NOT store payment gateway credentials in source code or database
4. WHEN environment variables are missing, THE Payment_System SHALL disable the corresponding payment method
5. THE Payment_System SHALL validate that all required configuration values are present on application startup
6. IF required configuration is missing, THEN THE Payment_System SHALL log a warning message indicating which payment method is disabled

### Requirement 10: Payment Method Selection UI

**User Story:** As a customer, I want to see available payment methods clearly, so that I can choose my preferred option.

#### Acceptance Criteria

1. WHEN a customer views the checkout page, THE Payment_System SHALL display all enabled payment methods with icons
2. THE Payment_System SHALL display COD with description "Pay when you receive your order"
3. THE Payment_System SHALL display VNPay with description "Pay securely with VNPay"
4. THE Payment_System SHALL display Momo with description "Pay with Momo e-wallet"
5. WHEN a payment method is disabled due to missing configuration, THE Payment_System SHALL hide that payment option
6. THE Payment_System SHALL pre-select COD as the default payment method
7. WHEN a customer selects a payment method, THE UI SHALL highlight the selected option visually

### Requirement 11: Admin Payment Transaction Management

**User Story:** As an administrator, I want to view and manage payment transactions, so that I can track payment status and resolve issues.

#### Acceptance Criteria

1. THE Payment_System SHALL provide an admin interface to list all transactions with filters for status and payment method
2. WHEN an admin views transaction details, THE Payment_System SHALL display order ID, amount, payment method, status, gateway Transaction_ID, and timestamps
3. THE Payment_System SHALL allow admins to manually mark COD payments as completed after delivery confirmation
4. THE Payment_System SHALL display callback logs for each transaction showing all gateway notifications received
5. WHEN an admin searches transactions, THE Payment_System SHALL support search by order ID, Transaction_ID, or customer name
6. THE Payment_System SHALL allow admins to export transaction reports in CSV format with date range filter

### Requirement 12: Payment Refund Support

**User Story:** As an administrator, I want to initiate refunds for cancelled orders, so that customers receive their money back.

#### Acceptance Criteria

1. WHEN an admin cancels a paid order, THE Payment_System SHALL display refund options based on payment method
2. WHERE payment method is VNPay, THE Payment_System SHALL provide a button to initiate VNPay refund API call
3. WHERE payment method is Momo, THE Payment_System SHALL provide a button to initiate Momo refund API call
4. WHERE payment method is COD, THE Payment_System SHALL mark the transaction as cancelled without refund processing
5. WHEN a refund is initiated, THE Payment_System SHALL create a refund record with status "pending"
6. WHEN a refund callback is received, THE Payment_System SHALL update refund status to "completed" or "failed"
7. THE Payment_System SHALL store refund Transaction_ID from gateway for tracking purposes

### Requirement 13: Payment Security and Logging

**User Story:** As a system administrator, I want comprehensive payment logs, so that I can audit transactions and investigate issues.

#### Acceptance Criteria

1. THE Payment_System SHALL log all payment gateway API requests with timestamp, order ID, amount, and request parameters (excluding sensitive keys)
2. THE Payment_System SHALL log all payment gateway API responses with timestamp, status code, and response body
3. THE Payment_System SHALL log all callback requests with timestamp, source IP, and verification result
4. THE Payment_System SHALL log all Hash_Signature verification attempts with success or failure result
5. WHEN a security violation is detected, THE Payment_System SHALL log the incident with severity level "critical"
6. THE Payment_System SHALL retain payment logs for at least 12 months for compliance purposes
7. THE Payment_System SHALL NOT log Hash_Secret, ACCESS_KEY, or SECRET_KEY values in plain text

### Requirement 14: Payment Amount Validation

**User Story:** As the system, I want to validate payment amounts, so that customers are charged the correct amount.

#### Acceptance Criteria

1. WHEN generating a payment request, THE Payment_System SHALL verify that payment amount matches order total
2. WHEN a callback is received, THE Payment_System SHALL verify that the paid amount matches the order total
3. IF callback amount does not match order total, THEN THE Payment_System SHALL mark transaction as "amount_mismatch" and notify admin
4. THE Payment_System SHALL calculate order total including product prices, shipping fee, and discount deductions
5. THE Payment_System SHALL format payment amounts as integers in the smallest currency unit (VND has no decimal places)
6. WHEN displaying amounts to customers, THE Payment_System SHALL format numbers with thousand separators (e.g., "1.000.000đ")

### Requirement 15: Payment Gateway Health Monitoring

**User Story:** As a system administrator, I want to monitor payment gateway availability, so that I can respond to outages quickly.

#### Acceptance Criteria

1. THE Payment_System SHALL track successful and failed payment gateway API calls
2. WHEN payment gateway API call fails, THE Payment_System SHALL increment failure counter for that gateway
3. WHEN failure rate exceeds 50% over 10 consecutive requests, THE Payment_System SHALL log a critical alert
4. THE Payment_System SHALL provide an admin dashboard showing payment success rate by gateway for the last 24 hours
5. THE Payment_System SHALL display average payment processing time for each gateway
6. WHEN a gateway is experiencing issues, THE Payment_System SHALL display a warning message to customers suggesting alternative payment methods
