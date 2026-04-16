# Requirements Document

## Introduction

This document specifies the requirements for extending the existing Admin Notification System with read/unread status tracking and a dedicated notification list page. The system will allow administrators to mark notifications as read/unread, track read status in Redis with a 7-day TTL, implement browser back button undo functionality, and provide a comprehensive notification management page with filtering, sorting, and pagination capabilities.

## Glossary

- **Notification_System**: The existing notification feature including backend API, frontend polling mechanism, and UI components
- **Read_Status_Manager**: The component responsible for tracking and managing read/unread status of notifications
- **Notification_List_Page**: The dedicated page at `/admin/notifications` displaying all notifications with filters and pagination
- **Read_Status_Cache**: Redis storage containing Sets of read notification IDs per admin user
- **Notification_Dropdown**: The existing UI component in the header displaying recent notifications
- **Badge_Counter**: The visual indicator showing the count of unread notifications only
- **Browser_History_State**: Browser history API state used to implement undo functionality on back navigation
- **Notification_Filter**: UI component allowing filtering by category, priority, and read status
- **Notification_Sorter**: UI component allowing sorting by time and priority
- **Mark_All_Read_Action**: Action to mark all notifications as read for the current admin user
- **Notification_Poller**: The existing frontend JavaScript component that periodically calls the Notification API

## Requirements

### Requirement 1: Read Status Storage in Redis

**User Story:** As a system administrator, I want notification read status stored in Redis with appropriate TTL, so that the system performs efficiently and automatically cleans up old data.

#### Acceptance Criteria

1. THE Read_Status_Cache SHALL store read notification IDs in Redis using Set data structure with key pattern `notification:read:{admin_id}`
2. THE Read_Status_Cache SHALL set TTL to 7 days (604800 seconds) for each admin's read status Set
3. WHEN a notification is marked as read, THE Read_Status_Manager SHALL add the notification ID to the admin's read status Set in Redis
4. WHEN a notification is marked as unread, THE Read_Status_Manager SHALL remove the notification ID from the admin's read status Set in Redis
5. THE Read_Status_Manager SHALL extend the TTL to 7 days whenever the read status Set is modified
6. WHEN retrieving notifications, THE Notification_System SHALL check the Read_Status_Cache to determine read/unread status for each notification

### Requirement 2: Mark Notification as Read

**User Story:** As an administrator, I want to mark a notification as read when I click on it, so that I can track which notifications I have already seen.

#### Acceptance Criteria

1. WHEN an administrator clicks a notification in the Notification_Dropdown, THE Read_Status_Manager SHALL mark that notification as read
2. THE Read_Status_Manager SHALL send a POST request to `/admin/api/notifications/mark-read` with the notification ID
3. THE Read_Status_Manager SHALL update the UI to display the notification with read styling without page reload
4. THE Read_Status_Manager SHALL decrement the Badge_Counter by 1 when a notification is marked as read
5. WHEN the mark-read API call fails, THE Read_Status_Manager SHALL revert the UI changes and display an error message
6. THE Read_Status_Manager SHALL store the previous state in Browser_History_State for undo functionality

### Requirement 3: Browser Back Button Undo

**User Story:** As an administrator, I want notifications to return to unread status when I use the browser back button, so that I can easily undo accidental clicks.

#### Acceptance Criteria

1. WHEN an administrator clicks a notification, THE Read_Status_Manager SHALL push a new history state using the Browser History API
2. WHEN an administrator clicks the browser back button, THE Read_Status_Manager SHALL detect the popstate event
3. WHEN a popstate event is detected, THE Read_Status_Manager SHALL mark the notification as unread via POST request to `/admin/api/notifications/mark-unread`
4. THE Read_Status_Manager SHALL update the UI to display the notification with unread styling
5. THE Read_Status_Manager SHALL increment the Badge_Counter by 1 when a notification is marked as unread via back button
6. THE Read_Status_Manager SHALL handle multiple consecutive back button presses correctly

### Requirement 4: Read/Unread Visual Styling

**User Story:** As an administrator, I want to visually distinguish between read and unread notifications, so that I can quickly identify which notifications require attention.

#### Acceptance Criteria

