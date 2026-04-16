# Design Document: Admin Notification System

## Overview

The Admin Notification System provides real-time awareness of critical events across the e-commerce platform through AJAX polling. The system monitors four key categories: Orders & Payments, Inventory & Products, Customer Interactions, and System Alerts & Promotions. It uses a lightweight polling mechanism (30-60 second intervals) to fetch notifications from a backend API, which returns JSON data to update the UI dropdown and badge counter.

### Key Design Decisions

1. **AJAX Polling over WebSockets**: Chosen for simplicity, compatibility with existing PHP infrastructure, and reduced server resource requirements. Polling interval of 30-60 seconds provides near-real-time updates without excessive server load.

2. **Redis for State Management**: Using Redis to cache Last_Check_Timestamp per admin session enables efficient delta queries, reducing database load by only fetching new records since the last poll.

3. **Stateless API Design**: The notification API is stateless, relying on Redis for session state. This allows horizontal scaling and simplifies deployment.

4. **Priority-Based Grouping**: Notifications are grouped by category and sorted by priority (high/medium/low) to ensure critical items are visible first.

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                     Admin Dashboard UI                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Badge Counter│  │   Dropdown   │  │  Poller JS   │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ AJAX Poll (30-60s)
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                   Backend API Layer                          │
│  ┌──────────────────────────────────────────────────────┐  │
│  │         NotificationController                        │  │
│  │  - /admin/api/notifications (GET)                    │  │
│  └──────────────────────────────────────────────────────┘  │
│                            │                                 │
│                            ▼                                 │
│  ┌──────────────────────────────────────────────────────┐  │
│  │         NotificationService                           │  │
│  │  - aggregateNotifications()                          │  │
│  │  - getOrderNotifications()                           │  │
│  │  - getInventoryNotifications()                       │  │
│  │  - getCustomerNotifications()                        │  │
│  │  - getSystemNotifications()                          │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                ┌───────────┴───────────┐
                ▼                       ▼
┌──────────────────────┐    ┌──────────────────────┐
│   Redis Cache        │    │   MySQL Database     │
│                      │    │                      │
│ Key Pattern:         │    │ Tables:              │
│ notification:        │    │ - don_hang           │
│   last_check:        │    │ - thanh_toan         │
│   {admin_id}         │    │ - refund             │
│                      │    │ - phien_ban_san_pham │
│ TTL: 2 hours         │    │ - danh_gia           │
│                      │    │ - transaction_log    │
│                      │    │ - gateway_health     │
│                      │    │ - ma_giam_gia        │
└──────────────────────┘    └──────────────────────┘
```

### Data Flow

1. **Initialization**: When admin logs in, Last_Check_Timestamp is set to current time minus 24 hours
2. **Polling Cycle**:
   - Frontend JavaScript calls `/admin/api/notifications` every 30-60 seconds
   - Backend retrieves Last_Check_Timestamp from Redis using admin session ID
   - Service layer queries database tables for records created/updated since Last_Check_Timestamp
   - Results are aggregated, grouped by category, and sorted by priority
   - Last_Check_Timestamp is updated in Redis to current server time
   - JSON response is returned to frontend
3. **UI Update**: Frontend updates badge counter and dropdown with new notifications
4. **User Interaction**: Clicking a notification navigates to the relevant detail page

## Components and Interfaces

### Backend Components

#### 1. NotificationController

**Location**: `app/controllers/admin/NotificationController.php`

**Responsibilities**:
- Handle HTTP requests to `/admin/api/notifications`
- Validate admin authentication
- Coordinate with NotificationService
- Return JSON responses

**Methods**:

```php
class NotificationController
{
    /**
     * GET /admin/api/notifications
     * Returns aggregated notifications for the current admin user
     * 
     * @return void (outputs JSON)
     */
    public function index(): void
    {
        // 1. Verify admin authentication
        // 2. Get admin user ID from session
        // 3. Call NotificationService->aggregateNotifications($adminId)
        // 4. Return JSON response
    }
}
```

**Response Format**:

```json
{
  "success": true,
  "total_notifications": 15,
  "items": [
    {
      "group": "orders",
      "type": "new_order_pending",
      "count": 3,
      "message": "3 đơn hàng mới chờ duyệt",
      "url_redirect": "/admin/don-hang?trang_thai=CHO_DUYET",
      "priority": "high",
      "icon": "bi-cart-check"
    },
    {
      "group": "inventory",
      "type": "low_stock_warning",
      "count": 5,
      "message": "5 sản phẩm sắp hết hàng",
      "url_redirect": "/admin/san-pham?filter=low_stock",
      "priority": "medium",
      "icon": "bi-box-seam"
    }
  ],
  "last_check": "2024-01-15 10:30:45"
}
```

**Error Response Format**:

```json
{
  "success": false,
  "error": "Authentication required",
  "code": 401
}
```

#### 2. NotificationService

**Location**: `app/services/notification/NotificationService.php`

**Responsibilities**:
- Aggregate notifications from multiple sources
- Query database tables for new records
- Manage Last_Check_Timestamp in Redis
- Format notification messages
- Calculate priority levels

**Methods**:

```php
class NotificationService
{
    private RedisService $redis;
    private DonHang $donHangModel;
    private ThanhToan $thanhToanModel;
    private Refund $refundModel;
    private PhienBanSanPham $phienBanModel;
    private DanhGia $danhGiaModel;
    private TransactionLog $transactionLogModel;
    private GatewayHealth $gatewayHealthModel;
    private MaGiamGia $maGiamGiaModel;

