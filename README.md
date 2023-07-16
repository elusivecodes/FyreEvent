# FyreEvent

**FyreEvent** is a free, open-source events library for *PHP*.


## Table Of Contents
- [Installation](#installation)
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


## Methods

**Clear**

Clear all events.

```php
Event::clear();
```

**Has**

Check if an event exists.

- `$name` is a string representing the event name.

```php
$hasEvent = Event::has($name);
```

**Off**

Remove event(s).

- `$name` is a string representing the event name.
- `$callback` is the callback to remove.

```php
$removed = Event::off($name, $callback);
```

If the `$callback` argument is omitted, all events will be removed instead.

```php
Event::off($name);
```

**On**

Add an event.

- `$name` is a string representing the event name.
- `$callback` is the callback to execute.
- `$priority` is a number representing the callback priority, and will default to *Event::PRIORITY_NORMAL*.

```php
Event::on($name, $callback, $priority);
```

**Trigger**

Trigger an event.

- `$name` is a string representing the event name.

Any additional arguments supplied will be passed to the event callback.

```php
Event::trigger($name, ...$args);
```