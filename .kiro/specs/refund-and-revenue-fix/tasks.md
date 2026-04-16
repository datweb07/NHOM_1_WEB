# Implementation Plan: Refund and Revenue Calculation Fix

## Overview

This implementation plan breaks down the refund workflow and revenue calculation fix into discrete, actionable tasks. The feature enables administrators to process refunds through payment gateways (VNPay, Momo) and corrects dashboard revenue calculations to only include approved, non-refunded payments.

**Implementation Strategy**:
1. Database schema updates first (foundation)
2. Service layer implementation (business logic)
3. Controller and routing (API endpoints)
4. UI components (admin interface)
5. Revenue calculation fixes (dashboard)
6. Testing and validation

## Tasks

- [x] 1. Database schema migration and model updates
  - [x] 1.1 Add admin_id column to refund table
    - Create migration SQL to add `admin_id INT` column with foreign key to `nguoi_dung` table
    - Add `ON DELETE SET NULL` constraint for referential integrity
    - _Requirements: 3.3, 10.3_
  
  - [x] 1.2 Create database indexes for performance
    - Add index on `thanh_toan.trang_thai_duyet` for revenue queries
    - Add composite index on `refund(thanh_toan_id, status)` for refund lookups
    - Add composite index on `don_hang(trang_thai, ngay_tao)` for dashboard queries
    - _Requirements: 5.1, 5.2, 6.1_
  
  - [x] 1.3 Update Refund model with new methods
    - Implement `hasCompletedRefund(int $thanhToanId): bool` method
    - Implement `getRefundStats(int $thanhToanId): array` method returning total_refunded and refund_count
    - Add `admin_id` field to model properties and CRUD operations
    - _Requirements: 3.1, 3.2, 3.3, 6.1_

- [x] 2. Implement RefundService class
  - [x] 2.1 Create RefundService class structure
    - Create `app/services/refund/RefundService.php` file
    - Define class with dependencies: Refund model, ThanhToan model, TransactionLog model, PaymentService
    - Implement constructor with dependency injection
    - _Requirements: 2.1, 2.2, 2.3_
  
  - [x] 2.2 Implement canRefund validation method
    - Check payment approval status is THANH_CONG
    - Check payment method is not COD or ZaloPay
    - Check no existing COMPLETED refund exists
    - Return array with `can_refund` boolean and `reason` string
    - _Requirements: 1.2, 1.3, 1.4, 1.5, 9.1_
  
  - [x] 2.3 Implement initiateRefund method
    - Validate payment can be refunded using canRefund method
    - Create Refund record with status PENDING, store admin_id
    - Get appropriate payment gateway from PaymentService
    - Call gateway initiateRefund method with transaction ID, amount, reason
    - Update Refund status to COMPLETED or FAILED based on gateway response
    - Store gateway_refund_id and completed_at timestamp on success
    - Return array with success boolean, message, and refund_id
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [x] 2.4 Implement transaction logging in RefundService
    - Log REFUND_INITIATED when refund process starts
    - Log REFUND_COMPLETED with gateway_refund_id on success
    - Log REFUND_FAILED with error message on failure
    - Include refund amount, reason, admin_id in all logs
    - _Requirements: 11.1, 11.2, 11.3, 11.4_

- [x] 3. Update payment gateway integration
  - [x] 3.1 Verify VNPayGateway initiateRefund implementation
    - Review existing `initiateRefund(string $transactionId, float $amount, string $reason)` method
    - Ensure it returns array with `success` boolean and `refund_id` or `message`
    - Test with VNPay sandbox credentials
    - _Requirements: 7.1, 7.2, 7.3, 7.4_
  
  - [x] 3.2 Verify MomoGateway initiateRefund implementation
    - Review existing `initiateRefund(string $transactionId, float $amount, string $reason)` method
    - Ensure it returns array with `success` boolean and `refund_id` or `message`
    - Test with Momo sandbox credentials
    - _Requirements: 8.1, 8.2, 8.3, 8.4_
  
  - [x] 3.3 Handle ZaloPay refund limitation
    - Ensure ZaloPay is excluded from refund eligibility in canRefund method
    - Document that ZaloPay refunds are not supported in code comments
    - _Requirements: 9.1, 9.2, 9.3_

