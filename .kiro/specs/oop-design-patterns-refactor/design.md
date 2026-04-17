# Design Document: OOP Design Patterns Refactor

## Overview

This design refactors the existing payment and notification systems to implement two classic OOP design patterns: **Factory Pattern** for payment gateway instantiation and **Observer Pattern** for event-driven notifications. Additionally, it introduces XML-based AJAX endpoints to complement the existing JSON API infrastructure.

### Goals

1. **Centralize Gateway Creation**: Replace scattered gateway instantiation logic with a dedicated Factory class
2. **Event-Driven Notifications**: Implement Observer Pattern to automatically notify admins of order events
3. **XML API Support**: Add XML response format for product search to support diverse client integrations
4. **Maintain Backward Compatibility**: Ensure existing payment and notification flows continue working

### Non-Goals

- Refactoring the entire codebase to use design patterns
- Replacing existing JSON APIs
- Modifying database schema
- Changing payment gateway implementations

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Client Layer                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ Order Form   │  │ Search Input │  │ Admin Panel  │      │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘      │
└─────────┼──────────────────┼──────────────────┼─────────────┘
          │                  │                  │
          │ HTTP POST        │ AJAX XML         │ HTTP GET
          │                  │                  │
┌─────────▼──────────────────▼──────────────────▼─────────────┐
│                   Controller Layer                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ ThanhToan    │  │ TimKiem      │  │ Notification │      │
│  │ Controller   │  │ Controller   │  │ Controller   │      │
│  └──────┬───────┘  └──────┬───────┘  └──────────────┘      │
└─────────┼──────────────────┼──────────────────────────────────┘
          │                  │
          │ uses             │ queries
          │                  │
┌─────────▼──────────────────▼──────────────────────────────────┐
│                    Service Layer                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │ Payment      │  │ Event        │  │ Notification │       │
│  │ Service      │  │ Manager      │  │ Service      │       │
│  └──────┬───────┘  └──────┬───────┘  └──────────────┘       │
│         │                  │                                  │
│         │ creates          │ notifies                         │
│         │                  │                                  │
│  ┌──────▼───────┐  ┌──────▼───────┐                         │
│  │ Payment      │  │ Order        │                         │
│  │ Gateway      │  │ Observer     │                         │
│  │ Factory      │  │              │                         │
│  └──────┬───────┘  └──────────────┘                         │
└─────────┼──────────────────────────────────────────────────────┘
          │
          │ instantiates
          │
┌─────────▼──────────────────────────────────────────────────────┐
│                  Gateway Implementations                        │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐      │
│  │ VNPay    │  │ VietQR   │  │ PayPal   │  │ COD      │      │
│  │ Gateway  │  │ Gateway  │  │ Gateway  │  │ Handler  │      │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘      │
└─────────────────────────────────────────────────────────────────┘
```

### Component Interaction Flow

#### Payment Gateway Creation Flow (Factory Pattern)

```
PaymentService
    │
    ├─> PaymentGatewayFactory::create('CHUYEN_KHOAN')
    │       │
    │       ├─> Validate payment method
    │       ├─> Check gateway file exists
    │       └─> Instantiate VNPayGateway
    │
    └─> Use gateway instance
```

#### Order Notification Flow (Observer Pattern)

```
ThanhToanController
    │
    ├─> Create order successfully
    │
    ├─> EventManager::notify('order_created', $orderData)
    │       │
    │       ├─> Loop through observers
    │       │
    │       └─> OrderObserver::update('order_created', $orderData)
    │               │
    │               └─> NotificationService::createNotification()
    │
    └─> Return success response
```

#### XML Search Flow

```
Client (JavaScript)
    │
    ├─> User types in search input
    │
    ├─> Debounce 300ms
    │
    ├─> XMLHttpRequest to /san-pham/tim-kiem-xml?q=keyword
    │
    └─> TimKiemController::timKiemXML()
            │
            ├─> Query products from database
            │
            ├─> Build XML document
            │
            ├─> Set Content-Type: application/xml
            │
            └─> Return XML response
                    │
                    └─> Client parses XML and renders results
