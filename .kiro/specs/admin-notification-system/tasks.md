# Implementation Plan: Admin Notification System

## Overview

This implementation plan breaks down the Admin Notification System into discrete coding tasks. The system provides real-time notifications to administrators through AJAX polling, monitoring four key categories: Orders & Payments, Inventory & Products, Customer Interactions, and System Alerts & Promotions. The implementation follows a bottom-up approach: database optimization first, then backend services, API endpoints, frontend polling mechanism, and finally UI integration.

## Tasks

- [x] 1. Database optimization and index creation
  - Create all required indexes on timestamp and status columns for efficient notification queries
  - Verify index creation and measure query performance improvements
  - _Requirements: 9.2_

- [x] 2. Implement NotificationService core infrastructure
  - [x] 2.1 Create NotificationService class with Redis integration
    - Create `app/services/notification/NotificationService.php`
    - Implement constructor with dependency injection for RedisService and all model dependencies
    - Implement `getLastCheckTimestamp()` method to retrieve timestamp from Redis (default to 24 hours ago if not exists)
    - Implement `updateLastCheckTimestamp()` method to store current timestamp in Redis with 2-hour TTL
    - Implement `clearNotificationState()` method for logout cleanup
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6_

  - [x] 2.2 Implement order and payment notification queries
    - Implement `getOrderNotifications()` method with four queries: new orders pending (CHO_DUYET), payments pending approval, refund requests (PENDING), cancelled/returned orders
    - Return array of notification items with group='orders', appropriate type, count, message, url_redirect, priority='high', and icon
    - Use parameterized queries with timestamp parameter for all database queries
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

  - [x] 2.3 Implement inventory notification queries
    - Implement `getInventoryNotifications()` method with two queries: low stock warning (so_luong_ton < 5), out of stock (trang_thai = 'HET_HANG')
    - Group multiple low stock products into single notification with count
    - Return array with group='inventory', priority='medium', appropriate messages and url_redirect
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

  - [x] 2.4 Implement customer interaction notification queries
    - Implement `getCustomerNotifications()` method to query danh_gia table for new reviews since timestamp
    - Assign priority='high' for reviews with so_sao between 1-3, priority='low' for 4-5 stars
    - Include product name and rating in notification message
    - Return array with group='customer', url_redirect to review detail page
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [x] 2.5 Implement system alert notification queries
    - Implement `getSystemNotifications()` method with three queries: payment gateway failures (transaction_log status='FAILED'), gateway health degradation (failure_count increase > 5), voucher exhausted (so_luot_da_dung >= gioi_han_su_dung)
    - Assign priority='high' for gateway errors, priority='medium' for voucher exhausted
    - Return array with group='system', appropriate url_redirect values
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

  - [x] 2.6 Implement notification aggregation logic
    - Implement `aggregateNotifications()` method that calls all four notification getter methods
    - Retrieve Last_Check_Timestamp from Redis, pass to all getter methods
    - Combine results from all categories, calculate total_notifications count
    - Update Last_Check_Timestamp in Redis to current server time
    - Return structured array with total_notifications, items array, and last_check timestamp
    - Limit maximum returned notifications to 50 items per request
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 9.4_

- [x] 3. Implement NotificationController API endpoint
  - [x] 3.1 Create NotificationController with authentication
    - Create `app/controllers/admin/NotificationController.php`
    - Implement `index()` method to handle GET requests
    - Verify admin authentication using session validation
    - Extract admin user ID from session
    - _Requirements: 1.1, 1.5_

  - [x] 3.2 Implement API response handling
    - Call `NotificationService->aggregateNotifications($adminId)` in controller
    - Return JSON response with success=true, total_notifications, items array, and last_check timestamp
    - Set appropriate HTTP headers (Content-Type: application/json, X-Requested-With validation)
    - Return HTTP 200 status code on success
    - _Requirements: 1.1, 1.5_

  - [x] 3.3 Implement error handling and logging
    - Catch database connection failures, return HTTP 503 with error message
    - Catch Redis connection failures, log warning and fall back to session storage
    - Return HTTP 401 for invalid admin sessions
    - Return detailed error JSON with success=false, error message, user_message, code, and retry_after
    - Log all errors for monitoring
    - Implement query timeout of 1 second, return partial results if timeout occurs
    - _Requirements: 1.6, 10.1, 10.2, 10.3, 10.4, 10.5, 10.6_

  - [x] 3.4 Register API route
    - Add route `GET /admin/api/notifications` to `app/routes/admin/admin.php`
    - Apply AdminMiddleware for authentication
    - Map route to `NotificationController@index`
    - _Requirements: 1.1_

