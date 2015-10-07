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
 * A method that can be benchmarked with optional parameters.
 *
 * The closure is called with a single integer $n. It is best to let $n
 * control a simple loop in your closure e.g. `while ($n--) { ... }`
 *
 * Your closure may optionally accept a second parameter. In this case you must
 * add parameters to the Unit object. You must omit the second parameter if you
 * do not add any parameters to your Unit object.
 *
 * This library will try to figure out the optimal values for $n for each
 * method. That way stable measurements can be taken that are easy to compare.
 *
 * If your test is computationally intensive by nature this might not work
 * for you. In this case use $n to control the cost or difficulty of your
 * algorithm.
 *
 * It is important that you **do not use a loop in addition** when doing
 * this because the duration is not expected to increase exponentially.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class Method
{
    /**
     * @var callable|\Closure
     */
    private $method;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;

    /**
     * @param callable|\Closure $method
     * @param string            $name
     * @param string            $description Optional description
     */
    public function __construct($method, $name, $description = '')
    {
        $this->method = $method;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * Return the name of the method.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the description of the method.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return callable|\Closure
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns the source code of the closure.
     *
     * The beginning of each line will be trimmed uniformly to remove excessive
     * whitespace.
     *
     * e.g.
     * ```
     *     while {
     *         do();
     *     }
     * ```
     * becomes
     * ```
     * while {
     *     do();
     * }
     * ```
     *
     * @return string
     */
    public function getSourceCode()
    {
        $reflection = new \ReflectionFunction($this->method);
        $source = file($reflection->getFileName(), FILE_IGNORE_NEW_LINES);
        $startLine = $reflection->getStartLine();
        $endLine = $reflection->getEndLine() - 1;
        $lines = array_slice($source, $startLine, $endLine - $startLine);
        return implode(PHP_EOL, $this->trimSourceLines($lines));
    }

    /**
     * @param Parameter $parameter
     *
     * @return Result
     */
    public function time(Parameter $parameter = null)
    {
        $timer = new Timer();
        return $timer->time($this, $parameter);
    }

    /**
     * @param $lines
     *
     * @return array
     */
    private function trimSourceLines($lines)
    {
        $minSpaces = PHP_INT_MAX;
        foreach ($lines as $line) {
            $spaces = strlen($line) - strlen(ltrim($line, ' '));
            $minSpaces = min($minSpaces, $spaces);
        }
        $trimmedLines = array();
        foreach ($lines as $line) {
            $trimmedLines[] = substr($line, $minSpaces);
        }
        return $trimmedLines;
    }
}