1. THE Notification_Dropdown SHALL display unread notifications with full opacity and normal font weight
2. THE Notification_Dropdown SHALL display read notifications with reduced opacity (0.6) and lighter background color
3. THE Notification_List_Page SHALL display unread notifications with bold text and a visual indicator (dot or badge)
4. THE Notification_List_Page SHALL display read notifications with normal font weight and no visual indicator
5. THE Notification_System SHALL apply read/unread styling consistently across all UI components
6. THE Notification_System SHALL update styling immediately when read status changes without page reload

### Requirement 5: Badge Counter for Unread Only

**User Story:** As an administrator, I want the badge counter to show only unread notifications, so that I know how many notifications require my attention.

#### Acceptance Criteria

1. THE Badge_Counter SHALL display only the count of unread notifications
2. THE Badge_Counter SHALL exclude read notifications from the count
3. WHEN all notifications are marked as read, THE Badge_Counter SHALL display zero or be hidden
4. WHEN a notification is marked as read, THE Badge_Counter SHALL decrement by 1
5. WHEN a notification is marked as unread, THE Badge_Counter SHALL increment by 1
6. THE Notification_Poller SHALL update the Badge_Counter with unread count on each polling cycle

### Requirement 6: Notification List Page Structure

**User Story:** As an administrator, I want a dedicated page to view all notifications, so that I can manage notifications more effectively than in the dropdown.

#### Acceptance Criteria

1. THE Notification_System SHALL provide a Notification_List_Page accessible at `/admin/notifications`
2. THE Notification_List_Page SHALL display notifications in a table or list format with columns: Icon, Message, Category, Priority, Time, Status
3. THE Notification_List_Page SHALL display 20 notifications per page with pagination controls
4. THE Notification_List_Page SHALL include a "Xem tất cả" (View All) link in the Notification_Dropdown that navigates to this page
5. THE Notification_List_Page SHALL require admin authentication to access
6. THE Notification_List_Page SHALL display a message "Không có thông báo" (No notifications) when no notifications exist

### Requirement 7: Notification Filtering

**User Story:** As an administrator, I want to filter notifications by category, priority, and status, so that I can focus on specific types of notifications.

#### Acceptance Criteria

1. THE Notification_Filter SHALL provide a dropdown to filter by Category with options: All, Orders, Inventory, Customer, System
2. THE Notification_Filter SHALL provide a dropdown to filter by Priority with options: All, High, Medium, Low
3. THE Notification_Filter SHALL provide a dropdown to filter by Status with options: All, Read, Unread
4. WHEN a filter is applied, THE Notification_List_Page SHALL display only notifications matching all selected filter criteria
5. THE Notification_Filter SHALL preserve filter selections in URL query parameters for bookmarking and sharing
6. THE Notification_Filter SHALL update the notification list without full page reload when filters change

### Requirement 8: Notification Sorting

**User Story:** As an administrator, I want to sort notifications by time and priority, so that I can view notifications in my preferred order.

#### Acceptance Criteria

1. THE Notification_Sorter SHALL provide a dropdown to sort by Time with options: Newest First (default), Oldest First
2. THE Notification_Sorter SHALL provide a dropdown to sort by Priority with options: High to Low, Low to High
3. WHEN a sort option is selected, THE Notification_List_Page SHALL reorder notifications according to the selected criteria
4. THE Notification_Sorter SHALL preserve sort selections in URL query parameters
5. THE Notification_Sorter SHALL update the notification list without full page reload when sort order changes
6. THE Notification_List_Page SHALL default to "Newest First" sorting when no sort parameter is specified

### Requirement 9: Mark All as Read

**User Story:** As an administrator, I want to mark all notifications as read with one click, so that I can quickly clear my notification list.

#### Acceptance Criteria

1. THE Notification_List_Page SHALL provide a "Mark all as read" button visible at the top of the page
2. WHEN the "Mark all as read" button is clicked, THE Read_Status_Manager SHALL send a POST request to `/admin/api/notifications/mark-all-read`
3. THE Read_Status_Manager SHALL mark all currently visible notifications (respecting filters) as read
4. THE Read_Status_Manager SHALL update the UI to display all affected notifications with read styling
5. THE Read_Status_Manager SHALL set the Badge_Counter to zero after marking all as read
6. THE Read_Status_Manager SHALL display a confirmation message "Đã đánh dấu tất cả là đã đọc" (All marked as read)

### Requirement 10: Individual Mark as Unread

