<?php
/**
 * @file
 * AfasServer class
 */

class AfasServer
{
  // -----------------------------------------------------------------------------
  // STATIC PROPERTIES
  // -----------------------------------------------------------------------------
  
  /**
   * New temporary ID
   *
   * @var int
   * @access private
   * @static
   */
  static private $nextNewId = -1;
  
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------
  
  /**
   * @var int $server_id
   * @access private
   */
  private $server_id;
  
  /**
   * Name of this connection
   * @var string $name
   * @access private
   */
  private $name;
  
  /**
   * Het IP-adres van AFAS Connector
   * @var string $ip_address
   * @access private
   */
  private $ip_address;
  
  /**
   * De te gebruiken AFAS omgeving
   * @var string $environment
   * @access private
   */
  private $environment;
  
  /**
   * Gebruiker van de AFAS Connector
   * @var string $user
   * @access private
   */
  private $user;
  
  /**
   * Wachtwoord van de gebruiker van de AFAS Connector
   * @var string $user
   * @access private
   */
  private $password;
  
  /**
   * Of dit de standaard connectie is
   * @var boolean $default
   * @access private
   */
  private $is_default;
  
  /**
   * TRUE if the connection is changed after being loaded or created.
   * @var boolean
   * @access private
   */
  private $dirty;
  
  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------
  
  /**
   * AfasServer object constructor
   * @param object $p_oRecord
   *   Database record object
   * @access public
   * @return void
   */
  public function __construct($p_oRecord = NULL) {
    // Set server ID if given
    if (isset($p_oRecord->server_id)) {
      $this->server_id = $p_oRecord->server_id;
    }
    else {      
      // We always need an ID
      $this->server_id = self::$nextNewId--;
    }
    
    // Set other given values
    if (is_object($p_oRecord)) {
      foreach ($p_oRecord as $sProperty => $mValue) {
        $this->privSetProperty($sProperty, $mValue);
      }
    }
    
    if ($this->server_id > 0) {
      // If a connection is just loaded, mark this instance as 'clean' (= unchanged).
      $this->dirty = FALSE;
    }
    
    // Add to connection list
    AfasServerList::getInstance()->addServer($this);
  }
  
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
  
  /**
   * Getter
   * @param string $p_sMember
   * @access public
   * @return mixed
   */
  public function __get($p_sMember) {
    if (isset($this->$p_sMember)) {
      return $this->$p_sMember;
    }
    elseif ($p_sMember == 'id') {
      return $this->server_id;
    }
    return NULL;
  }
  
  /**
   * Returns if this connection is the default one
   * @access public
   * @return boolean
   */
  public function isDefault() {
    return $this->is_default;
  }
  
  /**
   * Return as an array of values.
   *
   * @access public
   * @return array
   */
  public function toArray() {
    $aData = array(
      'server_id' => $this->server_id,
      'name' => $this->name,
      'ip_address' => $this->ip_address,
      'environment' => $this->environment,
      'user' => $this->user,
      'password' => $this->password,
      'is_default' => $this->is_default,
    );
    return $aData;
  }
  
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------
  
  /**
   * Setter
   * @param string $p_sMember
   * @param string $p_sValue
   * @access public
   * @return boolean
   */
  public function __set($p_sMember, $p_sValue) {
    switch ($p_sMember) {
      case 'name':
      case 'ip_address':
      case 'environment':
      case 'user':
      case 'password':
        $this->$p_sMember = (string) $p_sValue;
        $this->dirty = TRUE;
        return TRUE;
    }
    return FALSE;
  }

  /**
   * Sets this connection as the default connection
   * @access public
   * @return void
   */
  public function setDefault() {
    AfasServerList::getInstance()->setServerAsDefault($this);
  }
  
  // -----------------------------------------------------------------------------
  // SAVING / DELETING
  // -----------------------------------------------------------------------------

  /**
   * Saves connection
   *
   * @access public
   * @return void
   * @throws AfasException
   */
  public function save() {
    if ($this->dirty) {
      $aData = $this->toArray();
      if ($this->server_id < 0) {
        $result = server_write_record('afas', 'afas_servers', $aData);
        $this->server_id = $aData['server_id'];
      }
      else {
        $result = server_write_record('afas', 'afas_servers', $aData, array('server_id'));
      }
      if ($result === FALSE) {
        throw new AfasException(t('Failed to write connection with id = %server_id', array('%server_id' => $this->server_id)));
      }
    }
  }
  
  /**
   * Deletes connection from list
   *
   * @access public
   * @return boolean
   */
  public function delete() {
    return AfasServerList::getInstance()->deleteServer($this->server_id);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------
  
  /**
   * Creates an updateConnector
   * @param string $p_sConnectorType
   * @access public
   * @return AfasUpdateConnector
   */
  public function updateConnector($p_sConnectorType) {
    $oConnector = new AfasUpdateConnector($p_sConnectorType, $this);
    return $oConnector;
  }
  
  /**
   * Creates an getConnector
   * @access public
   * @return AfasGetConnector
   */
  public function getConnector() {
    $oConnector = new AfasGetConnector($this);
    return $oConnector;
  }
  
  // --------------------------------------------------------------
  // PRIVATE METHODS
  // to be called by AfasServerList
  // --------------------------------------------------------------
  
  /**
   * Sets a private variable
   *
   * This method should only be called by AfasServerList.
   *
   * @param string $p_sMember
   * @param mixed $p_mValue
   * @access public
   * @return void
   */
  public function privSetProperty($p_sMember, $p_mValue) {
    switch ($p_sMember) {
      case 'default':
      case 'is_default':
        $this->is_default = ($p_mValue) ? TRUE:FALSE;
        break;
      default:
        $this->__set($p_sMember, $p_mValue);
        break;
    }
    $this->dirty = TRUE;
  }
}
