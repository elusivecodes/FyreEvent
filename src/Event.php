<?php
declare(strict_types=1);

namespace Fyre\Event;

use function array_key_exists;
use function uasort;

/**
 * Event
 */
class Event
{
    public const PRIORITY_HIGH = 10;

    public const PRIORITY_LOW = 200;

    public const PRIORITY_NORMAL = 100;

    protected array $events = [];

    /**
     * Clear all events.
     */
    public function clear(): void
    {
        $this->events = [];
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
     * @return bool TRUE if the event was removed, otherwise FALSE.
     */
    public function off(string $name, callable|null $callback = null): bool
    {
        if (!array_key_exists($name, $this->events)) {
            return false;
        }

        if ($callback === null) {
            unset($this->events[$name]);

            return true;
        }

        $hasEvent = false;
        $newEvents = [];

        foreach ($this->events[$name] as $event) {
            if ($event['callback'] === $callback) {
                $hasEvent |= true;

                continue;
            }

            $newEvents[] = $event;
        }

        if (!$hasEvent) {
            return false;
        }

        if ($newEvents === []) {
            unset($this->events[$name]);
        } else {
            $this->events[$name] = $newEvents;
        }

        return true;
    }

    /**
     * Add an event.
     *
     * @param string $name The event name.
     * @param callable $callback The callback.
     * @param int|null $priority The event priority.
     */
    public function on(string $name, callable $callback, int|null $priority = null): void
    {
        $this->events[$name] ??= [];

        $this->events[$name][] = [
            'callback' => $callback,
            'priority' => $priority ?? static::PRIORITY_NORMAL,
        ];

        uasort(
            $this->events[$name],
            fn(array $a, array $b): int => $a['priority'] <=> $b['priority']
        );
    }

    /**
     * Trigger an event.
     *
     * @param string $name The event name.
     * @param mixed ...$args The event arguments.
     * @return bool FALSE if the event was cancelled, otherwise TRUE.
     */
    public function trigger(string $name, mixed ...$args): bool
    {
        if (!array_key_exists($name, $this->events)) {
            return true;
        }

        foreach ($this->events[$name] as $listener) {
            if ($listener['callback'](...$args) === false) {
                return false;
            }
        }

        return true;
    }
}
