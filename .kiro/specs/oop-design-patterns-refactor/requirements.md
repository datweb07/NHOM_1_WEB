# Requirements Document

## Introduction

This feature refactors the existing codebase to enhance OOP principles and design patterns implementation. The system currently demonstrates Encapsulation, Inheritance, Abstraction, and Polymorphism through BaseModel, NguoiDung abstract class, and payment gateway interfaces. It also implements Singleton (CloudinaryService) and Strategy (PaymentGatewayInterface) patterns.

This refactor adds Factory Pattern for payment gateway instantiation, Observer Pattern for order event notifications, and XML-based AJAX endpoints to complement existing JSON APIs.

## Glossary

- **Payment_Gateway_Factory**: A factory class that creates payment gateway instances based on payment method
- **Event_Manager**: A subject that manages observers and notifies them of events
- **Order_Observer**: An observer that listens to order events and creates notifications
- **Search_Controller**: Controller handling product search with XML response capability
- **XML_Response**: Product search results formatted as XML document
- **Payment_Service**: Service managing payment transactions and gateway selection
- **Notification_Service**: Service managing admin notifications for system events

## Requirements

### Requirement 1: Factory Pattern for Payment Gateway Creation

**User Story:** As a developer, I want a dedicated factory class for creating payment gateways, so that gateway instantiation logic is centralized and follows the Factory Pattern.

#### Acceptance Criteria

1. THE Payment_Gateway_Factory SHALL provide a static create method that accepts payment method string and returns PaymentGatewayInterface instance
2. WHEN an invalid payment method is provided, THE Payment_Gateway_Factory SHALL return null
3. WHEN a valid payment method is provided (COD, CHUYEN_KHOAN, VIETQR, PAYPAL), THE Payment_Gateway_Factory SHALL instantiate and return the corresponding gateway class
4. THE Payment_Service SHALL use Payment_Gateway_Factory instead of getGatewayInstance method for gateway creation
5. THE Payment_Gateway_Factory SHALL handle gateway file existence validation before instantiation

### Requirement 2: Observer Pattern for Order Notifications

**User Story:** As a system administrator, I want automatic notifications when orders are created, so that I am immediately informed of new orders without manual checking.

#### Acceptance Criteria

1. THE Event_Manager SHALL maintain a list of registered observers
2. THE Event_Manager SHALL provide attach method to register observers
3. THE Event_Manager SHALL provide detach method to unregister observers
4. WHEN an event is triggered, THE Event_Manager SHALL notify all registered observers
5. THE Order_Observer SHALL implement ObserverInterface with update method
6. WHEN Order_Observer receives order creation event, THE Order_Observer SHALL create notification via Notification_Service
7. THE Order_Observer SHALL extract order ID and timestamp from event data
8. THE Event_Manager SHALL pass event type and event data to observer update method

### Requirement 3: XML Response for Product Search

**User Story:** As a client application developer, I want product search results in XML format, so that I can integrate with XML-based systems.

#### Acceptance Criteria

1. THE Search_Controller SHALL provide timKiemXML method that accepts search keyword via query parameter
2. WHEN timKiemXML is called, THE Search_Controller SHALL query products matching the keyword
3. THE Search_Controller SHALL format search results as valid XML document with proper structure
4. THE XML_Response SHALL include product ID, name, price, image URL, and stock status for each product
5. THE Search_Controller SHALL set Content-Type header to "application/xml; charset=utf-8"
6. WHEN no products match the search, THE XML_Response SHALL return empty products list with success status
7. WHEN an error occurs during search, THE XML_Response SHALL return error element with message

### Requirement 4: Client-Side AJAX for XML Search

**User Story:** As a user, I want live search results as I type, so that I can quickly find products without page reload.

#### Acceptance Criteria

1. THE Search_AJAX_Script SHALL send XMLHttpRequest to /san-pham/tim-kiem-xml endpoint
2. WHEN user types in search input, THE Search_AJAX_Script SHALL debounce requests by 300ms
3. THE Search_AJAX_Script SHALL parse XML response using DOMParser
4. THE Search_AJAX_Script SHALL extract product elements from XML and render them in search results container
5. WHEN XML parsing fails, THE Search_AJAX_Script SHALL log error and display fallback message
6. THE Search_AJAX_Script SHALL display loading indicator while request is in progress
7. WHEN search input is empty, THE Search_AJAX_Script SHALL clear search results

### Requirement 5: Observer Pattern Integration with Order Creation

**User Story:** As a developer, I want order creation to automatically trigger notifications, so that the Observer Pattern is demonstrated in production code.

#### Acceptance Criteria

1. THE Don_Hang_Controller SHALL initialize Event_Manager with Order_Observer attached
2. WHEN a new order is successfully created, THE Don_Hang_Controller SHALL trigger "order_created" event via Event_Manager
3. THE Event_Manager SHALL pass order ID and creation timestamp to all observers
4. THE Order_Observer SHALL receive event notification and create admin notification record
5. THE notification creation SHALL not block order creation process
6. IF notification creation fails, THE system SHALL log error but continue order processing

### Requirement 6: Factory Pattern Testing and Validation

**User Story:** As a quality assurance engineer, I want the Factory Pattern to handle edge cases correctly, so that the system is robust against invalid inputs.

#### Acceptance Criteria

1. WHEN Payment_Gateway_Factory receives null payment method, THE Payment_Gateway_Factory SHALL return null
2. WHEN Payment_Gateway_Factory receives empty string payment method, THE Payment_Gateway_Factory SHALL return null
3. WHEN Payment_Gateway_Factory receives unknown payment method, THE Payment_Gateway_Factory SHALL return null
4. WHEN Payment_Gateway_Factory creates gateway successfully, THE returned instance SHALL implement PaymentGatewayInterface
5. THE Payment_Gateway_Factory SHALL not throw exceptions for invalid inputs

### Requirement 7: XML Response Structure and Validation

**User Story:** As an API consumer, I want well-formed XML responses, so that I can reliably parse search results.

#### Acceptance Criteria

1. THE XML_Response SHALL include XML declaration with version 1.0 and UTF-8 encoding
2. THE XML_Response SHALL have root element named "search_results"
3. THE search_results element SHALL contain "success" element with boolean value
4. THE search_results element SHALL contain "count" element with integer product count
5. THE search_results element SHALL contain "products" element wrapping all product elements
6. EACH product element SHALL contain child elements: id, name, price, image_url, stock_status
7. THE XML_Response SHALL escape special characters in text content (ampersand, less-than, greater-than, quotes)

### Requirement 8: Observer Pattern Error Handling

**User Story:** As a system administrator, I want observer failures to be logged without breaking the application, so that the system remains stable even when notifications fail.

#### Acceptance Criteria

1. WHEN an observer update method throws exception, THE Event_Manager SHALL catch the exception
2. THE Event_Manager SHALL log observer exception with observer class name and error message
3. THE Event_Manager SHALL continue notifying remaining observers after one observer fails
4. THE Event_Manager SHALL not propagate observer exceptions to the event trigger caller
5. THE Event_Manager SHALL return count of successfully notified observers

