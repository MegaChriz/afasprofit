<?php
/**
 * @file
 * AfasServerList class
 */

class AfasServerList
{
  // --------------------------------------------------------------
  // STATIC PROPERTIES
  // --------------------------------------------------------------
  
  /**
   * @var AfasServerList
   * @static
   * @access private
   */
  private static $s_oSingleton;
  
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------
  
  /**
   * @var array
   * @access private
   */
  private $m_aServers;
  
  /**
   * @var AfasServer
   * @access private
   */
  private $m_oDefaultServer;
  
  /**
   * @var boolean
   * @access private
   */
  private $m_bAllLoaded;
  
  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------
  
  /**
   * AfasServerList object constructor
   * @access private
   * @return void
   */
  private function __construct() {
    $this->m_aServers = array();
    $this->m_bAllLoaded = FALSE;
  }
  
  /**
   * Disallow cloning
   * @access private
   * @return void
   */
  private function __clone() {}
  
  /**
   * Get list instance
   * @access public
   * @static
   * @return AfasServerList
   */
  public static function getInstance() {
    if (!(self::$s_oSingleton instanceof AfasServerList)) {
      self::$s_oSingleton = new self();
    }
    return self::$s_oSingleton;
  }
  
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------
  
  /**
   * Adds connection to the list
   *
   * @param AfasServer $p_oServer
   * @access public
   * @return AfasServer
   */
  public function addServer(AfasServer $p_oServer = null) {
    if (!$p_oServer) {
      return new AfasServer();
    }
    
    // Check if connection is already available in the list
    foreach ($this->m_aServers as $oServer) {
      if ($oServer === $p_oServer) {
        // Yes, already available. Returning connection.
        return $p_oServer;
      }
    }

    // Save the default connection
    if ($p_oServer->isDefault()) {
      foreach ($this->m_aServers as $oServer) {
        if ($oServer->isDefault()) {
          $oServer->privSetProperty('default', FALSE);
        }
      }
      $this->m_oDefaultServer = $p_oServer;
    }
    
    // Add connection to the list
    $this->m_aServers[$p_oServer->server_id] = $p_oServer;
    return $p_oServer;
  }
  
  /**
   * Set a connection as the default connection
   * @param AfasServer $p_oServer
   * @access public
   * @return void
   */
  public function setServerAsDefault(AfasServer $p_oServer) {
    try {
      $oServer = $this->getDefault();
      $oServer->privSetProperty('default', FALSE);
    }
    catch (Exception $e) {}
    
    $p_oServer->privSetProperty('default', TRUE);
    $this->m_oDefaultServer = $p_oServer;
  }
  
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
  
  /**
   * Get a connection by ID
   *
   * @param int $p_iServer_id
   *   The id of the connection
   * @access public
   * @return AfasServer
   */
  public function getServer($p_iServer_id) {
    $this->loadOne($p_iServer_id);
    if (isset($this->m_aServers[$p_iServer_id])) {
      return $this->m_aServers[$p_iServer_id];
    }
    return FALSE;
  }
  
  /**
   * Returns default AfasServer
   * @param boolean $return_empty
   *   If FALSE, an exception will be throwed when a default connection is
   *   not available.
   *   If TRUE, when there is no default connection, a new connection will be returned
   *   and an exception will not be throwed.
   * @access public
   * @return AfasServer
   * @throws AfasException
   */
  public function getDefault($return_empty = FALSE) {
    if (!($this->m_oDefaultServer instanceof AfasServer)) {
      $this->loadDefault();
    }
    if (!($this->m_oDefaultServer instanceof AfasServer)) {
      if ($return_empty) {
        return new AfasServer();
      }
      else {
        throw new AfasException(t('No default connection available.'));
      }
    }
    return $this->m_oDefaultServer;
  }
  
  /**
   * Get all connections
   *
   * @access public
   * @return array
   */
  public function getServers() {
    $this->loadAll();
    return $this->m_aServers;
  }
  