**User Story:** As an administrator, I want to mark individual notifications as unread, so that I can flag notifications that need follow-up attention.

#### Acceptance Criteria

1. THE Notification_List_Page SHALL provide a "Mark as unread" button or icon for each read notification
2. WHEN the "Mark as unread" button is clicked, THE Read_Status_Manager SHALL send a POST request to `/admin/api/notifications/mark-unread` with the notification ID
3. THE Read_Status_Manager SHALL update the UI to display the notification with unread styling
4. THE Read_Status_Manager SHALL increment the Badge_Counter by 1
5. THE Read_Status_Manager SHALL display the "Mark as unread" button only for read notifications
6. THE Read_Status_Manager SHALL handle the action without page reload

### Requirement 11: Notification Click Navigation

**User Story:** As an administrator, I want to navigate to the relevant detail page when I click a notification, so that I can take action on the notification.

#### Acceptance Criteria

1. WHEN an administrator clicks a notification in the Notification_List_Page, THE Notification_System SHALL navigate to the url_redirect specified in the notification
2. THE Notification_System SHALL mark the notification as read before navigation
3. THE Notification_System SHALL open the detail page in the same browser tab
4. THE Notification_System SHALL preserve the notification list state in Browser_History_State for back button functionality
5. WHEN an administrator returns via back button, THE Notification_List_Page SHALL restore the previous scroll position and filter state
6. THE Notification_System SHALL handle invalid or missing url_redirect gracefully by displaying an error message

### Requirement 12: API Endpoint for Mark Read

**User Story:** As a developer, I want a dedicated API endpoint to mark notifications as read, so that the frontend can update read status efficiently.

#### Acceptance Criteria

1. THE Notification_System SHALL provide a POST endpoint at `/admin/api/notifications/mark-read`
2. THE endpoint SHALL accept a JSON payload with field `notification_id` (string or array of strings)
3. THE endpoint SHALL validate that the requesting user is an authenticated admin
4. THE endpoint SHALL add the notification ID(s) to the admin's read status Set in Redis
5. THE endpoint SHALL return HTTP 200 with JSON response `{"success": true, "marked_count": N}` on success
6. IF the request is invalid or unauthorized, THEN THE endpoint SHALL return appropriate HTTP error status (400, 401, 403) with error details

### Requirement 13: API Endpoint for Mark Unread

**User Story:** As a developer, I want a dedicated API endpoint to mark notifications as unread, so that the frontend can implement undo and manual unread functionality.

#### Acceptance Criteria

1. THE Notification_System SHALL provide a POST endpoint at `/admin/api/notifications/mark-unread`
2. THE endpoint SHALL accept a JSON payload with field `notification_id` (string or array of strings)
3. THE endpoint SHALL validate that the requesting user is an authenticated admin
4. THE endpoint SHALL remove the notification ID(s) from the admin's read status Set in Redis
5. THE endpoint SHALL return HTTP 200 with JSON response `{"success": true, "unmarked_count": N}` on success
6. IF the request is invalid or unauthorized, THEN THE endpoint SHALL return appropriate HTTP error status (400, 401, 403) with error details

### Requirement 14: API Endpoint for Mark All Read

**User Story:** As a developer, I want a dedicated API endpoint to mark all notifications as read, so that the frontend can implement bulk read functionality.

#### Acceptance Criteria

1. THE Notification_System SHALL provide a POST endpoint at `/admin/api/notifications/mark-all-read`
2. THE endpoint SHALL validate that the requesting user is an authenticated admin
3. THE endpoint SHALL retrieve all current notification IDs for the admin user
4. THE endpoint SHALL add all notification IDs to the admin's read status Set in Redis
5. THE endpoint SHALL return HTTP 200 with JSON response `{"success": true, "marked_count": N}` on success
6. IF the request is invalid or unauthorized, THEN THE endpoint SHALL return appropriate HTTP error status (401, 403) with error details

### Requirement 15: API Endpoint for Notification List

**User Story:** As a developer, I want a dedicated API endpoint to retrieve paginated and filtered notifications, so that the notification list page can display data efficiently.

#### Acceptance Criteria

