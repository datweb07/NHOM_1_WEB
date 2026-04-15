# Implementation Plan

## Overview

This implementation plan follows the exploratory bugfix workflow using the bug condition methodology. The tasks are ordered to:
1. **Explore** - Write tests BEFORE fix to understand the bug (Bug Condition)
2. **Preserve** - Write tests for non-buggy behavior (Preservation Requirements)
3. **Implement** - Apply the fix with understanding (Expected Behavior)
4. **Validate** - Verify fix works and doesn't break anything

## Tasks

- [x] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - Payment Cancellation Not Handled
  - **CRITICAL**: This test MUST FAIL on unfixed code - failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **NOTE**: This test encodes the expected behavior - it will validate the fix when it passes after implementation
  - **GOAL**: Surface counterexamples that demonstrate the bug exists
  - **Scoped PBT Approach**: For deterministic bugs, scope the property to the concrete failing case(s) to ensure reproducibility
  - Test implementation details from Bug Condition in design:
    - VNPay callback with `vnp_ResponseCode = '24'` (user canceled) and valid signature
    - Momo callback with `resultCode = '1006'` (user declined) and valid signature
    - VNPay callback with `vnp_ResponseCode = '51'` (insufficient funds) and valid signature
    - Momo callback with `resultCode = '1001'` (insufficient funds) and valid signature
  - The test assertions should match the Expected Behavior Properties from design:
    - Payment status should be "THẤT BẠI" (Failed)
    - Order status should be "ĐÃ HỦY" (Canceled)
    - Product inventory should be restored to pre-order quantities
    - Gateway health metrics should record "FAILURE"
  - Run test on UNFIXED code
  - **EXPECTED OUTCOME**: Test FAILS (this is correct - it proves the bug exists)
  - Document counterexamples found to understand root cause:
    - Which response codes fail to update statuses correctly?
    - Are statuses remaining "CHỜ DUYỆT" (Pending)?
    - Is inventory not being restored?
    - Are gateway health metrics incorrectly showing "SUCCESS"?
  - Mark task complete when test is written, run, and failure is documented
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.7, 1.8_

- [x] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Successful Payment Handling Unchanged
  - **IMPORTANT**: Follow observation-first methodology
  - Observe behavior on UNFIXED code for non-buggy inputs (successful payments):
    - VNPay callback with `vnp_ResponseCode = '00'` (success) and valid signature
    - Momo callback with `resultCode = '0'` (success) and valid signature
  - Observe and record the actual outputs:
    - Payment status updates to "THANH_CONG" (Success)
    - Order status updates to "DA_XAC_NHAN" (Confirmed)
    - Inventory remains sold (not restored)
    - Gateway health records "SUCCESS"
  - Write property-based tests capturing observed behavior patterns from Preservation Requirements:
    - For all VNPay callbacks with response code '00', verify success handling
    - For all Momo callbacks with result code '0', verify success handling
    - For all callbacks with invalid signatures, verify rejection and security logging
    - For all expired transactions, verify timeout handling
  - Property-based testing generates many test cases for stronger guarantees
  - Run tests on UNFIXED code
  - **EXPECTED OUTCOME**: Tests PASS (this confirms baseline behavior to preserve)
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9, 3.10, 3.11, 3.12, 3.13_

