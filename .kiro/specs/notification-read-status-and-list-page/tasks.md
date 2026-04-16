# Implementation Plan: Notification Read Status and List Page

## Overview

This implementation plan extends the existing Admin Notification System with read/unread status tracking using Redis Sets and a comprehensive notification management page. The implementation will be done incrementally, starting with backend infrastructure (Redis operations, notification ID generation), then API endpoints, followed by frontend components (Read Status Manager, UI updates), and finally the notification list page with filtering and pagination.

## Tasks

- [x] 1. Extend RedisService with Set operations
  - Add methods for Redis Set operations: `sAdd()`, `sRem()`, `sIsMember()`, `sMembers()`, `sCard()`
  - Implement error handling for Redis connection failures
  - Add TTL management with `expire()` method
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 19.1, 19.2_

- [ ]* 1.1 Write unit tests for RedisService Set operations
  - Test each Set operation method (add, remove, check membership, get all members)
  - Test error handling when Redis is unavailable
  - Test TTL setting and expiration
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [x] 2. Implement notification ID generation in NotificationService
  - [x] 2.1 Add `generateNotificationId()` method to NotificationService
    - Implement format: `{type}:{entity_id}` for single entities
    - Implement format: `{type}:aggregate` for aggregate notifications
    - Ensure deterministic ID generation for consistency across polling cycles
    - _Requirements: 17.1, 17.2, 17.3, 17.4_
  
  - [ ]* 2.2 Write unit tests for notification ID generation
    - Test single entity ID format
    - Test aggregate ID format
    - Test various notification types
    - Test ID consistency across multiple calls
    - _Requirements: 17.1, 17.2, 17.3, 17.4, 17.5_

- [x] 3. Implement read status tracking methods in NotificationService
  - [x] 3.1 Add `markAsRead()` method
    - Accept admin ID and notification ID(s) array
    - Use Redis SADD to add notification IDs to read Set
    - Set/extend TTL to 7 days (604800 seconds)
    - Return count of marked notifications
    - _Requirements: 1.3, 1.5, 2.2, 12.4, 12.5_
  
  - [x] 3.2 Add `markAsUnread()` method
    - Accept admin ID and notification ID(s) array
    - Use Redis SREM to remove notification IDs from read Set
    - Extend TTL to 7 days
    - Return count of unmarked notifications
    - _Requirements: 1.4, 1.5, 3.3, 13.4, 13.5_
  
  - [x] 3.3 Add `markAllAsRead()` method
    - Retrieve all current notification IDs for admin
    - Use Redis SADD to add all IDs to read Set
    - Set/extend TTL to 7 days
    - Return count of marked notifications
    - _Requirements: 9.2, 9.3, 14.3, 14.4, 14.5_
  
  - [x] 3.4 Add `isNotificationRead()` method
    - Check if notification ID exists in admin's read Set using SISMEMBER
    - Return boolean result
    - _Requirements: 1.6, 19.2_
  
  - [x] 3.5 Add `getReadNotificationIds()` private method
    - Use Redis SMEMBERS to retrieve all read notification IDs for admin
    - Return array of notification IDs
    - Implement error handling for Redis unavailability
    - _Requirements: 1.6, 19.2, 19.3, 20.1_

- [ ]* 3.6 Write unit tests for read status tracking methods
  - Test markAsRead with single and multiple IDs
  - Test markAsUnread with single and multiple IDs
  - Test markAllAsRead
  - Test isNotificationRead for read and unread notifications
  - Test error handling when Redis is unavailable
  - _Requirements: 1.3, 1.4, 1.5, 1.6, 20.1_

- [x] 4. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 5. Implement mark-read API endpoint
  - [x] 5.1 Add `markAsRead()` method to NotificationController
    - Create POST endpoint at `/admin/api/notifications/mark-read`
    - Validate admin authentication using AdminMiddleware
    - Accept JSON payload with `notification_id` (string or array)
    - Validate notification ID format
    - Call NotificationService `markAsRead()` method
    - Return JSON response: `{"success": true, "marked_count": N}`
    - Handle errors: return 400 for invalid ID, 401 for unauthorized, 500 for server errors
    - _Requirements: 2.2, 12.1, 12.2, 12.3, 12.4, 12.5, 12.6, 20.2_
  
  - [ ]* 5.2 Write integration tests for mark-read API endpoint
    - Test successful mark as read with valid notification ID
    - Test with array of notification IDs
    - Test with invalid notification ID format (should return 400)
    - Test without authentication (should return 401)
    - Test Redis unavailability handling
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5, 12.6, 20.2_