  // --------------------------------------------------------------
  // SAVING / DELETING
  // --------------------------------------------------------------
  
  /**
   * Saves every connection currently loaded.
   *
   * @access public
   * @return void
   */
  public function save() {
    // Make sure at least one connection is the default one.
    try {
      $oDefault = $this->getDefault();
    }
    catch (Exception $e) {
      // No default connection found
      $oServer = current($this->m_aServers);
      $oServer->setDefault();
    }
  
    foreach ($this->m_aServers as $iServer_id => $oServer) {
      $oServer->save();
    }
  }
  
  /**
   * Deletes a connection by ID
   *
   * This will delete a connection from the database.
   * Returns true if deleting was succesful.
   *
   * @param int $p_iServer_id
   *   The id of the connection
   * @access public
   * @return boolean
   */
  public function deleteServer($p_iServer_id) {
    return $this->deleteOne($p_iServer_id);
  }
  
  // --------------------------------------------------------------
  // DATABASE REQUESTS
  // --------------------------------------------------------------
  
  /**
   * Load a single connection
   * @param int $p_iServer_id
   * @access private
   * @return void
   */
  private function loadOne($p_iServer_id) {
    // Reasons to skip out early
    if ($this->m_bAllLoaded) {
      return;
    }

    if (isset($this->m_aServers[$p_iServer_id])) {
      return;
    }

    // Execute query
    $sQuery = "SELECT * FROM {afas_servers} WHERE server_id=%d";
    $rResult = db_query($sQuery, $p_iServer_id);
    
    $this->dbResultToServer($rResult);
  }
  
  /**
   * Load default connection
   * @access private
   * @return void
   */
  private function loadDefault() {
    // Reasons to skip out early
    if ($this->m_oDefaultServer instanceof AfasServer) {
      return;
    }

    // Execute query
    $sQuery = "SELECT * FROM {afas_servers} WHERE is_default=1";
    $rResult = db_query($sQuery);
    
    $this->dbResultToServer($rResult);
  }
  
  /**
   * Load all connections
   * @access private
   * @return void
   */
  private function loadAll() {
    // Reasons to skip out early
    if ($this->m_bAllLoaded) {
      return;
    }
    
    // Execute query
    $sQuery = "SELECT * FROM {afas_servers}";
    $rResult = db_query($sQuery);
    
    $this->dbResultToServer($rResult);
  }
  
  /**
   * Creates AfasServer objects from a database resource.
   *
   * @param resource $result
   *   Database result
   * @access private
   * @return void
   * @throws AfasException
   */
  private function dbResultToServer($result) {
    if ($result === FALSE) {
      throw new AfasException(t('Failed to read from database table afas_servers'));
    }
    
    // Create each AfasServer object from the database record
    while ($obj = db_fetch_object($result)) {
      // Skip connections that have already been loaded (and perhaps modified)
      if (!isset($this->m_aServers[$obj->server_id])) {
        $oServer = new AfasServer($obj);
        if ($oServer->isDefault()) {
          $this->m_oDefaultServer = $oServer;
        }
        $this->m_aServers[$oServer->id] = $oServer;
      }
    }
  }
  
  /**
   * Deletes one connection
   *
   * @param int $p_iServer_id
   * @access private
   * @return boolean
   * @throws AfasException
   */
  private function deleteOne($p_iServer_id) {
    // We can't delete a connection that is a default connection, so
    // we'll need to make sure this connection is loaded
    $this->loadOne($p_iServer_id);
    
    $oServer = $this->getServer($p_iServer_id);
    
    if ($oServer->isDefault()) {
      return FALSE;
    }
    
    // Delete the connection
    $result = db_query("DELETE FROM {afas_servers} WHERE server_id = %d", $oServer->server_id);
    if ($result === FALSE || db_affected_rows() == 0) {
      throw new AfasException(t('Failed to delete a connection from database table afas_servers'));
    }
    
    // Remove from the list
    unset($this->m_aServer[$oServer->server_id]);
    
    return TRUE;
  }
}