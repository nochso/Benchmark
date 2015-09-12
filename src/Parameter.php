<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

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
