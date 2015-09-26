<?php
require 'vendor/autoload.php';

use nochso\Benchmark\Parameter;
use nochso\Benchmark\Report;
use nochso\Benchmark\Unit;

$report = new Report('Performance of searching in lists in PHP', 'This report compares different ways of searching elements in lists.');
$unit = new Unit('Searching a string', 'How much of a difference is there between searching through all elements and accessing it by key?');

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
$unit->addClosure($foreachExact, 'Strict `foreach $v === $n`', 'Strict comparison in a foreach loop');
$unit->addClosure($foreachEquals, 'Loose `foreach $v == $n`', 'Simple comparison in a foreach loop');
$unit->addClosure($arraySearch, '`array_search($n, $l)`', 'Search for value in list');
$unit->addClosure($arrayKeyExists, '`array_key_exists($n, $map)`', 'Check for string key in a map');

// Here's the verbose way:
$unit->addMethod(new \nochso\Benchmark\Method($isset, '`isset($map[$n])`', 'Check if string key is set'));
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

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$sortUnit = new Unit('Searching in a sorted list', <<<'TAG'
Looking for an object in sorted list can be actually slow or quite fast.

**Assocative arrays** are still your best friend. Instead of sorting by
position, objects are accessed by index.

A **binary search** makes use of the fact that the list is sorted by the search
needle. The complexity is O(log(n)) with n being the array size.

**Iterative searches** are O(n) and are a good use for small lists.
However searching in big lists will become quite slow.
TAG
);
$sortUnit->addClosure(function ($n, $p) {
    $list = $p['list'];
    $needle = $p['needle'];
    while ($n--) {
        $result = null;
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            if ($list[$i] === $needle) {
                $result = $i;
                break;
            }
        }
        if ($list[$result] !== $needle) {
            throw new \Exception('');
        }
    }
}, '`for ($i = 0; $i < $count; $i++) break;`');
$sortUnit->addClosure(function ($n, $p) {
    $list = $p['list'];
    $needle = $p['needle'];
    while ($n--) {
        $count = count($list);
        $left = 0;
        $right = $count - 1;
        $result = null;
        while ($left <= $right) {
            $middle = (int)($left + (($right - $left) / 2));
            if ($list[$middle] === $needle) {
                $result = $middle;
                break;
            }
            if ($list[$middle] > $needle) {
                $right = $middle - 1;
            } else {
                $left = $middle + 1;
            }
        }
        if ($list[$result] !== $needle) {
            throw new \Exception('');
        }
    }
}, 'Binary search');

$sortUnit->addClosure(function ($n, $p) {
    $map = $p['map'];
    $needle = $p['needle'];
    while ($n--) {
        $result = null;
        if (isset($map[$needle . '_'])) {
            $result = $map[$needle . '_'];
        }
        if ($result !== $needle) {
            throw new \Exception('');
        }
    }
}, '`isset()`');

$list = array();
$map = array();
// Fill with a sorted list
for ($i = 0; $i < 1000; $i++) {
    $list[] = $i * 2;
    $map[$i * 2 . '_'] = $i * 2;
}
$params = array(
    'list' => $list,
    'map' => $map,
    'needle' => $list[0]
);
$sortUnit->addParam(new Parameter($params, '1/1000'));
$params['needle'] = $list[332];
$sortUnit->addParam(new Parameter($params, '333/1000'));
$params['needle'] = $list[499];
$sortUnit->addParam(new Parameter($params, '500/1000'));
$params['needle'] = $list[999];
$sortUnit->addParam(new Parameter($params, '1000/1000'));

$list = array();
$map = array();
for ($i = 0; $i < 100000; $i++) {
    $list[] = $i * 2;
    $map[$i * 2 . '_'] = $i * 2;
}
$params = array(
    'list' => $list,
    'map' => $map,
    'needle' => $list[0]
);
$sortUnit->addParam(new Parameter($params, '1/100k'));
$params['needle'] = $list[33332];
$sortUnit->addParam(new Parameter($params, '3333/100k'));
$params['needle'] = $list[49999];
$sortUnit->addParam(new Parameter($params, '50k/100k'));
$params['needle'] = $list[66665];
$sortUnit->addParam(new Parameter($params, '6666/100k'));
$params['needle'] = $list[99999];
$sortUnit->addParam(new Parameter($params, '100k/100k'));
$report->unitList->add($sortUnit);
$report->run();
