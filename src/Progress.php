<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

use nochso\Benchmark\Util\Out;

/**
 * Progress.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class Progress
{
    /**
     * @var int
     */
    private $step;
    /**
     * @var int
     */
    private $totalSteps;
    private $text;

    public function prepareUnitList(UnitList $unitList)
    {
        $this->step = 0;
        $this->totalSteps = 0;
        foreach ($unitList as $unit) {
            $methods = count($unit->getMethods());
            $parameters = count($unit->getParams());
            $this->totalSteps += $methods * max($parameters, 1);
        }
        $this->show();
    }

    public function step($text = null)
    {
        $this->step++;
        $this->text = $text;
        $this->show();
    }

    public function text($text = null) {
        $this->text = $text;
        $this->show();
    }

    private function show()
    {
        $width = 25;
        $progress = round($this->step / $this->totalSteps * $width);
        $left = $width - $progress;
        $percentage = round($this->step / $this->totalSteps * 100.0);
        Out::writeSticky('[' . str_repeat('#', $progress) . str_repeat('.', $left) . '] ' . $percentage . '% ' . $this->text);
        if ($this->step >= $this->totalSteps) {
            Out::writeSticky('');
        }
    }
}