- [x] 4. Checkpoint - Backend API testing
  - Test NotificationService methods with sample data in database
  - Verify API endpoint returns correct JSON structure
  - Test Redis cache operations (get/set/clear)
  - Verify all database queries execute within 500ms
  - Test error handling scenarios (database failure, Redis failure, invalid session)
  - Ensure all tests pass, ask the user if questions arise.

- [-] 5. Implement frontend NotificationPoller class
  - [x] 5.1 Create NotificationPoller JavaScript class
    - Create `public/assets/admin/js/notification-poller.js`
    - Implement constructor with apiUrl and interval parameters (default 45000ms)
    - Initialize properties: timerId, failureCount, maxFailures=3, isActive=true
    - _Requirements: 6.1_

  - [x] 5.2 Implement polling mechanism
    - Implement `start()` method to begin polling cycle, call `poll()` immediately then set interval
    - Implement `stop()` method to clear interval timer
    - Implement `poll()` async method to fetch API using fetch() with GET method, JSON headers, and credentials
    - Handle successful responses by calling `handleSuccess(data)`
    - Handle errors by calling `handleError(error)`
    - _Requirements: 6.1, 6.2, 6.4_

  - [x] 5.3 Implement response handling
    - Implement `handleSuccess(data)` method to reset failureCount, call updateBadge and updateDropdown, clear warning
    - Implement `handleError(error)` method to increment failureCount, log to console, show warning after 3 failures
    - Implement exponential backoff: double interval on each failure, max 5 minutes
    - _Requirements: 6.2, 6.3, 10.1, 10.2, 10.3_

  - [x] 5.4 Implement page visibility handling
    - Implement `setupVisibilityHandler()` method to listen for visibilitychange event
    - Stop polling when document.hidden is true
    - Resume polling immediately when page becomes visible again
    - _Requirements: 6.5, 6.6_

- [-] 6. Implement UI update functions
  - [x] 6.1 Implement badge counter update
    - Implement `updateBadge(count)` method to update .notification-badge element
    - Set badge text content to count value
    - Hide badge (display: none) when count is zero, show (display: inline-block) when count > 0
    - _Requirements: 7.5, 7.6_

  - [x] 6.2 Implement dropdown content update
    - Implement `updateDropdown(items)` method to update .notification-dropdown-content element
    - Display "Không có thông báo mới" message when items array is empty
    - Call `groupByCategory(items)` and `renderGroupedNotifications(grouped)` for non-empty items
    - _Requirements: 7.1, 7.7_

  - [x] 6.3 Implement notification grouping and rendering
    - Implement `groupByCategory(items)` method to group notifications by group field (orders, inventory, customer, system)
    - Implement `renderGroupedNotifications(grouped)` method to generate HTML for each category
    - Display category headers with Vietnamese labels (Đơn hàng & Thanh toán, Kho hàng, Khách hàng, Hệ thống)
    - Sort items within each category by priority (high > medium > low)
    - Render each notification with icon, message, priority color class, and count badge if count > 1
    - Make each notification clickable with url_redirect as href
    - _Requirements: 7.1, 7.2, 7.3, 7.4_

  - [x] 6.4 Implement warning message display
    - Implement `showWarning(message)` method to prepend warning div to dropdown
    - Implement `clearWarning()` method to remove .notification-warning element
    - Display warning after 3 consecutive polling failures, clear on successful poll
    - _Requirements: 10.4, 10.5_

