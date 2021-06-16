# FyreEvents

**FyreEvents** is a free, events library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)



## Installation

**Using Composer**

```
composer install fyre/events
```

In PHP:

```php
use Fyre\Events;
```


## Methods

**Clear**

Clear all events.

```php
Events::clear();
```

**On**

Add an event.

- `$name` is a string representing the event name.
- `$callback` is the callback to execute.
- `$priority` is a number representing the callback priority, and will default to *Events::PRIORITY_NORMAL*.

```php
Events::on($name, $callback, $priority);
```

**Remove**

Remove event(s).

- `$name` is a string representing the event name.
- `$callback` is the callback to remove.

```php
Events::remove($name, $callback);
```

If the `$callback` argument is omitted, all events will be removed instead.

```php
Events::remove($name);
```

**Trigger**

Trigger an event.

- `$name` is a string representing the event name.

Any additional arguments supplied will be passed to the event callback.

```php
Events::trigger($name, ...$args);
```