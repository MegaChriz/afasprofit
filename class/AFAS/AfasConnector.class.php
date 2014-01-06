<?php
/**
 * @file
 * AFAS Connector Class
 *
 * This class is the base class for AFAS connections.
 * You can use this class to send out all type of requests.
 */

class AfasConnector {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------
  
  /**
   * @var AfasServer
   * @access protected
   */
  protected $m_oServer;
  
  /**
   * @var SoapClient $m_oSoapClient
   * @access protected
   */
  protected $m_oSoapClient;
  
  /**
   * @var string $m_sLocation
   * @access protected
   */
  protected $m_sLocation;
  
  /**
   * @var string $m_sURI
   * @access protected
   */
  protected $m_sURI;
  
  /**
   * @var string $m_sEnvironment
   * @access protected
   */
  protected $m_sEnvironment;
  
  /**
   * @var string $m_sUser
   * @access protected
   */
  protected $m_sUser;
  
  /**
   * @var string $m_sPassword
   * @access protected
   */
  protected $m_sPassword;
  
  /**
   * @var string $m_sLogonAs
   * @access protected
   */
  protected $m_sLogonAs;
  
  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------
    
  /**
   * AfasConnector object constructor
   * @param AfasServer $p_oServer
   * @access public
   * @return void
   */
  public function __construct($p_oServer = NULL) {
    if ($p_oServer instanceof AfasServer) {
      $this->m_oServer = $p_oServer;
    }
    else {
      $this->m_oServer = AfasServerList::getInstance()->getDefault();
    }
    $this->init();
  }
  
  /**
   * Initialize values
   * @access public
   * @return void
   */
  public function init() {
    // Set default values
    $this->m_oSoapClient = NULL;
    $this->m_sLocation = "http://" . $this->m_oServer->ip_address . "/profitservices/";
    $this->m_sURI = 'urn:Afas.Profit.Services';
    $this->m_sEnvironment = $this->m_oServer->environment;
    $this->m_sLogonAs = '';
    $this->m_sUser = $this->m_oServer->user;
    $this->m_sPassword = $this->m_oServer->password;
  }
  
  /**
   * Initialize values
   * @access public
   * @return void
   */
  public function initOld() {
    // Set default values
    $this->m_oSoapClient = NULL;
    $this->m_sLocation = "http://" . AFAS_Config::$ip_address . "/profitservices/";
    $this->m_sURI = 'urn:Afas.Profit.Services';
    $this->m_sEnvironment = AFAS_Config::$environment;
    $this->m_sLogonAs = '';
    $this->m_sUser = AFAS_Config::$user;
    $this->m_sPassword = AFAS_Config::$password;
  }
  
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------
  
  /**
   * Setter
   * @param string $p_sMember
   * @param mixed $p_mValue
   * @access public
   * @return boolean
   */
  public function __set($p_sMember, $p_mValue) {
    $p_sMember = strtolower($p_sMember);
    switch ($p_sMember) {
      case 'location':
        $this->m_sLocation = (string) $p_mValue;
        return true;
        break;
      case 'uri':
        $this->m_sURI = (string) $p_mValue;
        return true;
        break;
      case 'environment':
      case 'environmentid':
        $this->m_sEnvironment = (string) $p_mValue;
        return true;
        break;
      case 'user':
      case 'userid':
        $this->m_sUser = (string) $p_mValue;
        return true;
        break;
      case 'password':
        $this->m_sPassword = (string) $p_mValue;
        return true;
        break;
      case 'logonas':
        $this->m_sLogonAs = (string) $p_mValue;
        return true;
        break;
    }
    return false;
  }
  
  /**
   * Load an existing item from an array.
   * @param array $p_aParams
   * @access public
   * @return void
   */
  function from_array($p_aParams) {
    foreach ($p_aParams as $sKey => $mValue) {
      $this->__set($sKey, $mValue);
    }
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
    $p_sMember = strtolower($p_sMember);
    switch ($p_sMember) {
      case 'location':
        return $this->m_sLocation;
      case 'uri':
        $this->m_sURI = (string) $p_mValue;
        return $this->m_sURI;
      case 'environment':
      case 'environmentid':
        $this->m_sEnvironment = (string) $p_mValue;
        return $this->m_sEnvironment;
      case 'user':
      case 'userid':
        return $this->m_sUser;
      case 'password':
        return $this->m_sPassword;
      case 'logonas':
        return $this->m_sLogonAs;
    }
    return NULL;
  }
  
