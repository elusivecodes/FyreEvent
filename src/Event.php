<?php
declare(strict_types=1);

namespace Fyre\Event;

use function array_key_exists;
use function call_user_func_array;
use function count;
use function uasort;

/**
 * Event
 */
abstract class Event
{

    const PRIORITY_LOW = 200;
    const PRIORITY_NORMAL = 100;
    const PRIORITY_HIGH = 10;

    protected static array $events = [];

    /**
     * Clear all events.
     */
    public static function clear(): void
    {
        static::$events = [];
    }

    /**
     * Determine whether an event exists.
     * @param string $name The event name.
     * @return bool TRUE if the event exists, otherwise FALSE.
     */
    public static function has(string $name): bool
    {
        return array_key_exists($name, static::$events);
    }

    /**
     * Remove an event.
     * @param string $name The event name.
     * @param callable|null $callback The callback.
     * @return bool TRUE if the event was removed, otherwise FALSE.
     */
    public static function off(string $name, callable|null $callback = null): bool
    {
        if (!array_key_exists($name, static::$events)) {
            return false;
        }

        if ($callback === null) {
            unset(static::$events[$name]);

            return true;
        }

        $hasEvent = false;
        $newEvents = [];

        foreach (static::$events[$name] AS $event) {
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
            unset(static::$events[$name]);
        } else {
            static::$events[$name] = $newEvents;
        }

        return true;
    }

    /**
     * Add an event.
     * @param string $name The event name.
     * @param callable $callback The callback.
     * @param int|null $priority The event priority.
     */
    public static function on(string $name, callable $callback, int|null $priority = null): void
    {
        static::$events[$name] ??= [];

        static::$events[$name][] = [
            'callback' => $callback,
            'priority' => $priority ?? static::PRIORITY_NORMAL
        ];

        uasort(
            static::$events[$name],
            fn(array $a, array $b): int => $a['priority'] <=> $b['priority']
        );
    }

    /**
     * Trigger an event.
     * @param string $name The event name.
     * @param mixed ...$args The event arguments.
     * @return bool FALSE if the event was cancelled, otherwise TRUE. 
     */
    public static function trigger(string $name, mixed ...$args): bool
    {
        if (!array_key_exists($name, static::$events)) {
            return true;
        }

        foreach (static::$events[$name] AS $listener) {
            if (call_user_func_array($listener['callback'], $args) === false) {
                return false;
            }
        }

        return true;
    }

}
