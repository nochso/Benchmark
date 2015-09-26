<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

use nochso\Benchmark\Util\Out;

/**
 * Unit is a list of related methods and the parameters they're called with.
 *
 * The combination of different implementations (Method objects) and their
 * parameters should be related as their results should be comparable.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
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
     * @var UnitResult
     */
    private $result;
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
     * @return UnitResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param Method $method
     */
    public function addMethod(Method $method)
    {
        $this->methods[$method->getName()] = $method;
    }

    /**
     * @param \Closure $closure
     * @param string   $name
     * @param string   $description
     */
    public function addClosure(\Closure $closure, $name, $description = '')
    {
        $this->methods[$name] = new Method($closure, $name, $description);
    }

    /**
     * @param Parameter $parameter
     */
    public function addParam(Parameter $parameter)
    {
        $this->params[$parameter->getName()] = $parameter;
    }

    /**
     * Runs all combinations and returns results grouped by method name.
     *
     * @return UnitResult
     */
    public function run()
    {
        $this->result = new UnitResult();
        foreach ($this->methods as $method) {
            Out::writeLine('Method: ' . $method->getName());
            $this->fetchMethodResults($method);
            $this->addAverageMethodResult($method);
        }
        return $this->result;
    }

    /**
     * @param Method $method
     */
    private function fetchMethodResults(Method $method)
    {
        $params = $this->params;
        if (count($params) === 0) {
            $params[] = null;
        }
        foreach ($params as $paramKey => $parameter) {
            Out::writeLine('Parameter: ' . ($parameter !== null ? $parameter->getName() : 'null'));
            $result = $method->time($parameter);
            $this->result->add($result);
            Out::writeLine();
        }
    }

    /**
     * @param Method $method
     */
    private function addAverageMethodResult(Method $method)
    {
        // No need for averages with only one result.
        if (count($this->params) === 0) {
            return;
        }
        $averageResult = $this->result->getAverageMethodResult($method);
        $this->result->add($averageResult);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