```

## Components and Interfaces

### 1. PaymentGatewayFactory (Factory Pattern)

**Location**: `app/services/payment/PaymentGatewayFactory.php`

**Responsibility**: Centralized creation of payment gateway instances

**Interface**:
```php
class PaymentGatewayFactory
{
    /**
     * Create payment gateway instance based on payment method
     * 
     * @param string|null $paymentMethod Payment method code
     * @return PaymentGatewayInterface|null Gateway instance or null if invalid
     */
    public static function create(?string $paymentMethod): ?PaymentGatewayInterface;
}
```

**Implementation Details**:
- Static factory method for stateless gateway creation
- Maps payment method codes to gateway class names
- Validates gateway file existence before instantiation
- Returns null for invalid inputs (null, empty string, unknown method)
- No exception throwing for invalid inputs

**Gateway Mapping**:
```php
[
    'COD' => 'CODHandler',
    'CHUYEN_KHOAN' => 'VNPayGateway',
    'VIETQR' => 'VietQRGateway',
    'PAYPAL' => 'PayPalGateway'
]
```

### 2. EventManager (Observer Pattern - Subject)

**Location**: `app/services/events/EventManager.php`

**Responsibility**: Manage observers and notify them of events

**Interface**:
```php
class EventManager
{
    /**
     * Attach an observer to the event manager
     * 
     * @param ObserverInterface $observer Observer to attach
     * @return void
     */
    public function attach(ObserverInterface $observer): void;
    
    /**
     * Detach an observer from the event manager
     * 
     * @param ObserverInterface $observer Observer to detach
     * @return void
     */
    public function detach(ObserverInterface $observer): void;
    
    /**
     * Notify all observers of an event
     * 
     * @param string $eventType Type of event (e.g., 'order_created')
     * @param array $eventData Event data to pass to observers
     * @return int Count of successfully notified observers
     */
    public function notify(string $eventType, array $eventData): int;
}
```

**Implementation Details**:
- Maintains array of ObserverInterface instances
- Iterates through observers on notify()
- Catches and logs observer exceptions without propagating
- Returns count of successfully notified observers
- Continues notifying remaining observers if one fails

### 3. ObserverInterface

**Location**: `app/services/events/ObserverInterface.php`

**Responsibility**: Define contract for event observers

**Interface**:
```php
interface ObserverInterface
{
    /**
     * Receive event notification
     * 
     * @param string $eventType Type of event
     * @param array $eventData Event data
     * @return void
     */
    public function update(string $eventType, array $eventData): void;
}
```

### 4. OrderObserver (Observer Pattern - Concrete Observer)

**Location**: `app/services/events/OrderObserver.php`

**Responsibility**: Listen to order events and create notifications

**Interface**:
```php
class OrderObserver implements ObserverInterface
{
    /**
     * Constructor
     * 
     * @param NotificationService $notificationService Notification service instance
     */
    public function __construct(NotificationService $notificationService);
    
