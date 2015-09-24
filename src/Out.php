<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

class Out
{
    public static function writeLine($line = '')
    {
        self::write($line . "\n");
    }

    public static function write($line = '')
    {
        if (self::isQuiet()) {
            return;
        }
        echo $line;
    }

    /**
     * @return bool
     */
    public static function isQuiet()
    {
        if (defined('PHPUNIT_RUNNING')) {
            return true;
        }
        return false;
    }
}
