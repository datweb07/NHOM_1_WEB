# Implementation Plan: Payment Gateway Integration

## Overview

This implementation plan breaks down the payment gateway integration feature into discrete coding tasks. The system will support three payment methods: Cash on Delivery (COD), VNPay online payment gateway, and Momo e-wallet. The implementation follows a service-oriented architecture with clear separation between gateway logic, business logic, and data persistence.

## Tasks

- [x] 1. Set up payment gateway infrastructure and configuration
  - Create `.env` configuration entries for VNPay and Momo credentials
  - Add VNPAY_TMN_CODE, VNPAY_HASH_SECRET, VNPAY_URL environment variables
  - Add MOMO_PARTNER_CODE, MOMO_ACCESS_KEY, MOMO_SECRET_KEY, MOMO_URL environment variables
  - Update `.env.example` with placeholder values for documentation
  - _Requirements: 9.1, 9.2, 9.3, 9.6_

- [x] 2. Create database schema for transaction logging
  - [x] 2.1 Create `transaction_log` table migration
    - Add columns: id, thanh_toan_id, gateway_transaction_id, gateway_name, request_data, response_data, callback_data, status, created_at
    - Add foreign key constraint to thanh_toan table
    - Add index on gateway_transaction_id for idempotency checks
    - _Requirements: 8.1, 8.5, 13.1, 13.2, 13.3_
  
  - [x] 2.2 Update `thanh_toan` table schema
    - Add gateway_transaction_id VARCHAR(255) column
    - Add gateway_name VARCHAR(50) column
    - Add expiration_time DATETIME column
    - Add payment_url TEXT column
    - Add error_code VARCHAR(50) column
    - Add error_message TEXT column
    - _Requirements: 6.1, 7.6, 8.5_

- [x] 3. Create TransactionLog model
  - Create `app/models/entities/TransactionLog.php` extending BaseModel
  - Implement `logRequest($thanhToanId, $gatewayName, $requestData)` method
  - Implement `logResponse($thanhToanId, $responseData, $status)` method
  - Implement `logCallback($thanhToanId, $callbackData, $verificationResult)` method
  - Implement `findByGatewayTransactionId($gatewayTransactionId)` method for duplicate checking
  - _Requirements: 8.1, 13.1, 13.2, 13.3_

- [x] 4. Create base PaymentGateway interface and service
  - [x] 4.1 Create PaymentGateway interface
    - Create `app/services/payment/PaymentGatewayInterface.php`
    - Define methods: `generatePaymentUrl($transaction)`, `verifyCallback($data)`, `verifyReturnUrl($data)`, `getErrorMessage($errorCode)`
    - _Requirements: 2.1, 3.1_
  
  - [x] 4.2 Create PaymentService class
    - Create `app/services/payment/PaymentService.php`
    - Implement `createTransaction($donHangId, $paymentMethod, $amount)` method
    - Implement `processPayment($transactionId, $paymentMethod)` method
    - Implement `checkTransactionTimeout($transactionId)` method
    - Implement `validateAmount($transactionId, $paidAmount)` method
    - Add database transaction handling for status updates
    - _Requirements: 1.1, 6.1, 6.2, 14.1, 14.2_

- [x] 5. Implement COD payment handler
  - [x] 5.1 Create CODHandler class
    - Create `app/services/payment/CODHandler.php` implementing PaymentGatewayInterface
    - Implement `generatePaymentUrl()` to return null (no redirect needed)
    - Implement `processPayment()` to create pending transaction
    - Update order status to "awaiting_confirmation"
    - _Requirements: 1.1, 1.2, 1.3_
  
  - [ ]* 5.2 Write unit tests for CODHandler
    - Test transaction creation with pending status
    - Test order status update to awaiting_confirmation
    - Test that no payment URL is generated
    - _Requirements: 1.1, 1.2, 1.3_

