# Requirements Document

## Introduction

This document specifies the requirements for a real-time notification system for the Admin Dashboard. The system will automatically push notifications to administrators about critical events across four main categories: Orders & Payments, Inventory & Products, Customer Interactions, and System Alerts & Promotions. The implementation uses AJAX polling (30-60 second intervals) to fetch notifications from the backend, which returns JSON data to update the UI dropdown and badge counter.

## Glossary

- **Notification_System**: The complete notification feature including backend API, frontend polling mechanism, and UI components
- **Notification_API**: The backend endpoint that returns notification data in JSON format
- **Notification_Poller**: The frontend JavaScript component that periodically calls the Notification_API
- **Notification_Dropdown**: The UI component displaying the list of notifications
- **Badge_Counter**: The visual indicator showing the total count of unread notifications
- **Admin_Dashboard**: The administrative interface where notifications are displayed
- **Order_Notification**: Notifications related to orders and payment transactions
- **Inventory_Notification**: Notifications related to product stock levels
- **Customer_Notification**: Notifications related to customer reviews and interactions
- **System_Notification**: Notifications related to system errors and promotion limits
- **Notification_Cache**: Redis or in-memory cache storing notification state between polling requests
- **Last_Check_Timestamp**: The timestamp of the last successful notification poll, used to query only new records

## Requirements

### Requirement 1: Notification API Endpoint

**User Story:** As an administrator, I want the system to provide a notification API endpoint, so that the frontend can retrieve notification data periodically.

#### Acceptance Criteria

1. THE Notification_API SHALL return JSON data containing total_notifications, items array with group, type, count, message, url_redirect, priority, and icon fields
2. WHEN the Notification_API is called, THE Notification_API SHALL query only records created or updated since the Last_Check_Timestamp
3. THE Notification_API SHALL use Notification_Cache to store the Last_Check_Timestamp between requests
4. THE Notification_API SHALL return notifications grouped by category (orders, inventory, customer, system)
5. THE Notification_API SHALL return an HTTP 200 status code with valid JSON on success
6. IF the Notification_API encounters an error, THEN THE Notification_API SHALL return an appropriate HTTP error status code with error details

### Requirement 2: Order and Payment Notifications

**User Story:** As an administrator, I want to receive notifications about order and payment events, so that I can respond to critical order management tasks promptly.

#### Acceptance Criteria

1. WHEN a new record appears in don_hang table with trang_thai = 'CHO_DUYET', THE Notification_System SHALL create an Order_Notification with type "new_order_pending"
2. WHEN a new record appears in thanh_toan table with trang_thai_duyet = 'CHO_DUYET' AND anh_bien_lai IS NOT NULL, THE Notification_System SHALL create an Order_Notification with type "payment_pending_approval"
3. WHEN a new record appears in refund table with status = 'PENDING', THE Notification_System SHALL create an Order_Notification with type "refund_request"
4. WHEN trang_thai in don_hang table changes to 'DA_HUY' OR 'TRA_HANG', THE Notification_System SHALL create an Order_Notification with type "order_cancelled_or_returned"
5. THE Notification_System SHALL assign priority "high" to all Order_Notification items
6. THE Notification_System SHALL include url_redirect pointing to the relevant order or payment detail page

### Requirement 3: Inventory and Product Notifications

**User Story:** As an administrator, I want to receive notifications about low stock or out-of-stock products, so that I can replenish inventory before stockouts occur.

#### Acceptance Criteria

1. WHEN so_luong_ton in phien_ban_san_pham table is less than 5, THE Notification_System SHALL create an Inventory_Notification with type "low_stock_warning"
2. WHEN trang_thai in phien_ban_san_pham table equals 'HET_HANG', THE Notification_System SHALL create an Inventory_Notification with type "out_of_stock"
3. THE Notification_System SHALL group multiple low stock products into a single notification with count
4. THE Notification_System SHALL assign priority "medium" to Inventory_Notification items
5. THE Notification_System SHALL include url_redirect pointing to the product variant management page

### Requirement 4: Customer Interaction Notifications

**User Story:** As an administrator, I want to receive notifications about new customer reviews, so that I can monitor product feedback and respond to negative reviews.

#### Acceptance Criteria

1. WHEN a new record appears in danh_gia table, THE Notification_System SHALL create a Customer_Notification with type "new_review"
2. WHEN a new record in danh_gia table has so_sao between 1 and 3 inclusive, THE Notification_System SHALL assign priority "high" to the Customer_Notification
3. WHEN a new record in danh_gia table has so_sao between 4 and 5 inclusive, THE Notification_System SHALL assign priority "low" to the Customer_Notification
4. THE Notification_System SHALL include the product name and rating in the notification message
5. THE Notification_System SHALL include url_redirect pointing to the review detail page

### Requirement 5: System Alert and Promotion Notifications