    /**
     * Aggregate all notifications for an admin user
     * 
     * @param int $adminId Admin user ID
     * @return array Notification data structure
     */
    public function aggregateNotifications(int $adminId): array;

    /**
     * Get Last_Check_Timestamp from Redis
     * If not exists, initialize to 24 hours ago
     * 
     * @param int $adminId Admin user ID
     * @return string Timestamp in Y-m-d H:i:s format
     */
    private function getLastCheckTimestamp(int $adminId): string;

    /**
     * Update Last_Check_Timestamp in Redis
     * 
     * @param int $adminId Admin user ID
     * @param string $timestamp Current timestamp
     * @return bool Success status
     */
    private function updateLastCheckTimestamp(int $adminId, string $timestamp): bool;

    /**
     * Get order and payment notifications
     * 
     * @param string $since Timestamp to query from
     * @return array Notification items
     */
    private function getOrderNotifications(string $since): array;

    /**
     * Get inventory and product notifications
     * 
     * @param string $since Timestamp to query from
     * @return array Notification items
     */
    private function getInventoryNotifications(string $since): array;

    /**
     * Get customer interaction notifications
     * 
     * @param string $since Timestamp to query from
     * @return array Notification items
     */
    private function getCustomerNotifications(string $since): array;

    /**
     * Get system alert and promotion notifications
     * 
     * @param string $since Timestamp to query from
     * @return array Notification items
     */
    private function getSystemNotifications(string $since): array;

    /**
     * Clear Last_Check_Timestamp on logout
     * 
     * @param int $adminId Admin user ID
     * @return bool Success status
     */
    public function clearNotificationState(int $adminId): bool;
}
```

### Frontend Components

#### 1. NotificationPoller

**Location**: `public/assets/admin/js/notification-poller.js`

**Responsibilities**:
- Poll notification API at regular intervals
- Handle API responses and errors
- Update UI components
- Implement exponential backoff on errors
- Pause/resume polling based on page visibility

**Structure**:

```javascript
class NotificationPoller {
    constructor(apiUrl, interval = 45000) {
        this.apiUrl = apiUrl;
        this.interval = interval;
        this.timerId = null;
        this.failureCount = 0;
        this.maxFailures = 3;
        this.isActive = true;
    }

    /**
     * Start polling
     */
    start() {
        this.poll();
        this.timerId = setInterval(() => this.poll(), this.interval);
        this.setupVisibilityHandler();
    }

    /**
     * Stop polling
     */
    stop() {
        if (this.timerId) {
            clearInterval(this.timerId);
            this.timerId = null;
        }
    }

