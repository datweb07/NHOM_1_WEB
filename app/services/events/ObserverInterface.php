<?php

namespace App\Services\Events;

interface ObserverInterface
{
    public function update(string $eventType, array $eventData): void;
}
