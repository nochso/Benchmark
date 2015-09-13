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
 * Timer runs a closure for a minimum duration to ensure stable results.
 *
 * The minimum duration is configurable and defaults to 1000 milliseconds.
 */
class Timer
{
    /**
     * Increase $n only by this much between runs.
     */
    const MAX_FACTOR = 15.0;
    /**
     * Increase $n at least this much between runs.
     */
    const BONUS_GAIN = 1.03;
    /**
     * @var int Default minimum duration in milliseconds
     */
    public static $defaultMinDuration = 1000;
    /**
     * @var int Milliseconds
     */
    private $minDuration;
    /**
     * @var int
     */
    private $iterationCount;
    /**
     * @var Parameter
     */
    private $parameter;
    /**
     * @var Method
     */
    private $method;
    /**
     * @var bool
     */
    private $debug;

    /**
     * Runs and times a closure until a minimum duration has been reached.
     *
     * The closure is called with a single integer $n. It is best to let $n
     * control a simple loop in your closure.
     *
     * If your test is computationally intensive by nature this might not work
     * for you. In this case use $n to control the cost or difficulty of your
     * algorithm.
     *
     * It is important that you **do not use a loop in addition** when doing
     * this. This also means you can not change the parameters of your
     * algorithm when you are using a loop based on $n.
     *
     * @param Method    $method
     * @param Parameter $parameter
     *
     * @return Result
     */
    public function time(Method $method, Parameter $parameter = null)
    {
        $this->method = $method;
        $this->iterationCount = 1;
        $this->parameter = $parameter;

        $result = $this->createResult();
        if ($result->getDuration() >= $this->minDuration) {
            return $result;
        }
        $this->iterationCount = $this->adjust($result);
        while ($result->getDuration() < $this->minDuration) {
            $result = $this->createResult();
            $this->iterationCount = $this->adjust($result);
        }
        return $result;
    }

    public function __construct($minDuration = null, $debug = false)
    {
        $this->debug = $debug;
        $this->minDuration = self::$defaultMinDuration;
        if ($minDuration !== null) {
            $this->minDuration = $minDuration;
        }
    }

    /**
     * Runs $closure and returns the duration in milliseconds.
     *
     * @return Result
     *
     * @see Timer::time
     */
    private function createResult()
    {
        $duration = $this->measure();
        if ($this->debug) {
            echo $this->iterationCount . ' iterations in ' . number_format($duration) . "ms\n";
        }
        return new Result($duration, $this->iterationCount, $this->method, $this->parameter);
    }

    /**
     * @return float The time it took to execute in milliseconds
     */
    private function measure()
    {
        $closure = $this->method->getMethod();
        if ($this->parameter !== null) {
            // Make sure getParameter() won't be measured.
            $parameterValue = $this->parameter->getParameter();
            $start = microtime(true);
            $closure($this->iterationCount, $parameterValue);
            return (microtime(true) - $start) * 1000.0;
        }

        // Have to omit the parameter because the closure won't accept it.
        $start = microtime(true);
        $closure($this->iterationCount);
        return (microtime(true) - $start) * 1000.0;
    }

    /**
     * Returns an adjusted iteration count based on a previous run's duration.
     *
     * @param Result $result
     *
     * @return int
     */
    private function adjust(Result $result)
    {
        if ($result->getOperations() === 1 && $result->getDuration() < 0.01) {
            return 10000;
        }
        $factor = $this->minDuration / $result->getDuration() * self::BONUS_GAIN;
        $factor = min(self::MAX_FACTOR, $factor);
        $new = (int) ($result->getOperations() * $factor);
        $new = max($new, $result->getOperations() + 1);
        return $new;
    }

    /**
     * Sets the minimum duration of a run in milliseconds.
     *
     * @return int Minimum duration in milliseconds
     */
    public function getMinDuration()
    {
        return $this->minDuration;
    }

    /**
     * Gets the minimum duration of a run in milliseconds.
     *
     * @param int $minDuration Minimum duration in milliseconds
     */
    public function setMinDuration($minDuration)
    {
        $this->minDuration = (int) $minDuration;
    }
}
