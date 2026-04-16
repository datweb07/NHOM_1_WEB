# Manual Testing Guide: Refund and Revenue Fix

## Overview

This guide provides step-by-step instructions for manually testing the refund workflow and revenue calculation features. Follow each test case to ensure the system works correctly before deployment.

## Prerequisites

- Admin account with login credentials
- Access to admin dashboard at `/admin`
- Test payment records in various states
- VNPay sandbox credentials configured in `.env`
- Database access for verification queries

## Test Environment Setup

### 1. Create Test Data

Run these SQL queries to create test data:

```sql
-- Create test orders with different payment states
-- Test Case 1: Approved payment (VNPay) - should show refund button
INSERT INTO don_hang (nguoi_dung_id, tong_thanh_toan, trang_thai, ngay_tao) 
VALUES (1, 1500000, 'DANG_GIAO', NOW());

INSERT INTO thanh_toan (don_hang_id, so_tien, phuong_thuc, trang_thai_duyet, gateway_transaction_id, ngay_tao)
VALUES (LAST_INSERT_ID(), 1500000, 'VNPay', 'THANH_CONG', 'TEST_VNP_001', NOW());

-- Test Case 2: Pending approval payment - should disable refund button
INSERT INTO don_hang (nguoi_dung_id, tong_thanh_toan, trang_thai, ngay_tao) 
VALUES (1, 2000000, 'CHO_XAC_NHAN', NOW());

INSERT INTO thanh_toan (don_hang_id, so_tien, phuong_thuc, trang_thai_duyet, gateway_transaction_id, ngay_tao)
VALUES (LAST_INSERT_ID(), 2000000, 'VNPay', 'CHO_DUYET', 'TEST_VNP_002', NOW());

-- Test Case 3: COD payment - should hide refund button
INSERT INTO don_hang (nguoi_dung_id, tong_thanh_toan, trang_thai, ngay_tao) 
VALUES (1, 500000, 'DANG_GIAO', NOW());

INSERT INTO thanh_toan (don_hang_id, so_tien, phuong_thuc, trang_thai_duyet, ngay_tao)
VALUES (LAST_INSERT_ID(), 500000, 'COD', 'THANH_CONG', NOW());

-- Test Case 4: Already refunded payment - should hide refund button
INSERT INTO don_hang (nguoi_dung_id, tong_thanh_toan, trang_thai, ngay_tao) 
VALUES (1, 3000000, 'DANG_GIAO', NOW());

INSERT INTO thanh_toan (don_hang_id, so_tien, phuong_thuc, trang_thai_duyet, gateway_transaction_id, ngay_tao)
VALUES (LAST_INSERT_ID(), 3000000, 'VNPay', 'THANH_CONG', 'TEST_VNP_003', NOW());

INSERT INTO refund (thanh_toan_id, amount, status, reason, gateway_refund_id, created_at, completed_at, admin_id)
VALUES (LAST_INSERT_ID(), 3000000, 'COMPLETED', 'Test refund', 'REF_001', NOW(), NOW(), 1);
```

## Test Cases

### 11.1 Manual Testing of Refund Workflow

#### Test 11.1.1: Refund Button Visibility - Approved Payment

**Steps:**
1. Login to admin panel at `/admin/auth/login`
2. Navigate to Payments list at `/admin/thanh-toan`
3. Find payment with `trang_thai_duyet = 'THANH_CONG'` and `phuong_thuc = 'VNPay'`
4. Click "Chi tiết" to view payment detail page

**Expected Result:**
- ✅ Refund button is visible and enabled
- ✅ Button shows icon and text "Hoàn tiền"
- ✅ Button has warning color (yellow/orange)

**Verification Query:**
```sql
SELECT tt.id, tt.trang_thai_duyet, tt.phuong_thuc, 
       COUNT(r.id) as refund_count
FROM thanh_toan tt
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.id = [PAYMENT_ID]
GROUP BY tt.id;
```

