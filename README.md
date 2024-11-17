# FyreEvent

**FyreEvent** is a free, open-source events library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)



## Installation

**Using Composer**

```
composer require fyre/event
```

In PHP:

```php
use Fyre\Event\Event;
```


## Basic Usage

```php
$event = new Event();
```


## Methods

**Clear**

Clear all events.

```php
$event->clear();
```

**Has**

Check if an event exists.

- `$name` is a string representing the event name.

```php
$hasEvent = $event->has($name);
```

**Off**

Remove event(s).

- `$name` is a string representing the event name.
- `$callback` is the callback to remove.

```php
$removed = $event->off($name, $callback);
```

If the `$callback` argument is omitted, all events will be removed instead.

```php
$event->off($name);
```

**On**

Add an event.

- `$name` is a string representing the event name.
- `$callback` is the callback to execute.
- `$priority` is a number representing the callback priority, and will default to *Event::PRIORITY_NORMAL*.

```php
$event->on($name, $callback, $priority);
```

**Trigger**

Trigger an event.

- `$name` is a string representing the event name.

Any additional arguments supplied will be passed to the event callback.

```php
$event->trigger($name, ...$args);
```