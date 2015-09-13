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
 * Path is utility class with static methods.
 */
class Path
{
    const SEPARATORS = '/\\';

    /**
     * Joins any number of arguments into a single path.
     *
     * Slashes around each argument will be trimmed. The leading slashes of the
     * first argument will be preserved.
     * The arguments will be joined by DIRECTORY_SEPERATOR
     *
     * @param string $path As many parameters as paths
     *
     * @return string
     */
    public static function join($path)
    {
        $arguments = func_get_args();
        $argumentCount = count($arguments);
        if ($argumentCount === 0) {
            return null;
        }
        // Trim trailing slash and preserve leading slash
        $parts = array(rtrim($arguments[0], self::SEPARATORS));
        // Trim around everything else
        for ($index = 1; $index < $argumentCount; $index++) {
            $parts[] = trim($arguments[$index], self::SEPARATORS);
        }
        $trimmedPath = implode(DIRECTORY_SEPARATOR, $parts);
        $normalizedPath = self::normalizeSlashes($trimmedPath);
        return $normalizedPath;
    }

    /**
     * Replaces all slashes with $separator.
     *
     * @param string $path
     * @param string $separator Optional: Default is DIRECTORY_SEPARATOR and OS dependant.
     *
     * @return string
     */
    public static function normalizeSlashes($path, $separator = DIRECTORY_SEPARATOR)
    {
        return str_replace(array('\\', '/'), $separator, $path);
    }
}
