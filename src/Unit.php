<?php

namespace nochso\Benchmark;

class Unit
{
    /**
     * @var Method[]
     */
    private $methods = array();
    /**
     * @var Parameter[]
     */
    private $params = array();
    /**
     * List of Result objects with varying parameters grouped by method name.
     *
     * @var Result[][]
     */
    private $results = array();
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param Method $method
     */
    public function addMethod(Method $method)
    {
        $this->methods[$method->getName()] = $method;
    }

    /**
     * @param Parameter|string $parameter
     */
    public function addParam(Parameter $parameter)
    {
        $this->params[$parameter->getName()] = $parameter;
    }

    /**
     * Runs all combinations and returns results grouped by method name.
     *
     * @return Result[][]
     */
    public function run()
    {
        $this->results = array();
        foreach ($this->methods as $methodName => $method) {
            if (count($this->params) > 0) {
                foreach ($this->params as $paramKey => $parameter) {
                    $this->results[$methodName][] = $method->time($parameter);
                }
            } else {
                $this->results[$methodName][] = $method->time();
            }
        }

        return $this->results;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
