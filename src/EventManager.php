<?php
declare(strict_types=1);

namespace Fyre\Event;

use Closure;

use function array_key_exists;
use function array_values;
use function is_array;
use function is_string;
use function uasort;

/**
 * EventManager
 */
class EventManager
{
    public const PRIORITY_HIGH = 10;

    public const PRIORITY_LOW = 200;

    public const PRIORITY_NORMAL = 100;

    protected array $events = [];

    protected EventManager|null $parentEventManager = null;

    /**
     * New EventManager constructor.
     *
     * @param EventManager|null $parentEventManager The parent EventManager.
     */
    public function __construct(EventManager|null $parentEventManager = null)
    {
        $this->parentEventManager = $parentEventManager;
    }

    /**
     * Add an EventListener.
     *
     * @param EventListenerInterface $listener The EventListener.
     * @return EventManager The EventManager.
     */
    public function addListener(EventListenerInterface $listener): static
    {
        foreach ($listener->implementedEvents() as $name => $data) {
            [$callback, $priority] = static::normalizeListenerEvent($listener, $data);

            $this->on($name, $callback, $priority);
        }

        return $this;
    }

    /**
     * Clear all events.
     */
    public function clear(): void
    {
        $this->events = [];
    }

    /**
     * Dispatch an event.
     *
     * @param Event $event The Event.
     * @return Event The Event.
     */
    public function dispatch(Event $event): Event
    {
        $name = $event->getName();

        if (!array_key_exists($name, $this->events)) {
            return $event;
        }

        foreach ($this->events[$name] as $listener) {
            if ($event->isStopped()) {
                break;
            }

            $result = $listener['callback']($event, ...array_values($event->getData()));

            if ($result === false) {
                $event->preventDefault();
                $event->stopImmediatePropagation();
            }

            if ($result !== null) {
                $event->setResult($result);
            }
        }

        if ($this->parentEventManager && !$event->isPropagationStopped()) {
            return $this->parentEventManager->dispatch($event);
        }

        return $event;
    }

    /**
     * Determine whether an event exists.
     *
     * @param string $name The event name.
     * @return bool TRUE if the event exists, otherwise FALSE.
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->events);
    }

    /**
     * Remove an event.
     *
     * @param string $name The event name.
     * @param callable|null $callback The callback.
     * @return EventManager The EventManager.
     */
    public function off(string $name, callable|null $callback = null): static
    {
        if (!array_key_exists($name, $this->events)) {
            return $this;
        }

        if ($callback === null) {
            unset($this->events[$name]);

            return $this;
        }

        $callback = $callback(...);

        foreach ($this->events[$name] as $i => $event) {
            if ($event['callback'] != $callback) {
                continue;
            }

            unset($this->events[$name][$i]);
        }

        if ($this->events[$name] === []) {
            unset($this->events[$name]);
        } else {
            $this->events[$name] = array_values($this->events[$name]);
        }

        return $this;
    }

    /**
     * Add an event.
     *
     * @param string $name The event name.
     * @param callable $callback The callback.
     * @param int|null $priority The event priority.
     * @return EventManager The EventManager.
     */
    public function on(string $name, callable $callback, int|null $priority = null): static
    {
        $this->events[$name] ??= [];

        $this->events[$name][] = [
            'callback' => $callback(...),
            'priority' => $priority ?? static::PRIORITY_NORMAL,
        ];

        uasort(
            $this->events[$name],
            fn(array $a, array $b): int => $a['priority'] <=> $b['priority']
        );

        return $this;
    }

    /**
     * Remove an EventListener.
     *
     * @param EventListenerInterface $listener The EventListener.
     * @return EventManager The EventManager.
     */
    public function removeListener(EventListenerInterface $listener): static
    {
        foreach ($listener->implementedEvents() as $name => $data) {
            [$callback] = static::normalizeListenerEvent($listener, $data);

            $this->off($name, $callback);
        }

        return $this;
    }

    /**
     * Trigger an event.
     *
     * @param string $name The event name.
     * @param mixed ...$args The event arguments.
     * @return Event The Event.
     */
    public function trigger(string $name, mixed ...$args): Event
    {
        $event = new Event($name, null, $args);

        return $this->dispatch($event);
    }

    /**
     * Normalize a listener event.
     *
     * @param EventListenerInterface $listener The EventListener.
     * @param callable $data The callback or event data.
     * @return array The normalized event data.
     */
    protected static function normalizeListenerEvent(EventListenerInterface $listener, array|Closure|string $data): array
    {
        if (is_array($data)) {
            $callback = $data['callback'] ?? null;
            $priority = $data['priority'] ?? null;
        } else {
            $callback = $data;
            $priority = null;
        }

        if (is_string($callback)) {
            $callback = [$listener, $callback];
        }

        return [$callback, $priority];
    }
}