- [x] 6. Implement mark-unread API endpoint
  - [x] 6.1 Add `markAsUnread()` method to NotificationController
    - Create POST endpoint at `/admin/api/notifications/mark-unread`
    - Validate admin authentication using AdminMiddleware
    - Accept JSON payload with `notification_id` (string or array)
    - Validate notification ID format
    - Call NotificationService `markAsUnread()` method
    - Return JSON response: `{"success": true, "unmarked_count": N}`
    - Handle errors: return 400 for invalid ID, 401 for unauthorized, 500 for server errors
    - _Requirements: 3.3, 10.2, 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 20.3_
  
  - [ ]* 6.2 Write integration tests for mark-unread API endpoint
    - Test successful mark as unread with valid notification ID
    - Test with array of notification IDs
    - Test with invalid notification ID format (should return 400)
    - Test without authentication (should return 401)
    - Test Redis unavailability handling
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 20.3_

- [x] 7. Implement mark-all-read API endpoint
  - [x] 7.1 Add `markAllAsRead()` method to NotificationController
    - Create POST endpoint at `/admin/api/notifications/mark-all-read`
    - Validate admin authentication using AdminMiddleware
    - Call NotificationService `markAllAsRead()` method
    - Return JSON response: `{"success": true, "marked_count": N}`
    - Handle errors: return 401 for unauthorized, 500 for server errors
    - _Requirements: 9.2, 9.3, 14.1, 14.2, 14.3, 14.4, 14.5, 14.6_
  
  - [ ]* 7.2 Write integration tests for mark-all-read API endpoint
    - Test successful mark all as read
    - Test without authentication (should return 401)
    - Test with no notifications (should return marked_count: 0)
    - Test Redis unavailability handling
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5, 14.6_

- [x] 8. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 9. Extend existing notification aggregation to include notification IDs
  - [x] 9.1 Update `aggregateNotifications()` method in NotificationService
    - Call `generateNotificationId()` for each notification
    - Include `id` field in notification array returned to frontend
    - Maintain backward compatibility with existing notification structure
    - _Requirements: 17.4, 17.5, 18.1_
  
  - [x] 9.2 Update existing notification API endpoint to include read status
    - Retrieve read notification IDs for admin using `getReadNotificationIds()`
    - Add `is_read` boolean field to each notification based on read Set membership
    - Ensure backward compatibility with existing polling mechanism
    - _Requirements: 1.6, 5.1, 5.2, 18.1, 18.2, 18.5_

- [ ]* 9.3 Write integration tests for updated notification API
  - Test that notification IDs are included in response
  - Test that is_read field is accurate based on Redis read Set
  - Test backward compatibility with existing notification structure
  - Test with Redis unavailable (all notifications should be unread)
  - _Requirements: 1.6, 17.5, 18.1, 18.2, 18.5, 20.1_

- [x] 10. Implement notification list API endpoint with filtering and pagination
  - [x] 10.1 Add `getNotificationList()` method to NotificationService
    - Accept parameters: admin ID, page, per_page, category, priority, status, sort_by, sort_order
    - Query notification source data (orders, payments, inventory, reviews)
    - Generate notification IDs for each item
    - Retrieve read notification IDs using `getReadNotificationIds()`
    - Check read status for each notification in memory (avoid N+1 queries)
    - Apply filters: category, priority, status (read/unread)
    - Apply sorting: by time (newest/oldest first) or priority (high to low, low to high)
    - Apply pagination: calculate offset, limit, total pages
    - Return array with notifications and pagination metadata
    - _Requirements: 1.6, 7.1, 7.2, 7.3, 7.4, 8.1, 8.2, 8.3, 15.2, 15.4, 15.5, 19.2, 19.3, 19.5_
  
  - [x] 10.2 Add `getNotificationList()` method to NotificationController
    - Create GET endpoint at `/admin/api/notifications/list`
    - Validate admin authentication using AdminMiddleware
    - Parse and validate query parameters (page, per_page, category, priority, status, sort_by, sort_order)
    - Use default values for missing parameters (page=1, per_page=20, sort_by=time, sort_order=desc)
    - Validate filter values against allowed options
    - Call NotificationService `getNotificationList()` method
    - Return JSON response with notifications array, pagination object, and filters object
    - Handle errors: return 401 for unauthorized, 503 for database unavailable
    - _Requirements: 15.1, 15.2, 15.3, 15.4, 15.5, 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 19.5, 20.4, 20.5_

