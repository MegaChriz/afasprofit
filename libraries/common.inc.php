<?php

/**
 * @file
 * Common functions.
 */

/**
 * First bootstrap phase: pure initializion.
 */
define('AFAS_BOOTSTRAP_INIT', 0);

/**
 * Second bootstrap phase: initialize configuration.
 */
define('AFAS_BOOTSTRAP_CONFIGURATION', 1);

/**
 * Full path to default location for config file.
 */
define('AFAS_DEFAULT_CONFIG_FILE', AFAS_ROOT . '/afasconfig.inc.php');

/**
 * Start Afas.
 *
 * @param $phase
 *   A constant. Allowed values are the DRUPAL_BOOTSTRAP_* constants.
 */
function afas_initialize($phase = NULL) {
  $phases = array(
    AFAS_BOOTSTRAP_INIT,
    AFAS_BOOTSTRAP_CONFIGURATION,
  );

  if (!isset($phase)) {
    $phase = AFAS_BOOTSTRAP_CONFIGURATION;
  }

  $current_phase = -1;

  if (isset($phase)) {
    while ($phases && $phase > $current_phase) {
      $current_phase = array_shift($phases);

      switch ($current_phase) {
        case AFAS_BOOTSTRAP_INIT:
          // Register autoload function.
          spl_autoload_register('afas_autoload');
          break;

        case AFAS_BOOTSTRAP_CONFIGURATION:
          afas_load_config(AFAS_DEFAULT_CONFIG_FILE);
          break;
      }
    }
  }
}

/**
 * Loads in config file.
 *
 * @param string $file
 *   The file to load.
 *
 * @return void
 */
function afas_load_config($file) {
  AfasConfig::getInstance()->load($file);
}

/**
 * Autoloads Afas classes.
 */
function afas_autoload($name = NULL) {
  if (class_exists($name, FALSE) || interface_exists($name, FALSE)) {
    return TRUE;
  }

  $lib_path = AFAS_ROOT . '/libraries';
  $files = scandir($lib_path);
  foreach ($files as $file) {
    if (is_dir($lib_path . '/' . $file) && $file != '.' && $file != '..') {
      // Look for a class first.
      $filepath = $lib_path . '/' . $file . '/' . $name . '.class.php';
      if (file_exists($filepath)) {
        require_once($filepath);
        return TRUE;
      }

      // Else look for an interface.
      $filepath = $lib_path . '/' . $file . '/' . $name . '.interface.php';
      if (file_exists($filepath)) {
        require_once($filepath);
        return TRUE;
      }
    }
  }

  return FALSE;
}
