# Design Document: Notification Read Status and List Page

## Overview

This design extends the existing Admin Notification System with read/unread status tracking and a comprehensive notification management page. The system will track read status in Redis using Set data structures with 7-day TTL, implement browser back button undo functionality using the History API, and provide a dedicated notification list page with filtering, sorting, and pagination capabilities.

### Key Design Goals

1. **Efficient Read Status Tracking**: Use Redis Sets for O(1) read/write operations on notification read status
2. **Seamless User Experience**: Implement browser back button undo without page reloads
3. **Comprehensive Management**: Provide a full-featured notification list page with filters and bulk actions
4. **Performance**: Minimize Redis queries and database operations for fast response times
5. **Backward Compatibility**: Integrate with existing notification polling mechanism without breaking changes

### Design Principles

- **Stateless Read Status**: Read status stored in Redis with TTL, not in database
- **Client-Side State Management**: Use browser History API for undo functionality
- **Progressive Enhancement**: Core notification features work even if read status tracking fails
- **Consistent Notification IDs**: Generate deterministic IDs based on notification type and entity

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                     Admin Frontend                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Notification │  │ Notification │  │ Read Status  │     │
│  │   Dropdown   │  │  List Page   │  │   Manager    │     │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘     │
│         │                  │                  │              │
│         └──────────────────┴──────────────────┘              │
│                            │                                 │
└────────────────────────────┼─────────────────────────────────┘
                             │ AJAX/Fetch API
┌────────────────────────────┼─────────────────────────────────┐
│                     Backend API Layer                        │
│  ┌──────────────────────────────────────────────────────┐   │
│  │         NotificationController                       │   │
│  │  - /admin/api/notifications (existing)               │   │
│  │  - /admin/api/notifications/mark-read (new)          │   │
│  │  - /admin/api/notifications/mark-unread (new)        │   │
│  │  - /admin/api/notifications/mark-all-read (new)      │   │
│  │  - /admin/api/notifications/list (new)               │   │
│  └──────────────────┬───────────────────────────────────┘   │
│                     │                                        │
│  ┌──────────────────┴───────────────────────────────────┐   │
│  │         NotificationService                          │   │
│  │  - aggregateNotifications() (existing)               │   │
│  │  - generateNotificationId() (new)                    │   │
│  │  - getNotificationList() (new)                       │   │
│  └──────────────────┬───────────────────────────────────┘   │
│                     │                                        │
└─────────────────────┼────────────────────────────────────────┘
                      │
        ┌─────────────┴─────────────┐
        │                           │
┌───────▼────────┐         ┌────────▼────────┐
│ Redis Service  │         │ MySQL Database  │
│                │         │                 │
│ Read Status    │         │ Notification    │
│ Cache (Sets)   │         │ Source Data     │
│                │         │ (Orders, etc)   │
│ TTL: 7 days    │         │                 │
└────────────────┘         └─────────────────┘
```

### Data Flow

#### Mark Notification as Read Flow

```
User clicks notification
    │
    ├─> Push history state (for undo)
    │
    ├─> POST /admin/api/notifications/mark-read
    │       │
    │       ├─> Validate admin session
    │       │
    │       ├─> SADD notification:read:{admin_id} {notification_id}
    │       │
    │       ├─> EXPIRE notification:read:{admin_id} 604800
    │       │
    │       └─> Return success
    │
    ├─> Update UI (reduce opacity, decrement badge)
    │
    └─> Navigate to url_redirect
```

#### Browser Back Button Undo Flow

```
User clicks back button
    │
    ├─> Detect popstate event
    │
    ├─> POST /admin/api/notifications/mark-unread
    │       │
    │       ├─> Validate admin session
    │       │
    │       ├─> SREM notification:read:{admin_id} {notification_id}
    │       │
    │       ├─> EXPIRE notification:read:{admin_id} 604800
    │       │
    │       └─> Return success
    │
    └─> Update UI (restore opacity, increment badge)
```

#### Notification List Page Load Flow

```
User navigates to /admin/notifications
    │
    ├─> GET /admin/api/notifications/list?page=1&status=all
    │       │
    │       ├─> Validate admin session
    │       │
    │       ├─> Query notification source data (orders, inventory, etc)
    │       │
    │       ├─> Generate notification IDs for each item
    │       │
    │       ├─> SMEMBERS notification:read:{admin_id}
    │       │
    │       ├─> Check each notification ID against read set (in memory)
    │       │
    │       ├─> Apply filters (category, priority, status)
    │       │
    │       ├─> Apply sorting (time, priority)
    │       │
    │       ├─> Apply pagination
    │       │
    │       └─> Return notifications with is_read flag
    │
    └─> Render notification list with read/unread styling
