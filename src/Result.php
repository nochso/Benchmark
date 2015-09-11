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
     * @return int
     */
    public function getOperations()
    {
        return $this->operations;
    }

    public function __toString()
    {
        $ops = $this->formatNumber($this->getOperationsPerSecond());
        return $ops . ' op/sec';
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

    /**
     * @return float
     */
    public function getOperationsPerSecond()
    {
        return ($this->operations / $this->duration) * 1000.0;
    }

    /**
     * @return Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return Parameter
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}
