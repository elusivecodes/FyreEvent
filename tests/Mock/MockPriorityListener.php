<?php
declare(strict_types=1);

namespace Tests\Mock;

use Fyre\Event\Event;
use Fyre\Event\EventListenerInterface;
use Fyre\Event\EventManager;

class MockPriorityListener implements EventListenerInterface
{
    protected mixed $result = null;

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function implementedEvents(): array
    {
        return [
            'test' => [
                'callback' => 'setResult',
                'priority' => EventManager::PRIORITY_HIGH,
            ],
        ];
    }

    public function setResult(Event $event, mixed $result): void
    {
        $event->stopPropagation();

        $this->result = $result;
    }
}
