# Performance Testing Guide

## Overview

This guide provides instructions for testing the performance of the refund and revenue calculation features to ensure they meet production requirements.

## Performance Requirements

### Response Time Targets

| Operation | Target | Maximum Acceptable |
|-----------|--------|-------------------|
| Payment detail page load | < 1s | < 2s |
| Refund modal open | < 200ms | < 500ms |
| Refund processing | < 5s | < 10s |
| Dashboard load | < 2s | < 5s |
| Revenue calculation | < 500ms | < 1s |

### Throughput Targets

| Metric | Target |
|--------|--------|
| Concurrent refund requests | 10 requests/second |
| Dashboard page views | 100 requests/second |
| Database connections | < 50 concurrent |

## Test Environment Setup

### 1. Create Large Dataset

Generate test data to simulate production load:

```sql
-- Create 100,000 test orders
DELIMITER $$
CREATE PROCEDURE generate_test_orders(IN num_orders INT)
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE order_id INT;
    
    WHILE i < num_orders DO
        -- Insert order
        INSERT INTO don_hang (
            nguoi_dung_id, 
            tong_thanh_toan, 
            trang_thai, 
            ngay_tao
        ) VALUES (
            FLOOR(1 + RAND() * 1000),
            FLOOR(100000 + RAND() * 10000000),
            CASE FLOOR(RAND() * 5)
                WHEN 0 THEN 'CHO_XAC_NHAN'
                WHEN 1 THEN 'DANG_GIAO'
                WHEN 2 THEN 'HOAN_THANH'
                WHEN 3 THEN 'DA_HUY'
                ELSE 'TRA_HANG'
            END,
            DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 365) DAY)
        );
        
        SET order_id = LAST_INSERT_ID();
        
        -- Insert payment (80% of orders have payments)
        IF RAND() < 0.8 THEN
            INSERT INTO thanh_toan (
                don_hang_id,
                so_tien,
                phuong_thuc,
                trang_thai_duyet,
                gateway_transaction_id,
                ngay_tao
            ) VALUES (
                order_id,
                (SELECT tong_thanh_toan FROM don_hang WHERE id = order_id),
                CASE FLOOR(RAND() * 4)
                    WHEN 0 THEN 'VNPay'
                    WHEN 1 THEN 'Momo'
                    WHEN 2 THEN 'ZaloPay'
                    ELSE 'COD'
                END,
                CASE FLOOR(RAND() * 3)
                    WHEN 0 THEN 'CHO_DUYET'
                    WHEN 1 THEN 'THANH_CONG'
                    ELSE 'THAT_BAI'
                END,
                CONCAT('TEST_', UUID()),
                DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 365) DAY)
            );
            
            -- Insert refund (5% of payments are refunded)
            IF RAND() < 0.05 THEN
                INSERT INTO refund (
                    thanh_toan_id,
                    amount,
                    status,
                    reason,
                    gateway_refund_id,
                    created_at,
                    completed_at,
                    admin_id
                ) VALUES (
                    LAST_INSERT_ID(),
                    (SELECT so_tien FROM thanh_toan WHERE id = LAST_INSERT_ID()),
                    'COMPLETED',
                    'Test refund',
                    CONCAT('REF_', UUID()),
                    DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 365) DAY),
                    DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 365) DAY),
                    1
                );
            END IF;
        END IF;
        
        SET i = i + 1;
        
        -- Progress indicator
        IF i % 1000 = 0 THEN
            SELECT CONCAT('Generated ', i, ' orders') AS progress;
        END IF;
    END WHILE;
END$$
DELIMITER ;

-- Execute procedure
CALL generate_test_orders(100000);

-- Verify data
SELECT 
    COUNT(*) as total_orders,
    (SELECT COUNT(*) FROM thanh_toan) as total_payments,
    (SELECT COUNT(*) FROM refund) as total_refunds
FROM don_hang;
```

### 2. Verify Indexes

```sql
-- Check if performance indexes exist
SHOW INDEX FROM thanh_toan WHERE Key_name = 'idx_thanh_toan_duyet';
SHOW INDEX FROM refund WHERE Key_name = 'idx_refund_thanh_toan';
SHOW INDEX FROM don_hang WHERE Key_name = 'idx_don_hang_revenue';

-- If missing, create them
CREATE INDEX idx_thanh_toan_duyet ON thanh_toan(trang_thai_duyet);
CREATE INDEX idx_refund_thanh_toan ON refund(thanh_toan_id, status);
CREATE INDEX idx_don_hang_revenue ON don_hang(trang_thai, ngay_tao);
```

