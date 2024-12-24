<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Event\EventManager;
use PHPUnit\Framework\TestCase;
use Tests\Mock\MockDispatcher;

final class EventDispatcherTest extends TestCase
{
    protected MockDispatcher $dispatcher;

    public function testDispatchEvent(): void
    {
        $eventManager = $this->dispatcher->getEventManager();

        $ran = false;
        $eventManager->on('test', function() use (&$ran): void {
            $ran = true;
        });

        $event = $this->dispatcher->dispatchEvent('test', ['a' => 1]);

        $this->assertSame('test', $event->getName());

        $this->assertSame($this->dispatcher, $event->getSubject());

        $this->assertSame(['a' => 1], $event->getData());

        $this->assertTrue($ran);
    }

    public function testGetEventManager(): void
    {
        $eventManager = $this->dispatcher->getEventManager();

        $this->assertInstanceOf(
            EventManager::class,
            $eventManager
        );

        $this->assertSame(
            $eventManager,
            $this->dispatcher->getEventManager()
        );
    }

    public function testSetEventManager(): void
    {
        $eventManager = new EventManager();

        $this->assertSame(
            $this->dispatcher,
            $this->dispatcher->setEventManager($eventManager)
        );

        $this->assertSame(
            $eventManager,
            $this->dispatcher->getEventManager()
        );
    }

    protected function setUp(): void
    {
        $this->dispatcher = new MockDispatcher();
    }
}