```

## Components and Interfaces

### Frontend Components

#### 1. Read Status Manager (JavaScript)

**Responsibilities:**
- Track read/unread state changes
- Manage browser history for undo functionality
- Update UI without page reloads
- Handle API communication for read status operations

**Key Methods:**
```javascript
class ReadStatusManager {
    // Mark notification as read and push history state
    markAsRead(notificationId, urlRedirect)
    
    // Mark notification as unread (for undo)
    markAsUnread(notificationId)
    
    // Mark all visible notifications as read
    markAllAsRead(notificationIds)
    
    // Handle browser back button (popstate event)
    handleBackButton(event)
    
    // Update UI styling for read/unread
    updateNotificationUI(notificationId, isRead)
    
    // Update badge counter
    updateBadgeCounter(delta)
}
```

**Browser History State Format:**
```javascript
{
    action: 'mark_read',
    notification_id: 'new_order_pending:123',
    previous_url: '/admin/dashboard',
    timestamp: 1234567890
}
```

#### 2. Notification List Page Component

**Responsibilities:**
- Display paginated notification list
- Provide filtering and sorting controls
- Handle bulk actions (mark all as read)
- Manage URL query parameters for state persistence

**UI Structure:**
```
┌─────────────────────────────────────────────────────────┐
│ Notifications                          [Mark all as read]│
├─────────────────────────────────────────────────────────┤
│ Filters:                                                 │
│ Category: [All ▼] Priority: [All ▼] Status: [All ▼]    │
│ Sort: [Newest First ▼]                                  │
├─────────────────────────────────────────────────────────┤
│ ● [Icon] New order pending (#123)          2 hours ago  │
│   Category: Orders | Priority: High                      │
│                                                          │
│   [Icon] Low stock warning (5 products)   3 hours ago   │
│   Category: Inventory | Priority: Medium                 │
│                                                          │
│ ● [Icon] New review received              5 hours ago   │
│   Category: Customer | Priority: Low                     │
├─────────────────────────────────────────────────────────┤
│ Showing 1-20 of 45    [< Previous] [1] [2] [3] [Next >] │
└─────────────────────────────────────────────────────────┘

Legend: ● = Unread (bold), no dot = Read (lighter)
```

### Backend Components

#### 1. NotificationController (Extended)

**New Endpoints:**

```php
// Mark single or multiple notifications as read
POST /admin/api/notifications/mark-read
Request: {
    "notification_id": "new_order_pending:123" | ["id1", "id2"]
}
Response: {
    "success": true,
    "marked_count": 1
}

// Mark single or multiple notifications as unread
POST /admin/api/notifications/mark-unread
Request: {
    "notification_id": "new_order_pending:123" | ["id1", "id2"]
}
Response: {
    "success": true,
    "unmarked_count": 1
}

// Mark all notifications as read
POST /admin/api/notifications/mark-all-read
Request: {} (empty body)
Response: {
    "success": true,
    "marked_count": 45
}

// Get paginated notification list with filters
GET /admin/api/notifications/list?page=1&per_page=20&category=orders&priority=high&status=unread&sort_by=time&sort_order=desc
Response: {
    "success": true,
    "notifications": [
        {
            "id": "new_order_pending:123",
            "group": "orders",
            "type": "new_order_pending",
            "message": "1 đơn hàng mới chờ duyệt",
            "url_redirect": "/admin/don-hang?trang_thai=CHO_DUYET",
            "priority": "high",
            "icon": "bi-cart-check",
            "timestamp": "2024-01-15 14:30:00",
            "is_read": false
        }
    ],
    "pagination": {
        "total": 45,
        "page": 1,
        "per_page": 20,
        "total_pages": 3,
        "has_next": true,
        "has_prev": false
    },
    "filters": {
        "category": "orders",
        "priority": "high",
        "status": "unread"
    }
}
```

#### 2. NotificationService (Extended)

**New Methods:**

```php
class NotificationService {
    // Generate consistent notification ID
    public function generateNotificationId(
        string $type, 
        ?int $entityId = null
    ): string
    
    // Get paginated notification list with read status
    public function getNotificationList(
        int $adminId,
        int $page = 1,
        int $perPage = 20,
        ?string $category = null,
        ?string $priority = null,
        ?string $status = null,
        string $sortBy = 'time',
        string $sortOrder = 'desc'
    ): array
    
    // Mark notifications as read
    public function markAsRead(
        int $adminId, 
        array $notificationIds
    ): int
    
    // Mark notifications as unread
    public function markAsUnread(
        int $adminId, 
        array $notificationIds
    ): int
    
    // Mark all notifications as read
    public function markAllAsRead(int $adminId): int
    
    // Check if notification is read
    public function isNotificationRead(
        int $adminId, 
        string $notificationId
    ): bool
    
    // Get read notification IDs for admin
    private function getReadNotificationIds(int $adminId): array
}
```

#### 3. RedisService (Extended)

**New Methods for Set Operations:**

```php
class RedisService {
    // Add member(s) to set
    public function sAdd(string $key, string|array $members): int
    
    // Remove member(s) from set
    public function sRem(string $key, string|array $members): int
    
    // Check if member exists in set
    public function sIsMember(string $key, string $member): bool
    
    // Get all members of set
    public function sMembers(string $key): array
    
    // Get set cardinality (count)
    public function sCard(string $key): int
}
```

## Data Models

### Redis Data Structures

#### Read Status Set

**Key Pattern:** `notification:read:{admin_id}`

**Data Structure:** Redis Set

**Members:** Notification IDs (strings)

**TTL:** 604800 seconds (7 days)

**Example:**
```
Key: notification:read:1
Type: Set
Members: [
    "new_order_pending:123",
    "payment_pending_approval:456",
    "low_stock_warning:aggregate",
    "new_review:789"
]
TTL: 604800
```

**Operations:**
- `SADD notification:read:1 "new_order_pending:123"` - Mark as read
- `SREM notification:read:1 "new_order_pending:123"` - Mark as unread
- `SISMEMBER notification:read:1 "new_order_pending:123"` - Check if read
- `SMEMBERS notification:read:1` - Get all read notification IDs
- `EXPIRE notification:read:1 604800` - Reset TTL to 7 days

### Notification ID Format

Notification IDs are generated deterministically based on notification type and entity ID to ensure consistency across polling cycles.

**Format Rules:**

1. **Single Entity Notifications:**
   - Format: `{type}:{entity_id}`
   - Example: `new_order_pending:123` (order ID 123)
   - Example: `payment_pending_approval:456` (payment ID 456)
   - Example: `new_review:789` (review ID 789)

2. **Aggregate Notifications (count > 1):**
   - Format: `{type}:aggregate`
   - Example: `low_stock_warning:aggregate` (multiple products)
   - Example: `out_of_stock:aggregate` (multiple products)

3. **System-Wide Notifications:**
   - Format: `{type}:aggregate`
   - Example: `payment_gateway_error:aggregate`
   - Example: `voucher_exhausted:aggregate`

**ID Generation Logic:**

```php
public function generateNotificationId(string $type, ?int $entityId = null): string
{
    if ($entityId !== null) {
        return "{$type}:{$entityId}";
    }
    return "{$type}:aggregate";
}
```

**Consistency Guarantee:**

The same underlying data (e.g., order #123 pending) will always generate the same notification ID (`new_order_pending:123`), ensuring read status persists across polling cycles until the notification condition changes.

### Notification List Response Model

```php
[
    'success' => true,
    'notifications' => [
        [
            'id' => 'new_order_pending:123',
            'group' => 'orders',
            'type' => 'new_order_pending',
            'message' => '1 đơn hàng mới chờ duyệt',
            'url_redirect' => '/admin/don-hang?trang_thai=CHO_DUYET',
            'priority' => 'high',
            'icon' => 'bi-cart-check',
            'timestamp' => '2024-01-15 14:30:00',
            'is_read' => false
        ]
    ],
    'pagination' => [
        'total' => 45,
        'page' => 1,
        'per_page' => 20,
        'total_pages' => 3,
        'has_next' => true,
        'has_prev' => false
    ],
    'filters' => [
        'category' => 'orders',
        'priority' => 'high',
        'status' => 'unread'
    ]
]
```

### Browser History State Model

```javascript
{
    action: 'mark_read',
    notification_id: 'new_order_pending:123',
    previous_url: '/admin/dashboard',
    timestamp: 1234567890
}
```


## Error Handling

### Redis Unavailability

**Scenario:** Redis service is down or unreachable

**Handling Strategy:**
1. Log error with context (operation, key, admin ID)
2. Return graceful degradation response
3. Treat all notifications as unread (fail-safe default)
4. Display warning message to user: "Read status tracking temporarily unavailable"
5. Continue core notification functionality

**Implementation:**
```php
try {
    $readIds = $this->redis->sMembers("notification:read:{$adminId}");
} catch (Exception $e) {
    error_log("[NotificationService] Redis unavailable: " . $e->getMessage());
    $readIds = []; // Treat all as unread
    // Set flag for UI warning
    $response['warning'] = 'Read status tracking temporarily unavailable';
}
```

### API Request Failures

**Scenario:** Mark read/unread API call fails (network, server error)

**Handling Strategy:**
1. Display error toast/message to user
2. Revert UI changes (restore previous styling, badge count)
3. Log error for monitoring
4. Provide retry option for user

**Frontend Implementation:**
```javascript
async markAsRead(notificationId) {
    // Optimistic UI update
    this.updateNotificationUI(notificationId, true);
    this.updateBadgeCounter(-1);
    
    try {
        const response = await fetch('/admin/api/notifications/mark-read', {
            method: 'POST',
            body: JSON.stringify({ notification_id: notificationId })
        });
        
        if (!response.ok) {
            throw new Error('API request failed');
        }
    } catch (error) {
        // Revert UI changes
        this.updateNotificationUI(notificationId, false);
        this.updateBadgeCounter(1);
        
        // Show error message
        this.showError('Failed to mark notification as read. Please try again.');
        
        console.error('Mark as read failed:', error);
    }
}
```

### Invalid Notification IDs

**Scenario:** Client sends invalid or malformed notification ID

**Handling Strategy:**
1. Validate notification ID format on backend
2. Return HTTP 400 with descriptive error message
3. Log validation failure
4. Do not modify Redis state

**Validation:**
```php
private function validateNotificationId(string $id): bool
{
    // Format: {type}:{entity_id} or {type}:aggregate
    return preg_match('/^[a-z_]+:(\\d+|aggregate)$/', $id) === 1;
}
```

### Session/Authentication Errors

**Scenario:** Admin session expired or invalid

**Handling Strategy:**
1. Return HTTP 401 Unauthorized
2. Clear client-side state
3. Redirect to login page
4. Preserve intended action for post-login redirect

**Implementation:**
```php
if (!Session::isLoggedIn() || !Session::isAdmin()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Authentication required',
        'code' => 401,
        'redirect' => '/admin/auth/login'
    ]);
    exit;
}
```

### Concurrent Modification

**Scenario:** User marks notification as read in multiple tabs/windows

**Handling Strategy:**
1. Redis Set operations are atomic (SADD/SREM)
2. Last operation wins (idempotent)
3. Polling mechanism syncs state across tabs on next cycle
4. No explicit conflict resolution needed

### Database Query Failures

**Scenario:** Database unavailable when fetching notification source data

**Handling Strategy:**
1. Catch database exceptions
2. Return HTTP 503 Service Unavailable
3. Include retry_after header (60 seconds)
4. Log error with stack trace
5. Display user-friendly error message

**Implementation:**
```php
try {
    $notifications = $this->getNotificationList($adminId, $page, $perPage);
} catch (PDOException $e) {
    error_log("[NotificationService] Database error: " . $e->getMessage());
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'error' => 'Database service unavailable',
        'code' => 503,
        'retry_after' => 60
    ]);
    exit;
}
```

### Pagination Edge Cases

**Scenario:** User requests page beyond available data

**Handling Strategy:**
1. Validate page number (must be >= 1)
2. If page > total_pages, return empty array with correct pagination metadata
3. Do not return error, allow graceful handling

**Implementation:**
```php
$totalPages = ceil($total / $perPage);
if ($page > $totalPages && $total > 0) {
    return [
        'success' => true,
        'notifications' => [],
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'has_next' => false,
            'has_prev' => true
        ]
    ];
}
```

### Browser History State Corruption

**Scenario:** History state is missing or malformed during popstate event

**Handling Strategy:**
1. Check if state exists and has required fields
2. If invalid, skip undo operation
3. Log warning for debugging
4. Continue normal back navigation

**Implementation:**
```javascript
handleBackButton(event) {
    const state = event.state;
    
    if (!state || !state.action || !state.notification_id) {
        console.warn('Invalid history state, skipping undo');
        return;
    }
    
    if (state.action === 'mark_read') {
        this.markAsUnread(state.notification_id);
    }
}
```

### TTL Expiration

**Scenario:** Read status Set expires after 7 days

**Handling Strategy:**
1. This is expected behavior (automatic cleanup)
2. Notifications older than 7 days treated as unread
3. No error handling needed
4. TTL is extended on every modification

### Filter/Sort Parameter Validation

**Scenario:** Invalid filter or sort parameters in URL

**Handling Strategy:**
1. Validate against allowed values
2. Use default values for invalid parameters
3. Do not return error, apply defaults silently
4. Log validation warnings

**Allowed Values:**
```php
private const ALLOWED_CATEGORIES = ['all', 'orders', 'inventory', 'customer', 'system'];
private const ALLOWED_PRIORITIES = ['all', 'high', 'medium', 'low'];
private const ALLOWED_STATUSES = ['all', 'read', 'unread'];
private const ALLOWED_SORT_BY = ['time', 'priority'];
private const ALLOWED_SORT_ORDER = ['asc', 'desc'];