1. THE Notification_System SHALL provide a GET endpoint at `/admin/api/notifications/list`
2. THE endpoint SHALL accept query parameters: `page` (default 1), `per_page` (default 20), `category` (optional), `priority` (optional), `status` (optional), `sort_by` (optional), `sort_order` (optional)
3. THE endpoint SHALL validate that the requesting user is an authenticated admin
4. THE endpoint SHALL return notifications with read status included for each item
5. THE endpoint SHALL return JSON response with fields: `success`, `notifications` (array), `pagination` (object with total, page, per_page, total_pages)
6. THE endpoint SHALL execute queries within 500 milliseconds under normal load

### Requirement 16: Notification List API Response Format

**User Story:** As a developer, I want a consistent API response format for the notification list, so that the frontend can reliably parse and display data.

#### Acceptance Criteria

1. THE Notification_List API SHALL return each notification with fields: `id`, `group`, `type`, `message`, `url_redirect`, `priority`, `icon`, `timestamp`, `is_read`
2. THE `is_read` field SHALL be a boolean indicating whether the notification is in the admin's read status Set
3. THE `timestamp` field SHALL be in ISO 8601 format (Y-m-d H:i:s)
4. THE pagination object SHALL include fields: `total`, `page`, `per_page`, `total_pages`, `has_next`, `has_prev`
5. THE response SHALL include applied filters in a `filters` object for UI state synchronization
6. THE response SHALL return HTTP 200 on success and appropriate error codes (400, 401, 500) on failure

### Requirement 17: Notification ID Generation

**User Story:** As a developer, I want unique and consistent notification IDs, so that read status can be tracked reliably across sessions.

#### Acceptance Criteria

1. THE Notification_System SHALL generate a unique notification ID for each notification based on notification type and relevant entity ID
2. THE notification ID format SHALL be `{type}:{entity_id}` (e.g., `new_order_pending:123`, `low_stock_warning:456`)
3. FOR notifications with multiple items (count > 1), THE notification ID SHALL be `{type}:aggregate`
4. THE notification ID SHALL remain consistent across polling cycles for the same underlying data
5. THE Notification_System SHALL include the notification ID in all API responses
6. THE notification ID SHALL be used as the key for read status tracking in Redis

### Requirement 18: Integration with Existing Notification Poller

**User Story:** As a developer, I want the read status feature to integrate seamlessly with the existing notification poller, so that the system remains cohesive.

#### Acceptance Criteria

1. THE Notification_Poller SHALL include read status information in the notification data structure
2. THE Notification_Poller SHALL update the Badge_Counter to show only unread notifications
3. THE Notification_Poller SHALL apply read/unread styling to notifications in the Notification_Dropdown
4. THE Notification_Poller SHALL continue polling at the existing interval (30-60 seconds)
5. THE Notification_Poller SHALL handle read status changes made in other tabs or windows by refreshing on next poll
6. THE Notification_Poller SHALL maintain backward compatibility with the existing notification API response format

### Requirement 19: Performance Optimization for Read Status

**User Story:** As a system administrator, I want the read status feature to perform efficiently, so that it does not impact system performance.

#### Acceptance Criteria

1. THE Read_Status_Manager SHALL use Redis Set operations (SADD, SREM, SISMEMBER) for O(1) read/write performance
2. THE Notification_List API SHALL use a single Redis SMEMBERS call to retrieve all read notification IDs for the admin
3. THE Notification_List API SHALL check read status in memory after retrieving the Set, avoiding N+1 Redis queries
4. THE Read_Status_Cache SHALL use pipelining for bulk operations (mark all as read)
5. THE Notification_System SHALL limit the notification list API to maximum 100 items per page to prevent performance degradation
6. THE Notification_System SHALL add database indexes on timestamp columns if not already present

### Requirement 20: Error Handling for Read Status Operations

**User Story:** As an administrator, I want the system to handle read status errors gracefully, so that temporary failures do not disrupt my workflow.

#### Acceptance Criteria

1. WHEN Redis is unavailable, THE Read_Status_Manager SHALL log the error and treat all notifications as unread
2. WHEN a mark-read API call fails, THE Read_Status_Manager SHALL display an error message and revert UI changes
3. WHEN a mark-unread API call fails, THE Read_Status_Manager SHALL display an error message and revert UI changes
4. THE Notification_List_Page SHALL display a warning message if read status cannot be retrieved
5. THE Notification_System SHALL continue to function with core notification features even if read status tracking fails
6. THE Notification_System SHALL log all read status errors for monitoring and debugging
