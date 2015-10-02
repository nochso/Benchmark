<?php

/*
 * This file is part of nochso/benchmark.
 *
 * @copyright Copyright (c) 2015 Marcel Voigt <mv@noch.so>
 * @license   https://github.com/nochso/benchmark/blob/master/LICENSE ISC
 * @link      https://github.com/nochso/benchmark
 */

namespace nochso\Benchmark\Twig;

use Twig_Token;

class MinifyHtmlTokenParser extends \Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $lineNumber = $token->getLine();
        $stream = $this->parser->getStream();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideMinifyHtmlEnd'), true);
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $nodes = array('body' => $body);
        return new MinifyHtmlNode($nodes, array(), $lineNumber, $this->getTag());
    }

    public function getTag()
    {
        return 'minify_html';
    }

    public function decideMinifyHtmlEnd(Twig_Token $token)
    {
        return $token->test('end_minify_html');
    }
}
