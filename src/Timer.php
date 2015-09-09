<?php

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
    const DEFAULT_MIN_DURATION = 1000;

    /**
     * @var int Milliseconds
     */
    private $minDuration = self::DEFAULT_MIN_DURATION;

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
     * @param \Closure $closure
     *
     * @return Result
     */
    public function time(\Closure $closure)
    {
        $n = $lastn = 1;
        $duration = $this->run($closure, $n);
        if ($duration < 0.01) {
            $n = 10000;
        } else {
            $n = $this->adjust($n, $duration);
        }
        while ($duration < $this->minDuration) {
            $duration = $this->run($closure, $n);
            $lastn = $n;
            $n = $this->adjust($n, $duration);
        }
        $result = new Result($duration, $lastn);
        return $result;
    }

    /**
     * Runs $closure and returns the duration in milliseconds.
     *
     * @param \Closure $closure
     * @param int      $n       Iteration count or difficulty used by the closure
     *
     * @return float
     *
     * @see Timer::time
     */
    private function run(\Closure $closure, $n)
    {
        $start = microtime(true);
        $closure($n);
        $end = microtime(true);
        $duration = ($end - $start) * 1000;
        return $duration;
    }

    /**
     * Returns an adjusted iteration count based on a previous run's duration.
     *
     * @param int   $n        Iteration count of a previous run
     * @param float $duration Duration when using $n iterations
     *
     * @return int
     */
    private function adjust($n, $duration)
    {
        $factor = $this->minDuration / $duration * self::BONUS_GAIN;
        $factor = min(self::MAX_FACTOR, $factor);
        $new = (int) ($n * $factor);
        $new = max($new, $n + 1);
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