- [x] 3. Fix for payment cancellation status handling

  - [x] 3.1 Implement the fix in CallbackHandler.php
    - Modify `handleVNPayCallback()` method to ensure all non-'00' response codes are handled as failures
    - Modify `handleMomoCallback()` method to ensure all non-'0' result codes are handled as failures
    - Add explicit logging for cancellation and failure scenarios
    - Ensure `handleFailedPayment()` is called for all non-success response codes
    - Verify that `handleFailedPayment()` correctly:
      - Updates payment status to "THẤT BẠI" via `PaymentService::updateTransactionStatus()`
      - Updates order status to "ĐÃ HỦY" via `DonHang::update()`
      - Restores inventory via `restoreInventory()` method
      - Logs the failure correctly via `TransactionLog::logResponse()`
    - Add defensive checks to ensure database updates are committed
    - _Bug_Condition: isBugCondition(input) where input.gateway == 'VNPAY' AND input.vnp_ResponseCode != '00' AND input.signature_valid == true OR input.gateway == 'MOMO' AND input.resultCode != '0' AND input.signature_valid == true_
    - _Expected_Behavior: expectedBehavior(result) where payment_status == "THẤT BẠI" AND order_status == "ĐÃ HỦY" AND inventory_restored == true AND gateway_health == "FAILURE"_
    - _Preservation: Successful payment handling (VNPay '00', Momo '0') must continue to work exactly as before. Signature validation, transaction logging, security violation handling, timeout/expiration handling, amount validation, and duplicate transaction detection must remain unchanged._
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.7, 2.8, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9, 3.10, 3.11, 3.12, 3.13_

  - [x] 3.2 Verify gateway health metrics recording
    - Review `VNPayGateway::verifyCallback()` method to confirm health metrics are recorded based on response code
    - Review `MomoGateway::verifyCallback()` method to confirm health metrics are recorded based on result code
    - Ensure `recordHealthFailure()` is called for all non-success response/result codes
    - Ensure `recordHealthSuccess()` is called only for success response/result codes ('00' for VNPay, '0' for Momo)
    - Add logging to track health metric recording for debugging
    - _Requirements: 2.3, 2.7, 2.8, 3.3, 3.9_

  - [x] 3.3 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - Payment Cancellation Handled Correctly
    - **IMPORTANT**: Re-run the SAME test from task 1 - do NOT write a new test
    - The test from task 1 encodes the expected behavior
    - When this test passes, it confirms the expected behavior is satisfied
    - Run bug condition exploration test from step 1
    - **EXPECTED OUTCOME**: Test PASSES (confirms bug is fixed)
    - Verify all assertions pass:
      - Payment status is "THẤT BẠI" for all non-success response codes
      - Order status is "ĐÃ HỦY" for all non-success response codes
      - Inventory is restored for all non-success response codes
      - Gateway health records "FAILURE" for all non-success response codes
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.7, 2.8_

  - [x] 3.4 Verify preservation tests still pass
    - **Property 2: Preservation** - Successful Payment Handling Unchanged
    - **IMPORTANT**: Re-run the SAME tests from task 2 - do NOT write new tests
    - Run preservation property tests from step 2
    - **EXPECTED OUTCOME**: Tests PASS (confirms no regressions)
    - Confirm all tests still pass after fix:
      - Successful payment handling (VNPay '00', Momo '0') works exactly as before
      - Signature validation works exactly as before
      - Transaction logging works exactly as before
      - Security violation handling works exactly as before
      - Timeout/expiration handling works exactly as before
      - Amount validation works exactly as before
      - Duplicate transaction detection works exactly as before
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9, 3.10, 3.11, 3.12, 3.13_