private function validateFilters(array $params): array
{
    return [
        'category' => in_array($params['category'] ?? 'all', self::ALLOWED_CATEGORIES) 
            ? $params['category'] : 'all',
        'priority' => in_array($params['priority'] ?? 'all', self::ALLOWED_PRIORITIES) 
            ? $params['priority'] : 'all',
        'status' => in_array($params['status'] ?? 'all', self::ALLOWED_STATUSES) 
            ? $params['status'] : 'all',
        'sort_by' => in_array($params['sort_by'] ?? 'time', self::ALLOWED_SORT_BY) 
            ? $params['sort_by'] : 'time',
        'sort_order' => in_array($params['sort_order'] ?? 'desc', self::ALLOWED_SORT_ORDER) 
            ? $params['sort_order'] : 'desc'
    ];
}
```

## Testing Strategy

This feature involves infrastructure integration (Redis), browser APIs (History), UI interactions, and API endpoints. Property-based testing is not applicable as the feature does not involve pure business logic with universal properties. Instead, we will use a combination of unit tests, integration tests, and manual testing.

### Unit Tests

**Focus:** Individual component logic and edge cases

**Test Cases:**

1. **Notification ID Generation**
   - Test single entity ID format: `generateNotificationId('new_order', 123)` → `'new_order:123'`
   - Test aggregate ID format: `generateNotificationId('low_stock')` → `'low_stock:aggregate'`
   - Test various notification types

2. **Notification ID Validation**
   - Valid formats: `'new_order:123'`, `'low_stock:aggregate'`
   - Invalid formats: `'invalid'`, `'123'`, `'type:'`, `':123'`, `'type:abc'`

3. **Filter Parameter Validation**
   - Valid parameters return unchanged
   - Invalid parameters return defaults
   - Missing parameters return defaults

4. **Pagination Calculation**
   - Calculate total_pages correctly
   - Calculate has_next and has_prev correctly
   - Handle edge cases (0 items, 1 item, exact page boundary)

5. **Read Status Checking**
   - Notification in read set returns true
   - Notification not in read set returns false
   - Empty read set returns false for all

**Example Unit Test (PHPUnit):**
```php
class NotificationServiceTest extends TestCase
{
    public function testGenerateNotificationIdWithEntity()
    {
        $service = new NotificationService(/* dependencies */);
        $id = $service->generateNotificationId('new_order', 123);
        $this->assertEquals('new_order:123', $id);
    }
    
