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
 * Result contains the test results for a specific method and parameter.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class Result
{
    /**
     * @var float Milliseconds
     */
    private $duration;
    /**
     * @var int
     */
    private $operations;
    /**
     * @var Method
     */
    private $method;
    /**
     * @var Parameter
     */
    private $parameter;

    /**
     * @param float     $duration
     * @param int       $operations
     * @param Method    $method
     * @param Parameter $parameter
     */
    public function __construct($duration, $operations, Method $method, Parameter $parameter = null)
    {
        $this->duration = $duration;
        $this->operations = $operations;
        $this->method = $method;
        $this->parameter = $parameter;
    }

    /**
     * Duration in milliseconds.
     *
     * @return float Milliseconds
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Get the number of iterations.
     *
     * @return int
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * Get the operations per second as a float.
     *
     * @return float
     */
    public function getOperationsPerSecond()
    {
        return $secsPerOp = 1 / ($this->duration / 1000 / $this->operations);
    }

    /**
     * Get the method used to create this result.
     *
     * @return Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the parameter that was used to call the method.
     *
     * @return Parameter
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    public function __toString()
    {
        $ops = $this->getOperationsPerSecond();
        if ($ops >= 1) {
            $ops = $this->formatNumber($ops);
            return $ops . ' op/s';
        }
        $ops *= 60;
        if ($ops >= 1) {
            $ops = $this->formatNumber($ops);
            return $ops . ' op/m';
        }
        $ops *= 60;
        $ops = $this->formatNumber($ops);
        return $ops . ' op/h';
    }

    /**
     * Turns 2000 into 2K, 2,000,000 into 2M, etc.
     *
     * This uses base10. Don't use it for bytes.
     *
     * @param int|float $value
     * @param int       $decimals
     *
     * @return string
     *
     * @link http://stackoverflow.com/a/2510540
     */
    private function formatNumber($value, $decimals = 1)
    {
        if ($value < 1) {
            return number_format($value, $decimals);
        }
        $base = log($value, 1000);
        $newValue = pow(1000, $base - floor($base));

        $suffixes = array('', 'K', 'M', 'G', 'T');
        $suffix = $suffixes[intval($base)];

        return number_format($newValue, $decimals) . $suffix;
    }
}