## Performance Tests

### Test 1: Revenue Query Performance

**Objective:** Verify revenue calculation completes within 500ms

**Test Query:**
```sql
-- Enable query profiling
SET profiling = 1;

-- Execute revenue query
SELECT SUM(dh.tong_thanh_toan) as total_revenue
FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.trang_thai_duyet = 'THANH_CONG'
  AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
  AND r.id IS NULL;

-- Check execution time
SHOW PROFILES;

-- Get detailed profile
SHOW PROFILE FOR QUERY 1;
```

**Expected Results:**
- ✅ Query execution time < 500ms
- ✅ No full table scans
- ✅ Indexes are being used

**Verification:**
```sql
-- Analyze query execution plan
EXPLAIN SELECT SUM(dh.tong_thanh_toan) as total_revenue
FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.trang_thai_duyet = 'THANH_CONG'
  AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
  AND r.id IS NULL;
```

**Look for:**
- `type` column should show "ref" or "index" (not "ALL")
- `key` column should show index names
- `Extra` column should show "Using index" or "Using where"

---

### Test 2: Monthly Revenue Query Performance

**Objective:** Verify monthly revenue calculation with date filtering

**Test Query:**
```sql
SET profiling = 1;

SELECT SUM(dh.tong_thanh_toan) as monthly_revenue
FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.trang_thai_duyet = 'THANH_CONG'
  AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
  AND r.id IS NULL
  AND MONTH(dh.ngay_tao) = MONTH(CURRENT_DATE())
  AND YEAR(dh.ngay_tao) = YEAR(CURRENT_DATE());

SHOW PROFILES;
```

**Expected Results:**
- ✅ Query execution time < 500ms
- ✅ Date filtering uses index

---

### Test 3: Refund Lookup Performance

**Objective:** Verify refund status check is fast

**Test Query:**
```sql
SET profiling = 1;

-- Check if payment has completed refund
SELECT COUNT(*) as has_refund
FROM refund
WHERE thanh_toan_id = 12345
  AND status = 'COMPLETED';

SHOW PROFILES;
```

**Expected Results:**
- ✅ Query execution time < 50ms
- ✅ Uses idx_refund_thanh_toan index

---

### Test 4: Payment Detail Page Load Time

**Objective:** Measure full page load time including all queries

**Test Steps:**
1. Open browser developer tools (F12)
2. Navigate to Network tab
3. Clear cache
4. Load payment detail page: `/admin/thanh-toan/chi-tiet?id=12345`
5. Check timing in Network tab

**Queries Executed:**
```sql
-- Payment data
SELECT * FROM thanh_toan WHERE id = 12345;

-- Order data
SELECT * FROM don_hang WHERE id = (SELECT don_hang_id FROM thanh_toan WHERE id = 12345);

-- Refund history
SELECT * FROM refund WHERE thanh_toan_id = 12345 ORDER BY created_at DESC;

-- Check if can refund
SELECT COUNT(*) FROM refund WHERE thanh_toan_id = 12345 AND status = 'COMPLETED';
```

**Expected Results:**
- ✅ Total page load time < 1 second
- ✅ All queries complete in < 100ms each
- ✅ No N+1 query problems

---

### Test 5: Dashboard Load Performance

**Objective:** Measure dashboard page load with all metrics

**Test Steps:**
1. Clear application cache
2. Open browser developer tools
3. Load dashboard: `/admin/dashboard`
4. Measure total load time

**Queries Executed:**
```sql
-- Total revenue
SELECT SUM(dh.tong_thanh_toan) FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.trang_thai_duyet = 'THANH_CONG'
  AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
  AND r.id IS NULL;

-- Monthly revenue
SELECT SUM(dh.tong_thanh_toan) FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.trang_thai_duyet = 'THANH_CONG'
  AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
  AND r.id IS NULL
  AND MONTH(dh.ngay_tao) = MONTH(CURRENT_DATE())
  AND YEAR(dh.ngay_tao) = YEAR(CURRENT_DATE());

-- Other dashboard metrics...
```

**Expected Results:**
- ✅ Total page load time < 2 seconds
- ✅ Revenue queries complete in < 500ms each
- ✅ No timeout errors

---

### Test 6: Concurrent Refund Processing

**Objective:** Test system under concurrent refund load

