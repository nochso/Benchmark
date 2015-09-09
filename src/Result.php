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
}