    /**
     * Execute a single poll request
     */
    async poll() {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            this.handleSuccess(data);
        } catch (error) {
            this.handleError(error);
        }
    }

    /**
     * Handle successful API response
     */
    handleSuccess(data) {
        this.failureCount = 0;
        this.updateBadge(data.total_notifications);
        this.updateDropdown(data.items);
        this.clearWarning();
    }

    /**
     * Handle API error with exponential backoff
     */
    handleError(error) {
        console.error('[NotificationPoller] Error:', error);
        this.failureCount++;

        if (this.failureCount >= this.maxFailures) {
            this.showWarning('Không thể tải thông báo. Đang thử lại...');
        }

        // Exponential backoff: double interval on each failure
        if (this.failureCount > 1) {
            this.stop();
            const backoffInterval = this.interval * Math.pow(2, this.failureCount - 1);
            setTimeout(() => this.start(), Math.min(backoffInterval, 300000)); // Max 5 minutes
        }
    }

    /**
     * Update badge counter
     */
    updateBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    /**
     * Update dropdown content
     */
    updateDropdown(items) {
        const dropdown = document.querySelector('.notification-dropdown-content');
        if (!dropdown) return;

        if (items.length === 0) {
            dropdown.innerHTML = '<div class="dropdown-item text-center text-muted">Không có thông báo mới</div>';
            return;
        }

        // Group by category
        const grouped = this.groupByCategory(items);
        dropdown.innerHTML = this.renderGroupedNotifications(grouped);
    }

    /**
     * Group notifications by category
     */
    groupByCategory(items) {
        const groups = {
            orders: [],
            inventory: [],
            customer: [],
            system: []
        };

        items.forEach(item => {
            if (groups[item.group]) {
                groups[item.group].push(item);
            }
        });

        return groups;
    }

    /**
     * Render grouped notifications HTML
     */
    renderGroupedNotifications(grouped) {
        let html = '';
        const categoryLabels = {
            orders: 'Đơn hàng & Thanh toán',
            inventory: 'Kho hàng',
            customer: 'Khách hàng',
            system: 'Hệ thống'
        };

        for (const [category, items] of Object.entries(grouped)) {
            if (items.length === 0) continue;

            html += `<div class="dropdown-header">${categoryLabels[category]}</div>`;
            
            // Sort by priority: high > medium > low
            items.sort((a, b) => {
                const priorityOrder = { high: 0, medium: 1, low: 2 };
                return priorityOrder[a.priority] - priorityOrder[b.priority];
            });

            items.forEach(item => {
                const priorityClass = item.priority === 'high' ? 'text-danger' : 
                                     item.priority === 'medium' ? 'text-warning' : '';
                html += `
                    <a href="${item.url_redirect}" class="dropdown-item">
                        <i class="bi ${item.icon} me-2 ${priorityClass}"></i>
                        <span>${item.message}</span>
                        ${item.count > 1 ? `<span class="badge bg-secondary ms-2">${item.count}</span>` : ''}
                    </a>
                `;
            });

            html += '<div class="dropdown-divider"></div>';
        }

        return html;
    }

    /**
     * Setup page visibility handler
     */
    setupVisibilityHandler() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stop();
            } else {
                this.start();
            }
        });
    }

    /**
     * Show warning message
     */
    showWarning(message) {
        const dropdown = document.querySelector('.notification-dropdown-content');
        if (dropdown) {
            const warning = document.createElement('div');
            warning.className = 'dropdown-item text-warning notification-warning';
            warning.innerHTML = `<i class="bi bi-exclamation-triangle me-2"></i>${message}`;
            dropdown.prepend(warning);
        }
    }

    /**
     * Clear warning message
     */
    clearWarning() {
        const warning = document.querySelector('.notification-warning');
        if (warning) {
            warning.remove();
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    const poller = new NotificationPoller('/admin/api/notifications', 45000);
    poller.start();
});
```

#### 2. UI Components

**Badge Counter HTML** (in `app/views/admin/layouts/header.php`):

```html
<li class="nav-item dropdown">
  <a class="nav-link" data-bs-toggle="dropdown" href="#" id="notificationDropdown">
    <i class="bi bi-bell-fill"></i>
    <span class="navbar-badge badge text-bg-warning notification-badge" style="display: none;">0</span>
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end notification-dropdown">
    <span class="dropdown-item dropdown-header">Thông báo</span>
    <div class="dropdown-divider"></div>
    <div class="notification-dropdown-content">
      <div class="dropdown-item text-center text-muted">Đang tải...</div>
    </div>
    <div class="dropdown-divider"></div>
    <a href="#" class="dropdown-item dropdown-footer">Xem tất cả</a>
  </div>