**Test Script (PHP):**
```php
<?php
// concurrent_refund_test.php

$numRequests = 10;
$paymentIds = [123, 456, 789, 101, 112, 131, 415, 161, 718, 192]; // Test payment IDs

$multiHandle = curl_multi_init();
$curlHandles = [];

// Initialize all requests
foreach ($paymentIds as $index => $paymentId) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/admin/thanh-toan/refund?id=$paymentId");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'amount' => 1000000,
        'reason' => "Concurrent test refund $index"
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, "admin_session=your_session_cookie");
    
    curl_multi_add_handle($multiHandle, $ch);
    $curlHandles[] = $ch;
}

// Execute all requests concurrently
$startTime = microtime(true);
$running = null;
do {
    curl_multi_exec($multiHandle, $running);
    curl_multi_select($multiHandle);
} while ($running > 0);
$endTime = microtime(true);

// Collect results
$results = [];
foreach ($curlHandles as $ch) {
    $results[] = [
        'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        'total_time' => curl_getinfo($ch, CURLINFO_TOTAL_TIME),
        'response' => curl_multi_getcontent($ch)
    ];
    curl_multi_remove_handle($multiHandle, $ch);
    curl_close($ch);
}
curl_multi_close($multiHandle);

// Analyze results
$totalTime = $endTime - $startTime;
$avgTime = array_sum(array_column($results, 'total_time')) / count($results);
$maxTime = max(array_column($results, 'total_time'));
$successCount = count(array_filter($results, fn($r) => $r['http_code'] == 302));

echo "Concurrent Refund Test Results:\n";
echo "Total requests: " . count($results) . "\n";
echo "Successful: $successCount\n";
echo "Total time: " . round($totalTime, 2) . "s\n";
echo "Average time per request: " . round($avgTime, 2) . "s\n";
echo "Max time: " . round($maxTime, 2) . "s\n";
```

**Expected Results:**
- ✅ All requests complete successfully
- ✅ Average response time < 5 seconds
- ✅ No database deadlocks
- ✅ No timeout errors

---

### Test 7: Gateway Response Time

**Objective:** Measure payment gateway API response time

**Test Script:**
```php
<?php
// gateway_performance_test.php

require_once 'app/services/payment/VNPayGateway.php';

$gateway = new VNPayGateway();
$iterations = 10;
$times = [];

for ($i = 0; $i < $iterations; $i++) {
    $startTime = microtime(true);
    
    $result = $gateway->initiateRefund(
        "TEST_TXN_" . uniqid(),
        1000000,
        "Performance test refund"
    );
    
    $endTime = microtime(true);
    $times[] = $endTime - $startTime;
    
    echo "Iteration " . ($i + 1) . ": " . round($times[$i], 2) . "s\n";
    sleep(1); // Avoid rate limiting
}

$avgTime = array_sum($times) / count($times);
$maxTime = max($times);
$minTime = min($times);

echo "\nGateway Performance Results:\n";
echo "Average response time: " . round($avgTime, 2) . "s\n";
echo "Min response time: " . round($minTime, 2) . "s\n";
echo "Max response time: " . round($maxTime, 2) . "s\n";
```

**Expected Results:**
- ✅ Average response time < 3 seconds
- ✅ Max response time < 5 seconds
- ✅ No timeout errors

---

### Test 8: Transaction Logging Performance

**Objective:** Verify logging doesn't block refund process

**Test Query:**
```sql
-- Measure insert performance
SET profiling = 1;

INSERT INTO transaction_log (
    thanh_toan_id,
    action_type,
    request_data,
    response_data,
    created_at
) VALUES (
    12345,
    'REFUND_COMPLETED',
    '{"amount": 1000000, "reason": "Test"}',
    '{"success": true, "refund_id": "REF_001"}',
    NOW()
);

SHOW PROFILES;
```

**Expected Results:**
- ✅ Insert time < 10ms
- ✅ No table locks
- ✅ Doesn't block other operations

---

### Test 9: Database Connection Pool

**Objective:** Verify connection pool handles load efficiently

**Test Steps:**
1. Monitor database connections during load test
2. Check for connection leaks
3. Verify connection reuse

**Monitoring Query:**
```sql
-- Check active connections
SHOW PROCESSLIST;

-- Check connection statistics
SHOW STATUS LIKE 'Threads_connected';
SHOW STATUS LIKE 'Threads_running';
SHOW STATUS LIKE 'Max_used_connections';

-- Check for long-running queries
SELECT * FROM information_schema.processlist
WHERE time > 5
ORDER BY time DESC;
```

