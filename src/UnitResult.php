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
    private $maxOpsPerSec = null;
    private $minOpsPerSec = null;
    private $min = array();
    private $max = array();

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
        if ($includeAverages) {
            $results = $this->results[$method->getName()];
            $results[] = $this->getAverageMethodResult($method);
            $results[] = $this->getMedianMethodResult($method);
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
            $duration += $result->getDuration();
            $operations += $result->getOperations();
        }
        $averageResult = new Result($duration, $operations, $method, new Parameter(null, 'Average'));
        return $averageResult;
    }

    public function getMedianMethodResult(Method $method)
    {
        $sortedResults = $this->getMethodResults($method);
        usort($sortedResults, function (Result $first, Result $second) {
            $firstOps = $first->getOperationsPerSecond();
            $secondOps = $second->getOperationsPerSecond();
            if ($firstOps === $secondOps) {
                return 0;
            }
            return $firstOps > $secondOps ? 1 : -1;
        });
        $medianResult = $sortedResults[(int) (count($sortedResults) / 2)];
        $parameter = new Parameter(null, 'Median');
        $result = new Result($medianResult->getDuration(), $medianResult->getOperations(), $method, $parameter);
        return $result;
    }

    public function getMethodScore(Method $method)
    {
        $this->prepareBoundsMedian();
        return $this->maxOpsPerSec / $this->getMedianMethodResult($method)->getOperationsPerSecond();
    }

    public function getMethodScoreColor(Method $method)
    {
        $this->prepareBoundsMedian();
        $score = $this->getMethodScore($method);
        if ($score <= 3) {
            return '#' . $this->blendHex('71EF71', 'FFFFFF', ($score - 1) / 2);
        }
        if ($score <= 6) {
            return '#FFFFFF';
        }
        $worst = $this->maxOpsPerSec / $this->minOpsPerSec;
        return '#' . $this->blendHex('FFFFFF', 'FB4E4E', $score / $worst);
    }

    public function getParameterScore(Result $result)
    {
        $this->prepareBoundsParameter();
        $paramName = null;
        $parameter = $result->getParameter();
        if ($parameter !== null) {
            $paramName = $parameter->getName();
        }
        return $this->max['parameter'][$paramName] / $result->getOperationsPerSecond();
    }

    public function getParameterScoreColor(Result $result)
    {
        $score = $this->getParameterScore($result);
        if ($score <= 3) {
            return '#' . $this->blendHex('71EF71', 'FFFFFF', ($score - 1) / 2);
        }
        if ($score <= 6) {
            return '#ffffff';
        }
        $parameter = $result->getParameter();
        $paramName = $parameter->getName();
        $worst = $this->max['parameter'][$paramName] / $this->min['parameter'][$paramName];
        return '#' . $this->blendHex('FFFFFF', 'FB4E4E', $score / $worst);
    }

    /**
     * Blend two hexadecimal colours specifying the fractional position.
     *
     * Example:
     *     // 10% along the gradient between #66cc00 and #cc2200
     *     blend_hex('66cc00', 'cc2200', 0.1); // "70bb00"
     *
     * @link http://www.sitepoint.com/forums/showthread.php?606853#post4195901
     *
     * @param $fromHex
     * @param $toHex
     * @param float $position
     *
     * @return string
     */
    private function blendHex($fromHex, $toHex, $position = 0.5)
    {
        // 1. Grab RGB fromHex each colour
        list($fromRed, $fromGreen, $fromBlue) = sscanf($fromHex, '%2x%2x%2x');
        list($toRed, $toGreen, $toBlue) = sscanf($toHex, '%2x%2x%2x');

        // 2. Calculate colour based on fractional position
        $red = (int) ($fromRed - (($fromRed - $toRed) * $position));
        $green = (int) ($fromGreen - (($fromGreen - $toGreen) * $position));
        $blue = (int) ($fromBlue - (($fromBlue - $toBlue) * $position));

        // 3. Format toHex 6-char HEX colour string
        return sprintf('%02x%02x%02x', $red, $green, $blue);
    }

    private function prepareBoundsMedian()
    {
        if ($this->maxOpsPerSec !== null) {
            return;
        }
        $this->maxOpsPerSec = 0;
        $this->minOpsPerSec = PHP_INT_MAX;
        foreach ($this->results as $methodName => $results) {
            $res = reset($results);
            $methodResult = $this->getMedianMethodResult($res->getMethod());
            $opsPerSec = $methodResult->getOperationsPerSecond();
            $this->maxOpsPerSec = max($this->maxOpsPerSec, $opsPerSec);
            $this->minOpsPerSec = min($this->minOpsPerSec, $opsPerSec);
        }
    }

    private function prepareBoundsParameter()
    {
        if (isset($this->max['parameter']) && count($this->max['parameter']) > 0) {
            return;
        }
        $this->max['parameter'] = array();
        $this->min['parameter'] = array();
        foreach ($this->results as $methodName => $results) {
            $this->prepareBoundsMethodResults($results);
        }
    }

    /**
     * @param Result[] $results
     */
    private function prepareBoundsMethodResults($results)
    {
        /** @var Result $first */
        $first = reset($results);
        $method = $first->getMethod();
        foreach ($this->getMethodResults($method, true) as $result) {
            $this->prepareBoundsMethodResult($result);
        }
    }

    /**
     * @param Result $result
     */
    private function prepareBoundsMethodResult(Result $result)
    {
        $paramName = null;
        $parameter = $result->getParameter();
        if ($parameter !== null) {
            $paramName = $parameter->getName();
        }
        if (!isset($this->max['parameter'][$paramName])) {
            $this->max['parameter'][$paramName] = 0;
        }
        if (!isset($this->min['parameter'][$paramName])) {
            $this->min['parameter'][$paramName] = PHP_INT_MAX;
        }
        $ops = $result->getOperationsPerSecond();
        $this->max['parameter'][$paramName] = max($this->max['parameter'][$paramName], $ops);
        $this->min['parameter'][$paramName] = min($this->min['parameter'][$paramName], $ops);
    }
}
