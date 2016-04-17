<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;
use nochso\Omni\Arrays;
use nochso\Omni\Format\Duration;
use nochso\Omni\Format\Quantity;

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
     * @return string
     */
    public function getPrettyDuration() {
        return Duration::create(Duration::FORMAT_LONG)->format($this->duration / 1000);
    }

    public function getPrettyAverageDuration() {
        return Duration::create()->limitPeriods(2)->format($this->duration / $this->operations / 1000);
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

    public function getPrettyOperations() {
        return $this->formatNumber($this->operations, 0) . Quantity::format(' op(s)', $this->operations);
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

    public function getNormalizedOperationsPerUnit(UnitResult $unitResult) {
        $allResults = Arrays::flatten($unitResult->getResults());
        $minOpsPerSec = PHP_INT_MAX;
        /** @var Result $result */
        foreach ($allResults as $result) {
            $minOpsPerSec = min($minOpsPerSec, $result->getOperationsPerSecond());
        }
        $opsPerUnit = $this->getOperationsPerSecond();
        $unit = 's';
        if ($minOpsPerSec < 1) {
            $minOpsPerSec *= 60;
            $opsPerUnit *= 60;
            $unit = 'm';
        }
        if ($minOpsPerSec < 1) {
            $opsPerUnit *= 60;
            $unit = 'h';
        }
        return $this->formatNumber($opsPerUnit) . '/'. $unit ;
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
