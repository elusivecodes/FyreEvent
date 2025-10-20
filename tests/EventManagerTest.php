<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Event\Event;
use Fyre\Event\EventManager;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;
use Tests\Mock\MockListener;
use Tests\Mock\MockPriorityListener;

use function class_uses;

final class EventManagerTest extends TestCase
{
    protected EventManager $eventManager;

    public function testAddListener(): void
    {
        $listener = new MockListener();

        $this->assertSame(
            $this->eventManager,
            $this->eventManager->addListener($listener)
        );

        $this->eventManager->trigger('test', 1);

        $this->assertSame(1, $listener->getResult());
    }

    public function testAddListenerPriority(): void
    {
        $listener1 = new MockListener();
        $listener2 = new MockPriorityListener();

        $this->eventManager->addListener($listener1);
        $this->eventManager->addListener($listener2);

        $this->eventManager->trigger('test', 1);

        $this->assertNull($listener1->getResult());
        $this->assertSame(1, $listener2->getResult());
    }

    public function testDispatch(): void
    {
        $event1 = new Event('test');

        $i = 0;

        $this->eventManager->on('test', function() use (&$i): void {
            $i++;
        });

        $event2 = $this->eventManager->dispatch($event1);

        $this->assertInstanceOf(Event::class, $event2);

        $this->assertSame($event1, $event2);

        $this->assertSame(1, $i);
    }

    public function testEventPreventDefault(): void
    {
        $this->eventManager->on('test', function(Event $event): void {
            $event->preventDefault();
        });

        $event = $this->eventManager->trigger('test');

        $this->assertInstanceOf(Event::class, $event);

        $this->assertTrue(
            $event->isDefaultPrevented()
        );
    }

    public function testEventPropagation(): void
    {
        $eventManager = new EventManager($this->eventManager);

        $results = [];

        $this->eventManager->on('test', function() use (&$results): void {
            $results[] = 1;
        });
        $eventManager->on('test', function() use (&$results): void {
            $results[] = 2;
        });

        $event = $eventManager->trigger('test');

        $this->assertSame([2, 1], $results);
    }

    public function testEventResult(): void
    {
        $this->eventManager->on('test', function(): int {
            return 1;
        });

        $event = $this->eventManager->trigger('test');

        $this->assertInstanceOf(Event::class, $event);

        $this->assertSame(
            1,
            $event->getResult()
        );
    }

    public function testEventResultFalse(): void
    {
        $eventManager = new EventManager($this->eventManager);

        $ran = false;

        $this->eventManager->on('test', function() use (&$ran): void {
            $ran = true;
        });
        $eventManager->on('test', function() use (&$ran): void {
            $ran = true;
        });
        $eventManager->on('test', function(): bool {
            return false;
        }, EventManager::PRIORITY_HIGH);

        $event = $eventManager->trigger('test');

        $this->assertTrue(
            $event->isDefaultPrevented()
        );

        $this->assertFalse($ran);
    }

    public function testEventStopPropagation(): void
    {
        $eventManager = new EventManager($this->eventManager);

        $ran = false;

        $this->eventManager->on('test', function() use (&$ran): void {
            $ran = true;
        });
        $eventManager->on('test', function() use (&$ran): void {
            $ran = true;
        });
        $eventManager->on('test', function(Event $event): void {
            $event->stopPropagation();
        }, EventManager::PRIORITY_HIGH);

        $event = $eventManager->trigger('test');

        $this->assertFalse(
            $event->isDefaultPrevented()
        );

        $this->assertFalse($ran);
    }

    public function testHas(): void
    {
        $this->assertSame(
            $this->eventManager,
            $this->eventManager->on('test', function(): void {})
        );

        $this->assertTrue(
            $this->eventManager->has('test')
        );
    }

    public function testHasInvalid(): void
    {
        $this->assertFalse(
            $this->eventManager->has('test')
        );
    }

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(EventManager::class)
        );
    }

    public function testOff(): void
    {
        $i = 0;

        $this->eventManager->on('test', function() use (&$i): void {
            $i++;
        });
        $this->eventManager->on('test', function() use (&$i): void {
            $i++;
        });

        $this->assertSame(
            $this->eventManager,
            $this->eventManager->off('test')
        );

        $this->eventManager->trigger('test');

        $this->assertSame(0, $i);
    }

    public function testOffCallback(): void
    {
        $i = 0;
        $callback = function() use (&$i): void {
            $i++;
        };

        $this->eventManager->on('test', $callback);
        $this->eventManager->on('test', function() use (&$i): void {
            $i++;
        });

        $this->assertSame(
            $this->eventManager,
            $this->eventManager->off('test', $callback)
        );

        $this->eventManager->trigger('test');

        $this->assertSame(1, $i);
    }

    public function testOffCallbackInvalid(): void
    {
        $i = 0;
        $this->eventManager->on('test', function() use (&$i): void {
            $i++;
        });

        $this->assertSame(
            $this->eventManager,
            $this->eventManager->off('test', function() use (&$i): void {})
        );

        $this->eventManager->trigger('test');

        $this->assertSame(1, $i);
    }

    public function testOffInvalid(): void
    {
        $i = 0;
        $this->eventManager->on('test1', function() use (&$i): void {
            $i++;
        });

        $this->assertSame(
            $this->eventManager,
            $this->eventManager->off('test2')
        );

        $this->eventManager->trigger('test1');

        $this->assertSame(1, $i);
    }

    public function testRemoveListener(): void
    {
        $listener = new MockListener();

        $this->eventManager->addListener($listener);

        $this->assertSame(
            $this->eventManager,
            $this->eventManager->removeListener($listener)
        );

        $this->eventManager->trigger('test', 1);

        $this->assertNull($listener->getResult());
    }

    public function testTriggerArguments(): void
    {
        $i = 0;
        $this->eventManager->on('test', function(Event $event, int $a, bool $b) use (&$i): void {
            if ($b) {
                $i += $a;
            }
        });

        $this->eventManager->trigger('test', 2, true);

        $this->assertSame(2, $i);
    }

    public function testTriggerPriority(): void
    {
        $results = [];

        $this->eventManager->on('test', function() use (&$results): void {
            $results[] = 1;
        });
        $this->eventManager->on('test', function() use (&$results): void {
            $results[] = 2;
        }, EventManager::PRIORITY_HIGH);

        $this->eventManager->trigger('test');

        $this->assertSame([2, 1], $results);
    }

    protected function setUp(): void
    {
        $this->eventManager = new EventManager();
    }
}
