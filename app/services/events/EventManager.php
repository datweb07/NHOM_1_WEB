<?php

namespace App\Services\Events;

require_once __DIR__ . '/ObserverInterface.php';

/**
 * EventManager
 * 
 * Observer Pattern Subject implementation.
 * Manages observers and notifies them of events.
 */
class EventManager
{
    /**
     * List of registered observers
     * @var ObserverInterface[]
     */
    private array $observers = [];

    /**
     * Attach an observer to the event manager
     * 
     * @param ObserverInterface $observer Observer to attach
     * @return void
     */
    public function attach(ObserverInterface $observer): void
    {
        $observerClass = get_class($observer);
        
        // Prevent duplicate observers
        foreach ($this->observers as $existingObserver) {
            if (get_class($existingObserver) === $observerClass) {
                error_log("[EventManager] Observer {$observerClass} already attached");
                return;
            }
        }
        
        $this->observers[] = $observer;
        error_log("[EventManager] Attached observer: {$observerClass}");
    }

    /**
     * Detach an observer from the event manager
     * 
     * @param ObserverInterface $observer Observer to detach
     * @return void
     */
    public function detach(ObserverInterface $observer): void
    {
        $observerClass = get_class($observer);
        
        foreach ($this->observers as $key => $existingObserver) {
            if (get_class($existingObserver) === $observerClass) {
                unset($this->observers[$key]);
                $this->observers = array_values($this->observers); // Re-index array
                error_log("[EventManager] Detached observer: {$observerClass}");
                return;
            }
        }
        
        error_log("[EventManager] Observer {$observerClass} not found for detachment");
    }

    /**
     * Notify all observers of an event
     * 
     * Iterates through all registered observers and calls their update method.
     * If an observer throws an exception, it is caught and logged, and the
     * notification continues to remaining observers.
     * 
     * @param string $eventType Type of event (e.g., 'order_created')
     * @param array $eventData Event data to pass to observers
     * @return int Count of successfully notified observers
     */
    public function notify(string $eventType, array $eventData): int
    {
        $observerCount = count($this->observers);
        $successCount = 0;
        
        error_log("[EventManager] Notifying {$observerCount} observers of event: {$eventType}");
        
        foreach ($this->observers as $observer) {
            $observerClass = get_class($observer);
            
            try {
                $observer->update($eventType, $eventData);
                $successCount++;
                error_log("[EventManager] Successfully notified observer: {$observerClass}");
            } catch (\Exception $e) {
                error_log("[EventManager] Observer {$observerClass} failed: " . $e->getMessage());
                // Continue notifying remaining observers
            }
        }
        
        error_log("[EventManager] Notification complete. Success: {$successCount}/{$observerCount}");
        return $successCount;
    }
}
