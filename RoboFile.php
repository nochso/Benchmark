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
     * Create docs and add them to gh-pages without comitting
     */
    public function docs()
    {
        $this->taskGitStack()
            ->checkout('master')
            ->run();

        $this->taskExec('php vendor/sami/sami/sami.php update sami.php --force')
            ->run();

        $this->taskGitStack()
            ->checkout('gh-pages')
            ->run();

        $this->_deleteDir('docs');
        $this->_copyDir('doc/build', 'docs');

        $this->taskGitStack()
            ->add('--all docs/')
            ->exec('status')
            ->run();
    }

    public function reports() {
        $this->taskGitStack()
            ->checkout('master')
            ->run();
        $this->_exec('php reports/search.php');
        $this->taskGitStack()
            ->checkout('gh-pages')
            ->run();
        $this->_deleteDir('reports');
        $this->_copyDir('build', 'reports');
        $this->_rename('reports/index.html', 'reports/search.html');
        $this->taskGitStack()
            ->add('--all reports/')
            ->run();
    }
}
