<?php

namespace App\Services\Events;

require_once __DIR__ . '/ObserverInterface.php';

class EventManager
{
    private array $observers = [];

    public function attach(ObserverInterface $observer): void
    {
        $observerClass = get_class($observer);
        
        foreach ($this->observers as $existingObserver) {
            if (get_class($existingObserver) === $observerClass) {
                error_log("[EventManager] Observer {$observerClass} already attached");
                return;
            }
        }
        
        $this->observers[] = $observer;
        error_log("[EventManager] Attached observer: {$observerClass}");
    }

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
            }
        }
        
        error_log("[EventManager] Notification complete. Success: {$successCount}/{$observerCount}");
        return $successCount;
    }
}