---

#### Test 11.1.2: Refund Button Visibility - Pending Approval

**Steps:**
1. Navigate to payment with `trang_thai_duyet = 'CHO_DUYET'`
2. View payment detail page

**Expected Result:**
- ✅ Refund button is disabled (grayed out)
- ✅ Tooltip shows reason: "Chỉ có thể hoàn tiền cho thanh toán đã được duyệt"

---

#### Test 11.1.3: Refund Button Visibility - COD Payment

**Steps:**
1. Navigate to payment with `phuong_thuc = 'COD'`
2. View payment detail page

**Expected Result:**
- ✅ Refund button is hidden completely
- ✅ No refund-related UI elements visible

---

#### Test 11.1.4: Refund Button Visibility - Already Refunded

**Steps:**
1. Navigate to payment that has a COMPLETED refund record
2. View payment detail page

**Expected Result:**
- ✅ Refund button is hidden
- ✅ Refund history section shows the completed refund

---

#### Test 11.1.5: Refund Modal Display

**Steps:**
1. Navigate to approved VNPay payment
2. Click "Hoàn tiền" button

**Expected Result:**
- ✅ Modal opens with title "Xác nhận hoàn tiền"
- ✅ Warning alert displayed about refund action
- ✅ Amount field is readonly and pre-filled with payment amount
- ✅ Reason textarea is empty and required
- ✅ "Hủy" and "Xác nhận hoàn tiền" buttons visible

---

#### Test 11.1.6: Successful Refund with VNPay (Sandbox)

**Steps:**
1. Open refund modal for approved VNPay payment
2. Enter reason: "Khách hàng yêu cầu hoàn tiền do sản phẩm lỗi"
3. Click "Xác nhận hoàn tiền"
4. Wait for processing

**Expected Result:**
- ✅ Page redirects back to payment detail
- ✅ Success message displayed: "Hoàn tiền thành công. Mã giao dịch: [REFUND_ID]"
- ✅ Refund button is now hidden
- ✅ Refund history section shows new refund with status "Hoàn thành"

**Verification Query:**
```sql
SELECT * FROM refund 
WHERE thanh_toan_id = [PAYMENT_ID]
ORDER BY created_at DESC LIMIT 1;

SELECT * FROM transaction_log 
WHERE thanh_toan_id = [PAYMENT_ID] 
AND action_type LIKE '%REFUND%'
ORDER BY created_at DESC;
```

---

#### Test 11.1.7: Refund Error Handling - Missing Reason

**Steps:**
1. Open refund modal
2. Leave reason field empty
3. Click "Xác nhận hoàn tiền"

**Expected Result:**
- ✅ Browser validation prevents form submission
- ✅ Error message: "Please fill out this field"

---

#### Test 11.1.8: Refund Error Handling - Gateway Failure

**Steps:**
1. Temporarily misconfigure VNPay credentials in `.env`
2. Attempt to process refund

**Expected Result:**
- ✅ Error message displayed with gateway error details
- ✅ Refund record created with status "FAILED"
- ✅ Transaction log shows REFUND_FAILED entry

---

#### Test 11.1.9: Refund History Display

**Steps:**
1. Navigate to payment with multiple refund attempts
2. View refund history section

**Expected Result:**
- ✅ Table shows all refund records
- ✅ Columns: Amount, Status, Reason, Gateway Refund ID, Created Date, Completed Date
- ✅ Status badges: Green (COMPLETED), Yellow (PENDING), Red (FAILED)
- ✅ Dates formatted as dd/mm/yyyy HH:ii
- ✅ "N/A" shown for null values

---

### 11.2 Manual Testing of Revenue Calculation

#### Test 11.2.1: Create Test Data Mix

**Steps:**
Run this SQL to create diverse test data:

