<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    /**
     * Create docs and add them to gh-pages without comitting.
     */
    public function docs()
    {
        $this->checkout('master');

        $this->taskExec('php vendor/sami/sami/sami.php update sami.php --force')
            ->run();

        $this->checkout('gh-pages');

        $this->_deleteDir('docs');
        $this->_copyDir('doc/build', 'docs');

        $this->taskGitStack()
            ->add('--all docs/')
            ->exec('status')
            ->run();
    }

    private function checkout($branch)
    {
        $this->taskGitStack()
            ->checkout($branch)
            ->run();
    }

    public function reports()
    {
        $this->checkout('master');
        $this->_exec('php reports/search.php');
        $this->checkout('gh-pages');
        $this->_deleteDir('reports');
        $this->_copyDir('build', 'reports');
        $this->_rename('reports/index.html', 'reports/search.html');
        $this->taskGitStack()
            ->add('--all reports/')
            ->run();
    }
}
