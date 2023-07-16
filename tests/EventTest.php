<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Event\Event;
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{

    private int $i = 0;
    private int $j = 0;

    public function testHas(): void
    {
        Event::on('test', function() {
            $this->i++;
        });

        $this->assertTrue(
            Event::has('test')
        );
    }

    public function testHasInvalid(): void
    {
        $this->assertFalse(
            Event::has('test')
        );
    }

    public function testOff(): void
    {
        Event::on('test', function() {
            $this->i++;
        });
        Event::on('test', function() {
            $this->i++;
        });

        $this->assertTrue(
            Event::off('test')
        );

        Event::trigger('test');

        $this->assertSame(0, $this->i);
    }

    public function testOffCallback(): void
    {
        $callback = function() {
            $this->i++;
        };

        Event::on('test', $callback);
        Event::on('test', function() {
            $this->j++;
        });

        $this->assertTrue(
            Event::off('test', $callback)
        );

        Event::trigger('test');

        $this->assertSame(0, $this->i);
        $this->assertSame(1, $this->j);
    }

    public function testOffInvalid(): void
    {
        $this->assertFalse(
            Event::off('test')
        );
    }

    public function testOffCallbackInvalid(): void
    {
        Event::on('test', function() {
            $this->i++;
        });

        $this->assertFalse(
            Event::off('test', function() {
                $this->j++;
            })
        );
    }

    public function testTrigger(): void
    {
        Event::on('test1', function() {
            $this->i++;
        });
        Event::on('test2', function() {
            $this->j++;
        });

        Event::trigger('test1');

        $this->assertSame(1, $this->i);
        $this->assertSame(0, $this->j);
    }

    public function testTriggerPriority(): void
    {
        Event::on('test', function() {
            if ($this->j > 0) {
                $this->i++;
            }
        });
        Event::on('test', function() {
            $this->j++;
        }, Event::PRIORITY_HIGH);

        Event::trigger('test');

        $this->assertSame(1, $this->i);
        $this->assertSame(1, $this->j);
    }

    public function testTriggerArguments(): void
    {
        Event::on('test', function($a, $b) {
            if ($b) {
                $this->i += $a;
            }
        });

        Event::trigger('test', 2, true);

        $this->assertSame(2, $this->i);
    }

    protected function setUp(): void
    {
        Event::clear();
        $this->i = 0;
        $this->j = 0;
    }

}
