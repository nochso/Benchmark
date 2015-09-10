<?php

namespace nochso\Benchmark;

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
     * @param float $duration
     * @param int   $operations
     */
    public function __construct($duration, $operations)
    {
        $this->duration = $duration;
        $this->operations = $operations;
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
     * @return int
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * @return float
     */
    public function getOperationsPerSecond()
    {
        return ($this->operations / $this->duration) * 1000.0;
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
        $base = log($value, 1000);
        $newValue = pow(1000, $base - floor($base));

        $suffixes = array('', 'K', 'M', 'G', 'T');
        $suffix = $suffixes[intval($base)];

        return number_format($newValue, $decimals) . $suffix;
    }

    public function __toString()
    {
        $ops = $this->formatNumber($this->getOperationsPerSecond());
        return $ops . ' op/sec';
    }
}
