# FyreEvent

**FyreEvent** is a free, events library for *PHP*.


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

**On**

Add an event.

- `$name` is a string representing the event name.
- `$callback` is the callback to execute.
- `$priority` is a number representing the callback priority, and will default to *Events::PRIORITY_NORMAL*.

```php
Event::on($name, $callback, $priority);
```

**Remove**

Remove event(s).

- `$name` is a string representing the event name.
- `$callback` is the callback to remove.

```php
Event::remove($name, $callback);
```

If the `$callback` argument is omitted, all events will be removed instead.

```php
Event::remove($name);
```

**Trigger**

Trigger an event.

- `$name` is a string representing the event name.

Any additional arguments supplied will be passed to the event callback.

```php
Event::trigger($name, ...$args);
```