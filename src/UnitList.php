<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * UnitList.
 *
 * @method Unit|null offsetGet($offset)
 * @method Unit|null get($key)
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class UnitList extends ArrayCollection
{
    /**
     * @param Unit $value
     *
     * @return bool
     */
    public function add($value)
    {
        if (!$value instanceof Unit) {
            throw new \InvalidArgumentException('Expected object of type ' . get_class(new Unit('')));
        }
        parent::set($value->getName(), $value);
        return true;
    }

    /**
     * Key will be ignored and the unit's name will be used instead.
     *
     * @param int|string $key
     * @param Unit       $value
     */
    public function set($key, $value)
    {
        $this->add($value);
    }

    /**
     * Initializes a new ArrayCollection.
     *
     * @param array $elements
     */
    public function __construct(array $elements = array())
    {
        parent::__construct(array());
        foreach ($elements as $unit) {
            $this->add($unit);
        }
    }
}