- [x] 6. Implement VNPay gateway integration
  - [x] 6.1 Create VNPayGateway class
    - Create `app/services/payment/VNPayGateway.php` implementing PaymentGatewayInterface
    - Implement `generatePaymentUrl($transaction)` method
    - Build VNPay payment URL with order details, amount, return URL, IPN URL
    - Implement HMAC-SHA512 signature generation using VNPAY_HASH_SECRET
    - Set transaction expiration to 15 minutes
    - Store payment URL in transaction record
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 6.1_
  
  - [x] 6.2 Implement VNPay signature verification
    - Implement `verifyCallback($data)` method with HMAC-SHA512 verification
    - Implement `verifyReturnUrl($data)` method with HMAC-SHA512 verification
    - Return boolean indicating signature validity
    - _Requirements: 4.1, 5.1_
  
  - [x] 6.3 Implement VNPay error code mapping
    - Implement `getErrorMessage($errorCode)` method
    - Map VNPay error codes to Vietnamese user-friendly messages
    - Handle common errors: insufficient balance, invalid card, timeout, cancelled
    - _Requirements: 7.1, 7.4_
  
  - [ ]* 6.4 Write unit tests for VNPayGateway
    - Test payment URL generation with correct parameters
    - Test HMAC-SHA512 signature generation
    - Test signature verification for valid and invalid signatures
    - Test error code mapping
    - _Requirements: 2.1, 2.4, 4.1, 7.1_

- [x] 7. Implement Momo gateway integration
  - [x] 7.1 Create MomoGateway class
    - Create `app/services/payment/MomoGateway.php` implementing PaymentGatewayInterface
    - Implement `generatePaymentUrl($transaction)` method
    - Build Momo payment request with order details, amount, redirect URL, IPN URL
    - Implement HMAC-SHA256 signature generation using MOMO_SECRET_KEY
    - Make HTTP POST request to Momo API endpoint
    - Parse response and extract payment URL
    - Set transaction expiration to 15 minutes
    - Store payment URL in transaction record
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 6.1_
  
  - [x] 7.2 Implement Momo signature verification
    - Implement `verifyCallback($data)` method with HMAC-SHA256 verification
    - Implement `verifyReturnUrl($data)` method with HMAC-SHA256 verification
    - Return boolean indicating signature validity
    - _Requirements: 4.2, 5.2_
  
  - [x] 7.3 Implement Momo error code mapping
    - Implement `getErrorMessage($errorCode)` method
    - Map Momo error codes to Vietnamese user-friendly messages
    - Handle common errors: insufficient balance, invalid account, timeout, cancelled
    - _Requirements: 7.2, 7.4_
  
  - [ ]* 7.4 Write unit tests for MomoGateway
    - Test payment request generation with correct parameters
    - Test HMAC-SHA256 signature generation
    - Test signature verification for valid and invalid signatures
    - Test error code mapping
    - Test HTTP request handling and response parsing
    - _Requirements: 3.1, 3.4, 4.2, 7.2_

- [ ] 8. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 9. Create payment callback handler
  - [x] 9.1 Create CallbackHandler class
    - Create `app/services/payment/CallbackHandler.php`
    - Implement `handleVNPayCallback($data)` method
    - Implement `handleMomoCallback($data)` method
    - Verify signature before processing using respective gateway classes
    - Check for duplicate transactions using gateway_transaction_id
    - Implement database locking for transaction status updates
    - Log all callback requests with TransactionLog model
    - Return HTTP 200 response within 3 seconds
    - _Requirements: 4.1, 4.2, 4.3, 4.8, 4.9, 8.1, 8.2, 8.3, 13.3_
  
  - [x] 9.2 Implement callback success handling
    - Update transaction status to "completed" when payment succeeds
    - Update order status to "confirmed"
    - Store gateway_transaction_id in transaction record
    - Log success event with TransactionLog
    - _Requirements: 4.4, 4.5, 8.5_
  
  - [x] 9.3 Implement callback failure handling
    - Update transaction status to "failed" when payment fails
    - Restore product inventory quantities using PhienBanSanPham model
    - Log failure event with TransactionLog
    - _Requirements: 4.6, 4.7_
  
  - [x] 9.4 Implement timeout and expiration handling
    - Check transaction expiration time in callback handler
    - Reject callbacks for expired transactions
    - Update expired transactions to "expired" status
    - Cancel associated orders and restore inventory
    - _Requirements: 6.2, 6.3, 6.4, 6.5, 6.6_
  
  - [ ]* 9.5 Write unit tests for CallbackHandler
    - Test signature verification rejection
    - Test duplicate transaction prevention
    - Test successful payment processing
    - Test failed payment processing with inventory restoration
    - Test expired transaction rejection
    - Test database locking mechanism
    - _Requirements: 4.1, 4.3, 8.1, 8.3, 6.6_

