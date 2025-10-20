# FyreEvent

**FyreEvent** is a free, open-source events library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Events](#events)
- [Event Listeners](#event-listeners)
- [Event Dispatchers](#event-dispatchers)



## Installation

**Using Composer**

```
composer require fyre/event
```

In PHP:

```php
use Fyre\Event\EventManager;
```


## Basic Usage

- `$parentEventManager` is an *EventManager* that will handle propagated events, and will default to *null*.

```php
$eventManager = new EventManager($parentEventManager);
```


## Methods

**Add Listener**

Add an [*EventListener*](#event-listeners).

- `$eventListener` is an [*EventListener*](#event-listeners).

```php
$eventManager->addListener($eventListener);
```

**Clear**

Clear all events.

```php
$eventManager->clear();
```

**Dispatch**

Dispatch an [*Event*](#events).

- `$event` is an [*Event*](#events).

```php
$eventManager->dispatch($event);
```

**Has**

Check if an event exists.

- `$name` is a string representing the event name.

```php
$hasEvent = $eventManager->has($name);
```

**Off**

Remove event(s).

- `$name` is a string representing the event name.
- `$callback` is the callback to remove.

```php
$eventManager->off($name, $callback);
```

If the `$callback` argument is omitted, all events will be removed instead.

```php
$eventManager->off($name);
```

**On**

Add an event.

- `$name` is a string representing the event name.
- `$callback` is the callback to execute.
- `$priority` is a number representing the callback priority, and will default to *EventManager::PRIORITY_NORMAL*.

```php
$eventManager->on($name, $callback, $priority);
```

**Remove Listener**

Remove an [*EventListener*](#event-listeners).

- `$eventListener` is an [*EventListener*](#event-listeners).

```php
$eventManager->removeListener($eventListener);
```

**Trigger**

Trigger an event.

- `$name` is a string representing the event name.

Any additional arguments supplied will be passed to the event callback.

```php
$event = $eventManager->trigger($name, ...$args);
```


## Events

```php
use Fyre\Event\Event;
```

- `$name` is a string representing the name of the *Event* .
- `$subject` is an object representing the *Event* subject, and will default to *null*.
- `$data` is an array containing the *Event* data, and will default to *[]*.
- `$cancelable` is a boolean indicating whether the event can be cancelled, and will default to *true*.

```php
$event = new Event($name, $subject, $data, $cancelable);
```

**Get Data**

Get the *Event* data.

```php
$data = $event->getData();
```

**Get Name**

Get the *Event* name.

```php
$name = $event->getName();
```

**Get Result**

Get the *Event* result.

```php
$result = $event->getResult();
```

**Get Subject**

Get the *Event* subject.

```php
$subject = $event->getSubject();
```

**Is Default Prevented**

Determine whether the default *Event* should occur.

```php
$isDefaultPrevented = $event->isDefaultPrevented();
```

**Is Propagation Stopped**

Determine whether the *Event* propagation was stopped.

```php
$isPropagationStopped = $event->isPropagationStopped();
```

**Prevent Default**

Prevent the default *Event*.

```php
$event->preventDefault();
```

**Set Data**

- `$data` is an array containing the *Event* data.

```php
$event->setData($data);
```

**Set Result**

- `$result` is the *Event* result.

```php
$event->setResult($result);
```

**Stop Propagation**

Stop the *Event* propagating.

```php
$event->stopPropagation();
```


## Event Listeners

Custom event listeners can be created by implementing the `Fyre\Event\EventListenerInterface`, ensuring all below methods are implemented.

```php
use Fyre\Event\EventListenerInterface;

class MyListener implements EventListenerInterface
{

}
```

**Implemented Events**

Get the implemented events.

```php
$events = $listener->implementedEvents();
```


## Event Dispatchers

Custom event dispatchers can be created by using the `Fyre\Event\EventDispatcherTrait`.

```php
use Fyre\Event\EventDispatcherTrait;

class MyDispatcher
{
    use EventDispatcherTrait;
}
```

**Dispatch Event**

Dispatch an [*Event*](#events).

- `$name` is a string representing the event name.
- `$data` is an array containing the *Event* data, and will default to *[]*.
- `$subject` is an object representing the *Event* subject, and will default to the event dispatcher.
- `$cancelable` is a boolean indicating whether the event can be cancelled, and will default to *true*.

```php
$this->dispatchEvent($name, $data, $subject, $cancelable);
```

**Get Event Manager**

Get the *EventManager*.

```php
$eventManager = $this->getEventManager();
```

**Set Event Manager**

Set the *EventManager*.

- `$eventManager` is an *EventManager*.

```php
$this->setEventManager($eventManager);
```