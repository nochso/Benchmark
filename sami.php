<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

return new Sami\Sami('src', array(
    'title' => 'nochso/Benchmark documentation',
    'build_dir' => __DIR__ . '/doc/build',
    'cache_dir' => __DIR__ . '/doc/cache',
    'default_opened_level' => 2,
));