- [x] 7. Integrate UI components into admin header
  - [x] 7.1 Add notification dropdown HTML to admin header
    - Modify `app/views/admin/layouts/header.php` to add notification dropdown nav item
    - Add bell icon with badge counter span (initially hidden)
    - Add dropdown menu with header, content area, and "Xem tất cả" footer link
    - Set initial content to "Đang tải..." loading message
    - _Requirements: 7.1, 7.5, 7.6, 7.7_

  - [x] 7.2 Add CSS styling for notification components
    - Create or update admin CSS file with styles for .notification-badge, .notification-dropdown, .notification-dropdown-content
    - Style priority indicators (text-danger for high, text-warning for medium)
    - Style notification items with hover effects
    - Style warning message with appropriate colors
    - _Requirements: 7.1, 7.2, 7.3_

  - [x] 7.3 Initialize NotificationPoller on page load
    - Add script tag in admin header or footer to include notification-poller.js
    - Add DOMContentLoaded event listener to initialize NotificationPoller with API URL '/admin/api/notifications' and 45-second interval
    - Call poller.start() to begin polling
    - _Requirements: 6.1, 6.4_

- [x] 8. Implement logout notification cleanup
  - [x] 8.1 Add notification state cleanup to admin logout
    - Modify admin logout handler to call `NotificationService->clearNotificationState($adminId)`
    - Clear Last_Check_Timestamp from Redis on logout
    - _Requirements: 8.5_

- [ ] 9. Checkpoint - End-to-end integration testing
  - Create test data across all four notification categories
  - Verify polling starts automatically on admin dashboard load
  - Verify badge counter updates with correct count
  - Verify dropdown displays grouped notifications with correct priority ordering
  - Verify clicking notification navigates to correct detail page
  - Verify badge hides when no notifications exist
  - Verify polling pauses when tab becomes inactive and resumes when active
  - Test error scenarios: API unreachable, invalid session, database failure
  - Verify warning displays after 3 consecutive failures and clears on recovery
  - Verify logout clears notification state
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 10. Performance optimization and monitoring
  - [ ] 10.1 Verify database query performance
    - Run EXPLAIN on all notification queries to verify index usage
    - Measure query execution time with realistic data volumes
    - Ensure all queries complete within 500ms under normal load
    - _Requirements: 9.1, 9.2_

  - [ ] 10.2 Implement performance logging
    - Add performance metric logging to NotificationService (query times, cache hit rate)
    - Log slow queries (> 100ms) for optimization
    - Log API response times
    - _Requirements: 9.6_

  - [ ] 10.3 Test concurrent user load
    - Simulate 10+ concurrent admin users polling simultaneously
    - Verify system performance remains acceptable
    - Verify Redis connection pooling works correctly
    - _Requirements: 9.1, 9.3_

- [ ] 11. Documentation and deployment preparation
  - [ ] 11.1 Create deployment documentation
    - Document all required database indexes with CREATE INDEX statements
    - Document required environment variables (REDIS_HOST, REDIS_PORT, etc.)
    - Document deployment steps and rollback plan
    - Create deployment checklist
    - _Requirements: All_

  - [ ] 11.2 Create administrator user guide
    - Document how to use the notification system
    - Explain notification categories and priority levels
    - Document expected behavior (polling interval, badge counter, dropdown)
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7_

## Notes

- All tasks reference specific requirements for traceability
- Database indexes must be created before backend implementation to ensure accurate performance testing
- Backend API must be fully tested before frontend integration
- Checkpoints ensure incremental validation at critical integration points
- No property-based tests are included because this is an infrastructure/integration feature without universal correctness properties
- Unit tests and integration tests should focus on API response format, error handling, and UI behavior
- Performance testing is critical due to the 500ms query time requirement