    /**
     * Handle event notification
     * 
     * @param string $eventType Type of event
     * @param array $eventData Event data containing order_id and timestamp
     * @return void
     */
    public function update(string $eventType, array $eventData): void;
}
```

**Implementation Details**:
- Receives NotificationService via constructor injection
- Handles 'order_created' event type
- Extracts order_id and timestamp from event data
- Creates notification via NotificationService
- Logs errors if notification creation fails

### 5. TimKiemController XML Endpoint

**Location**: `app/controllers/client/TimKiemController.php`

**New Method**: `timKiemXML()`

**Responsibility**: Provide product search results in XML format

**Implementation Details**:
- Accepts 'q' query parameter for search keyword
- Queries SanPham model for matching products
- Builds XML document with proper structure
- Sets Content-Type header to "application/xml; charset=utf-8"
- Escapes special XML characters in content
- Returns empty products list for no results
- Returns error element for exceptions

### 6. Client-Side XML Search Script

**Location**: `public/assets/client/js/xml-search.js`

**Responsibility**: Handle live search with XML parsing

**Implementation Details**:
- Listens to input events on search field
- Debounces requests by 300ms
- Sends XMLHttpRequest to `/san-pham/tim-kiem-xml`
- Parses XML response using DOMParser
- Extracts product elements and renders HTML
- Displays loading indicator during request
- Clears results when input is empty
- Logs errors and shows fallback message on parse failure

## Data Models

### XML Response Structure

```xml
<?xml version="1.0" encoding="UTF-8"?>
<search_results>
    <success>true</success>
    <count>2</count>
    <products>
        <product>
            <id>123</id>
            <name>iPhone 15 Pro Max</name>
            <price>29990000</price>
            <image_url>https://example.com/image.jpg</image_url>
            <stock_status>IN_STOCK</stock_status>
        </product>
        <product>
            <id>124</id>
            <name>iPhone 15 Pro</name>
            <price>25990000</price>
            <image_url>https://example.com/image2.jpg</image_url>
            <stock_status>LOW_STOCK</stock_status>
        </product>
    </products>
</search_results>
```

### Event Data Structure

```php
[
    'order_id' => 123,
    'timestamp' => '2024-01-15 10:30:00',
    'user_id' => 456,
    'total_amount' => 29990000
]
```

### Notification Data Structure

```php
[
    'group' => 'orders',
    'type' => 'new_order_pending',
    'count' => 1,
    'message' => 'Đơn hàng #123 chờ duyệt',
    'url_redirect' => '/admin/don-hang/chi-tiet?id=123',
    'priority' => 'high',
    'icon' => 'bi-cart-check',
    'timestamp' => '2024-01-15 10:30:00'
]
```

## Error Handling

### PaymentGatewayFactory Error Handling

**Invalid Input Scenarios**:
1. Null payment method → Return null
2. Empty string payment method → Return null
3. Unknown payment method → Return null
4. Gateway file not found → Return null

**No Exceptions**: Factory returns null for all error cases to maintain backward compatibility

### EventManager Error Handling

**Observer Exception Handling**:
```php
try {
    $observer->update($eventType, $eventData);
    $successCount++;
} catch (Exception $e) {
    error_log("[EventManager] Observer " . get_class($observer) . 
              " failed: " . $e->getMessage());
    // Continue with next observer
}
```

**Guarantees**:
- One observer failure doesn't affect others
- All observers receive notification attempt
- Exceptions are logged but not propagated
- Returns count of successful notifications

### XML Search Error Handling

**Server-Side**:
```php
try {
    $products = $this->sanPhamModel->timKiem($keyword);
    // Build XML
} catch (Exception $e) {
    error_log("[TimKiemController] XML search failed: " . $e->getMessage());
    // Return error XML
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<search_results>';
    echo '<success>false</success>';
    echo '<error>' . htmlspecialchars($e->getMessage()) . '</error>';
    echo '</search_results>';
}
```

**Client-Side**:
```javascript
try {
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(xhr.responseText, 'application/xml');
    
    // Check for parse errors
    const parserError = xmlDoc.querySelector('parsererror');
    if (parserError) {
        throw new Error('XML parsing failed');
    }
    
    // Process XML
} catch (error) {
    console.error('[XML Search] Parse error:', error);
    displayFallbackMessage('Không thể tải kết quả tìm kiếm');
}
```

## Testing Strategy

### Unit Testing

**PaymentGatewayFactory Tests**:
- Test valid payment methods return correct gateway instances
- Test invalid payment methods return null
- Test null input returns null
- Test empty string returns null
- Test unknown payment method returns null
- Test returned instances implement PaymentGatewayInterface

**EventManager Tests**:
- Test attach adds observer to list
- Test detach removes observer from list
- Test notify calls update on all observers
- Test observer exception doesn't stop other notifications
- Test notify returns correct success count
- Test multiple observers receive same event data

**OrderObserver Tests**:
- Test update creates notification for order_created event
- Test update extracts order_id from event data
- Test update extracts timestamp from event data
- Test update handles missing event data gracefully
- Test update logs errors on notification failure

### Integration Testing

**Payment Flow with Factory**:
- Test PaymentService uses factory to create gateways
- Test payment processing with factory-created gateways
- Test factory integration doesn't break existing payment flows

**Observer Pattern Integration**:
- Test order creation triggers event notification
- Test OrderObserver receives event and creates notification
- Test notification appears in admin panel
- Test observer failure doesn't block order creation

**XML Search Integration**:
- Test XML endpoint returns valid XML
- Test XML contains correct product data
- Test client-side script parses XML correctly
- Test search results render in UI
- Test debouncing prevents excessive requests

### Manual Testing

**Factory Pattern**:
1. Create order with each payment method
2. Verify correct gateway is instantiated
3. Verify payment processing completes successfully

**Observer Pattern**:
1. Create new order
2. Check admin notification panel
3. Verify notification appears immediately
4. Verify notification contains correct order ID

**XML Search**:
1. Type in search input
2. Verify results appear after 300ms
3. Verify results match search keyword
4. Verify XML structure is valid
5. Test with special characters (ampersand, quotes)

## Performance Considerations

### Factory Pattern Performance

**Optimization**: Static factory method with no state
- No object instantiation overhead
- Direct class mapping lookup O(1)
- File existence check only when needed

**Impact**: Negligible performance impact compared to existing getGatewayInstance method

### Observer Pattern Performance

**Optimization**: Asynchronous notification consideration
- Current implementation: Synchronous notification during order creation
- Future enhancement: Queue-based asynchronous notification
- Trade-off: Immediate notification vs. order creation speed

**Impact**: 
- Adds ~10-50ms per observer to order creation
- Acceptable for current scale (1-2 observers)
- Consider async queue if observers exceed 5

### XML Search Performance

**Optimization**: 
- Debouncing reduces server requests
- Limit search results to 20 products
- Use existing database indexes on product name

**Impact**:
- XML generation: ~5-10ms for 20 products
- Network transfer: Similar to JSON (slightly larger)
- Client parsing: ~2-5ms with DOMParser

**Caching Strategy**: Consider Redis caching for popular search terms

## Security Considerations

### XML Injection Prevention

**Input Sanitization**:
```php
// Escape special XML characters
function escapeXml($text) {
    return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}
