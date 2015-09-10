<?php

namespace nochso\Benchmark;

class Parameter
{
    /**
     * @var mixed
     */
    private $parameter;
    /**
     * @var string
     */
    private $description;

    public function __construct($parameter, $description)
    {
        $this->parameter = $parameter;
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
