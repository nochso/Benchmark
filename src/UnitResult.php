<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

class UnitResult
{
    /**
     * First key is the method name. The inner array is a list of results per method.
     *
     * @var Result[][]
     */
    private $results = array();
    private $averageResults = array();
    private $maxOpsPerSec = null;
    private $minOpsPerSec = null;

    /**
     * @param Result $result
     */
    public function add($result)
    {
        $this->results[$result->getMethod()->getName()][] = $result;
    }

    /**
     * @param Method $method
     *
     * @return Result[]
     */
    public function getMethodResults(Method $method)
    {
        return $this->results[$method->getName()];
    }

    /**
     * @param Method $method
     *
     * @return Result
     */
    public function getAverageMethodResult(Method $method)
    {
        if (isset($this->averageResults[$method->getName()])) {
            return $this->averageResults[$method->getName()];
        }
        $duration = 0.0;
        $operations = 0;
        foreach ($this->results[$method->getName()] as $result) {
            $duration += $result->getDuration();
            $operations += $result->getOperations();
        }
        $averageResult = new Result($duration, $operations, $method, new Parameter(null, 'Average'));
        $this->averageResults[$method->getName()] = $averageResult;
        return $averageResult;
    }

    public function getMethodScore(Method $method)
    {
        $this->prepareBounds();
        return $this->maxOpsPerSec / $this->getAverageMethodResult($method)->getOperationsPerSecond();
    }

    public function getMethodScoreColor(Method $method)
    {
        $this->prepareBounds();
        $score = $this->getMethodScore($method);
        if ($score <= 3) {
            return '#' . $this->blendHex('71EF71', 'FFFFFF', ($score - 1) / 2);
        }
        if ($score <= 6) {
            return 'white';
        }
        $worst = $this->maxOpsPerSec / $this->minOpsPerSec;
        return '#' . $this->blendHex('FFFFFF', 'FB4E4E', $score / $worst);
    }

    /**
     * Blend two hexadecimal colours specifying the fractional position.
     *
     * Example:
     *     // 10% along the gradient between #66cc00 and #cc2200
     *     blend_hex('66cc00', 'cc2200', 0.1); // "70bb00"
     *
     * @link http://www.sitepoint.com/forums/showthread.php?606853-On-a-scale-of-red-to-green&s=79cdcc0fff69e031276f7ab7794b0889&p=4195901&viewfull=1#post4195901
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

    private function prepareBounds()
    {
        if ($this->maxOpsPerSec !== null) {
            return;
        }
        $this->maxOpsPerSec = 0;
        $this->minOpsPerSec = PHP_INT_MAX;
        foreach ($this->results as $methodName => $results) {
            $res = reset($results);
            $opsPerSec = $this->getAverageMethodResult($res->getMethod())->getOperationsPerSecond();
            $this->maxOpsPerSec = max($this->maxOpsPerSec, $opsPerSec);
            $this->minOpsPerSec = min($this->minOpsPerSec, $opsPerSec);
        }
    }
}