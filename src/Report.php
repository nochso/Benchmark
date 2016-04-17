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
use nochso\Benchmark\Twig\GithubMarkdownExtension;
use nochso\Benchmark\Twig\ReportExtension;
use nochso\Benchmark\Util\Out;
use nochso\Benchmark\Util\Path;
use Symfony\Component\Finder\Finder;

/**
 * Report takes a list of Unit objects and creates a HTML report of the results.
 *
 * @author Marcel Voigt <mv@noch.so>
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 */
class Report
{
    const BENCHMARK_VERSION = '0.5.1';

    /**
     * @var array
     */
    protected $defaultConfig = array(
        'template_dir' => __DIR__ . '/../template',
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
        $this->twig->addExtension(new ReportExtension());
        $this->twig->addExtension(new \Jralph\Twig\Markdown\Extension(
            new GithubMarkdownExtension()
        ));
        $this->twig->addExtension(new \nochso\HtmlCompressTwig\Extension());
    }

    /**
     * Return the title of this report.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return the description of this report.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Runs all units and creates a HTML report.
     */
    public function run()
    {
        Out::writeLine('Running nochso/benchmark ' . self::BENCHMARK_VERSION);
        Out::writeLine();
        $duration = -microtime(true);
        $progress = new Progress();
        $progress->prepareUnitList($this->unitList);
        foreach ($this->unitList as $unitName => $unit) {
            $unit->setProgress($progress);
            $unit->run();
        }
        $duration += microtime(true);
        $data = array(
            'report' => $this,
            'title' => $this->getTitle(),
            'duration' => $duration,
            'min_duration' => Timer::$defaultMinDuration,
            'os' => php_uname('s') . ' ' . php_uname('r') . php_uname('m'),
            'zend_extensions' => get_loaded_extensions(true),
        );

        $this->render($data);
    }

    /**
     * @param $data
     */
    private function render($data)
    {
        $outputDir = $this->config['output_dir'];
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $path = Path::join($outputDir, 'index.html');
        Out::writeLine('Saving HTML report to ' . $path);
        $html = $this->twig->render('report.twig', $data);
        file_put_contents($path, $html);
    }

    /**
     * Returns the combined and minified JS files from template_dir/asset/js.
     *
     * @return string
     */
    public function getJavascript()
    {
        $finder = Finder::create()
            ->in(Path::join($this->config['template_dir'], 'asset/js'))
            ->files()
            ->name('*.js');
        $javascript = '';
        foreach ($finder as $filepath) {
            $javascript .= file_get_contents($filepath) . "\n";
        }
        return $javascript;
    }

    /**
     * @return string
     */
    public function getCSS()
    {
        $names = array(
            'normalize',
            'skeleton',
            'prism',
            'benchmark',
        );
        $cssDir = Path::join($this->config['template_dir'], 'asset/css');
        $css = '';
        foreach ($names as $name) {
            $path = Path::join($cssDir, $name . '.css');
            $css .= file_get_contents($path) . "\n";
        }
        return $css;
    }
}
