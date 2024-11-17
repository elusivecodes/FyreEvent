<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Event\Event;
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
    protected Event $event;

    private int $i = 0;

    private int $j = 0;

    public function testHas(): void
    {
        $this->event->on('test', function() {
            $this->i++;
        });

        $this->assertTrue(
            $this->event->has('test')
        );
    }

    public function testHasInvalid(): void
    {
        $this->assertFalse(
            $this->event->has('test')
        );
    }

    public function testOff(): void
    {
        $this->event->on('test', function() {
            $this->i++;
        });
        $this->event->on('test', function() {
            $this->i++;
        });

        $this->assertTrue(
            $this->event->off('test')
        );

        $this->event->trigger('test');

        $this->assertSame(0, $this->i);
    }

    public function testOffCallback(): void
    {
        $callback = function() {
            $this->i++;
        };

        $this->event->on('test', $callback);
        $this->event->on('test', function() {
            $this->j++;
        });

        $this->assertTrue(
            $this->event->off('test', $callback)
        );

        $this->event->trigger('test');

        $this->assertSame(0, $this->i);
        $this->assertSame(1, $this->j);
    }

    public function testOffCallbackInvalid(): void
    {
        $this->event->on('test', function() {
            $this->i++;
        });

        $this->assertFalse(
            $this->event->off('test', function() {
                $this->j++;
            })
        );
    }

    public function testOffInvalid(): void
    {
        $this->assertFalse(
            $this->event->off('test')
        );
    }

    public function testTrigger(): void
    {
        $this->event->on('test1', function() {
            $this->i++;
        });
        $this->event->on('test2', function() {
            $this->j++;
        });

        $this->event->trigger('test1');

        $this->assertSame(1, $this->i);
        $this->assertSame(0, $this->j);
    }

    public function testTriggerArguments(): void
    {
        $this->event->on('test', function($a, $b) {
            if ($b) {
                $this->i += $a;
            }
        });

        $this->event->trigger('test', 2, true);

        $this->assertSame(2, $this->i);
    }

    public function testTriggerPriority(): void
    {
        $this->event->on('test', function() {
            if ($this->j > 0) {
                $this->i++;
            }
        });
        $this->event->on('test', function() {
            $this->j++;
        }, Event::PRIORITY_HIGH);

        $this->event->trigger('test');

        $this->assertSame(1, $this->i);
        $this->assertSame(1, $this->j);
    }

    protected function setUp(): void
    {
        $this->event = new Event();

        $this->i = 0;
        $this->j = 0;
    }
}