```sql
-- Approved payment (should be counted)
INSERT INTO don_hang (nguoi_dung_id, tong_thanh_toan, trang_thai, ngay_tao) 
VALUES (1, 5000000, 'HOAN_THANH', NOW());
INSERT INTO thanh_toan (don_hang_id, so_tien, phuong_thuc, trang_thai_duyet, ngay_tao)
VALUES (LAST_INSERT_ID(), 5000000, 'VNPay', 'THANH_CONG', NOW());

-- Unapproved payment (should NOT be counted)
INSERT INTO don_hang (nguoi_dung_id, tong_thanh_toan, trang_thai, ngay_tao) 
VALUES (1, 2000000, 'CHO_XAC_NHAN', NOW());
INSERT INTO thanh_toan (don_hang_id, so_tien, phuong_thuc, trang_thai_duyet, ngay_tao)
VALUES (LAST_INSERT_ID(), 2000000, 'VNPay', 'CHO_DUYET', NOW());

-- Cancelled order (should NOT be counted)
INSERT INTO don_hang (nguoi_dung_id, tong_thanh_toan, trang_thai, ngay_tao) 
VALUES (1, 1000000, 'DA_HUY', NOW());
INSERT INTO thanh_toan (don_hang_id, so_tien, phuong_thuc, trang_thai_duyet, ngay_tao)
VALUES (LAST_INSERT_ID(), 1000000, 'VNPay', 'THANH_CONG', NOW());

-- Refunded payment (should NOT be counted)
INSERT INTO don_hang (nguoi_dung_id, tong_thanh_toan, trang_thai, ngay_tao) 
VALUES (1, 3000000, 'HOAN_THANH', NOW());
INSERT INTO thanh_toan (don_hang_id, so_tien, phuong_thuc, trang_thai_duyet, ngay_tao)
VALUES (LAST_INSERT_ID(), 3000000, 'VNPay', 'THANH_CONG', NOW());
INSERT INTO refund (thanh_toan_id, amount, status, created_at, completed_at)
VALUES (LAST_INSERT_ID(), 3000000, 'COMPLETED', NOW(), NOW());
```

**Expected Revenue:** 5,000,000 VND (only the first order)

---

#### Test 11.2.2: Verify Dashboard Revenue

**Steps:**
1. Navigate to `/admin/dashboard`
2. Check revenue display

**Expected Result:**
- ✅ Total revenue shows only approved, non-refunded payments
- ✅ Monthly revenue excludes unapproved payments
- ✅ Revenue excludes cancelled/returned orders
- ✅ Revenue excludes refunded payments

**Verification Query:**
```sql
SELECT SUM(dh.tong_thanh_toan) as total_revenue
FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.trang_thai_duyet = 'THANH_CONG'
  AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
  AND r.id IS NULL;
```

---

#### Test 11.2.3: Revenue Updates After Approval Change

**Steps:**
1. Note current dashboard revenue
2. Find a pending payment: `UPDATE thanh_toan SET trang_thai_duyet = 'CHO_DUYET' WHERE id = X`
3. Refresh dashboard
4. Approve payment: `UPDATE thanh_toan SET trang_thai_duyet = 'THANH_CONG' WHERE id = X`
5. Refresh dashboard again

**Expected Result:**
- ✅ Revenue does NOT include payment when status is CHO_DUYET
- ✅ Revenue DOES include payment after status changes to THANH_CONG

---

#### Test 11.2.4: Revenue Updates After Refund

**Steps:**
1. Note current dashboard revenue
2. Process a refund for an approved payment
3. Refresh dashboard

**Expected Result:**
- ✅ Revenue decreases by the refunded amount
- ✅ Refunded payment no longer counted in revenue

---

### 11.3 Performance Testing

#### Test 11.3.1: Revenue Query Performance

