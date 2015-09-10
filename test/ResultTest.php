<?php

namespace nochso\Benchmark\test;

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
        $result = new Result($duration, $ops);
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