- [x] 4. Implement admin controller refund endpoints
  - [x] 4.1 Add refund routes to admin router
    - Add GET `/admin/thanh-toan/refund` route to `app/routes/admin/admin.php`
    - Add POST `/admin/thanh-toan/refund` route to `app/routes/admin/admin.php`
    - Ensure routes are protected by AdminMiddleware
    - _Requirements: 10.1, 10.2_
  
  - [x] 4.2 Implement showRefundForm method in ThanhToanController
    - Get payment ID from query parameter
    - Fetch payment record from ThanhToan model
    - Validate payment exists, redirect with error if not found
    - Check if user is admin, redirect to login if not
    - Pass payment data to refund modal/form view
    - _Requirements: 2.1, 10.1, 10.2_
  
  - [x] 4.3 Implement processRefund method in ThanhToanController
    - Get payment ID from query parameter
    - Validate POST data: amount (numeric), reason (required string)
    - Get admin user ID from session
    - Call RefundService->initiateRefund with payment ID, amount, reason, admin ID
    - Redirect to payment detail page with success or error message
    - Handle all error scenarios: payment not found, not approved, already refunded, gateway failure
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 10.3_

- [x] 5. Checkpoint - Ensure refund service and endpoints work
  - Ensure all tests pass, ask the user if questions arise.

- [-] 6. Implement admin UI for refund workflow
  - [-] 6.1 Add refund button to payment detail page
    - Modify `app/views/admin/thanh_toan/detail.php`
    - Add logic to determine if refund button should be shown based on payment status
    - Show enabled button for THANH_CONG payments without completed refunds
    - Show disabled button with tooltip for CHO_DUYET, THAT_BAI, COD, ZaloPay, or already refunded
    - Hide button completely if payment method is COD
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 9.1, 9.2_
  
  - [-] 6.2 Create refund confirmation modal
    - Add Bootstrap modal HTML to payment detail page
    - Display refund amount (readonly, pre-filled from payment)
    - Add textarea for refund reason (required field)
    - Add warning alert about refund action
    - Form submits to POST `/admin/thanh-toan/refund?id={payment_id}`
    - _Requirements: 2.1, 3.3_
  
  - [-] 6.3 Add refund history display section
    - Add card section below payment details showing all refund records
    - Display table with columns: amount, status, reason, gateway_refund_id, created_at, completed_at
    - Use badge colors: success (COMPLETED), warning (PENDING), danger (FAILED)
    - Format dates as dd/mm/yyyy HH:ii
    - Show "N/A" for null gateway_refund_id or completed_at
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_
  
  - [x] 6.4 Implement success and error flash messages
    - Add flash message display logic to payment detail page
    - Show success message: "Hoàn tiền thành công. Mã giao dịch: {gateway_refund_id}"
    - Show error messages for all failure scenarios
    - Use Bootstrap alert components with appropriate colors
    - _Requirements: 2.6, 2.7_

- [x] 7. Fix dashboard revenue calculation logic
  - [x] 7.1 Update DashboardController revenue query
    - Modify `calculateMonthlyRevenue()` method in `app/controllers/admin/DashboardController.php`
    - Add JOIN with `thanh_toan` table on `don_hang.id = thanh_toan.don_hang_id`
    - Add WHERE clause: `thanh_toan.trang_thai_duyet = 'THANH_CONG'`
    - Add WHERE clause: `don_hang.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')`
    - Add LEFT JOIN with `refund` table to check for completed refunds
    - Add WHERE clause: `refund.id IS NULL OR refund.status != 'COMPLETED'`
    - Sum `don_hang.tong_thanh_toan` for qualifying orders
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 6.1, 6.2, 6.3_
  
  - [x] 7.2 Update total revenue calculation
    - Modify `calculateTotalRevenue()` method with same JOIN and WHERE logic as monthly revenue
    - Ensure both methods use consistent filtering logic
    - _Requirements: 5.5, 6.1, 6.2, 6.3_
  
  - [x] 7.3 Verify dashboard displays corrected revenue
    - Test that dashboard shows updated revenue after payment approval changes
    - Test that dashboard excludes refunded payments from revenue
    - Test that dashboard excludes cancelled/returned orders
    - _Requirements: 12.1, 12.2, 12.3, 12.4_

