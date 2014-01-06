<?php
/**
 * @file
 * Contains the DBServices class
 */

/**
 * Base class for services provided by this database server
 */
class DBServices {
  // ------------------------------------------------------------
  // PROPERTIES
  // ------------------------------------------------------------

  /**
   * The modus the class operates in:
   * - MODUS_XMLRPC
   * - MODUS_PHP
   * Defaults to MODUS_XMLRPC
   *
   * @var int $modus
   * @access private
   */
  private $modus;

  // ------------------------------------------------------------
  // CONSTANTS
  // ------------------------------------------------------------

  /**
   * Modi
   * @var int
   */
  const MODUS_XMLRPC = 1;
  const MODUS_PHP = 2;

  // ------------------------------------------------------------
  // CONSTRUCT
  // ------------------------------------------------------------

  /**
   * DBServices object constructor
   *
   * @param int $modus
   *   The modus the class operates in:
   *   - MODUS_XMLRPC
   *   - MODUS_PHP
   *   Defaults to MODUS_XMLRPC
   * @access public
   *
   * @return void
   */
  public function __construct($modus = 1) {
    $this->setModus($modus);
  }

  // ------------------------------------------------------------
  // SETTERS
  // ------------------------------------------------------------

  /**
   * Sets the modus the class operates in.
   *
   * @param int $modus
   *   The modus the class operates in:
   *   - MODUS_XMLRPC
   *   - MODUS_PHP
   *   Defaults to MODUS_XMLRPC
   * @access public
   *
   * @return void
   * @throws Exception
   */
  public function setModus($modus) {
    switch ($modus) {
      case self::MODUS_XMLRPC:
      case self::MODUS_PHP:
        $this->modus = $modus;
        break;
      default:
        throw new Exception('Modus not available in DBServices.');
    }
  }

  // ------------------------------------------------------------
  // GETTERS
  // ------------------------------------------------------------
  
  /**
   * Returns the modus the class currently operates in.
   *
   * @return int
   */
  public function getModus() {
    return $this->modus;
  }

  // ------------------------------------------------------------
  // ACTION
  // ------------------------------------------------------------

  /**
   * Returns the parameters
   *
   * @param array
   *   The arguments
   * @param int $number
   *   The number of parameters to return
   *
   * @return array
   */
  protected function getParams($args, $number = 1) {
    $params = array();
    switch ($this->modus) {
      case self::MODUS_XMLRPC:
        $raw_params = $args[0];
        for ($i = 0; $i < $number; $i++) {
          $params[] = @php_xmlrpc_decode($raw_params->getParam($i));
        }
        break;
      case self::MODUS_PHP:
        return $args;
    }
    return $params;
  }

  /**
   * Generates response, based on the modus the class operates in.
   *
   * @param mixed $response
   *   The response to return.
   *
   * @return mixed
   */
  protected function response($response) {
    switch ($this->modus) {
      case self::MODUS_XMLRPC:
        return new xmlrpcresp(php_xmlrpc_encode($response));
      case self::MODUS_PHP:
        return $response;
    }
  }
}
