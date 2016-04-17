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
use nochso\Benchmark\Result;
use nochso\Benchmark\UnitResult;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $duration
     * @param $ops
     * @param $expected
     *
     * @dataProvider toStringProvider
     */
    public function testToString($duration, $ops, $expected)
    {
        $result = new Result($duration, $ops, new Method(null, ''), new Parameter(null, ''));
        $ur = new UnitResult();
        $ur->add($result);
        $this->assertEquals($expected, $result->getNormalizedOperationsPerUnit($ur));
    }

    public function toStringProvider()
    {
        return array(
            array(100.0, 1, '10.0/s'),
            array(2000.0, 1, '30.0/m'),
            array(4000.0, 1, '15.0/m'),
            array(60*1000, 1, '1.0/m'),
            array(60*60*1000, 1, '1.0/h'),
            array(1000.0, 1, '1.0/s'),
            array(1000.0, 5, '5.0/s'),
            array(2000.0, 10, '5.0/s'),
            array(1000.0, 1001, '1.0K/s'),
            array(1000.0, 1200, '1.2K/s'),
            array(1000.0, 1200000, '1.2M/s'),
            array(1000.0, 1200000000, '1.2G/s'),
            array(1000.0, 1200000000000, '1.2T/s'),
        );
    }
}
