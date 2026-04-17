# Implementation Plan: OOP Design Patterns Refactor

## Overview

This implementation refactors the payment and notification systems to implement Factory Pattern for payment gateway instantiation and Observer Pattern for event-driven notifications. Additionally, it adds XML-based AJAX endpoints for product search to complement existing JSON APIs.

The implementation is organized into three main phases: Factory Pattern implementation, Observer Pattern implementation, and XML Search implementation, followed by integration and testing.

## Tasks

- [x] 1. Implement Factory Pattern for Payment Gateway Creation
  - [x] 1.1 Create PaymentGatewayFactory class
    - Create `app/services/payment/PaymentGatewayFactory.php`
    - Implement static `create()` method that accepts payment method string
    - Add gateway mapping array (COD, CHUYEN_KHOAN, VIETQR, PAYPAL)
    - Implement file existence validation before instantiation
    - Return null for invalid inputs (null, empty string, unknown method)
    - Ensure returned instances implement PaymentGatewayInterface
    - _Requirements: 1.1, 1.2, 1.3, 1.5_
  
  - [ ]* 1.2 Write unit tests for PaymentGatewayFactory
    - Test valid payment methods return correct gateway instances
    - Test invalid payment methods return null
    - Test null input returns null
    - Test empty string returns null
    - Test unknown payment method returns null
    - Test returned instances implement PaymentGatewayInterface
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

- [x] 2. Refactor PaymentService to use Factory Pattern
  - [x] 2.1 Update PaymentService to use PaymentGatewayFactory
    - Replace `getGatewayInstance()` calls with `PaymentGatewayFactory::create()`
    - Update method to use factory for gateway instantiation
    - Maintain backward compatibility with existing payment flows
    - _Requirements: 1.4_
  
  - [ ]* 2.2 Write integration tests for PaymentService with Factory
    - Test PaymentService uses factory to create gateways
    - Test payment processing with factory-created gateways
    - Test factory integration doesn't break existing payment flows
    - _Requirements: 1.4_

- [x] 3. Checkpoint - Verify Factory Pattern implementation
  - Ensure all tests pass, verify payment flows work correctly, ask the user if questions arise.

- [x] 4. Implement Observer Pattern Infrastructure
  - [x] 4.1 Create ObserverInterface
    - Create `app/services/events/ObserverInterface.php`
    - Define `update(string $eventType, array $eventData): void` method
    - Add PHPDoc comments for interface contract
    - _Requirements: 2.5_
  
  - [x] 4.2 Create EventManager class
    - Create `app/services/events/EventManager.php`
    - Implement `attach(ObserverInterface $observer): void` method
    - Implement `detach(ObserverInterface $observer): void` method
    - Implement `notify(string $eventType, array $eventData): int` method
    - Add observer exception handling with error logging
    - Return count of successfully notified observers
    - Ensure one observer failure doesn't affect others
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 8.1, 8.2, 8.3, 8.4, 8.5_
  
  - [ ]* 4.3 Write unit tests for EventManager
    - Test attach adds observer to list
    - Test detach removes observer from list
    - Test notify calls update on all observers
    - Test observer exception doesn't stop other notifications
    - Test notify returns correct success count
    - Test multiple observers receive same event data
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ] 5. Implement OrderObserver for Notification Creation
  - [x] 5.1 Create OrderObserver class
    - Create `app/services/events/OrderObserver.php`
    - Implement ObserverInterface
    - Add constructor accepting NotificationService dependency
    - Implement `update()` method to handle 'order_created' event
    - Extract order_id and timestamp from event data
    - Create notification via NotificationService
    - Add error logging for notification creation failures
    - _Requirements: 2.5, 2.6, 2.7, 2.8_
  
  - [ ]* 5.2 Write unit tests for OrderObserver
    - Test update creates notification for order_created event
    - Test update extracts order_id from event data
    - Test update extracts timestamp from event data
    - Test update handles missing event data gracefully
    - Test update logs errors on notification failure
    - _Requirements: 2.6, 2.7, 2.8_

