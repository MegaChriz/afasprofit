<?php
/**
 * @file
 * AFAS Connector class for 'Update' connections.
 *
 * This class extends the AfasConnector class with specific methods for writing data
 */

class AfasUpdateConnector extends AfasConnector implements iAfasElement {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * Instance of AfasElement.
   *
   * Only the child objects of this instance will be used to
   * construct the XML. Other properties of this object will
   * be completely ignored.
   *
   * @var AfasElement
   * @access private
   */
  private $m_oElement;

  /**
   * Connector type.
   *
   * @var string
   * @access private
   */
  private $m_sConnectorType;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * AfasUpdateConnector object constructor.
   *
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
   * Initialize values.
   *
   * @overloaded
   * @access public
   * @return void
   */
  public function init() {
    parent::init();
    $this->m_oElement = new AfasElement($this, $this->m_sConnectorType);
    $this->m_sLocation = "https://" . $this->m_oServer->ip_address . "/profitservices/updateconnector.asmx";
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Adds an element object.
   *
   * @param string $p_sObject_id
   * @param string $p_sClass
   *
   * @access public
   * @return AfasElement
   * @throws AfasException
   */
  public function addElement($p_sElement_id = '', $p_sClass = 'AfasElement') {
    return $this->m_oElement->addChild($this->m_sConnectorType, $p_sElement_id, array(), $p_sClass);
  }

  /**
   * Adds an element object by giving the object.
   *
   * @param AfasElement $p_oElement
   * @access public
   * @return void
   */
  public function addElementByObject(AfasElement $p_oElement) {
    return $this->m_oElement->addChildByObject($p_oElement);
  }

  /**
   * Removes an element object.
   *
   * @param string $p_sObject_id
   * @access public
   * @return void
   */
  public function removeElement($p_sElement_id) {
    return $this->m_oElement->removeChild($p_sElement_id);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getLocation() {
    return $this->getServer()->getBaseUrl() . '/appconnectorupdate.asmx';
  }

  /**
   * Returns Elements
   * @access public
   * @return array
   */
  public function getElements() {
    return $this->m_oElement->getChilds();
  }

  /**
   * Returns specific element.
   *
   * @access public
   * @return AfasElement
   * @throws AfasException
   */
  public function getElement($p_sElement_id) {
    return $this->m_oElement->getChild($p_sElement_id);
  }

  /**
   * Returns Elements XML.
   *
   * @access public
   * @return string XML
   */
  public function getElementsXML() {
    $oDoc = new DOMDocument();
    // Connector XML.
    $oXMLConnector = $oDoc->createElement($this->m_sConnectorType);
    $oDoc->appendChild($oXMLConnector);
    $oXMLConnector->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    // Childs.
    foreach ($this->m_oElement->getChilds() as $oElement) {
      $oXMLChild = $oElement->getXML($oDoc);
      $oXMLConnector->appendChild($oXMLChild);
    }
    return $oDoc->saveXML($oXMLConnector);
  }

  /**
   * Returns which connector type was chosen when creating the update connector.
   *
   * @return string
   */
  public function getConnectorType() {
    return $this->m_sConnectorType;
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Sends a SOAP request.
   *
   * @param string $p_sFunction
   * @param string $p_sConnectorVersion
   * @param array $p_aParameters
   * @param array $p_aOptions
   * @access public
   * @return void
   */
  public function sendRequest($p_sFunction = 'Execute', $p_sConnectorVersion = 1, $p_aParameters = array(), $p_aOptions = array()) {
    $p_aParameters['connectorType'] = $this->m_sConnectorType;
    $p_aParameters['connectorVersion'] = $p_sConnectorVersion;
    $this->_sendRequest($p_sFunction, $p_aParameters, $p_aOptions);
  }

  // --------------------------------------------------------------
  // HOOK IMPLEMENTATIONS
  // --------------------------------------------------------------

  /**
   * Implements _additionalParameters().
   *
   * @param array $p_aParams
   * @overloaded
   * @access protected
   * @return void
   */
  protected function _additionalParameters(&$p_aParams) {
    // Add data to the request if defined.
    if (count($this->getElements()) > 0) {
      $p_aParams['dataXml'] = $this->getElementsXML();
    }
  }

  // --------------------------------------------------------------
  // INTERFACE IMPLEMENTATIONS
  // --------------------------------------------------------------

  /**
   * Adds a child object.
   *
   * @param string $p_sType
   * @param string $p_sObject_id
   * @param array $p_aFields
   *
   * @access public
   * @return AfasElement
   */
  public function addChild($p_sType, $p_sObject_id = '', $p_aFields = array(), $p_sClass = 'AfasElement') {
    return $this->m_oElement->addChild($p_sType, $p_sObject_id, $p_aFields, $p_sClass);
  }

  /**
   * Removes a child object.
   *
   * @param string $p_sObject_id
   *
   * @access public
   * @return void
   */
  public function removeChild($p_sObject_id) {
    $this->removeElement($p_sObject_id);
  }

  /**
   * Returns specific child object.
   *
   * @access public
   * @return AfasElement
   * @throws AfasException
   */
  public function getChild($p_sObject_id) {
    return $this->getElement($p_sObject_id);
  }
}