```

**Protected Characters**:
- `&` → `&amp;`
- `<` → `&lt;`
- `>` → `&gt;`
- `"` → `&quot;`
- `'` → `&apos;`

### Observer Pattern Security

**Injection Prevention**:
- Validate event data before passing to observers
- Sanitize order IDs and timestamps
- Use parameterized queries in NotificationService

**Access Control**:
- Verify admin authentication before showing notifications
- Ensure observers can't be attached by unauthorized code

### Factory Pattern Security

**Class Instantiation Safety**:
- Whitelist allowed gateway classes
- Validate class implements PaymentGatewayInterface
- No dynamic class name construction from user input

## Migration Strategy

### Phase 1: Factory Pattern Implementation

1. Create PaymentGatewayFactory class
2. Add unit tests for factory
3. Update PaymentService to use factory
4. Remove old getGatewayInstance method
5. Test payment flows end-to-end

### Phase 2: Observer Pattern Implementation

1. Create ObserverInterface
2. Create EventManager class
3. Create OrderObserver class
4. Add unit tests for observer components
5. Integrate EventManager into ThanhToanController
6. Test notification creation

### Phase 3: XML Search Implementation

1. Add timKiemXML method to TimKiemController
2. Create XML response builder helper
3. Add route for XML endpoint
4. Create client-side XML search script
5. Integrate script into search UI
6. Test XML parsing and rendering

### Rollback Plan

**Factory Pattern**: 
- Revert PaymentService to use getGatewayInstance
- Keep factory class for future use

