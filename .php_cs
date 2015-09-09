<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->exclude('doc');

return \Symfony\CS\Config\Config::create()
    ->level(\Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        'concat_with_spaces',
        '-concat_without_spaces',
        '-empty_return',
        '-phpdoc_no_empty_return',
        '-return',
        '-pre_increment',
    ])
    ->finder($finder);
