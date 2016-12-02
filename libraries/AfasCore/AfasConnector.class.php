<?php
/**
 * @file
 * AFAS Connector Class
 *
 * This class is the base class for AFAS connections.
 * You can use this class to send out all type of requests.
 */

abstract class AfasConnector {
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

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * AfasConnector object constructor.
   *
   * @param AfasServer $p_oServer
   * @access public
   * @return void
   */
  public function __construct(AfasServer $p_oServer) {
    if ($p_oServer instanceof AfasServer) {
      $this->m_oServer = $p_oServer;
    }
    $this->init();
  }

  /**
   * Initialize values.
   *
   * @access public
   * @return void
   */
  public function init() {
    // Set default values.
    $this->m_oSoapClient = NULL;
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Load an existing item from an array.
   *
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
   * Location of the soap service to call, usually an url.
   *
   * @return string
   *   The location of the soap service.
   */
  abstract public function getLocation();

  /**
   * Returns AfasServer-object.
   *
   * @return AfasServer
   *   The server that is used to send a request to.
   */
  public function getServer() {
    return $this->m_oServer;
  }

  // --------------------------------------------------------------
  // LOGIC
  // --------------------------------------------------------------

  /**
   * Check if Soap Client object has been initiated.
   *
   * @access public
   * @return boolean
   */
  public function isConnected() {
    if ($this->m_oSoapClient instanceof SoapClient) {
      return TRUE;
    }
    return FALSE;
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Initiate Soap Client object.
   *
   * @param array $p_aOptions
   * @access public
   * @return void
   */
  public function connect($p_aOptions = array()) {
    // Setup options.
    $aOptions = array(
      'location' => $this->getLocation(),
      'uri' => $this->m_oServer->getUri(),
      'trace' => 1,
      'style' => SOAP_RPC,
      'use' => SOAP_ENCODED,
    );
    // Merge with additional options
    $aOptions = array_merge($aOptions, $p_aOptions);

    $this->m_oSoapClient = new MSSoapClient(NULL, $aOptions);
  }

  /**
   * Sends a SOAP request.
   *
   * @param string $p_sFunction
   * @param array $p_aParameters
   * @param array $p_aOptions
   * @access protected
   * @return void
   */
  protected function _sendRequest($p_sFunction, $p_aParameters = array(), $p_aOptions = array()) {
    if (!$this->isConnected()) {
      // Connect first.
      $this->connect();
    }

    // Setup parameters.
    $aParams = array(
      'token' => $this->m_oServer->getApiKeyAsXML(),
    );
    // Merge with additional parameters defined by subclasses.
    $this->_additionalParameters($aParams);
    // Merge with additional parameters.
    $aParams = array_merge($aParams, $p_aParameters);
    // Convert to SOAP parameters.
    $aSoapParams = array();
    foreach ($aParams as $sKey => $mValue) {
      $aSoapParams[] = new SoapParam($mValue, $sKey);
    }

    // Setup options.
    $aOptions = array(
      'soapaction' => $this->m_oServer->getUri() . '/' . $p_sFunction,
      'uri' => $this->m_oServer->getUri(),
    );
    // Merge with additional parameters defined by subclasses.
    $this->_additionalOptions($aOptions);
    // Merge with additional options.
    $aOptions = array_merge($aOptions, $p_aOptions);

    // Send request.
    $this->m_oSoapClient->__soapCall($p_sFunction, $aSoapParams, $aOptions);
  }

  // --------------------------------------------------------------
  // HOOKS
  // --------------------------------------------------------------

  /**
   * Subclasses can override this method and add additional
   * parameters for the request.
   *
   * @param array $p_aParams
   * @access protected
   * @return void
   */
  protected function _additionalParameters(&$p_aParams) { }

  /**
   * Subclasses can override this method and add additional
   * options for the request.
   *
   * @param array $p_aOptions
   * @access protected
   * @return void
   */
  protected function _additionalOptions(&$p_aOptions) { }

  // --------------------------------------------------------------
  // TEST
  // --------------------------------------------------------------

  /**
   * outputResponse().
   *
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
   * Response output for test purposes.
   *
   * @access public
   * @return string
   */
  public function testResponse() {
    if ($this->isConnected()) {
      $output = '';
      $output .= "\nDumping request headers:\n" . $this->m_oSoapClient->__getLastRequestHeaders();
      $output .= "\nDumping request:\n" . htmlentities($this->m_oSoapClient->__getLastRequest());
      $output .= "\nDumping response headers:\n" . $this->m_oSoapClient->__getLastResponseHeaders();
      $output .= "\nDumping response:\n" . $this->m_oSoapClient->__getLastResponse();
      return $output;
    }
    else {
      return 'No response';
    }
  }
}
