<?php

/**
 * @file
 * Code that need to be loaded on every request.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

// Require global Afas class.
require_once dirname(__DIR__) . '/src/Afas.php';

// Require global yvklibrary and functions.
require_once '/Websites/library/importer.inc.php';
