<?php
declare(strict_types=1);

namespace Fyre\Event;

/**
 * EventDispatcherTrait
 */
trait EventDispatcherTrait
{
    protected EventManager $eventManager;

    /**
     * Dispatch an Event.
     *
     * @param string $name The Event name.
     * @param array $data The Event data.
     * @param bool $cancelable Whether the Event is cancelable.
     * @param object|null $subject The Event subject.
     */
    public function dispatchEvent(string $name, array $data = [], bool $cancelable = true, object|null $subject = null): Event
    {
        $subject ??= $this;

        $event = new Event($name, $subject, $data, $cancelable);

        return $this->getEventManager()->dispatch($event);
    }

    /**
     * Get the EventManager.
     *
     * @return EventManager The EventManager.
     */
    public function getEventManager(): EventManager
    {
        return $this->eventManager ??= new EventManager();
    }

    /**
     * Set the EventManager.
     *
     * @param EventManager $eventManager The EventManager.
     * @return static The static class.
     */
    public function setEventManager(EventManager $eventManager): static
    {
        $this->eventManager = $eventManager;

        return $this;
    }
}
