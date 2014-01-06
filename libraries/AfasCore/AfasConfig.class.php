<?php

/**
 * @file
 * Contains AfasConfig class.
 *
 * @todo In opbouw.
 */

/**
 * Class for managing Afas Configuration.
 */
class AfasConfig {
  // ---------------------------------------------------------------------------
  // STATIC PROPERTIES
  // ---------------------------------------------------------------------------

  /**
   * AfasConfig instance
   * @var AfasConfig
   */
  private static $_instance;

  // ---------------------------------------------------------------------------
  // PROPERTIES
  // ---------------------------------------------------------------------------

  /**
   * Stores default config from config.default.php
   *
   * @var array
   */
  private $cfg;

  /**
   * The config file that was loaded.
   *
   * @var string
   */
  private $source;

  // ---------------------------------------------------------------------------
  // CONSTRUCT
  // ---------------------------------------------------------------------------

  /**
   * Private constructor, use {@link getInstance()}
   */
  private function __construct() {
    $this->loadDefaults();
  }

  /**
   * Returns class instance.
   *
   * @return AfasConfig
   */
  public static function getInstance() {
    if (is_null(self::$_instance)) {
      self::$_instance = new AfasConfig();
    }
    return self::$_instance;
  }

  // ---------------------------------------------------------------------------
  // GETTERS
  // ---------------------------------------------------------------------------

  /**
   * Get a config setting.
   *
   * @param string $config
   *   The name of the config to load.
   *
   * @return mixed
   */
  public static function get($config) {
    return self::getInstance()->getSetting($config);
  }

  /**
   * Get a config setting.
   *
   * @param string $config
   *   The name of the config to load.
   *
   * @return mixed
   */
  public function getSetting($config) {
    if (isset($this->cfg[$config])) {
      return $this->cfg[$config];
    }
    return NULL;
  }

  /**
   * Returns source for current config.
   *
   * @return string
   *   Path to the config source file.
   */
  public function getSource() {
    return $this->source;
  }

  // ---------------------------------------------------------------------------
  // SETTERS
  // ---------------------------------------------------------------------------

  /**
   * Loads in default configuration.
   */
  public function loadDefaults() {
    // Load default config values.
    $this->cfg = array();
    $afas_conf = &$this->cfg;
    require AFAS_ROOT . '/libraries/config.default.php';
  }

  /**
   * Load a configuration file.
   */
  public function load($source = NULL) {
    $this->loadDefaults();

    if (!is_null($source)) {
      $this->setSource($source);
    }

    if (!$this->checkConfigSource()) {
      return FALSE;
    }

    $afas_conf = &$this->cfg;

    // Parses the configuration file.
    $old_error_reporting = error_reporting(0);
    if (function_exists('file_get_contents')) {
      $eval_result = eval('?' . '>' . trim(file_get_contents($this->getSource())));
    }
    else {
      $eval_result = eval('?' . '>' . trim(implode("\n", file($this->getSource()))));
    }
    error_reporting($old_error_reporting);

    // Fix up configs.
    require AFAS_ROOT . '/libraries/config-after-load.php';

    if ($eval_result === FALSE) {
      throw new AfasException('Error with config file.');
    }
  }

  /**
   * Sets config source file.
   *
   * @param string $source
   */
  private function setSource($source) {
    $this->source = trim($source);
  }

  /**
   * Sets configuration data (overrides old data).
   *
   * @param array $cfg
   *   The config to override.
   */
  public function setConfigData(array $cfg) {
    $this->cfg = $cfg;
  }

  // ---------------------------------------------------------------------------
  // ACTION
  // ---------------------------------------------------------------------------

  /**
   * check config source
   *
   * @return  boolean whether source is valid or not
   */
  function checkConfigSource() {
    if (!$this->getSource()) {
      // No configuration file set at all
      return FALSE;
    }

    if (!file_exists($this->getSource())) {
      return FALSE;
    }

    if (!is_readable($this->getSource())) {
      throw new AfasException('Existing configuration file (' . $this->getSource() . ') is not readable.');
    }

    return TRUE;
  }
}
