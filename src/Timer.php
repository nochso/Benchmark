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
    private $n;
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
        $this->n = 1;
        $this->parameter = $parameter;

        $result = $this->run();
        if ($result->getDuration() < $this->minDuration) {
            if ($result->getDuration() < 0.01) {
                $this->n = 10000;
            } else {
                $this->n = $this->adjust($result);
            }
            while ($result->getDuration() < $this->minDuration) {
                $result = $this->run();
                $this->n = $this->adjust($result);
            }
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
    private function run()
    {
        $closure = $this->method->getMethod();
        if ($this->parameter !== null) {
            $p = $this->parameter->getParameter();
            $start = microtime(true);
            $closure($this->n, $p);
        } else {
            $start = microtime(true);
            $closure($this->n);
        }
        $end = microtime(true);
        $duration = ($end - $start) * 1000;
        if ($this->debug) {
            echo $this->n . ' iterations in ' . number_format($duration) . "ms\n";
        }
        return new Result($duration, $this->n, $this->method, $this->parameter);
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
        $this->minDuration = $minDuration;
    }
}