</li>
```

## Data Models

### Redis Data Structure

**Key Pattern**: `notification:last_check:{admin_id}`

**Value**: Timestamp string in format `Y-m-d H:i:s`

**TTL**: 7200 seconds (2 hours, matching session timeout)

**Example**:
```
Key: notification:last_check:1
Value: "2024-01-15 10:30:45"
TTL: 7200
```

### Notification Item Structure

```php
[
    'group' => 'orders|inventory|customer|system',
    'type' => 'notification_type_identifier',
    'count' => 1,  // Number of items in this notification
    'message' => 'Human-readable message',
    'url_redirect' => '/admin/path/to/detail',
    'priority' => 'high|medium|low',
    'icon' => 'bootstrap-icon-class'
]
```

### Database Queries

#### Order Notifications

**New Orders Pending Approval**:
```sql
SELECT COUNT(*) as count
FROM don_hang
WHERE trang_thai = 'CHO_DUYET'
  AND ngay_tao >= ?
```

**Payments Pending Approval**:
```sql
SELECT COUNT(*) as count
FROM thanh_toan
WHERE trang_thai_duyet = 'CHO_DUYET'
  AND anh_bien_lai IS NOT NULL
  AND ngay_thanh_toan >= ?
```

**Refund Requests**:
```sql
SELECT COUNT(*) as count
FROM refund
WHERE status = 'PENDING'
  AND created_at >= ?
```

**Cancelled/Returned Orders**:
```sql
SELECT COUNT(*) as count
FROM don_hang
WHERE trang_thai IN ('DA_HUY', 'TRA_HANG')
  AND updated_at >= ?
```

#### Inventory Notifications

**Low Stock Warning**:
```sql
SELECT COUNT(*) as count
FROM phien_ban_san_pham
WHERE so_luong_ton < 5
  AND so_luong_ton > 0
  AND updated_at >= ?
```

**Out of Stock**:
```sql
SELECT COUNT(*) as count
FROM phien_ban_san_pham
WHERE trang_thai = 'HET_HANG'
  AND updated_at >= ?
```

#### Customer Notifications

**New Reviews**:
```sql
SELECT COUNT(*) as count, 
       AVG(so_sao) as avg_rating
FROM danh_gia
WHERE ngay_viet >= ?
```

**Low Rating Reviews** (1-3 stars):
```sql
SELECT COUNT(*) as count
FROM danh_gia
WHERE so_sao BETWEEN 1 AND 3
  AND ngay_viet >= ?
```

#### System Notifications

**Payment Gateway Failures**:
```sql
SELECT COUNT(*) as count
FROM transaction_log
WHERE status = 'FAILED'
  AND created_at >= ?
```

**Gateway Health Degraded**:
```sql
SELECT gateway_name, failure_count
FROM gateway_health
WHERE failure_count > (
    SELECT failure_count 
    FROM gateway_health_snapshot 
    WHERE snapshot_time = ?
) + 5
```

Note: `gateway_health_snapshot` is a temporary comparison point stored in Redis.

**Voucher Exhausted**:
```sql
SELECT COUNT(*) as count
FROM ma_giam_gia
WHERE so_luot_da_dung >= gioi_han_su_dung
  AND gioi_han_su_dung IS NOT NULL
  AND trang_thai = 'HOAT_DONG'
  AND updated_at >= ?
