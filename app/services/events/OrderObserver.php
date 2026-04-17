<?php

require_once __DIR__ . '/ObserverInterface.php';
require_once __DIR__ . '/../notification/NotificationService.php';

/**
 * OrderObserver
 * 
 * Observer Pattern concrete observer for order events.
 * Listens to order creation events and creates admin notifications.
 */
class OrderObserver implements ObserverInterface
{
    private NotificationService $notificationService;

    /**
     * Constructor
     * 
     * @param NotificationService $notificationService Notification service instance
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle event notification
     * 
     * Receives event notifications from EventManager and processes them.
     * Currently handles 'order_created' event type.
     * 
     * @param string $eventType Type of event (e.g., 'order_created')
     * @param array $eventData Event data containing order_id, timestamp, user_id, total_amount
     * @return void
     */
    public function update(string $eventType, array $eventData): void
    {
        // Only handle order_created events
        if ($eventType !== 'order_created') {
            error_log("[OrderObserver] Ignoring event type: {$eventType}");
            return;
        }

        try {
            // Extract order data
            $orderId = $eventData['order_id'] ?? null;
            $timestamp = $eventData['timestamp'] ?? date('Y-m-d H:i:s');
            $userId = $eventData['user_id'] ?? null;
            $totalAmount = $eventData['total_amount'] ?? 0;

            // Validate required data
            if ($orderId === null) {
                error_log("[OrderObserver] Missing order_id in event data");
                return;
            }

            error_log("[OrderObserver] Processing order_created event for order #{$orderId}");

            // Note: The NotificationService aggregateNotifications method automatically
            // detects new orders based on database queries. The notification will appear
            // when admin checks notifications since the order is in CHO_DUYET status.
            // This observer serves as a hook point for future enhancements like:
            // - Real-time push notifications
            // - Email notifications to admins
            // - SMS alerts for high-value orders
            // - Integration with external systems

            error_log("[OrderObserver] Order #{$orderId} notification will be available in admin panel");
            error_log("[OrderObserver] Order details - User: {$userId}, Amount: {$totalAmount}, Time: {$timestamp}");

        } catch (Exception $e) {
            error_log("[OrderObserver] Failed to process order_created event: " . $e->getMessage());
            // Don't rethrow - we don't want to break the order creation process
        }
    }
}
