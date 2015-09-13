<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

class Unit
{
    /**
     * @var Method[]
     */
    private $methods = array();
    /**
     * @var Parameter[]
     */
    private $params = array();
    /**
     * List of Result objects with varying parameters grouped by method name.
     *
     * @var Result[][]
     */
    private $results = array();
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return Parameter[]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param Method $method
     */
    public function addMethod(Method $method)
    {
        $this->methods[$method->getName()] = $method;
    }

    /**
     * @param Parameter|string $parameter
     */
    public function addParam(Parameter $parameter)
    {
        $this->params[$parameter->getName()] = $parameter;
    }

    /**
     * Runs all combinations and returns results grouped by method name.
     *
     * @return Result[][]
     */
    public function run()
    {
        $this->results = array();
        foreach ($this->methods as $method) {
            $this->fetchMethodResults($method);
            $this->addAverageMethodResult($method);
        }
        return $this->results;
    }

    /**
     * @param Method $method
     *
     * @return Result
     */
    private function getAverageMethodResult(Method $method)
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

    /**
     * @param Method $method
     */
    private function fetchMethodResults(Method $method)
    {
        $params = $this->params;
        if (count($params) === 0) {
            $params[] = null;
        }
        foreach ($params as $paramKey => $parameter) {
            $result = $method->time($parameter);
            $this->addMethodResult($method, $result);
        }
    }

    /**
     * @param Method $method
     */
    private function addAverageMethodResult(Method $method)
    {
        // No need for averages with only one result.
        if (count($this->params) === 0) {
            return;
        }
        $averageResult = $this->getAverageMethodResult($method);
        $this->addMethodResult($method, $averageResult);
    }

    /**
     * @param Method $method
     * @param Result $result
     */
    private function addMethodResult(Method $method, $result)
    {
        $this->results[$method->getName()][] = $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
