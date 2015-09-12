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
use nochso\Benchmark\Report;
use nochso\Benchmark\Timer;
use nochso\Benchmark\Unit;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    public function testReport()
    {
        Timer::$defaultMinDuration = 100;
        $report = new Report('Test report', 'Example benchmark.');
        $unit = new Unit('String concatenation');
        $unit->addMethod(new Method(function ($n) {
            while ($n--) {
                $x = "foobar $n";
            }
        }, '$x = "foobar $n"'));

        $unit->addMethod(new Method(function ($n) {
            while ($n--) {
                $x = 'foobar ' . $n;
            }
        }, '$x = \'foobar \' . $n'));
        $report->addUnits($unit);

        $unit2 = new Unit('String concatenation with varying length');
        foreach ($unit->getMethods() as $method) {
            $unit2->addMethod($method);
        }
        $unit2->addParam(new Parameter('x', '$p = \'x\''));
        $unit2->addParam(new Parameter('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '$p = \'xxxxx..\''));
        $report->addUnits($unit2);
        $report->run();
    }
}