- [x] 4. Integration testing

  - [x] 4.1 Test VNPay cancellation flow end-to-end
    - Create test order with product inventory tracking
    - Initiate VNPay payment
    - Simulate VNPay callback with response code '24' (user canceled)
    - Verify payment status is "THẤT BẠI"
    - Verify order status is "ĐÃ HỦY"
    - Verify product inventory is restored to original quantity
    - Verify gateway health dashboard shows "FAILURE" for this transaction
    - Verify admin order list shows order as "ĐÃ HỦY"
    - Verify client order history shows order as "ĐÃ HỦY"
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

  - [x] 4.2 Test Momo cancellation flow end-to-end
    - Create test order with product inventory tracking
    - Initiate Momo payment
    - Simulate Momo callback with result code '1006' (user declined)
    - Verify payment status is "THẤT BẠI"
    - Verify order status is "ĐÃ HỦY"
    - Verify product inventory is restored to original quantity
    - Verify gateway health dashboard shows "FAILURE" for this transaction
    - Verify admin order list shows order as "ĐÃ HỦY"
    - Verify client order history shows order as "ĐÃ HỦY"
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.8_

  - [x] 4.3 Test VNPay success flow preservation
    - Create test order with product inventory tracking
    - Initiate VNPay payment
    - Simulate VNPay callback with response code '00' (success)
    - Verify payment status is "THANH_CONG"
    - Verify order status is "DA_XAC_NHAN"
    - Verify product inventory remains sold (not restored)
    - Verify gateway health dashboard shows "SUCCESS" for this transaction
    - Verify admin order list shows order as "DA_XAC_NHAN"
    - Verify client order history shows order as "DA_XAC_NHAN"
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [x] 4.4 Test Momo success flow preservation
    - Create test order with product inventory tracking
    - Initiate Momo payment
    - Simulate Momo callback with result code '0' (success)
    - Verify payment status is "THANH_CONG"
    - Verify order status is "DA_XAC_NHAN"
    - Verify product inventory remains sold (not restored)
    - Verify gateway health dashboard shows "SUCCESS" for this transaction
    - Verify admin order list shows order as "DA_XAC_NHAN"
    - Verify client order history shows order as "DA_XAC_NHAN"
    - _Requirements: 3.7, 3.8, 3.9_

  - [x] 4.5 Test gateway health dashboard accuracy
    - Simulate multiple payment callbacks with various response codes
    - Include successful payments (VNPay '00', Momo '0')
    - Include canceled payments (VNPay '24', Momo '1006')
    - Include failed payments (VNPay '51', Momo '1001')
    - Verify gateway health dashboard correctly shows:
      - Success count matches number of '00'/'0' response codes
      - Failure count matches number of non-'00'/'0' response codes
      - Success rate is calculated correctly
      - Failure rate is calculated correctly
    - _Requirements: 2.3, 2.7, 2.8, 3.3, 3.9_

- [x] 5. Documentation updates

  - [x] 5.1 Update payment callback handling documentation
    - Document the fix for payment cancellation status handling
    - Explain the bug condition and expected behavior
    - Document the response codes that trigger failure handling:
      - VNPay: All codes except '00' are treated as failures
      - Momo: All codes except '0' are treated as failures
    - Document the status updates for failed/canceled payments:
      - Payment status: "THẤT BẠI"
      - Order status: "ĐÃ HỦY"
      - Inventory: Restored to pre-order quantities
      - Gateway health: Recorded as "FAILURE"
    - Add examples of common cancellation scenarios

  - [x] 5.2 Update admin documentation
    - Document how to identify canceled orders in the order list
    - Explain the difference between "CHỜ DUYỆT" (Pending) and "ĐÃ HỦY" (Canceled) statuses
    - Document how to view gateway health metrics
    - Explain how to interpret success/failure rates in the dashboard

  - [x] 5.3 Update client-facing documentation
    - Document what happens when a payment is canceled
    - Explain that canceled orders will show as "ĐÃ HỦY" in order history
    - Clarify that inventory is automatically restored for canceled orders
    - Provide guidance on how to retry a canceled order

- [x] 6. Checkpoint - Ensure all tests pass
  - Run all unit tests and verify they pass
  - Run all property-based tests and verify they pass
  - Run all integration tests and verify they pass
  - Verify no regressions in existing functionality
  - Verify gateway health dashboard shows accurate metrics
  - Verify admin can distinguish canceled orders from pending orders
  - Verify client sees correct order status after cancellation
  - Ask the user if questions arise

## Notes

- **Bug Condition Methodology**: This bugfix uses the bug condition methodology where:
  - **C(X)**: Bug Condition - identifies inputs that trigger the bug (non-success response codes)
  - **P(result)**: Property - desired behavior for buggy inputs (status updates, inventory restoration, health metrics)
  - **¬C(X)**: Non-buggy inputs that should be preserved (success response codes)
  - **F**: Original (unfixed) function
  - **F'**: Fixed function

- **Testing Strategy**: The testing follows a two-phase approach:
  1. **Exploratory Bug Condition Checking**: Surface counterexamples on unfixed code to confirm the bug exists
  2. **Fix Checking**: Verify the fix works for all bug conditions
  3. **Preservation Checking**: Verify existing behavior is unchanged for non-bug conditions

- **Property-Based Testing**: Property-based testing is used for stronger guarantees:
  - Generates many test cases automatically
  - Catches edge cases that manual tests might miss
  - Provides confidence that behavior is correct across the input domain
