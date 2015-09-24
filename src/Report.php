<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

use Cocur\Slugify\Bridge\Twig\SlugifyExtension;
use Cocur\Slugify\Slugify;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Report takes a list of Unit objects and creates a HTML report of the results.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class Report
{
    const BENCHMARK_VERSION = '0.1';

    /**
     * @var array
     */
    protected $defaultConfig = array(
        'template_dir' => 'template',
        'output_dir' => 'build',
        'twig' => array(
            'cache' => 'cache/twig',
            'auto_reload' => true,
            'strict_variables' => true,
        ),
    );
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var array
     */
    private $config = array();
    /**
     * @var UnitList
     */
    public $unitList;
    /**
     * Grouped by unit name and then method name.
     *
     * @var Result[][][]
     */
    private $results = array();
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;

    /**
     * @param string $title
     * @param string $description
     * @param array  $config
     */
    public function __construct($title, $description = '', $config = array())
    {
        $this->unitList = new UnitList();
        $this->title = $title;
        $this->description = $description;
        $this->config = array_replace_recursive($this->defaultConfig, $config);
        $this->config['output_dir'] = Path::join($this->config['output_dir']);
        $this->config['template_dir'] = Path::join($this->config['template_dir']);
        $loader = new \Twig_Loader_Filesystem($this->config['template_dir']);
        $this->twig = new \Twig_Environment($loader, $this->config['twig']);
        $this->twig->addExtension(new SlugifyExtension(Slugify::create()));
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function run()
    {
        $duration = -microtime(true);
        foreach ($this->unitList as $unitName => $unit) {
            $this->results[$unitName] = $unit->run();
        }
        $duration += microtime(true);
        $data = array(
            'results' => $this->results,
            'report' => $this,
            'title' => $this->getTitle(),
            'duration' => $duration,
            'min_duration' => Timer::$defaultMinDuration,
            'os' => php_uname('s') . ' ' . php_uname('r') . php_uname('m'),
            'zend_extensions' => get_loaded_extensions(true),
        );

        $this->render($data);
        $this->moveAssets();
    }

    /**
     * @param $data
     */
    private function render($data)
    {
        $outputDir = $this->config['output_dir'];
        $this->makeFolder($outputDir);
        $path = Path::join($outputDir, 'index.html');
        $html = $this->twig->render('report.twig', $data);
        file_put_contents($path, $html);
    }

    /**
     * @param $dir
     */
    private function makeFolder($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    private function moveAssets()
    {
        $assetDir = Path::join($this->config['template_dir'], 'asset');
        $assets = Finder::create()
            ->in($assetDir)
            ->files();
        foreach ($assets as $asset) {
            /* @var SplFileInfo $asset */
            $targetFile = Path::join($this->config['output_dir'], 'asset', $asset->getRelativePathname());
            $targetDir = dirname($targetFile);
            $this->makeFolder($targetDir);
            copy($asset->getPathname(), $targetFile);
        }
    }
}
