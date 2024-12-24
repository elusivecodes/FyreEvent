<?php
declare(strict_types=1);

namespace Fyre\Event;

/**
 * EventListenerInterface
 */
interface EventListenerInterface
{
    /**
     * Get the implemented events.
     *
     * @return array The implemented events.
     */
    public function implementedEvents(): array;
}