```

### Database Indexes

To ensure queries execute within 500ms, the following indexes are required:

```sql
-- don_hang table
CREATE INDEX idx_don_hang_trang_thai_ngay_tao 
ON don_hang(trang_thai, ngay_tao);

CREATE INDEX idx_don_hang_updated_at 
ON don_hang(updated_at);

-- thanh_toan table
CREATE INDEX idx_thanh_toan_trang_thai_duyet_ngay 
ON thanh_toan(trang_thai_duyet, ngay_thanh_toan);

-- refund table
CREATE INDEX idx_refund_status_created_at 
ON refund(status, created_at);

-- phien_ban_san_pham table
CREATE INDEX idx_phien_ban_so_luong_updated 
ON phien_ban_san_pham(so_luong_ton, updated_at);

CREATE INDEX idx_phien_ban_trang_thai_updated 
ON phien_ban_san_pham(trang_thai, updated_at);

-- danh_gia table
CREATE INDEX idx_danh_gia_ngay_viet 
ON danh_gia(ngay_viet);

CREATE INDEX idx_danh_gia_so_sao_ngay_viet 
ON danh_gia(so_sao, ngay_viet);

-- transaction_log table
CREATE INDEX idx_transaction_log_status_created 
ON transaction_log(status, created_at);

-- ma_giam_gia table
CREATE INDEX idx_ma_giam_gia_trang_thai_updated 
ON ma_giam_gia(trang_thai, updated_at);
```

## Error Handling

### Backend Error Scenarios

1. **Database Connection Failure**:
   - Return HTTP 503 with error message
   - Log error for monitoring
   - Frontend displays cached notifications (if available)

2. **Redis Connection Failure**:
   - Fall back to database-only mode
   - Use session storage for Last_Check_Timestamp
   - Log warning for monitoring

3. **Invalid Admin Session**:
   - Return HTTP 401 Unauthorized
   - Frontend redirects to login page

4. **Query Timeout**:
   - Set query timeout to 1 second
   - Return partial results if available
   - Log slow query for optimization

### Frontend Error Scenarios

1. **Network Failure**:
   - Retry after 60 seconds
   - Display previous notification state
   - Log error to console

2. **API Returns Error Status**:
   - Implement exponential backoff
   - Display warning after 3 consecutive failures
   - Continue polling with increased interval

3. **Invalid JSON Response**:
   - Log error to console
   - Maintain previous notification state
   - Retry on next poll cycle

4. **Page Visibility Change**:
   - Pause polling when page is hidden
   - Resume immediately when page becomes visible
   - Prevents unnecessary API calls

### Error Response Format

```json
{
  "success": false,
  "error": "Error message for logging",
  "user_message": "Không thể tải thông báo. Vui lòng thử lại sau.",
  "code": 500,
  "retry_after": 60
}
```

## Testing Strategy

### Unit Tests

1. **NotificationService Tests**:
   - Test each notification type query independently
   - Verify correct SQL generation with timestamps
   - Test Redis cache operations (get/set/clear)
   - Test notification aggregation logic
   - Test priority assignment rules

2. **NotificationController Tests**:
   - Test authentication validation
   - Test JSON response format
   - Test error handling for various scenarios
   - Test HTTP status codes

3. **Frontend Tests**:
   - Test polling start/stop functionality
   - Test exponential backoff calculation
   - Test UI update functions
   - Test visibility change handling

### Integration Tests

1. **End-to-End Polling Flow**:
   - Create test data in database
   - Verify API returns correct notifications
   - Verify Redis state updates correctly
   - Verify subsequent polls return only new data

2. **Multi-Category Notifications**:
   - Create notifications across all 4 categories
   - Verify correct grouping and sorting
   - Verify priority ordering within groups

3. **Error Recovery**:
   - Simulate database failure
   - Verify graceful degradation
   - Verify recovery after service restoration

4. **Performance Tests**:
   - Measure query execution time with various data volumes
   - Verify all queries complete within 500ms
   - Test concurrent admin users (10+ simultaneous polls)

### Manual Testing Checklist

- [ ] Badge counter updates correctly
- [ ] Dropdown displays all notification categories
- [ ] High priority notifications appear first
- [ ] Clicking notification navigates to correct page
- [ ] Badge hides when count is zero
- [ ] Polling pauses when tab is inactive
- [ ] Polling resumes when tab becomes active
- [ ] Warning displays after 3 consecutive failures
- [ ] Warning clears after successful poll
- [ ] Logout clears notification state

## Performance Optimization

### Database Optimization

1. **Indexed Queries**: All timestamp-based queries use composite indexes
2. **Query Limits**: Each notification type query limited to COUNT(*) operations
3. **Connection Pooling**: Reuse database connections across requests
4. **Query Caching**: Consider MySQL query cache for repeated queries

### Redis Optimization

1. **Key Expiration**: Automatic cleanup via TTL (2 hours)
2. **Minimal Data Storage**: Only store timestamp, not full notification data
3. **Connection Pooling**: Reuse Redis connections
4. **Fallback Strategy**: Continue operation if Redis unavailable

### Frontend Optimization

1. **Debouncing**: Prevent multiple simultaneous polls
2. **Visibility API**: Pause polling when page hidden
3. **Exponential Backoff**: Reduce server load during failures
4. **Minimal DOM Updates**: Only update changed elements

### Monitoring Metrics

1. **API Response Time**: Target < 500ms, alert if > 1000ms
2. **Poll Success Rate**: Target > 99%, alert if < 95%
3. **Redis Hit Rate**: Target > 95%
4. **Database Query Time**: Monitor slow queries (> 100ms)
5. **Concurrent Users**: Track peak concurrent admin sessions

## Security Considerations

### Authentication & Authorization

1. **Session Validation**: Every API request validates admin session
2. **CSRF Protection**: Use X-Requested-With header for AJAX requests
3. **Rate Limiting**: Limit to 1 request per 20 seconds per admin user

### Data Security

1. **SQL Injection Prevention**: Use parameterized queries for all database operations
2. **XSS Prevention**: Escape all user-generated content in notifications
3. **Information Disclosure**: Only show notifications relevant to admin's permissions

### Redis Security

1. **Key Namespacing**: Use `notification:` prefix to avoid key collisions
2. **Data Isolation**: Each admin has separate cache key
3. **Automatic Cleanup**: TTL ensures stale data is removed

## Deployment Considerations

### Prerequisites

1. **Redis Server**: Version 5.0 or higher
2. **PHP Extensions**: redis extension installed and enabled
3. **Database Indexes**: All required indexes created
4. **Session Configuration**: Session timeout set to 2 hours

### Configuration

**Environment Variables** (`.env`):
```
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DB=0
NOTIFICATION_POLL_INTERVAL=45000
NOTIFICATION_MAX_ITEMS=50
```

### Deployment Steps

1. Create database indexes (see Data Models section)
2. Deploy backend files:
   - `app/controllers/admin/NotificationController.php`
   - `app/services/notification/NotificationService.php`
3. Deploy frontend files:
   - `public/assets/admin/js/notification-poller.js`
4. Update admin header template with notification UI
5. Add route to `app/routes/admin/admin.php`
6. Verify Redis connection
7. Test with sample data
8. Monitor performance metrics

### Rollback Plan

1. Remove route from `admin.php`
2. Remove notification UI from header template
3. Remove JavaScript poller initialization
4. Database indexes can remain (no negative impact)

## Future Enhancements

1. **WebSocket Support**: Upgrade to WebSocket for true real-time notifications
2. **Push Notifications**: Browser push notifications for critical alerts
3. **Notification History**: Store notification history in database
4. **Mark as Read**: Allow admins to mark notifications as read
5. **Notification Preferences**: Let admins configure which notifications they receive
6. **Sound Alerts**: Optional sound for high-priority notifications
7. **Desktop Notifications**: Browser desktop notifications API integration
8. **Notification Aggregation**: Smart grouping of similar notifications
9. **Multi-Language Support**: Translate notification messages
10. **Analytics Dashboard**: Track notification response times and patterns
