<?php

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

//        foreach ($results as $methodName => $results) {
//            foreach ($results as $result) {
//                $param = $result->getParameter();
//                echo "$methodName $param $result\n";
//            }
//        }
    }
}
