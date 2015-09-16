<?php

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
    'theme' => 'nochso-benchmark',
    'title' => 'noch.so Benchmark documentation',
    'build_dir' => __DIR__ . '/doc/build',
    'cache_dir' => __DIR__ . '/doc/cache',
    'default_opened_level' => 2,
    'template_dirs' => array(__DIR__ . '/doc/theme'),
));
// Used for displaying source code
/** @var Twig_Environment[] $sami */
$sami['twig']->addFunction(new Twig_SimpleFunction('file_get_contents', 'file_get_contents'));
return $sami;
