<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark;

use Jralph\Twig\Markdown\Contracts\MarkdownInterface;

class GithubMarkdownExtension implements MarkdownInterface
{
    /**
     * Parse the provided markdown text.
     *
     * @param string $text
     *
     * @return string
     */
    public function parse($text)
    {
        $parser = new \cebe\markdown\GithubMarkdown();
        return $parser->parse($text);
    }
}
