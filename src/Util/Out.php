<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark\Util;

/**
 * Static helper for command line output.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class Out
{
    private static $sticky;

    /**
     * Outputs a line including newline.
     *
     * @param string $line
     */
    public static function writeLine($line = '')
    {
        self::write("\r" . str_pad($line, max(strlen($line), strlen(self::$sticky)), ' ', STR_PAD_RIGHT) . "\n");
    }

    /**
     * @param string $line
     */
    private static function write($line = '')
    {
        if (self::isQuiet()) {
            return;
        }
        echo $line;
        echo self::$sticky;
    }

    public static function writeSticky($line)
    {
        if (self::isQuiet()) {
            return;
        }
        echo "\r" . str_pad($line, max(strlen($line), strlen(self::$sticky)), ' ', STR_PAD_RIGHT);
        self::$sticky = $line;
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
