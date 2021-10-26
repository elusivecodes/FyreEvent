<?php
declare(strict_types=1);

namespace Fyre\Event;

use function
    array_filter,
    array_key_exists,
    call_user_func_array,
    count,
    uasort;

/**
 * Event
 */
class Event
{

    const PRIORITY_LOW = 200;
    const PRIORITY_NORMAL = 100;
    const PRIORITY_HIGH = 10;

    protected static array $listeners = [];

    /**
     * Clear all events.
     */
    public static function clear(): void
    {
        static::$listeners = [];
    }

    /**
     * Add an event.
     * @param string $name The event name.
     * @param callable $callback The callback.
     * @param int $priority The event priority.
     */
    public static function on(string $name, callable $callback, int $priority = self::PRIORITY_NORMAL): void
    {
        static::$listeners[$name] ??= [];

        static::$listeners[$name][] = [
            'callback' => $callback,
            'priority' => $priority
        ];

        static::sort($name);
    }

    /**
     * Remove an event.
     * @param string $name The event name.
     * @param callable|null $callback The callback.
     * @return bool TRUE if the event was removed, otherwise FALSE.
     */
    public static function remove(string $name, callable|null $callback = null): bool
    {
        if (!array_key_exists($name, static::$listeners)) {
            return false;
        }

        if ($callback === null) {
            unset(static::$listeners[$name]);

            return true;
        }

        $preCount = count(static::$listeners[$name]);

        static::$listeners[$name] = array_filter(
            static::$listeners[$name],
            fn($value) => $value['callback'] !== $callback
        );

        $postCount = count(static::$listeners[$name]);

        if ($preCount === $postCount) {
            return false;
        }

        if (empty(static::$listeners[$name])) {
            unset(static::$listeners[$name]);
        }

        return true;
    }

    /**
     * Trigger an event.
     * @param string $name The event name.
     * @param mixed ...$args The event arguments.
     * @return bool FALSE if the event was cancelled, otherwise TRUE. 
     */
    public static function trigger(string $name, ...$args): bool
    {
        if (!array_key_exists($name, static::$listeners)) {
            return true;
        }

        foreach (static::$listeners[$name] AS $listener) {
            if (call_user_func_array($listener['callback'], $args) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sort events by priority.
     * @param string $name The event name.
     */
    protected static function sort(string $name)
    {
        uasort(
            static::$listeners[$name],
            fn($a, $b) => $a['priority'] - $b['priority']
        );
    }

}
