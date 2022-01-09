<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Event\Event,
    PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{

    private int $i = 0;
    private int $j = 0;

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

    public function testRemove(): void
    {
        Event::on('test', function() {
            $this->i++;
        });
        Event::on('test', function() {
            $this->i++;
        });

        Event::remove('test');
        Event::trigger('test');

        $this->assertSame(0, $this->i);
    }

    public function testRemoveCallback(): void
    {
        $callback = function() {
            $this->i++;
        };

        Event::on('test', $callback);
        Event::on('test', function() {
            $this->j++;
        });

        Event::remove('test', $callback);
        Event::trigger('test');

        $this->assertSame(0, $this->i);
        $this->assertSame(1, $this->j);
    }

    protected function setUp(): void
    {
        Event::clear();
        $this->i = 0;
        $this->j = 0;
    }

}
