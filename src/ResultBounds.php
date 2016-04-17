<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

/**
 * ResultBounds.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class ResultBounds
{
    private $bounds = array();
    /**
     * @var UnitResult
     */
    private $unitResult;

    public function __construct($unitResult)
    {
        $this->unitResult = $unitResult;
    }

    /**
     * @param string $path
     * @param null   $default
     *
     * @return array|null
     */
    public function get($path, $default = null)
    {
        $paths = explode('.', $path);
        $result = $this->bounds;
        foreach ($paths as $path) {
            if (!array_key_exists($path, $result)) {
                return $default;
            }
            $result = $result[$path];
        }
        return $result;
    }

    /**
     * @param string $path
     * @param float  $value
     */
    public function set($path, $value)
    {
        $paths = explode('.', $path);
        $result = &$this->bounds;
        foreach ($paths as $key => $path) {
            if (!array_key_exists($path, $result)) {
                $result[$path] = array();
            }
            $result = &$result[$path];
        }
        $result = $value;
    }

    /**
     * @param string     $path
     * @param null|float $value
     *
     * @return array|null
     */
    public function max($path, $value = null)
    {
        $path = $path . '.max';
        if ($value !== null) {
            $this->set($path, max($this->get($path, 0), $value));
        }
        return $this->get($path);
    }

    /**
     * @param string     $path
     * @param null|float $value
     *
     * @return array|null
     */
    public function min($path, $value = null)
    {
        $path = $path . '.min';
        if ($value !== null) {
            $this->set($path, min($this->get($path, PHP_INT_MAX), $value));
        }
        return $this->get($path);
    }

    public function prepare()
    {
        $this->prepareBoundsParameter();
        $this->prepareBoundsAverage();
    }

    private function prepareBoundsParameter()
    {
        if ($this->get('parameter') !== null) {
            return;
        }
        foreach ($this->unitResult->getResults() as $methodName => $results) {
            $this->prepareBoundsMethodResults($results);
        }
    }

    private function prepareBoundsAverage()
    {
        if ($this->get('average.max') !== null) {
            return;
        }
        foreach ($this->unitResult->getResults() as $methodName => $results) {
            $res = reset($results);
            $methodResult = $this->unitResult->getAverageMethodResult($res->getMethod());
            $opsPerSec = $methodResult->getOperationsPerSecond();
            $this->max('average', $opsPerSec);
            $this->min('average', $opsPerSec);
        }
    }

    /**
     * @param Result[] $results
     */
    private function prepareBoundsMethodResults($results)
    {
        /** @var Result $first */
        $first = reset($results);
        $method = $first->getMethod();
        foreach ($this->unitResult->getMethodResults($method, true) as $result) {
            $this->prepareBoundsMethodResult($result);
        }
    }

    /**
     * @param Result $result
     */
    private function prepareBoundsMethodResult(Result $result)
    {
        $paramName = null;
        $parameter = $result->getParameter();
        if ($parameter !== null) {
            $paramName = $parameter->getName();
        }
        $ops = $result->getOperationsPerSecond();
        $this->max('parameter.' . $paramName, $ops);
        $this->min('parameter.' . $paramName, $ops);
    }
}
