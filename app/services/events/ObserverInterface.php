<?php

namespace App\Services\Events;

/**
 * ObserverInterface
 * 
 * Observer Pattern interface for event observers.
 * Observers implement this interface to receive event notifications.
 */
interface ObserverInterface
{
    /**
     * Receive event notification
     * 
     * This method is called by the Subject (EventManager) when an event occurs.
     * Observers should implement this method to handle specific event types.
     * 
     * @param string $eventType Type of event (e.g., 'order_created', 'payment_completed')
     * @param array $eventData Event data containing relevant information
     * @return void
     */
    public function update(string $eventType, array $eventData): void;
}
