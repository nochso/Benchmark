<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark\Twig;

use nochso\Benchmark\Util\Color;

/**
 * ReportExtension.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class ReportExtension extends \Twig_Extension
{
    /**
     * Returns the Twig functions of this extension.
     *
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('text_color', array($this, 'textColorForBackground')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'benchmark_report_extension';
    }

    /**
     * Returns an appropiate foreground color based on the brightness.
     *
     * @param Color|string $backgroundColor Color object or hex string. May include a leading '#'.
     *
     * @return string
     */
    public function textColorForBackground($backgroundColor)
    {
        if (!$backgroundColor instanceof Color) {
            $backgroundColor = Color::fromHex($backgroundColor);
        }
        if ($backgroundColor->getBrightness() < 0.6) {
            return new Color(255, 255, 255);
        }
        return new Color(0, 0, 0);
    }
}