- [ ]* 10.3 Write integration tests for notification list API endpoint
  - Test successful retrieval with default parameters
  - Test filtering by category (orders, inventory, customer, system)
  - Test filtering by priority (high, medium, low)
  - Test filtering by status (read, unread, all)
  - Test sorting by time (newest first, oldest first)
  - Test sorting by priority (high to low, low to high)
  - Test pagination (page 1, page 2, last page, beyond last page)
  - Test with invalid filter parameters (should use defaults)
  - Test without authentication (should return 401)
  - Test with Redis unavailable (all notifications should be unread)
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 8.1, 8.2, 8.3, 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 20.1, 20.4_

- [x] 11. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 12. Create Read Status Manager JavaScript component
  - [x] 12.1 Create `ReadStatusManager` class in frontend JavaScript
    - Implement `markAsRead(notificationId, urlRedirect)` method
    - Push history state using History API before marking as read
    - Send POST request to `/admin/api/notifications/mark-read`
    - Update notification UI styling (reduce opacity, lighter background)
    - Decrement badge counter by 1
    - Navigate to url_redirect after successful mark
    - Revert UI changes on API failure and show error message
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 4.2, 5.4, 20.2_
  
  - [x] 12.2 Implement `markAsUnread(notificationId)` method
    - Send POST request to `/admin/api/notifications/mark-unread`
    - Update notification UI styling (restore opacity, normal background)
    - Increment badge counter by 1
    - Revert UI changes on API failure and show error message
    - _Requirements: 3.3, 3.4, 3.5, 4.1, 5.5, 10.3, 10.4, 20.3_
  
  - [x] 12.3 Implement `markAllAsRead(notificationIds)` method
    - Send POST request to `/admin/api/notifications/mark-all-read`
    - Update all visible notification UI styling
    - Set badge counter to 0
    - Show confirmation message "Đã đánh dấu tất cả là đã đọc"
    - Revert UI changes on API failure and show error message
    - _Requirements: 9.2, 9.3, 9.4, 9.5, 9.6_
  
  - [x] 12.4 Implement `handleBackButton(event)` method
    - Listen for popstate events
    - Check if history state contains mark_read action
    - Call `markAsUnread()` for the notification ID in state
    - Handle invalid or missing history state gracefully
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 20.9_
  
  - [x] 12.5 Implement `updateNotificationUI(notificationId, isRead)` method
    - Apply read styling: reduced opacity (0.6), lighter background
    - Apply unread styling: full opacity, normal background
    - Update styling consistently across dropdown and list page
    - _Requirements: 4.1, 4.2, 4.5, 4.6_
  
  - [x] 12.6 Implement `updateBadgeCounter(delta)` method
    - Increment or decrement badge counter by delta
    - Hide badge when count is 0
    - Update badge immediately without page reload
    - _Requirements: 2.4, 3.5, 5.1, 5.2, 5.3, 5.4, 5.5, 9.5, 10.4_

- [ ]* 12.7 Write frontend unit tests for ReadStatusManager
  - Test markAsRead updates UI and calls API
  - Test markAsRead reverts on API failure
  - Test markAsUnread updates UI and calls API
  - Test markAsUnread reverts on API failure
  - Test markAllAsRead updates all notifications
  - Test handleBackButton detects popstate and marks as unread
  - Test updateNotificationUI applies correct styling
  - Test updateBadgeCounter increments and decrements correctly
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.3, 3.4, 3.5, 4.1, 4.2, 9.2, 9.3, 9.4, 9.5_