**Expected Results:**
- ✅ Connections < 50 concurrent
- ✅ No connection leaks
- ✅ No long-running queries (> 5s)

---

### Test 10: Memory Usage

**Objective:** Verify application memory usage is acceptable

**Test Steps:**
1. Monitor PHP memory usage during operations
2. Check for memory leaks

**Monitoring Code:**
```php
<?php
// Add to refund processing code
$memoryBefore = memory_get_usage();

// Process refund
$result = $refundService->initiateRefund($thanhToanId, $amount, $reason, $adminId);

$memoryAfter = memory_get_usage();
$memoryUsed = $memoryAfter - $memoryBefore;

error_log("Refund memory usage: " . round($memoryUsed / 1024 / 1024, 2) . " MB");
```

**Expected Results:**
- ✅ Memory usage per refund < 10 MB
- ✅ No memory leaks
- ✅ Memory released after operation

---

## Load Testing

### Apache Bench (ab) Test

```bash
# Test dashboard endpoint
ab -n 1000 -c 10 -C "admin_session=your_session_cookie" \
   http://localhost/admin/dashboard

# Test payment detail endpoint
ab -n 1000 -c 10 -C "admin_session=your_session_cookie" \
   http://localhost/admin/thanh-toan/chi-tiet?id=12345
```

**Expected Results:**
- ✅ Requests per second > 50
- ✅ 99th percentile response time < 2s
- ✅ No failed requests

### JMeter Test Plan

Create a JMeter test plan with:
1. Thread Group: 50 concurrent users
2. HTTP Request: Dashboard page
3. HTTP Request: Payment detail page
4. HTTP Request: Refund processing
5. Assertions: Response time < 2s
6. Listeners: View results tree, aggregate report

**Expected Results:**
- ✅ Throughput > 100 requests/second
- ✅ Error rate < 1%
- ✅ Average response time < 1s

---

## Performance Optimization Tips

### Database Optimization

1. **Add more indexes if needed:**
```sql
-- Index for date range queries
CREATE INDEX idx_don_hang_date ON don_hang(ngay_tao);

-- Index for payment method filtering
CREATE INDEX idx_thanh_toan_method ON thanh_toan(phuong_thuc);
```

2. **Optimize queries:**
```sql
-- Use STRAIGHT_JOIN if optimizer chooses wrong join order
SELECT STRAIGHT_JOIN SUM(dh.tong_thanh_toan)
FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
...
```

3. **Analyze and optimize tables:**
```sql
ANALYZE TABLE don_hang;
ANALYZE TABLE thanh_toan;
ANALYZE TABLE refund;

OPTIMIZE TABLE transaction_log;
```

### Application Optimization

1. **Enable query caching:**
```php
// Cache revenue calculation for 5 minutes
$revenue = Cache::remember('dashboard_revenue', 300, function() {
    return $this->calculateTotalRevenue();
});
```

2. **Use connection pooling:**
```php
// Configure PDO with persistent connections
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_PERSISTENT => true
]);
```

3. **Optimize gateway calls:**
```php
// Set reasonable timeout
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
```

### Server Optimization

1. **Enable OPcache:**
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

2. **Tune MySQL:**
```ini
; my.cnf
innodb_buffer_pool_size=1G
query_cache_size=64M
max_connections=200
```

3. **Use Redis for sessions:**
```php
// Configure Redis session handler
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://127.0.0.1:6379');
```

---

## Performance Test Results Template

| Test | Target | Actual | Status | Notes |
|------|--------|--------|--------|-------|
| Revenue query | < 500ms | ___ ms | ⬜ | |
| Monthly revenue query | < 500ms | ___ ms | ⬜ | |
| Refund lookup | < 50ms | ___ ms | ⬜ | |
| Payment detail load | < 1s | ___ s | ⬜ | |
| Dashboard load | < 2s | ___ s | ⬜ | |
| Concurrent refunds | < 5s avg | ___ s | ⬜ | |
| Gateway response | < 3s avg | ___ s | ⬜ | |
| Transaction logging | < 10ms | ___ ms | ⬜ | |
| DB connections | < 50 | ___ | ⬜ | |
| Memory per refund | < 10 MB | ___ MB | ⬜ | |

**Overall Performance Rating:** ⬜ Pass / ⬜ Fail

**Issues Found:**
- 

**Recommendations:**
- 

**Tested By:** _________________ Date: _________
