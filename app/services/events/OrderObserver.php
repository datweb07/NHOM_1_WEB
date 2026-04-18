<?php

namespace App\Services\Events;

require_once __DIR__ . '/ObserverInterface.php';
require_once __DIR__ . '/../notification/NotificationService.php';

class OrderObserver implements ObserverInterface
{
    private $notificationService; 

    public function __construct($notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function update(string $eventType, array $eventData): void
    {
        if ($eventType !== 'order_created') {
            error_log("[OrderObserver] Ignoring event type: {$eventType}");
            return;
        }

        try {
            $orderId = $eventData['order_id'] ?? null;
            $timestamp = $eventData['timestamp'] ?? date('Y-m-d H:i:s');
            $userId = $eventData['user_id'] ?? null;
            $totalAmount = $eventData['total_amount'] ?? 0;

            if ($orderId === null) {
                error_log("[OrderObserver] Missing order_id in event data");
                return;
            }

            error_log("[OrderObserver] Processing order_created event for order #{$orderId}");

            error_log("[OrderObserver] Order #{$orderId} notification will be available in admin panel");
            error_log("[OrderObserver] Order details - User: {$userId}, Amount: {$totalAmount}, Time: {$timestamp}");

        } catch (\Exception $e) {
            error_log("[OrderObserver] Failed to process order_created event: " . $e->getMessage());
        }
    }
}
