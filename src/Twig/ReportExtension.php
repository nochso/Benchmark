<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark\Twig;

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
            new \Twig_SimpleFilter('brightness', array($this, 'calculateBrightness')),
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
     * Returns the perceived brightness ranging from 0-1, zero being black.
     *
     * @param string $color Hex color. May include a leading '#'.
     *
     * @return float
     */
    public function calculateBrightness($color)
    {
        list($red, $green, $blue) = sscanf(ltrim($color, '#'), '%2x%2x%2x');
        return sqrt($red * $red * 0.299 + $green * $green * 0.587 + $blue * $blue * 0.114) / 255;
    }

    /**
     * Returns an appropiate foreground color based on the brightness.
     *
     * @param string $backgroundColor Hex color. May include a leading '#'.
     *
     * @return string
     */
    public function textColorForBackground($backgroundColor)
    {
        if ($this->calculateBrightness($backgroundColor) < 0.6) {
            return '#FFFFFF';
        }
        return '#000000';
    }
}
