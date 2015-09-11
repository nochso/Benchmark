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
    private $name;

    /**
     * @param mixed  $parameter
     * @param string $name
     */
    public function __construct($parameter, $name)
    {
        $this->parameter = $parameter;
        $this->name = $name;
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
    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
