<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

use nochso\Benchmark\Util\Color;

/**
 * UnitResult.
 *
 * The min/max variables and handling want to be refactored!
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class UnitResult
{
    /**
     * First key is the method name. The inner array is a list of results per method.
     *
     * @var Result[][]
     */
    private $results = array();
    /**
     * @var ResultBounds
     */
    private $bounds;

    public function __construct()
    {
        $this->bounds = new ResultBounds($this);
    }

    /**
     * @return Result[][]
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param Result $result
     */
    public function add($result)
    {
        $method = $result->getMethod();
        $name = $method->getName();
        $this->results[$name][] = $result;
    }

    /**
     * @param Method $method
     * @param bool   $includeAverages
     *
     * @return Result[]
     */
    public function getMethodResults(Method $method, $includeAverages = false)
    {
        // When no parameters were used, there's no point to include averages
        $first = reset($this->results[$method->getName()]);
        if ($first->getParameter() === null) {
            $includeAverages = false;
        }
        if ($includeAverages) {
            $results = $this->results[$method->getName()];
            $results[] = $this->getAverageMethodResult($method);
            return $results;
        }
        return $this->results[$method->getName()];
    }

    /**
     * @param Method $method
     *
     * @return Result
     */
    public function getAverageMethodResult(Method $method)
    {
        $duration = 0.0;
        $operations = 0;
        foreach ($this->results[$method->getName()] as $result) {
            $duration += ($result->getDuration() / $result->getOperations());
            $operations++;
        }
        $averageResult = new Result($duration, $operations, $method, new Parameter(null, 'Average'));
        return $averageResult;
    }

    public function getMethodScore(Method $method)
    {
        $this->bounds->prepare();
        return $this->bounds->max('average') / $this->getAverageMethodResult($method)->getOperationsPerSecond();
    }

    public function getMethodScoreColor(Method $method)
    {
        $this->bounds->prepare();
        $score = $this->getMethodScore($method);
        return $this->getScoreColor('average', $score);
    }

    public function getParameterScore(Result $result)
    {
        $this->bounds->prepare();
        $paramName = null;
        $parameter = $result->getParameter();
        if ($parameter !== null) {
            $paramName = $parameter->getName();
        }
        return $this->bounds->max('parameter.' . $paramName) / $result->getOperationsPerSecond();
    }

    public function getParameterScoreColor(Result $result)
    {
        $score = $this->getParameterScore($result);
        $parameter = $result->getParameter();
        return $this->getScoreColor('parameter.' . $parameter->getName(), $score);
    }

    /**
     * @param string $path
     * @param float  $score
     *
     * @return string
     */
    public function getScoreColor($path, $score)
    {
        $white = new Color(255, 255, 255);
        if ($score <= 3) {
            $green = Color::fromHex('71EF71');
            return $green->blendTo($white, ($score - 1) / 2);
        }
        if ($score <= 6) {
            return $white;
        }
        // Score after 6 to highest fades from white to red.
        $worst = $this->bounds->max($path) / $this->bounds->min($path);
        $red = Color::fromHex('FB4E4E');
        return $white->blendTo($red, $score / $worst);
    }
}