  // --------------------------------------------------------------
  // LOGIC
  // --------------------------------------------------------------
  
  /**
   * Check if Soap Client object has been initiated.
   * @access public
   * @return boolean
   */
  public function isConnected() {
    if ($this->m_oSoapClient instanceof SoapClient) {
      return true;
    }
    return false;
  }
  
  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------
  
  /**
   * Initiate Soap Client object
   * @param array $p_aOptions
   * @access public
   * @return void
   */
  public function connect($p_aOptions=array()) {
    // Setup options
    $aOptions = array(
      'location' => $this->m_sLocation,
      'uri' => $this->m_sURI,
      'trace' => 1,
      "style" => SOAP_RPC,
      "use"   => SOAP_ENCODED,      
    );
    // Merge with additional options
    $aOptions = array_merge($aOptions, $p_aOptions);
    
    $this->m_oSoapClient = new MSSoapClient(NULL, $aOptions);
  }

  /**
   * Send a SOAP request
   * @param string $p_sFunction
   * @param array $p_aParameters
   * @param array $p_aOptions
   * @access protected
   * @return void
   */
  protected function _sendRequest($p_sFunction, $p_aParameters=array(), $p_aOptions=array()) {
    if (!$this->isConnected()) {
      // Connect first
      $this->connect();
    }
    
    // Set action to call
    $this->m_oSoapClient->setAction($p_sFunction);
    
    // Setup parameters
    $aParams = array(
      'environmentId' => $this->m_sEnvironment,
      'userId' => $this->m_sUser,
      'password' => $this->m_sPassword,
      'logonAs' => $this->m_sLogonAs,
    );
    // Merge with additional parameters defined by subclasses
    $this->_additionalParameters($aParams);
    // Merge with additional parameters
    $aParams = array_merge($aParams, $p_aParameters);
    // Convert to SOAP parameters
    $aSoapParams = array();
    foreach ($aParams as $sKey => $mValue) {
      $aSoapParams[] = new SoapParam($mValue, $sKey);
    }
    
    // Setup options
    $aOptions = array(
      'soapaction' => $this->m_sURI . '/' . $p_sFunction,
      'uri' => $this->m_sURI,
    );
    // Merge with additional parameters defined by subclasses
    $this->_additionalOptions($aOptions);
    // Merge with additional options
    $aOptions = array_merge($aOptions, $p_aOptions);
    $this->m_oSoapClient->__soapCall($p_sFunction, $aSoapParams, $aOptions);
  }
  
  // --------------------------------------------------------------
  // HOOKS
  // --------------------------------------------------------------
  
  /**
   * Subclasses can override this method and add additional
   * parameters for the request
   * @param array $p_aParams
   * @access protected
   * @return void
   */
  protected function _additionalParameters(&$p_aParams) {
  
  }
  
  /**
   * Subclasses can override this method and add additional
   * options for the request
   * @param array $p_aOptions
   * @access protected
   * @return void
   */
  protected function _additionalOptions(&$p_aOptions) {
  
  }
  
  // --------------------------------------------------------------
  // TEST
  // --------------------------------------------------------------
  
  /**
   * outputResponse()
   * @access public
   * @return string XML
   */
  public function outputResponse() {
    if ($this->isConnected()) {
      header("content-type: text/xml");
      print $this->m_oSoapClient->__getLastResponse();
    }
  }
  
  /**
   * Response output for test purposes
   * @access public
   * @return string
   */
  public function testResponse() {
    if ($this->isConnected()) {
      $output = '';
      $output .= "\nDumping request headers:\n" . $this->m_oSoapClient->__getLastRequestHeaders();
      $output .= "\nDumping request:\n".$this->m_oSoapClient->__getLastRequest();      
      $output .= "\nDumping response headers:\n" . $this->m_oSoapClient->__getLastResponseHeaders();      
      $output .= "\nDumping response:\n".$this->m_oSoapClient->__getLastResponse();
      return $output;
    }
    else {
      return 'No response';
    }
  }
}