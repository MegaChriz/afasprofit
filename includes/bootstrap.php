<?php

/**
 * @file
 * Code that need to be loaded on every request.
 */

use Afas\Autoload;

// Require Autoloader.
require_once __DIR__ . '/../lib/Afas/Autoload.php';
Autoload::register();

// Require global Afas class.
require_once __DIR__ . '/../lib/Afas/Afas.php';

// Require global yvklibrary and functions.
require_once '/Websites/library/importer.inc.php';
