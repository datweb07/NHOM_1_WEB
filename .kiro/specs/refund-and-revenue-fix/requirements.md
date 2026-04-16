# Requirements Document

## Introduction

This feature addresses two critical business logic issues in the e-commerce system:

1. **Admin Refund Processing**: Enable administrators to initiate refunds for completed payments through the admin payment detail page
2. **Revenue Calculation Fix**: Correct the revenue calculation logic to only count orders where payment is both successful AND approved by admin

Currently, the system has a `refund` table and `Refund` model with basic CRUD operations, and payment gateways (VNPay, Momo, ZaloPay) have `initiateRefund()` methods, but there is no UI or workflow to trigger refunds. Additionally, the dashboard calculates revenue from all orders except cancelled/returned orders, ignoring the payment approval status (`trang_thai_duyet`).

## Glossary

- **Admin_System**: The administrative backend interface for managing orders and payments
- **Refund_Service**: The service responsible for processing refund requests through payment gateways
- **Payment_Gateway**: External payment service providers (VNPay, Momo, ZaloPay)
- **Dashboard_Service**: The service responsible for calculating and displaying business metrics
- **Payment_Record**: A record in the `thanh_toan` table representing a payment transaction
- **Order_Record**: A record in the `don_hang` table representing a customer order
- **Refund_Record**: A record in the `refund` table tracking refund transactions
- **Approval_Status**: The `trang_thai_duyet` field indicating payment approval state (CHO_DUYET/THANH_CONG/THAT_BAI)
- **Payment_Method**: The `phuong_thuc` field indicating payment type (VNPay/Momo/ZaloPay/COD)

## Requirements

### Requirement 1: Admin Refund Button Display

**User Story:** As an admin, I want to see a refund button on the payment detail page, so that I can initiate refunds for approved payments.

#### Acceptance Criteria

1. WHEN viewing a payment detail page, THE Admin_System SHALL display a refund button in the action section
2. WHILE the payment approval status is THANH_CONG, THE Admin_System SHALL enable the refund button
3. WHILE the payment approval status is CHO_DUYET or THAT_BAI, THE Admin_System SHALL disable the refund button
4. WHEN a refund record exists for the payment, THE Admin_System SHALL hide the refund button
5. WHILE the payment method is COD, THE Admin_System SHALL hide the refund button

### Requirement 2: Refund Initiation Workflow

**User Story:** As an admin, I want to initiate a refund by clicking the refund button, so that I can return money to customers.

#### Acceptance Criteria

1. WHEN an admin clicks the refund button, THE Admin_System SHALL display a confirmation dialog with refund amount and reason input
2. WHEN the admin confirms the refund, THE Admin_System SHALL create a Refund_Record with status PENDING
3. WHEN a Refund_Record is created, THE Refund_Service SHALL call the appropriate Payment_Gateway initiateRefund method
4. WHEN the Payment_Gateway returns success, THE Refund_Service SHALL update the Refund_Record status to COMPLETED
5. IF the Payment_Gateway returns failure, THEN THE Refund_Service SHALL update the Refund_Record status to FAILED
6. WHEN a refund is completed, THE Admin_System SHALL display a success message with the gateway refund ID
7. IF a refund fails, THEN THE Admin_System SHALL display an error message with the failure reason

### Requirement 3: Refund Record Association

**User Story:** As an admin, I want refund records to be linked to payment records, so that I can track refund history.

#### Acceptance Criteria

1. WHEN creating a Refund_Record, THE Refund_Service SHALL store the thanh_toan_id foreign key
2. WHEN creating a Refund_Record, THE Refund_Service SHALL store the refund amount equal to the payment amount
3. WHEN creating a Refund_Record, THE Refund_Service SHALL store the admin-provided reason
4. WHEN a Payment_Gateway returns a refund ID, THE Refund_Service SHALL store it in the gateway_refund_id field
5. WHEN a refund status changes to COMPLETED, THE Refund_Service SHALL record the completion timestamp

### Requirement 4: Refund Display on Payment Detail Page

**User Story:** As an admin, I want to see refund history on the payment detail page, so that I can verify refund status.

#### Acceptance Criteria

1. WHEN viewing a payment detail page, THE Admin_System SHALL display all associated Refund_Records
2. WHEN displaying a Refund_Record, THE Admin_System SHALL show the amount, status, reason, created date, and completion date
3. WHEN a Refund_Record has a gateway_refund_id, THE Admin_System SHALL display it
4. WHEN a Refund_Record status is PENDING, THE Admin_System SHALL display it with a warning badge
5. WHEN a Refund_Record status is COMPLETED, THE Admin_System SHALL display it with a success badge
6. WHEN a Refund_Record status is FAILED, THE Admin_System SHALL display it with a danger badge

### Requirement 5: Revenue Calculation Correction

**User Story:** As a business manager, I want revenue to only include approved payments, so that I can see accurate financial metrics.

#### Acceptance Criteria