    public function testGenerateNotificationIdAggregate()
    {
        $service = new NotificationService(/* dependencies */);
        $id = $service->generateNotificationId('low_stock');
        $this->assertEquals('low_stock:aggregate', $id);
    }
    
    public function testValidateNotificationIdValid()
    {
        $this->assertTrue($this->validateNotificationId('new_order:123'));
        $this->assertTrue($this->validateNotificationId('low_stock:aggregate'));
    }
    
    public function testValidateNotificationIdInvalid()
    {
        $this->assertFalse($this->validateNotificationId('invalid'));
        $this->assertFalse($this->validateNotificationId('type:'));
        $this->assertFalse($this->validateNotificationId(':123'));
    }
}
```

### Integration Tests

**Focus:** API endpoints, Redis operations, database queries

**Test Cases:**

1. **Mark as Read API**
   - POST with valid notification ID returns success
   - POST with invalid ID returns 400
   - POST without authentication returns 401
   - Redis Set contains notification ID after marking
   - TTL is set to 7 days

2. **Mark as Unread API**
   - POST with valid notification ID returns success
   - Redis Set does not contain notification ID after unmarking
   - TTL is extended to 7 days

3. **Mark All as Read API**
   - POST marks all current notifications as read
   - Returns correct marked_count
   - Redis Set contains all notification IDs

4. **Notification List API**
   - GET returns paginated notifications
   - Filtering by category works correctly
   - Filtering by priority works correctly
   - Filtering by status (read/unread) works correctly
   - Sorting by time works correctly
   - Sorting by priority works correctly
   - Pagination metadata is correct
   - is_read flag is accurate based on Redis Set

5. **Redis Unavailability**
   - When Redis is down, API returns graceful degradation
   - All notifications treated as unread
   - Warning message included in response

6. **Database Unavailability**
   - When database is down, API returns 503
   - Error message is user-friendly
   - retry_after header is included

**Example Integration Test (PHPUnit with Redis Mock):**
```php
class NotificationControllerIntegrationTest extends TestCase
{
    private $redis;
    private $controller;
    
