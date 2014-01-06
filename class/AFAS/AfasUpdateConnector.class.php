<?php
/**
 * @file
 * AFAS Connector class for 'Update' connections.
 *
 * This class extends the AfasConnector class with specific methods for writing data
 */
 
class AfasUpdateConnector extends AfasConnector implements iAFAS_Element
{
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------
  
  /**
   * Een lijst met objecten van het type AFAS_Element
   *
   * @var array
   * @access private
   */
  private $m_aElements;
  
  /**
   * Connector type
   *
   * @var string
   * @access private
   */
  private $m_sConnectorType;
  
  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------
  
  /**
   * AfasUpdateConnector object constructor
   * @param AfasServer $p_sServer
   * @param string $p_sConnectorType
   * @access public
   * @return void
   */
  public function __construct($p_sConnectorType, $p_oServer = NULL) {
    parent::__construct($p_oServer);
    $this->m_sConnectorType = (string) $p_sConnectorType;
    $this->init();
  }
  
  /**
   * Initialize values
   * @overloaded
   * @access public
   * @return void
   */
  public function init() {
    parent::init();
    $this->m_aElements = array();
    $this->m_sLocation = "http://" . $this->m_oServer->ip_address . "/profitservices/updateconnector.asmx";
  }
  
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------
  
  /**
   * Adds an element object
   * @param string $p_sObject_id
   * @param string $p_sClass
   * @access public
   * @return AFAS_Element
   * @throws AfasException
   */
  public function addElement($p_sElement_id = '', $p_sClass = 'AFAS_Element') {
    $p_sElement_id = (string) $p_sElement_id;
    
    // Check if class extends AFAS_Element
    if ($p_sClass != 'AFAS_Element') {
      $aParents = class_parents($p_sClass);
      if (!isset($aParents['AFAS_Element'])) {
        throw new AfasException('Element must be of type AFAS_Element');
      }
    }
    
    $oElement = new $p_sClass($this, $this->m_sConnectorType, $p_sElement_id);
    $this->m_aElements[$oElement->object_id] = $oElement;
    return $oElement;
  }
  
  /**
   * Adds an element object by giving the object
   * @param AFAS_Element $p_oElement
   * @access public
   * @return void
   */
  public function addElementByObject(AFAS_Element $p_oElement) {
    $this->m_aElements[$p_oElement->object_id] = $p_oElement;
    $p_oElement->changeParent($this);
  }
  
  /**
   * Removes an element object
   * @param string $p_sObject_id
   * @access public
   * @return void
   */
  public function removeElement($p_sElement_id) {
    if (isset($this->m_aElements[$p_sElement_id])) {
      unset($this->m_aElements[$p_sElement_id]);
    }
  }
  
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
  
  /**
   * Returns Elements
   * @access public
   * @return array
   */
  public function getElements() {
    return $this->m_aElements;
  }
  
  /**
   * Returns specific element
   * @access public
   * @return AFAS_Element
   * @throws AfasException
   */
  public function getElement($p_sElement_id) {
    if (!isset($this->m_aElements[$p_sElement_id])) {
      throw new AfasException('Element ' . $p_sElement_id  . ' does not exists');
    }
    return $this->m_aElements[$p_sElement_id];
  }
  
  /**
   * Returns Elements XML
   * @access public
   * @return string XML
   */
  public function getElementsXML() {
    $sOutput .= '<' . $this->m_sConnectorType . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
    foreach ($this->m_aElements as $oElement) {
      $sOutput .= $oElement->getXML();
    }    
    $sOutput .= '</' . $this->m_sConnectorType . '>';
    return $sOutput;
  }
  
  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------
  
  /**
   * Send a SOAP request
   * @param string $p_sFunction
   * @param string $p_sConnectorVersion
   * @param array $p_aParameters
   * @param array $p_aOptions
   * @access public
   * @return void
   */
  public function sendRequest($p_sFunction='Execute', $p_sConnectorVersion=1, $p_aParameters=array(), $p_aOptions=array()) {
    $p_aParameters['connectorType'] = $this->m_sConnectorType;
    $p_aParameters['connectorVersion'] = $p_sConnectorVersion;
    $this->_sendRequest($p_sFunction, $p_aParameters, $p_aOptions);
  }
  
  // --------------------------------------------------------------
  // HOOK IMPLEMENTATIONS
  // --------------------------------------------------------------
  
  /**
   * Implementation of _additionalParameters().
   * @param array $p_aParams
   * @overloaded
   * @access protected
   * @return void
   */
  protected function _additionalParameters(&$p_aParams) {
    // Add data to the request if defined
    if (count($this->m_aElements) > 0) {
      $p_aParams['dataXml'] = $this->getElementsXML();
    }
  }

  // --------------------------------------------------------------
  // INTERFACE IMPLEMENTATIONS
  // --------------------------------------------------------------
  
  /**
   * Adds a child object
   * @param string $p_sType
   * @param string $p_sObject_id
   * @param array $p_aFields
   * @access public
   * @return AFAS_Element
   */
  public function addChild($p_sType, $p_sObject_id = '', $p_aFields = array(), $p_sClass = 'AFAS_Element') {
    return $this->addElement($p_sObject_id, $p_sClass);
  }
  
  /**
   * Removes a child object
   * @param string $p_sObject_id
   * @access public
   * @return void
   */
  public function removeChild($p_sObject_id) {
    $this->removeElement($p_sObject_id);
  }
  
  /**
   * Returns specific child object
   * @access public
   * @return AFAS_Element
   * @throws AfasException
   */
  public function getChild($p_sObject_id) {
    return $this->getElement($p_sObject_id);
  }
}