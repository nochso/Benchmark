<?php
require __DIR__.'/vendor/autoload.php';

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in('src')
    ->in('vendor/doctrine/collections/lib/Doctrine/Common/Collections')
;

$sami = new Sami\Sami($iterator, array(
    'title' => 'noch.so Benchmark documentation',
    'build_dir' => __DIR__ . '/doc/build',
    'cache_dir' => __DIR__ . '/doc/cache',
));
nochso\SamiTheme\Theme::prepare($sami);
return $sami;
