<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Events,
    PHPUnit\Framework\TestCase;

final class EventsTest extends TestCase
{

    private int $i = 0;
    private int $j = 0;

    public function testEventsTrigger(): void
    {
        Events::on('test1', function() {
            $this->i++;
        });
        Events::on('test2', function() {
            $this->j++;
        });

        Events::trigger('test1');

        $this->assertEquals(1, $this->i);
        $this->assertEquals(0, $this->j);
    }

    public function testEventsTriggerPriority(): void
    {
        Events::on('test', function() {
            if ($this->j > 0) {
                $this->i++;
            }
        });
        Events::on('test', function() {
            $this->j++;
        }, Events::PRIORITY_HIGH);

        Events::trigger('test');

        $this->assertEquals(1, $this->i);
        $this->assertEquals(1, $this->j);
    }

    public function testEventsTriggerArguments(): void
    {
        Events::on('test', function($a, $b) {
            if ($b) {
                $this->i += $a;
            }
        });

        Events::trigger('test', 2, true);

        $this->assertEquals(2, $this->i);
    }

    public function testEventsRemove(): void
    {
        Events::on('test', function() {
            $this->i++;
        });
        Events::on('test', function() {
            $this->i++;
        });

        Events::remove('test');
        Events::trigger('test');

        $this->assertEquals(0, $this->i);
    }

    public function testEventsRemoveCallback(): void
    {
        $callback = function() {
            $this->i++;
        };

        Events::on('test', $callback);
        Events::on('test', function() {
            $this->j++;
        });

        Events::remove('test', $callback);
        Events::trigger('test');

        $this->assertEquals(0, $this->i);
        $this->assertEquals(1, $this->j);
    }

    protected function setUp(): void
    {
        Events::clear();
        $this->i = 0;
        $this->j = 0;
    }

}
