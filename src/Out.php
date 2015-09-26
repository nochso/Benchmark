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
 * Static helper for command line output.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class Out
{
    /**
     * Outputs a line including newline.
     *
     * @param string $line
     */
    public static function writeLine($line = '')
    {
        self::write($line . "\n");
    }

    /**
     * Outputs a line as it is.
     *
     * @param string $line
     */
    public static function write($line = '')
    {
        if (self::isQuiet()) {
            return;
        }
        echo $line;
    }

    /**
     * Decides whether to enable output or not.
     *
     * Currently output is only disabled when PHPUnit is running.
     *
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
