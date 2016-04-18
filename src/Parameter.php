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
     * @var string
     */
    private $description;

    /**
     * @param mixed $parameter
     * @param string $name
     * @param string $description
     */
    public function __construct($parameter, $name, $description = '')
    {
        $this->parameter = $parameter;
        $this->name = $name;
        $this->description = $description;
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

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    public function __toString()
    {
        return $this->name;
    }
}