- [x] 13. Update existing notification dropdown to use Read Status Manager
  - [x] 13.1 Integrate ReadStatusManager with notification dropdown
    - Initialize ReadStatusManager on page load
    - Attach click event listeners to notification items
    - Call `markAsRead()` when notification is clicked
    - Apply read/unread styling based on is_read flag from API
    - Update badge counter to show only unread count
    - _Requirements: 2.1, 2.3, 2.4, 4.1, 4.2, 5.1, 5.2, 5.4, 18.1, 18.2, 18.3_
  
  - [x] 13.2 Add "Xem tất cả" (View All) link to notification dropdown
    - Add link at bottom of dropdown
    - Link to `/admin/notifications` page
    - Style consistently with existing dropdown design
    - _Requirements: 6.4_

- [x] 14. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 15. Create notification list page view
  - [x] 15.1 Create `/app/views/admin/notifications/index.php` view file
    - Create page structure with header "Thông báo" (Notifications)
    - Add "Mark all as read" button at top of page
    - Create filter section with dropdowns for Category, Priority, Status
    - Create sort dropdown with options for Time and Priority
    - Create notification list container (table or list format)
    - Add pagination controls at bottom
    - Display "Không có thông báo" message when no notifications exist
    - Apply admin layout (header, sidebar, footer)
    - _Requirements: 6.1, 6.2, 6.3, 6.6, 7.1, 7.2, 7.3, 8.1, 9.1_
  
  - [x] 15.2 Implement notification list item rendering
    - Display each notification with: Icon, Message, Category, Priority, Time, Status
    - Apply unread styling: bold text, visual indicator (dot or badge)
    - Apply read styling: normal font weight, no visual indicator
    - Add "Mark as unread" button for read notifications
    - Make notification clickable to navigate to url_redirect
    - _Requirements: 4.3, 4.4, 4.5, 6.2, 10.1, 10.5, 11.1_
  
  - [x] 15.3 Implement filter controls
    - Create Category dropdown with options: All, Orders, Inventory, Customer, System
    - Create Priority dropdown with options: All, High, Medium, Low
    - Create Status dropdown with options: All, Read, Unread
    - Update notification list without page reload when filters change
    - Preserve filter selections in URL query parameters
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_
  
  - [x] 15.4 Implement sort controls
    - Create Sort dropdown with options: Newest First (default), Oldest First, Priority High to Low, Priority Low to High
    - Update notification list without page reload when sort changes
    - Preserve sort selection in URL query parameters
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6_
  
  - [x] 15.5 Implement pagination controls
    - Display current page, total pages, and item range
    - Create Previous/Next buttons
    - Create page number buttons (show 5 pages at a time)
    - Disable Previous on first page, disable Next on last page
    - Update notification list without page reload when page changes
    - Preserve page number in URL query parameters
    - _Requirements: 6.3, 20.6_

- [x] 16. Create notification list page controller and route
  - [x] 16.1 Add `index()` method to NotificationController
    - Create route at `/admin/notifications`
    - Validate admin authentication using AdminMiddleware
    - Render notification list page view
    - _Requirements: 6.1, 6.5_
  
  - [x] 16.2 Add route to admin routes file
    - Add GET route: `/admin/notifications` → `NotificationController@index`
    - Apply AdminMiddleware to route
    - _Requirements: 6.1, 6.5_

- [x] 17. Implement notification list page JavaScript functionality
  - [x] 17.1 Create notification list page JavaScript module
    - Initialize ReadStatusManager
    - Load notifications on page load using `/admin/api/notifications/list`
    - Render notification items dynamically
    - Attach click event listeners to notification items
    - Attach click event listener to "Mark all as read" button
    - Attach click event listeners to "Mark as unread" buttons
    - _Requirements: 6.2, 9.1, 9.2, 9.3, 10.1, 10.2, 10.6, 11.1, 11.2_
  
  - [x] 17.2 Implement filter change handlers
    - Listen for filter dropdown changes
    - Update URL query parameters
    - Reload notification list with new filters
    - Update list without full page reload
    - _Requirements: 7.4, 7.5, 7.6_
  
  - [x] 17.3 Implement sort change handlers
    - Listen for sort dropdown changes
    - Update URL query parameters
    - Reload notification list with new sort order
    - Update list without full page reload
    - _Requirements: 8.3, 8.4, 8.5_
  
  - [x] 17.4 Implement pagination click handlers
    - Listen for pagination button clicks
    - Update URL query parameters
    - Reload notification list with new page number
    - Update list without full page reload
    - Scroll to top of notification list
    - _Requirements: 6.3, 20.6_
  
  - [x] 17.5 Implement notification click handler
    - Mark notification as read before navigation
    - Store notification list state in history state
    - Navigate to url_redirect
    - Handle invalid or missing url_redirect gracefully
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.6_
  
  - [x] 17.6 Implement back button state restoration
    - Restore scroll position when returning via back button
    - Restore filter and sort state from URL parameters
    - _Requirements: 11.5_

