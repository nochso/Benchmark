<?php

namespace nochso\Benchmark\test;

use nochso\Benchmark\Timer;

class TimerTest extends \PHPUnit_Framework_TestCase
{
    private static $slowClosure;
    private static $fastClosure;

    public static function setUpBeforeClass()
    {
        self::$slowClosure = function ($n) {
            while ($n--) {
                $hash = password_hash('xxx', PASSWORD_DEFAULT);
            }
        };

        self::$fastClosure = function ($n) {
            while ($n--) {
                $i = $n * 2;
            }
        };
    }

    public function testMinDuration()
    {
        $timer = new Timer();
        $this->assertNotEquals(500, $timer->getMinDuration());
        $timer->setMinDuration(500);
        $this->assertEquals(500, $timer->getMinDuration());

        $start = microtime(true);
        $result = $timer->time(self::$slowClosure);
        $duration = (microtime(true) - $start) * 1000;

        $this->assertGreaterThan($timer->getMinDuration(), $duration);
        $this->assertGreaterThan($timer->getMinDuration(), $result->getDuration());
    }

    public function testAdjustmentOfFastTests()
    {
        $timer = new Timer();
        $result = $timer->time(self::$fastClosure);
        $this->assertGreaterThan($timer->getMinDuration(), $result->getDuration());
        $this->assertLessThan($timer->getMinDuration() * 1.2, $result->getDuration(), 'Minimum duration must not be exceeded by 20% for fast tests.');
    }
}