**Observer Pattern**:
- Remove EventManager::notify() calls
- Keep observer classes for future use
- Notifications continue via existing direct calls

**XML Search**:
- Remove XML endpoint route
- Remove client-side script inclusion
- Existing JSON search continues working

## Deployment Considerations

### File Additions

```
app/services/payment/PaymentGatewayFactory.php
app/services/events/EventManager.php
app/services/events/ObserverInterface.php
app/services/events/OrderObserver.php
public/assets/client/js/xml-search.js
```

### File Modifications

```
app/services/payment/PaymentService.php
app/controllers/client/TimKiemController.php
app/controllers/client/ThanhToanController.php
app/routes/client/client.php
```

### Configuration Changes

**Route Addition**:
```php
// app/routes/client/client.php
$router->get('/san-pham/tim-kiem-xml', 'TimKiemController@timKiemXML');
```

### Backward Compatibility

- Existing payment flows continue working
- Existing notification system remains functional
- JSON search API unchanged
- No database schema changes required

## Monitoring and Observability

### Logging Strategy

**Factory Pattern Logging**:
```php
error_log("[PaymentGatewayFactory] Creating gateway for method: {$paymentMethod}");
error_log("[PaymentGatewayFactory] Gateway file not found: {$gatewayPath}");
```

**Observer Pattern Logging**:
```php
error_log("[EventManager] Notifying {$observerCount} observers of {$eventType}");
error_log("[EventManager] Observer {$observerClass} failed: {$errorMessage}");
error_log("[OrderObserver] Created notification for order {$orderId}");
```

**XML Search Logging**:
```php
error_log("[TimKiemController] XML search for keyword: {$keyword}");
error_log("[TimKiemController] XML search returned {$count} products");
error_log("[TimKiemController] XML search failed: {$errorMessage}");
```

### Metrics to Track

1. **Factory Pattern**:
   - Gateway creation success rate
   - Invalid payment method frequency
   - Gateway instantiation time

2. **Observer Pattern**:
   - Event notification count
   - Observer failure rate
   - Notification creation latency

3. **XML Search**:
   - XML endpoint request count
   - XML parsing error rate
   - Search response time

### Alerting

**Critical Alerts**:
- Factory returns null for valid payment method (indicates missing gateway file)
- All observers fail for an event (indicates systemic issue)
- XML endpoint error rate exceeds 5%

**Warning Alerts**:
- Observer failure rate exceeds 10%
- XML search response time exceeds 500ms
- Factory called with unknown payment method (indicates integration issue)

## Future Enhancements

### Factory Pattern Enhancements

1. **Gateway Configuration**: Pass configuration to factory for gateway initialization
2. **Gateway Caching**: Cache gateway instances for reuse within request
3. **Gateway Health Check**: Factory validates gateway health before returning

### Observer Pattern Enhancements

1. **Async Observers**: Queue-based asynchronous notification
2. **Event Filtering**: Observers subscribe to specific event types
3. **Event History**: Store event log for debugging and replay
4. **Priority Observers**: Execute high-priority observers first

### XML Search Enhancements

1. **XML Caching**: Cache XML responses for popular searches
2. **XML Pagination**: Support paginated XML results
3. **XML Filtering**: Add filter parameters (category, price range)
4. **XML Schema**: Provide XSD schema for validation

## Conclusion

This design implements two fundamental OOP design patterns (Factory and Observer) while adding XML API support. The Factory Pattern centralizes payment gateway creation, the Observer Pattern enables event-driven notifications, and the XML endpoint provides flexible integration options.

The design maintains backward compatibility, includes comprehensive error handling, and provides clear migration and rollback strategies. Performance impact is minimal, and security considerations are addressed through input sanitization and access control.

Implementation follows SOLID principles: Single Responsibility (each class has one purpose), Open/Closed (extensible via new observers/gateways), Liskov Substitution (gateways implement common interface), Interface Segregation (focused interfaces), and Dependency Inversion (depend on abstractions).
