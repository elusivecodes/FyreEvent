<?php
declare(strict_types=1);

namespace Tests\Mock;

use Fyre\Event\Event;
use Fyre\Event\EventListenerInterface;

class MockListener implements EventListenerInterface
{
    protected mixed $result = null;

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function implementedEvents(): array
    {
        return [
            'test' => 'setResult',
        ];
    }

    public function setResult(Event $event, mixed $result): void
    {
        $this->result = $result;
    }
}