    protected function setUp(): void
    {
        $this->redis = $this->createMock(RedisService::class);
        $this->controller = new NotificationController(/* inject mocked redis */);
    }
    
    public function testMarkAsReadSuccess()
    {
        // Mock Redis SADD operation
        $this->redis->expects($this->once())
            ->method('sAdd')
            ->with('notification:read:1', 'new_order:123')
            ->willReturn(1);
        
        // Mock Redis EXPIRE operation
        $this->redis->expects($this->once())
            ->method('expire')
            ->with('notification:read:1', 604800)
            ->willReturn(true);
        
        // Simulate POST request
        $_POST = ['notification_id' => 'new_order:123'];
        $_SESSION['user_id'] = 1;
        $_SESSION['is_admin'] = true;
        
        ob_start();
        $this->controller->markAsRead();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertEquals(1, $response['marked_count']);
    }
    
    public function testNotificationListWithFilters()
    {
        // Mock Redis SMEMBERS operation
        $this->redis->expects($this->once())
            ->method('sMembers')
            ->with('notification:read:1')
            ->willReturn(['new_order:123']);
        
        // Simulate GET request
        $_GET = [
            'page' => 1,
            'per_page' => 20,
            'category' => 'orders',
            'status' => 'unread'
        ];
        $_SESSION['user_id'] = 1;
        $_SESSION['is_admin'] = true;
        
        ob_start();
        $this->controller->getNotificationList();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertIsArray($response['notifications']);
        $this->assertArrayHasKey('pagination', $response);
    }
}
```

### Frontend Tests (JavaScript)

**Focus:** Read Status Manager, UI updates, History API

**Test Cases:**

1. **Mark as Read**
   - Updates UI styling (opacity, font weight)
   - Decrements badge counter
   - Pushes history state
   - Calls API endpoint
   - Reverts on API failure

2. **Mark as Unread**
   - Updates UI styling
   - Increments badge counter
   - Calls API endpoint
   - Reverts on API failure

3. **Browser Back Button**
   - Detects popstate event
   - Calls mark as unread API
   - Updates UI correctly

4. **Mark All as Read**
   - Updates all visible notifications
   - Sets badge counter to 0
   - Calls API endpoint
   - Shows confirmation message

**Example Frontend Test (Jest):**
```javascript
describe('ReadStatusManager', () => {
    let manager;
    
    beforeEach(() => {
        manager = new ReadStatusManager();
        global.fetch = jest.fn();
    });
    
    test('markAsRead updates UI and calls API', async () => {
        fetch.mockResolvedValue({
            ok: true,
            json: async () => ({ success: true, marked_count: 1 })
        });
        
        await manager.markAsRead('new_order:123', '/admin/don-hang');
        
        expect(fetch).toHaveBeenCalledWith(
            '/admin/api/notifications/mark-read',
            expect.objectContaining({
                method: 'POST',
                body: JSON.stringify({ notification_id: 'new_order:123' })
            })
        );
        
        // Check UI updates
        const notification = document.querySelector('[data-id="new_order:123"]');
        expect(notification.classList.contains('read')).toBe(true);
    });
    
    test('markAsRead reverts on API failure', async () => {
        fetch.mockRejectedValue(new Error('Network error'));
        
        await manager.markAsRead('new_order:123', '/admin/don-hang');
        
        // Check UI reverted
        const notification = document.querySelector('[data-id="new_order:123"]');
        expect(notification.classList.contains('read')).toBe(false);
    });
});
```

### Manual Testing Checklist

**Functional Testing:**
- [ ] Click notification in dropdown marks it as read
- [ ] Badge counter decrements when notification marked as read
- [ ] Browser back button marks notification as unread
- [ ] Badge counter increments when notification marked as unread via back button
- [ ] Notification list page displays all notifications
- [ ] Filtering by category works
- [ ] Filtering by priority works
- [ ] Filtering by status (read/unread) works
- [ ] Sorting by time works (newest/oldest first)
- [ ] Sorting by priority works (high to low, low to high)
- [ ] Pagination works correctly
- [ ] Mark all as read button works
- [ ] Individual mark as unread button works
- [ ] Clicking notification navigates to correct URL
- [ ] Read/unread styling is consistent across dropdown and list page

**Error Handling Testing:**
- [ ] Redis down: notifications still display, warning shown
- [ ] Database down: appropriate error message shown
- [ ] Invalid notification ID: error message shown
- [ ] Session expired: redirects to login
- [ ] Network error: UI reverts, error message shown

**Cross-Tab Testing:**
- [ ] Mark as read in one tab syncs to other tabs on next poll
- [ ] Badge counter syncs across tabs

**Performance Testing:**
- [ ] Notification list loads within 500ms with 100 notifications
- [ ] Mark all as read completes within 1 second for 100 notifications
- [ ] No N+1 Redis queries when loading notification list

**Browser Compatibility:**
- [ ] Chrome: All features work
- [ ] Firefox: All features work
- [ ] Safari: All features work
- [ ] Edge: All features work

### Test Data Setup

**Redis Test Data:**
```
Key: notification:read:1
Members: ["new_order:123", "payment_pending:456"]
TTL: 604800
```

**Database Test Data:**
```sql
-- Orders
INSERT INTO don_hang (id, trang_thai, ngay_tao) VALUES
(123, 'CHO_DUYET', NOW() - INTERVAL 2 HOUR),
(124, 'CHO_DUYET', NOW() - INTERVAL 1 HOUR);

