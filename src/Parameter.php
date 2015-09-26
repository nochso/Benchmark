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
 * Parameter consists of any mixed value and a name.
 *
 * A parameter can be added to any Unit object. In this case the Unit's methods
 * must accept a second parameter which will be the parameter assigned to this
 * class.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
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
     * Return the parameter value.
     *
     * @return mixed
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Return the name of this parameter.
     *
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