- [x] 18. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 19. Add CSS styling for read/unread notifications
  - [x] 19.1 Add read/unread styles to notification dropdown
    - Unread: full opacity (1.0), normal background, normal font weight
    - Read: reduced opacity (0.6), lighter background (#f8f9fa)
    - Smooth transition between states (0.3s)
    - _Requirements: 4.1, 4.2, 4.5, 4.6_
  
  - [x] 19.2 Add read/unread styles to notification list page
    - Unread: bold text, visual indicator (blue dot or badge), normal background
    - Read: normal font weight, no visual indicator, lighter background
    - Hover effects for clickable notifications
    - Smooth transition between states (0.3s)
    - _Requirements: 4.3, 4.4, 4.5, 4.6_
  
  - [x] 19.3 Add responsive styles for mobile devices
    - Adjust notification list layout for small screens
    - Stack filter controls vertically on mobile
    - Adjust pagination controls for mobile
    - Ensure touch-friendly button sizes
    - _Requirements: 6.2, 7.1, 7.2, 7.3, 8.1_

- [x] 20. Add error handling and logging
  - [x] 20.1 Add error logging to NotificationService
    - Log Redis connection failures with context
    - Log database query failures with stack trace
    - Log invalid notification ID attempts
    - Use consistent log format with timestamp, component, operation, and error details
    - _Requirements: 20.1, 20.5, 20.10_
  
  - [x] 20.2 Add error handling to API endpoints
    - Return appropriate HTTP status codes (400, 401, 403, 500, 503)
    - Include descriptive error messages in JSON response
    - Include retry_after header for 503 errors
    - Log all API errors for monitoring
    - _Requirements: 12.6, 13.6, 14.6, 16.6, 20.2, 20.3, 20.4, 20.5_
  
  - [x] 20.3 Add error handling to frontend JavaScript
    - Display user-friendly error messages using toast or alert
    - Revert UI changes on API failures
    - Log errors to console for debugging
    - Provide retry options for failed operations
    - _Requirements: 2.5, 20.2, 20.3_

- [x] 21. Final integration and testing
  - [x] 21.1 Test complete workflow end-to-end
    - Test mark as read from dropdown
    - Test browser back button undo
    - Test notification list page with all filters and sorts
    - Test mark all as read
    - Test individual mark as unread
    - Test navigation from notification to detail page
    - Test cross-tab synchronization via polling
    - _Requirements: All requirements_
  
  - [x] 21.2 Test error scenarios
    - Test with Redis unavailable
    - Test with database unavailable
    - Test with invalid notification IDs
    - Test with expired session
    - Test with network errors
    - _Requirements: 20.1, 20.2, 20.3, 20.4, 20.5_
  
  - [x] 21.3 Verify performance requirements
    - Verify notification list API responds within 500ms
    - Verify mark as read API responds within 100ms
    - Verify mark all as read completes within 1 second for 100 notifications
    - Verify no N+1 Redis queries in notification list
    - _Requirements: 15.6, 19.1, 19.2, 19.3, 19.4, 19.5_

- [x] 22. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation at key milestones
- Implementation follows incremental approach: backend infrastructure → API endpoints → frontend components → UI integration
- Read status is stored in Redis with 7-day TTL for automatic cleanup
- Browser History API is used for undo functionality without server-side state
- Notification IDs are deterministic based on type and entity ID for consistency
- All API endpoints require admin authentication
- Error handling ensures graceful degradation when Redis is unavailable
