<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

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
     * @var string
     */
    private $description;
    /**
     * @var Progress
     */
    private $progress;

    /**
     * Both parameters are interpreted as Markdown.
     *
     * @param string $name
     * @param string $description Optional
     */
    public function __construct($name, $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * Return first list of all methods. Sorted by score if available.
     *
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param Progress $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
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
            $this->fetchMethodResults($method);
        }
        $this->sortByMethodScore();
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
            $result = $method->time($parameter);
            $this->result->add($result);
            $this->notifyProgress();
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    private function notifyProgress()
    {
        if ($this->progress !== null) {
            $this->progress->step();
        }
    }

    /**
     * Sort methods best score first.
     */
    private function sortByMethodScore()
    {
        uasort($this->methods, function ($first, $second) {
            $aScore = $this->result->getMethodScore($first);
            $bScore = $this->result->getMethodScore($second);
            if ($aScore === $bScore) {
                return 0;
            }
            return $aScore < $bScore ? -1 : 1;
        });
    }
}
