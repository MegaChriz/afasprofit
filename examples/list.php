<?php

/**
 * @file
 * Psuedo-code for testing creating lists.
 */

use Afas\Component\Test\TestList;

// Bootstrap.
require_once __DIR__ . '/../vendor/autoload.php';

$list = new TestList();
$list[] = 'item 1';
$list[] = 'item 2';
$list[] = 'item 3';
$list[] = array();
$list[] = new stdClass();

print '<pre>';
print_r($list);
