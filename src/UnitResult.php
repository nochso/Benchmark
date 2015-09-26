<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

class UnitResult
{
    /**
     * First key is the method name. The inner array is a list of results per method.
     *
     * @var Result[][]
     */
    private $results = array();

    /**
     * @param Result $result
     */
    public function add($result)
    {
        $this->results[$result->getMethod()->getName()][] = $result;
    }

    /**
     * @param Method $method
     *
     * @return Result[]
     */
    public function getMethodResults(Method $method)
    {
        return $this->results[$method->getName()];
    }

    /**
     * @param Method $method
     *
     * @return Result
     */
    public function getAverageMethodResult(Method $method)
    {
        $duration = 0.0;
        $operations = 0;
        foreach ($this->results[$method->getName()] as $result) {
            $duration += $result->getDuration();
            $operations += $result->getOperations();
        }
        $averageResult = new Result($duration, $operations, $method, new Parameter(null, 'Average'));
        return $averageResult;
    }
}
