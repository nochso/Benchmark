<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * @return callable|\Closure
     */
    public function getMethod()
    {
        return $this->method;
    }
}
