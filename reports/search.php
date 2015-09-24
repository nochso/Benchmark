<?php
require 'vendor/autoload.php';

use nochso\Benchmark\Parameter;
use nochso\Benchmark\Report;
use nochso\Benchmark\Unit;

$report = new Report('Searching in lists');
$unit = new Unit('Searching a specific string');

// Define closures to be added to the unit
$foreachExact = function ($n, $p) {
    $list = $p['list'];
    $needle = $p['needle'];
    while ($n--) {
        $found = false;
        foreach ($list as $value) {
            if ($value === $needle) {
                $found = true;
                break;
            }
        }
    }
};

$foreachEquals = function ($n, $p) {
    $list = $p['list'];
    $needle = $p['needle'];
    while ($n--) {
        $found = false;
        foreach ($list as $value) {
            if ($value == $needle) {
                $found = true;
                break;
            }
        }
    }
};

$arraySearch = function ($n, $p) {
    $list = $p['list'];
    $needle = $p['needle'];
    while ($n--) {
        $found = array_search($needle, $list);
    }
};

$arrayKeyExists = function ($n, $p) {
    $map = $p['map'];
    $needle = $p['needle'];
    while ($n--) {
        $found = array_key_exists($needle, $map);
    }
};

$isset = function ($n, $p) {
    $map = $p['map'];
    $needle = $p['needle'];
    while ($n--) {
        $found = isset($map[$needle]);
    }
};

// This is a shortcut to create a Method object and add it to the unit
$unit->addClosure($foreachExact, 'foreach ($l as $v) { if ($v === $n) { return true; } }');
$unit->addClosure($foreachEquals, 'foreach ($l as $v) { if ($v == $n) { return true; } }');
$unit->addClosure($arraySearch, 'array_search($n, $l)');
$unit->addClosure($arrayKeyExists, 'array_key_exists($n, $map)');

// Here's the verbose way:
$unit->addMethod(new \nochso\Benchmark\Method($isset, 'isset($map[$n])', 'Check if string key is set'));
// $unit->addClosure($isset, 'isset($map[$n]');

// Create the list to be searched by each method
$list = array();
for ($i = 0; $i < 1000; $i++) {
    $list[] = $i . 'There is an art to flying, or rather a knack. Its knack lies in learning to throw yourself at the ground and miss.';
}
// Also supply an associative version of the list
$map = array_flip($list);
$params = array(
    'list' => $list,
    'map' => $map,
    'needle' => $list[0]
);
$unit->addParam(new Parameter($params, 'First of 1000'));

// Add different parameters by changing the needle of the haystack
$params['needle'] = $list[count($list) - 1];
$unit->addParam(new Parameter($params, 'Last of 1000'));

$params['needle'] = '404 Not found';
$unit->addParam(new Parameter($params, 'Not found'));

// Finally add this single unit to the report and run it
$report->unitList->add($unit);
$report->run();
