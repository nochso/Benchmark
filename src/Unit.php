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
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return Parameter[]
     */
    public function getParams()
    {
        return $this->params;
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
            $duration = 0;
            $ops = 0;
            if (count($this->params) > 0) {
                foreach ($this->params as $paramKey => $parameter) {
                    $result = $method->time($parameter);
                    $this->results[$methodName][] = $result;
                    $duration += $result->getDuration();
                    $ops += $result->getOperations();
                }
                $this->results[$methodName][] = new Result($duration, $ops, $method, new Parameter(null, 'Average'));
            } else {
                $result = $method->time();
                $this->results[$methodName][] = $result;
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