- [x] 10. Update ThanhToanController for payment initiation
  - [x] 10.1 Refactor datHang() method to use PaymentService
    - Replace direct ThanhToan model calls with PaymentService
    - Call `PaymentService->createTransaction()` to create transaction with expiration
    - Call `PaymentService->processPayment()` to handle payment method routing
    - For COD: complete order creation flow as before
    - For VNPay/Momo: redirect to payment URL from gateway
    - Add error handling for gateway unavailability
    - _Requirements: 1.1, 2.1, 2.3, 3.1, 3.3, 7.3_
  
  - [x] 10.2 Add payment method validation
    - Validate selected payment method against PhuongThucThanhToan enum
    - Check if payment gateway configuration exists before allowing selection
    - Display error message if gateway is disabled due to missing config
    - _Requirements: 9.4, 9.5_
  
  - [x] 10.3 Implement amount validation
    - Validate payment amount matches order total before creating transaction
    - Format amounts as integers (VND has no decimals)
    - _Requirements: 14.1, 14.4, 14.5_

- [x] 11. Create payment callback routes and controller methods
  - [x] 11.1 Add callback routes
    - Add POST route `/thanh-toan/callback/vnpay` in `app/routes/client/client.php`
    - Add POST route `/thanh-toan/callback/momo` in `app/routes/client/client.php`
    - Routes should not require authentication (external gateway calls)
    - _Requirements: 4.1, 4.2_
  
  - [x] 11.2 Create callback controller methods
    - Add `callbackVNPay()` method in ThanhToanController
    - Add `callbackMomo()` method in ThanhToanController
    - Delegate to CallbackHandler for processing
    - Return JSON response with success status
    - Handle exceptions and return appropriate error responses
    - _Requirements: 4.8, 4.9_

- [x] 12. Create payment return URL handler
  - [x] 12.1 Add return URL routes
    - Add GET route `/thanh-toan/return/vnpay` in `app/routes/client/client.php`
    - Add GET route `/thanh-toan/return/momo` in `app/routes/client/client.php`
    - _Requirements: 5.1, 5.2_
  
  - [x] 12.2 Create return URL controller methods
    - Add `returnVNPay()` method in ThanhToanController
    - Add `returnMomo()` method in ThanhToanController
    - Verify return URL signature using respective gateway classes
    - Retrieve final transaction status from database (not from URL params)
    - Display success page with order details for successful payments
    - Display failure page with error message and retry option for failed payments
    - Display pending page for pending payments
    - Log signature verification failures
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 13.4_
  
  - [ ]* 12.3 Write integration tests for return URL flow
    - Test successful payment return flow
    - Test failed payment return flow
    - Test signature verification failure
    - Test status retrieval from database
    - _Requirements: 5.1, 5.4, 5.5, 5.7_

- [x] 13. Update payment method selection UI
  - [x] 13.1 Update checkout page view
    - Modify `app/views/client/thanh_toan/index.php`
    - Display payment method options with icons and descriptions
    - Add COD option: "Thanh toán khi nhận hàng (COD)"
    - Add VNPay option: "Thanh toán qua VNPay" (only if configured)
    - Add Momo option: "Thanh toán qua ví Momo" (only if configured)
    - Pre-select COD as default
    - Add visual highlighting for selected payment method
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7_
  
  - [x] 13.2 Add payment method availability check
    - Check environment variables in controller before rendering view
    - Pass availability flags to view (vnpay_enabled, momo_enabled)
    - Hide disabled payment methods in UI
    - _Requirements: 9.4, 10.5_

