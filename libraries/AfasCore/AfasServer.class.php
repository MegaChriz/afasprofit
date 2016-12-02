<?php

/**
 * @file
 * Contains AfasServer class.
 *
 * @todo In opbouw.
 */

/**
 * Class for an Afas server definition.
 */
class AfasServer {
  // --------------------------------------------------------------
  // STATIC PROPERTIES
  // --------------------------------------------------------------

  /**
   * List of servers currently loaded.
   */
  private static $_servers;

  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * Name of this connection
   * @var string $name
   * @access private
   */
  private $name;

  /**
   * The base url of the server.
   *
   * @var string
   */
  private $baseUrl;

  /**
   * The API key to use.
   *
   * @var string
   */
  private $apiKey;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * AfasServer object constructor.
   */
  protected function __construct($vars) {
    $this->name = $vars['name'];
    $this->baseUrl = $vars['host'];
    $this->uri = 'urn:Afas.Profit.Services';
    $this->apiKey = $vars['token'];
  }

  /**
   * Returns a server.
   *
   * @param string $name
   *
   * @return AfasServer
   *   An instance of AfasServer.
   */
  public static function get($name = NULL) {
    if (!empty($name) && isset(self::$_servers[$name])) {
      return self::$_servers[$name];
    }

    // Load server info from config.
    $servers = AfasConfig::get('servers');
    if (empty($servers)) {
      // No servers found!
      throw new AfasException('No Afas servers found. Specify servers in afasconfig.inc.php.');
    }
    if (empty($name)) {
      // Load the default server.
      $name = AfasConfig::get('default_server');
      if (!empty($name)) {
        return self::get($name);
      }
      else {
        // If, in the rare case, the server name is still empty,
        // load the first one in the list.
        $server = reset($servers);
        return self::get($server['name']);
      }
    }
    if (isset($servers[$name])) {
      $oServer = new self($servers[$name]);
      self::$_servers[$name] = $oServer;
      return $oServer;
    }

    // No server found.
    throw new AfasException(strtr('Server !server not found', array('!server' => $name)));
  }

  /**
   * Check if a given server is defined.
   */
  public static function exists($name) {
    if (isset(self::$_servers[$name])) {
      return TRUE;
    }

    // Load server info from config.
    $servers = AfasConfig::get('servers');
    if (isset($servers[$name])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Creates a new Server.
   *
   * @param array $vars
   *   The vars to fill the server with.
   *
   * @return AfasServer
   *   An instance of this class.
   */
  public static function create($vars) {
    // Check if it contains all required values.
    static $required = array(
      'name',
      'host',
      'token',
    );
    foreach ($required as $property_name) {
      if (!isset($vars[$property_name])) {
        throw new AfasException(strtr('Creating AfasServer failed, because !property was not defined.', array('!property' => $property_name)));
      }
    }

    // Now, check if this server already exists.
    if (self::exists($vars['name'])) {
      return self::get($vars['name']);
    }
    // Finally, create a new one.
    $oServer = new self($vars);
    self::$_servers[$vars['name']] = $oServer;
    return $oServer;
  }

  /**
   * Returns the default server.
   *
   * @return AfasServer
   *   An instance of AfasServer.
   * @throws AfasException
   *   In case there is no default server.
   */
  public static function getDefault() {
    return self::get(AfasConfig::get('default_server'));
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Magic Getter.
   *
   * @param string $property
   *   The property to get
   *
   * @return mixed
   */
  public function __get($property) {
    if (isset($this->$property)) {
      return $this->$property;
    }
    return NULL;
  }

  /**
   * Returns the server's name.
   *
   * @return string
   *   The server's name.
   */
  public function getId() {
    return $this->name;
  }

  /**
   * Returns the base url of the Profit server.
   *
   * @return string
   *   The server's base url.
   */
  public function getBaseUrl() {
    return $this->baseUrl;
  }

  /**
   * Returns the uri of the Profit server.
   *
   * @return string
   *   The server's uri.
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * Returns the Profit API key to use.
   *
   * @return string
   *   The server's api key.
   */
  public function getApiKey() {
    return $this->apiKey;
  }

  /**
   * Returns the Profit API key to use as XML.
   *
   * @return string
   *   The server's api key, generated as XML.
   */
  public function getApiKeyAsXML() {
    return '<token><version>1</version><data>' . $this->apiKey . '</data></token>';
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Creates an updateConnector.
   *
   * @param string $p_sConnectorType
   * @access public
   * @return AfasUpdateConnector
   */
  public function updateConnector($p_sConnectorType) {
    $oConnector = new AfasUpdateConnector($p_sConnectorType, $this);
    return $oConnector;
  }

  /**
   * Creates an getConnector.
   *
   * @access public
   * @return AfasGetConnector
   */
  public function getConnector() {
    $oConnector = new AfasGetConnector($this);
    return $oConnector;
  }
}
