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
        $this->assertEquals($expected, (string) $result);
    }

    public function toStringProvider()
    {
        return array(
            array(1000.0, 5, '5.0 op/sec'),
            array(2000.0, 10, '5.0 op/sec'),
            array(1000.0, 1001, '1.0K op/sec'),
            array(1000.0, 1200, '1.2K op/sec'),
            array(1000.0, 1200000, '1.2M op/sec'),
            array(1000.0, 1200000000, '1.2G op/sec'),
            array(1000.0, 1200000000000, '1.2T op/sec'),
        );
    }
}
