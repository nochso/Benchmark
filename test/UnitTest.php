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
use nochso\Benchmark\Parameter;
use nochso\Benchmark\Timer;
use nochso\Benchmark\Unit;

class UnitTest extends \PHPUnit_Framework_TestCase
{
    public function testUnit()
    {
        Timer::$defaultMinDuration = 100;
        $unit = new Unit('test');
        $unit->addMethod(new Method(function ($n, $cost) {
            if ($cost < 10) {
                while ($n--) {
                    $x = $n * 2;
                }
            } else {
                while ($n--) {
                    $x = $n * 2;
                    $x = number_format($x);
                }
            }
        }, 'calc', 'calculate stuff'));

        $unit->addMethod(new Method(function ($n, $cost) {
            while ($n--) {
                $x = password_hash('xx', PASSWORD_DEFAULT, array('cost' => $cost));
            }
        }, 'hash', 'password_hash'));

        $unit->addParam(new Parameter(5, 'easy'));
        $unit->addParam(new Parameter(10, 'hard'));
        $results = $unit->run();
    }
}