- [x] 14. Create payment result views
  - [x] 14.1 Create success view
    - Create `app/views/client/thanh_toan/success.php`
    - Display order confirmation with order number, amount, payment method
    - Show order details and delivery information
    - Add link to view order details page
    - _Requirements: 5.4_
  
  - [x] 14.2 Create failure view
    - Create `app/views/client/thanh_toan/failure.php`
    - Display error message in Vietnamese
    - Show failure reason from gateway error code mapping
    - Add button to retry payment or return to cart
    - _Requirements: 5.5, 7.1, 7.2, 7.4_
  
  - [x] 14.3 Create pending view
    - Create `app/views/client/thanh_toan/pending.php`
    - Display pending status message
    - Show estimated processing time (15 minutes)
    - Add button to check payment status
    - _Requirements: 5.6_

- [ ] 15. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 16. Implement admin transaction management
  - [x] 16.1 Update admin ThanhToanController
    - Modify `app/controllers/admin/ThanhToanController.php`
    - Update index() method to display payment method and gateway transaction ID
    - Add filters for payment method (COD, VNPay, Momo) and status
    - Add search by order ID, gateway transaction ID, or customer name
    - _Requirements: 11.1, 11.2, 11.5_
  
  - [x] 16.2 Create transaction detail view
    - Update `app/views/admin/thanh_toan/detail.php`
    - Display order ID, amount, payment method, status, gateway transaction ID
    - Display transaction timestamps (created, completed, expired)
    - Show callback logs from TransactionLog model
    - Add button to manually mark COD payments as completed
    - _Requirements: 11.2, 11.3, 11.4_
  
  - [x] 16.3 Implement manual COD payment confirmation
    - Add `confirmCODPayment($id)` method in admin ThanhToanController
    - Update transaction status to "completed"
    - Update order status to "confirmed"
    - Log admin action with user ID and timestamp
    - _Requirements: 1.5, 11.3_
  
  - [x] 16.4 Add transaction export functionality
    - Add `exportTransactions()` method in admin ThanhToanController
    - Generate CSV file with transaction data
    - Support date range filter
    - Include columns: order ID, amount, payment method, status, gateway transaction ID, timestamps
    - _Requirements: 11.6_

- [x] 17. Implement payment refund support
  - [x] 17.1 Create refund database schema
    - Create `refund` table with columns: id, thanh_toan_id, gateway_refund_id, amount, status, reason, created_at, completed_at
    - Add foreign key constraint to thanh_toan table
    - _Requirements: 12.5_
  
  - [x] 17.2 Create Refund model
    - Create `app/models/entities/Refund.php` extending BaseModel
    - Implement `createRefund($thanhToanId, $amount, $reason)` method
    - Implement `updateRefundStatus($id, $status, $gatewayRefundId)` method
    - Implement `findByThanhToanId($thanhToanId)` method
    - _Requirements: 12.5, 12.6, 12.7_
  
  - [x] 17.3 Implement VNPay refund API integration
    - Add `initiateRefund($transactionId, $amount, $reason)` method to VNPayGateway
    - Build VNPay refund API request with transaction details
    - Generate HMAC-SHA512 signature for refund request
    - Make HTTP POST request to VNPay refund endpoint
    - Parse response and return refund status
    - _Requirements: 12.2_
  
  - [x] 17.4 Implement Momo refund API integration
    - Add `initiateRefund($transactionId, $amount, $reason)` method to MomoGateway
    - Build Momo refund API request with transaction details
    - Generate HMAC-SHA256 signature for refund request
    - Make HTTP POST request to Momo refund endpoint
    - Parse response and return refund status
    - _Requirements: 12.3_
  
  - [x] 17.5 Add refund UI in admin
    - Update `app/views/admin/don_hang/detail.php`
    - Add "Initiate Refund" button for paid orders
    - Show refund options based on payment method (VNPay, Momo, COD)
    - For COD, mark as cancelled without refund processing
    - Display refund status and gateway refund ID
    - _Requirements: 12.1, 12.2, 12.3, 12.4_
  
  - [x] 17.6 Create refund controller methods
    - Add `initiateRefund($donHangId)` method in admin DonHangController
    - Validate order is paid and eligible for refund
    - Call appropriate gateway refund method based on payment method
    - Create refund record with pending status
    - Update order status to "refunding"
    - _Requirements: 12.1, 12.5_
  
  - [ ]* 17.7 Write unit tests for refund functionality
    - Test VNPay refund request generation
    - Test Momo refund request generation
    - Test refund record creation
    - Test COD cancellation without refund
    - _Requirements: 12.2, 12.3, 12.4, 12.5_

