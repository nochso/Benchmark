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
 * Color.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class Color
{
    /**
     * @var int
     */
    private $red;
    /**
     * @var int
     */
    private $green;
    /**
     * @var int
     */
    private $blue;

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function __construct($red, $green, $blue)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    /**
     * @param string $hex
     *
     * @return Color|null
     */
    public static function fromHex($hex)
    {
        $hex = (string) $hex;
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) != 6 || !ctype_xdigit($hex)) {
            return null;
        }
        list($red, $green, $blue) = sscanf($hex, '%2x%2x%2x');
        return new self($red, $green, $blue);
    }

    /**
     * @return int
     */
    public function getRed()
    {
        return $this->red;
    }

    /**
     * @return int
     */
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * @return int
     */
    public function getBlue()
    {
        return $this->blue;
    }

    /**
     * Returns the perceived brightness ranging from 0-1, zero being black.
     *
     * @return float
     */
    public function getBrightness()
    {
        $red = $this->red * $this->red * 0.299;
        $green = $this->green * $this->green * 0.587;
        $blue = $this->blue * $this->blue * 0.114;
        return sqrt($red + $green + $blue) / 255.0;
    }

    /**
     * @param Color $color
     * @param float $position
     *
     * @return Color
     */
    public function blendTo(Color $color, $position)
    {
        // 2. Calculate colour based on fractional position
        $red = (int) ($this->red - (($this->red - $color->getRed()) * $position));
        $green = (int) ($this->green - (($this->green - $color->getGreen()) * $position));
        $blue = (int) ($this->blue - (($this->blue - $color->getBlue()) * $position));

        return new self($red, $green, $blue);
    }

    /**
     * @return string
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return sprintf('#%02x%02x%02x', $this->red, $this->green, $this->blue);
    }
}
