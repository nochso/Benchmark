<?php

namespace nochso\Benchmark;

class Timer
{
    const MAX_FACTOR = 15.0;
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
     * @param \Closure $closure
     * @param $n
     *
     * @return float
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
     * @param $n
     * @param $duration
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
     * @return int Minimum duration in milliseconds
     */
    public function getMinDuration()
    {
        return $this->minDuration;
    }

    /**
     * @param int $minDuration Minimum duration in milliseconds
     */
    public function setMinDuration($minDuration)
    {
        $this->minDuration = $minDuration;
    }
}