- [x] 6. Integrate Observer Pattern with Order Creation
  - [x] 6.1 Update ThanhToanController to use EventManager
    - Initialize EventManager in controller
    - Attach OrderObserver with NotificationService dependency
    - Trigger 'order_created' event after successful order creation
    - Pass order_id, timestamp, user_id, and total_amount in event data
    - Ensure notification creation doesn't block order processing
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_
  
  - [ ]* 6.2 Write integration tests for Observer Pattern
    - Test order creation triggers event notification
    - Test OrderObserver receives event and creates notification
    - Test notification appears in admin panel
    - Test observer failure doesn't block order creation
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

- [x] 7. Checkpoint - Verify Observer Pattern implementation
  - Ensure all tests pass, verify notifications are created on order creation, ask the user if questions arise.

- [x] 8. Implement XML Search Endpoint
  - [x] 8.1 Add timKiemXML method to TimKiemController
    - Add `timKiemXML()` method to `app/controllers/client/TimKiemController.php`
    - Accept 'q' query parameter for search keyword
    - Query SanPham model for matching products
    - Build XML document with proper structure (declaration, root element, products)
    - Set Content-Type header to "application/xml; charset=utf-8"
    - Escape special XML characters using htmlspecialchars
    - Return empty products list for no results
    - Return error element for exceptions with error logging
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7_
  
  - [x] 8.2 Add XML search route
    - Add route `/san-pham/tim-kiem-xml` to `app/routes/client/client.php`
    - Map route to `TimKiemController@timKiemXML`
    - _Requirements: 3.1_
  
  - [ ]* 8.3 Write unit tests for XML search endpoint
    - Test XML endpoint returns valid XML
    - Test XML contains correct product data
    - Test XML structure matches schema
    - Test special character escaping
    - Test empty results handling
    - Test error handling
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7_

- [x] 9. Implement Client-Side XML Search Integration
  - [x] 9.1 Create XML search JavaScript file
    - Create `public/assets/client/js/xml-search.js`
    - Listen to input events on search field
    - Implement 300ms debouncing for requests
    - Send XMLHttpRequest to `/san-pham/tim-kiem-xml` endpoint
    - Parse XML response using DOMParser
    - Check for parse errors and handle gracefully
    - Extract product elements from XML
    - Render products in search results container
    - Display loading indicator during request
    - Clear results when input is empty
    - Log errors and show fallback message on parse failure
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_
  
  - [x] 9.2 Integrate XML search script into search UI
    - Include `xml-search.js` in relevant view templates
    - Add search results container element
    - Add loading indicator element
    - Ensure script initializes on page load
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_
  
  - [ ]* 9.3 Write integration tests for client-side XML search
    - Test client-side script parses XML correctly
    - Test search results render in UI
    - Test debouncing prevents excessive requests
    - Test loading indicator appears and disappears
    - Test error handling displays fallback message
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_

- [x] 10. Final Integration and Testing
  - [x] 10.1 End-to-end testing of all patterns
    - Test Factory Pattern with all payment methods
    - Test Observer Pattern with order creation flow
    - Test XML search with various keywords
    - Test error handling across all components
    - Verify backward compatibility with existing features
    - _Requirements: All_
  
  - [x] 10.2 Add logging and monitoring
    - Add logging statements to PaymentGatewayFactory
    - Add logging statements to EventManager
    - Add logging statements to OrderObserver
    - Add logging statements to TimKiemController XML method
    - Verify logs are written correctly
    - _Requirements: All_

- [x] 11. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, verify all features work end-to-end, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Factory Pattern centralizes payment gateway creation logic
- Observer Pattern enables event-driven notifications without tight coupling
- XML search provides flexible integration options for diverse clients
- All implementations maintain backward compatibility with existing code