**Steps:**
1. Create large dataset (10,000+ orders):
```sql
-- Run this in a loop or use a stored procedure
INSERT INTO don_hang (nguoi_dung_id, tong_thanh_toan, trang_thai, ngay_tao) 
SELECT 1, FLOOR(RAND() * 10000000), 
       CASE FLOOR(RAND() * 5)
           WHEN 0 THEN 'CHO_XAC_NHAN'
           WHEN 1 THEN 'DANG_GIAO'
           WHEN 2 THEN 'HOAN_THANH'
           WHEN 3 THEN 'DA_HUY'
           ELSE 'TRA_HANG'
       END,
       DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 365) DAY)
FROM (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) t1,
     (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) t2,
     (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) t3,
     (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) t4
LIMIT 10000;
```

2. Run EXPLAIN on revenue query:
```sql
EXPLAIN SELECT SUM(dh.tong_thanh_toan) as total_revenue
FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.trang_thai_duyet = 'THANH_CONG'
  AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
  AND r.id IS NULL;
```

**Expected Result:**
- ✅ Query execution time < 500ms
- ✅ EXPLAIN shows indexes are being used
- ✅ No full table scans on large tables

---

#### Test 11.3.2: Verify Indexes Usage

**Steps:**
1. Check if indexes exist:
```sql
SHOW INDEX FROM thanh_toan WHERE Key_name = 'idx_thanh_toan_duyet';
SHOW INDEX FROM refund WHERE Key_name = 'idx_refund_thanh_toan';
SHOW INDEX FROM don_hang WHERE Key_name = 'idx_don_hang_revenue';
```

**Expected Result:**
- ✅ All recommended indexes exist
- ✅ EXPLAIN shows "Using index" in Extra column

---

#### Test 11.3.3: Refund Gateway Response Time

**Steps:**
1. Process refund with VNPay sandbox
2. Measure time from button click to success message

**Expected Result:**
- ✅ Total response time < 5 seconds
- ✅ No timeout errors
- ✅ User sees loading indicator during processing

---

#### Test 11.3.4: Transaction Logging Performance

**Steps:**
1. Process multiple refunds in quick succession
2. Check transaction_log table

**Expected Result:**
- ✅ All refund attempts logged
- ✅ Logging doesn't block refund process
- ✅ No duplicate log entries

---

## Test Results Summary

Use this checklist to track test completion:

### Refund Workflow Tests
- [ ] 11.1.1: Refund button visible for approved payments
- [ ] 11.1.2: Refund button disabled for pending payments
- [ ] 11.1.3: Refund button hidden for COD payments
- [ ] 11.1.4: Refund button hidden for refunded payments
- [ ] 11.1.5: Refund modal displays correctly
- [ ] 11.1.6: Successful VNPay refund works
- [ ] 11.1.7: Missing reason validation works
- [ ] 11.1.8: Gateway failure handled correctly
- [ ] 11.1.9: Refund history displays correctly

### Revenue Calculation Tests
- [ ] 11.2.1: Test data created successfully
- [ ] 11.2.2: Dashboard shows correct revenue
- [ ] 11.2.3: Revenue updates after approval change
- [ ] 11.2.4: Revenue updates after refund

### Performance Tests
- [ ] 11.3.1: Revenue query performs well
- [ ] 11.3.2: Indexes are being used
- [ ] 11.3.3: Gateway response time acceptable
- [ ] 11.3.4: Transaction logging doesn't block

## Troubleshooting

### Issue: Refund button not showing
**Solution:** Check payment status and method. Verify no completed refunds exist.

### Issue: Gateway error during refund
**Solution:** Verify VNPay credentials in `.env`. Check sandbox mode is enabled.

### Issue: Revenue calculation incorrect
**Solution:** Run verification queries. Check for missing indexes.

### Issue: Slow dashboard loading
**Solution:** Run EXPLAIN on queries. Verify indexes exist. Consider adding more indexes.

## Notes

- Skip Momo and ZaloPay testing as per user request
- Focus on VNPay and COD payment methods
- All tests should be performed in a staging environment first
- Document any issues found during testing