1. WHEN calculating revenue, THE Dashboard_Service SHALL join the don_hang and thanh_toan tables
2. WHEN calculating revenue, THE Dashboard_Service SHALL only include orders where thanh_toan.trang_thai_duyet equals THANH_CONG
3. WHEN calculating revenue, THE Dashboard_Service SHALL exclude orders where don_hang.trang_thai is DA_HUY or TRA_HANG
4. WHEN calculating monthly revenue, THE Dashboard_Service SHALL sum the tong_thanh_toan field from qualifying orders
5. WHEN calculating total revenue, THE Dashboard_Service SHALL sum the tong_thanh_toan field from qualifying orders

### Requirement 6: Revenue Calculation Excludes Refunded Payments

**User Story:** As a business manager, I want refunded payments to be excluded from revenue, so that revenue reflects actual income.

#### Acceptance Criteria

1. WHEN calculating revenue, THE Dashboard_Service SHALL exclude payments that have a COMPLETED Refund_Record
2. WHEN a payment has multiple Refund_Records, THE Dashboard_Service SHALL exclude the payment if any refund is COMPLETED
3. WHEN calculating revenue, THE Dashboard_Service SHALL include payments with PENDING or FAILED Refund_Records

### Requirement 7: Refund Gateway Integration for VNPay

**User Story:** As a system, I want to call VNPay refund API, so that refunds are processed through the payment gateway.

#### Acceptance Criteria

1. WHEN initiating a VNPay refund, THE Refund_Service SHALL call VNPayGateway.initiateRefund with transaction ID, amount, and reason
2. WHEN VNPayGateway.initiateRefund returns success true, THE Refund_Service SHALL record the refund as COMPLETED
3. IF VNPayGateway.initiateRefund returns success false, THEN THE Refund_Service SHALL record the refund as FAILED
4. WHEN calling VNPayGateway.initiateRefund, THE Refund_Service SHALL use the gateway_transaction_id from the Payment_Record

### Requirement 8: Refund Gateway Integration for Momo

**User Story:** As a system, I want to call Momo refund API, so that refunds are processed through the payment gateway.

#### Acceptance Criteria

1. WHEN initiating a Momo refund, THE Refund_Service SHALL call MomoGateway.initiateRefund with transaction ID, amount, and reason
2. WHEN MomoGateway.initiateRefund returns success true, THE Refund_Service SHALL record the refund as COMPLETED
3. IF MomoGateway.initiateRefund returns success false, THEN THE Refund_Service SHALL record the refund as FAILED
4. WHEN calling MomoGateway.initiateRefund, THE Refund_Service SHALL use the gateway_transaction_id from the Payment_Record

### Requirement 9: Refund Not Supported for ZaloPay

**User Story:** As an admin, I want to see a clear message that ZaloPay refunds are not supported, so that I understand the limitation.

#### Acceptance Criteria

1. WHILE the payment method is ZaloPay, THE Admin_System SHALL disable the refund button
2. WHEN hovering over a disabled refund button for ZaloPay, THE Admin_System SHALL display a tooltip explaining refunds are not supported
3. IF an admin attempts to refund a ZaloPay payment, THEN THE Admin_System SHALL display an error message

### Requirement 10: Refund Authorization

**User Story:** As a system, I want to verify admin authorization before processing refunds, so that only authorized users can refund payments.

#### Acceptance Criteria

1. WHEN an admin initiates a refund, THE Admin_System SHALL verify the admin is logged in
2. IF the admin is not logged in, THEN THE Admin_System SHALL redirect to the login page
3. WHEN creating a Refund_Record, THE Refund_Service SHALL record the admin user ID who initiated the refund

### Requirement 11: Refund Transaction Logging

**User Story:** As a system administrator, I want refund attempts to be logged, so that I can audit refund operations.

#### Acceptance Criteria

1. WHEN initiating a refund, THE Refund_Service SHALL create a TransactionLog entry with action type REFUND_INITIATED
2. WHEN a refund succeeds, THE Refund_Service SHALL create a TransactionLog entry with action type REFUND_COMPLETED
3. IF a refund fails, THEN THE Refund_Service SHALL create a TransactionLog entry with action type REFUND_FAILED
4. WHEN logging refund transactions, THE Refund_Service SHALL include the refund amount, reason, and gateway response

### Requirement 12: Dashboard Revenue Display Update

**User Story:** As a business manager, I want the dashboard to show corrected revenue, so that I can make informed business decisions.

#### Acceptance Criteria

1. WHEN viewing the dashboard, THE Dashboard_Service SHALL display monthly revenue calculated with the corrected logic
2. WHEN viewing the dashboard, THE Dashboard_Service SHALL display total revenue calculated with the corrected logic
3. WHEN revenue changes due to payment approval, THE Dashboard_Service SHALL reflect the change on next dashboard load
4. WHEN revenue changes due to refund completion, THE Dashboard_Service SHALL reflect the change on next dashboard load