- [x] 8. Checkpoint - Ensure revenue calculation is correct
  - Ensure all tests pass, ask the user if questions arise.

- [x] 9. Error handling and edge cases
  - [x] 9.1 Implement comprehensive error handling in RefundService
    - Handle payment not found error
    - Handle payment not approved error
    - Handle already refunded error
    - Handle gateway not configured error
    - Handle gateway API failure with specific error messages
    - Handle unsupported payment method error
    - _Requirements: 2.7, 7.3, 8.3, 9.3_
  
  - [x] 9.2 Add authorization checks to refund endpoints
    - Verify admin is logged in before showing refund form
    - Verify admin is logged in before processing refund
    - Redirect to login page if not authenticated
    - _Requirements: 10.1, 10.2_
  
  - [x] 9.3 Implement transaction logging for all scenarios
    - Ensure all refund attempts are logged regardless of outcome
    - Include admin_id, payment_id, amount, reason in all logs
    - Log gateway responses for debugging
    - _Requirements: 11.1, 11.2, 11.3, 11.4_

- [ ] 10. Testing and validation
  - [ ]* 10.1 Write unit tests for RefundService
    - Test initiateRefund with valid payment returns success
    - Test initiateRefund with invalid payment returns error
    - Test canRefund returns true for approved payments
    - Test canRefund returns false for unapproved, COD, ZaloPay, or refunded payments
    - Mock gateway responses for testing
  
  - [ ]* 10.2 Write unit tests for revenue calculation
    - Test revenue excludes unapproved payments
    - Test revenue excludes refunded payments
    - Test revenue excludes cancelled/returned orders
    - Test revenue includes only THANH_CONG approved payments
  
  - [ ]* 10.3 Write integration tests for refund workflow
    - Test complete refund flow from button click to success message
    - Test refund with VNPay gateway (mocked)
    - Test refund with Momo gateway (mocked)
    - Test refund failure scenarios
    - Test transaction logging

- [x] 11. Final integration and deployment preparation
  - [x] 11.1 Manual testing of refund workflow
    - Test refund button visibility for all payment states
    - Test refund modal displays correct data
    - Test successful refund with VNPay (sandbox)
    - Test successful refund with Momo (sandbox)
    - Test error handling for all failure scenarios
    - Test refund history display
    - _Requirements: All refund requirements_
  
  - [x] 11.2 Manual testing of revenue calculation
    - Create test data with mix of approved/unapproved payments
    - Create test refunded payment
    - Verify dashboard shows correct revenue
    - Verify revenue updates after payment approval changes
    - Verify revenue updates after refund completion
    - _Requirements: All revenue requirements_
  
  - [x] 11.3 Performance testing
    - Test revenue query performance with large dataset
    - Verify indexes are being used (EXPLAIN query)
    - Test refund gateway API response times
    - Ensure transaction logging doesn't block refund process
  
  - [x] 11.4 Documentation and deployment checklist
    - Document refund workflow in admin user guide
    - Document revenue calculation logic changes
    - Create deployment checklist with migration steps
    - Document rollback procedure if needed

- [x] 12. Final checkpoint - Complete feature validation
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional testing tasks and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation at key milestones
- The implementation follows a bottom-up approach: database → service → controller → UI
- All refund operations are logged for audit purposes
- Revenue calculation changes are backward compatible (no breaking changes)
- Gateway integration uses existing `initiateRefund()` methods in payment gateway classes
- ZaloPay refunds are explicitly not supported and handled gracefully