- [x] 18. Implement payment logging and security
  - [ ] 18.1 Add comprehensive logging
    - Log all payment gateway API requests (excluding secret keys)
    - Log all payment gateway API responses
    - Log all callback requests with source IP
    - Log all signature verification attempts
    - Use TransactionLog model for all logging
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.7_
  
  - [ ] 18.2 Implement security violation logging
    - Log signature verification failures with severity "critical"
    - Log amount mismatch incidents with severity "critical"
    - Log expired transaction callback attempts
    - Include timestamp, order ID, gateway name, and violation details
    - _Requirements: 4.3, 13.5, 14.3_
  
  - [ ] 18.3 Implement log retention policy
    - Add database cleanup script for logs older than 12 months
    - Schedule as cron job or manual admin task
    - _Requirements: 13.6_

- [x] 19. Implement payment gateway health monitoring
  - [x] 19.1 Create gateway health tracking
    - Create `gateway_health` table with columns: id, gateway_name, success_count, failure_count, last_success_at, last_failure_at, updated_at
    - Create GatewayHealth model extending BaseModel
    - Implement `recordSuccess($gatewayName)` method
    - Implement `recordFailure($gatewayName)` method
    - Implement `getSuccessRate($gatewayName, $hours)` method
    - _Requirements: 15.1, 15.2_
  
  - [x] 19.2 Integrate health tracking into gateways
    - Update VNPayGateway to record success/failure after API calls
    - Update MomoGateway to record success/failure after API calls
    - Track both payment initiation and callback processing
    - _Requirements: 15.1, 15.2_
  
  - [x] 19.3 Create admin health dashboard
    - Add `healthDashboard()` method in admin ThanhToanController
    - Create `app/views/admin/thanh_toan/health.php` view
    - Display success rate by gateway for last 24 hours
    - Display average processing time per gateway
    - Show last success and failure timestamps
    - Add alert indicator when failure rate exceeds 50%
    - _Requirements: 15.3, 15.4, 15.5_
  
  - [x] 19.4 Add customer-facing gateway status warnings
    - Check gateway health before displaying payment options
    - Display warning message if gateway failure rate is high
    - Suggest alternative payment methods when gateway is experiencing issues
    - _Requirements: 15.6_

- [x] 20. Final integration and wiring
  - [x] 20.1 Update PhuongThucThanhToan enum
    - Verify enum values match gateway implementations (COD, VI_DIEN_TU for VNPay/Momo)
    - Update getLabel() method to include VNPay and Momo specific labels
    - Add method to map enum values to gateway classes
    - _Requirements: 10.1, 10.2, 10.3, 10.4_
  
  - [x] 20.2 Update environment configuration
    - Verify all required environment variables are documented in .env.example
    - Add configuration validation on application startup
    - Log warnings for missing payment gateway configurations
    - _Requirements: 9.1, 9.2, 9.5, 9.6_
  
  - [x] 20.3 Update database schema
    - Run all database migrations for new tables and columns
    - Verify foreign key constraints are properly set
    - Add indexes for performance optimization
    - _Requirements: 8.5, 12.5, 15.1_
  
  - [ ]* 20.4 Write end-to-end integration tests
    - Test complete COD payment flow
    - Test complete VNPay payment flow with mock gateway
    - Test complete Momo payment flow with mock gateway
    - Test callback processing and order status updates
    - Test refund flow for each payment method
    - _Requirements: 1.1, 2.1, 3.1, 4.4, 12.2, 12.3_

- [ ] 21. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- All payment gateway credentials must be stored in environment variables, never in code
- Signature verification is critical for security and must be implemented correctly
- Transaction logging is essential for debugging and compliance
- Amount validation prevents payment fraud and errors
- Idempotency checks prevent duplicate payment processing