-- Payments
INSERT INTO thanh_toan (id, trang_thai_duyet, anh_bien_lai, ngay_thanh_toan) VALUES
(456, 'CHO_DUYET', 'receipt.jpg', NOW() - INTERVAL 3 HOUR);

-- Reviews
INSERT INTO danh_gia (id, so_sao, ngay_viet) VALUES
(789, 2, NOW() - INTERVAL 5 HOUR);
```

### Performance Benchmarks

**Target Metrics:**
- Notification list API response time: < 500ms (p95)
- Mark as read API response time: < 100ms (p95)
- Mark all as read API response time: < 1000ms for 100 notifications (p95)
- Redis operations: < 10ms per operation (p95)
- Frontend UI update: < 50ms (perceived instant)

**Load Testing:**
- 100 concurrent admins polling notifications
- 1000 notifications in system
- 50 mark as read operations per second
- System should maintain target response times

### Monitoring and Logging

**Metrics to Track:**
- API endpoint response times
- Redis operation latencies
- Error rates by endpoint
- Read status cache hit rate
- Notification list page load times

**Logs to Capture:**
- Redis connection failures
- Database query failures
- Invalid notification ID attempts
- Session authentication failures
- API request/response for debugging

**Example Log Format:**
```
[2024-01-15 14:30:00] [NotificationService] Mark as read: admin_id=1, notification_id=new_order:123, success=true, duration=15ms
[2024-01-15 14:30:05] [NotificationService] Redis unavailable: operation=sMembers, key=notification:read:1, error=Connection refused
[2024-01-15 14:30:10] [NotificationController] Invalid notification ID: admin_id=1, notification_id=invalid_format
```