**User Story:** As an administrator, I want to receive notifications about payment gateway failures and exhausted promotion codes, so that I can address system issues and manage promotions effectively.

#### Acceptance Criteria

1. WHEN transaction_log table contains records with status = 'FAILED' created since Last_Check_Timestamp, THE Notification_System SHALL create a System_Notification with type "payment_gateway_error"
2. WHEN failure_count in gateway_health table increases by more than 5 within a polling interval, THE Notification_System SHALL create a System_Notification with type "gateway_health_degraded"
3. WHEN so_luot_da_dung equals gioi_han_su_dung in ma_giam_gia table, THE Notification_System SHALL create a System_Notification with type "voucher_exhausted"
4. THE Notification_System SHALL assign priority "high" to payment gateway error notifications
5. THE Notification_System SHALL assign priority "medium" to voucher exhausted notifications
6. THE Notification_System SHALL include url_redirect pointing to the payment gateway health page or voucher management page

### Requirement 6: Frontend Notification Polling

**User Story:** As an administrator, I want the dashboard to automatically check for new notifications, so that I receive updates without manually refreshing the page.

#### Acceptance Criteria

1. THE Notification_Poller SHALL call the Notification_API every 30 to 60 seconds
2. WHEN the Notification_API returns data, THE Notification_Poller SHALL update the Badge_Counter with total_notifications value
3. WHEN the Notification_API returns data, THE Notification_Poller SHALL update the Notification_Dropdown with the items array
4. THE Notification_Poller SHALL continue polling while the Admin_Dashboard page is active
5. WHEN the Admin_Dashboard page becomes inactive or hidden, THE Notification_Poller SHALL pause polling
6. WHEN the Admin_Dashboard page becomes active again, THE Notification_Poller SHALL resume polling immediately

### Requirement 7: Notification UI Display

**User Story:** As an administrator, I want to see notifications in a dropdown menu with visual indicators, so that I can quickly identify and access important notifications.

#### Acceptance Criteria

1. THE Notification_Dropdown SHALL display each notification with icon, message, and timestamp
2. THE Notification_Dropdown SHALL group notifications by category (orders, inventory, customer, system)
3. THE Notification_Dropdown SHALL display high priority notifications at the top of each category
4. WHEN a notification item is clicked, THE Admin_Dashboard SHALL navigate to the url_redirect specified in the notification
5. THE Badge_Counter SHALL display the total number of unread notifications
6. WHEN total_notifications is zero, THE Badge_Counter SHALL be hidden
7. THE Notification_Dropdown SHALL display a "No new notifications" message when items array is empty

### Requirement 8: Notification State Management

**User Story:** As an administrator, I want the system to track which notifications I have seen, so that I only see new notifications and the badge counter reflects unread items.

#### Acceptance Criteria

1. THE Notification_System SHALL store a Last_Check_Timestamp for each admin user session
2. WHEN the Notification_API is called, THE Notification_API SHALL update the Last_Check_Timestamp to the current server time
3. THE Notification_System SHALL query database tables for records created or updated after the Last_Check_Timestamp
4. THE Notification_Cache SHALL store the Last_Check_Timestamp with a session identifier or user ID as the key
5. WHEN an admin user logs out, THE Notification_System SHALL clear the Last_Check_Timestamp from Notification_Cache
6. WHEN an admin user logs in, THE Notification_System SHALL initialize Last_Check_Timestamp to the current time minus 24 hours

### Requirement 9: Notification Performance Optimization

**User Story:** As a system administrator, I want the notification system to perform efficiently, so that it does not impact database performance or user experience.

#### Acceptance Criteria

1. THE Notification_API SHALL execute all database queries within 500 milliseconds under normal load
2. THE Notification_System SHALL use database indexes on timestamp columns (ngay_tao, created_at, updated_at) for efficient querying
3. THE Notification_Cache SHALL use Redis or in-memory storage to minimize database queries
4. THE Notification_API SHALL limit the maximum number of returned notifications to 50 items per request
5. THE Notification_Poller SHALL implement exponential backoff when the Notification_API returns errors
6. THE Notification_System SHALL log performance metrics for monitoring and optimization

### Requirement 10: Notification Error Handling

**User Story:** As an administrator, I want the notification system to handle errors gracefully, so that temporary failures do not disrupt my workflow.

#### Acceptance Criteria

1. WHEN the Notification_API is unreachable, THE Notification_Poller SHALL retry after 60 seconds
2. WHEN the Notification_API returns an error status code, THE Notification_Poller SHALL display the previous notification state
3. THE Notification_Poller SHALL log errors to the browser console for debugging
4. WHEN three consecutive polling requests fail, THE Notification_Poller SHALL display a warning message in the Notification_Dropdown
5. WHEN polling resumes successfully after failures, THE Notification_Poller SHALL clear the warning message
6. THE Notification_API SHALL return detailed error messages in JSON format for client-side error handling
