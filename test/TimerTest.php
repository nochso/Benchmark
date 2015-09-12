<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark\Test;

use nochso\Benchmark\Method;
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
        $this->assertEquals(Timer::$defaultMinDuration, $timer->getMinDuration());
        $timer->setMinDuration(100);
        $this->assertEquals(100, $timer->getMinDuration());

        $method = new Method(self::$slowClosure, '');

        $start = microtime(true);
        $result = $timer->time($method);
        $duration = (microtime(true) - $start) * 1000;

        $this->assertGreaterThan($timer->getMinDuration(), $duration);
        $this->assertGreaterThan($timer->getMinDuration(), $result->getDuration());
    }

    public function testAdjustmentOfFastTests()
    {
        $timer = new Timer(100);
        $method = new Method(self::$fastClosure, '');
        $result = $timer->time($method);
        $this->assertGreaterThan($timer->getMinDuration(), $result->getDuration());
        $this->assertLessThan($timer->getMinDuration() * 1.2, $result->getDuration(), 'Minimum duration must not be exceeded by 20% for fast tests.');
    }
}
