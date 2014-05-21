<?php
/**
 * @file
 * AFAS Connector class for 'Get' connections.
 *
 * This class extends the AfasConnector class with specific methods for getting data.
 */

class AfasGetConnector extends AfasConnector {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * @var array $m_aFilters
   * @access protected
   */
  protected $m_aFilters;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Initialize values.
   *
   * @overloaded
   * @access public
   * @return void
   */
  public function init() {
    parent::init();
    $this->m_aFilters = array();
    $this->m_sLocation = "https://" . $this->m_oServer->ip_address . "/profitservices/getconnector.asmx";
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Adds a single filter in a new group.
   *
   * @param $p_sField
   * @param $p_sValue
   * @param mixed $p_mOperator
   * @access public
   * @return AFAS_Filter
   */
  public function addFilter($p_sField, $p_sValue = '', $p_mOperator = '=') {
    $oGroup = $this->addFilterGroup();
    return $oGroup->addFilter($p_sField, $p_sValue, $p_mOperator);
  }

  /**
   * Adds a new filter group.
   *
   * @param string $p_sFilter_id
   *  Optional name for filter group
   * @return AFAS_FilterGroup
   */
  public function addFilterGroup($p_sFilter_id = '') {
    if (!$p_sFilter_id) {
      // Think of a filter name
      $bSet = FALSE;
      for ($iAdd = 1; !$bSet; $iAdd++) {
        $p_sFilter_id = 'Filter' . (count($this->m_aFilters) + $iAdd);
        if (!isset($this->m_aFilters[$p_sFilter_id])) {
          $bSet = TRUE;
        }
      }
    }
    $oFilterGroup = new AfasFilterGroup($p_sFilter_id);
    $oFilterGroup->tree_id = $p_sFilter_id;
    $this->m_aFilters[$p_sFilter_id] = $oFilterGroup;
    return $oFilterGroup;
  }

  /**
   * Removes a filter if filter exists.
   *
   * @param string $p_sFilter_id
   * @access public
   * @return boolean
   */
  public function removeFilter($p_sFilter_id) {
    if (isset($this->m_aFilters[$p_sFilter_id])) {
      unset($this->m_aFilters[$p_sFilter_id]);
      return TRUE;
    }
    return FALSE;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns Filters.
   *
   * @access public
   * @return array
   */
  public function getFilters() {
    return $this->m_aFilters;
  }

  /**
   * Returns Filters XML.
   *
   * @access public
   * @return string XML
   */
  public function getFiltersXML() {
    $sOutput = '<Filters>';
    foreach ($this->m_aFilters as $oFilterGroup) {
      $sOutput .= $oFilterGroup->compile();
    }
    $sOutput .= '</Filters>';
    return $sOutput;
  }

  /**
   * Return response result as XML string.
   *
   * @access public
   * @return string XML
   */
  public function getResultXML() {
    $sXMLString = $this->m_oSoapClient->__getLastResponse();
    $oDoc = new DOMDocument();
    $oDoc->loadXML($sXMLString, LIBXML_PARSEHUGE);

    // Retrieve data result.
    $oList = $oDoc->getElementsByTagName('GetDataResult');
    $aData = array();
    foreach ($oList as $oNode) {
      foreach ($oNode->childNodes as $oChild) {
        $aData[] = array($oChild->nodeName => $oChild->nodeValue);
      }
    }

    // Create XML Document.
    return '<?xml version="1.0" encoding="utf-8"?>' . $aData[0]['#text'];
  }

  /**
   * Return response result in an array.
   *
   * @access public
   * @return array
   */
  public function getResultArray() {
    $sXMLString = $this->getResultXML();
    $oParser = new XML_Unserializer();
    $oParser->unserialize($sXMLString);
    return $oParser->get_unserialized_data();
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Sends a SOAP request.
   *
   * @param string $p_sFunction
   * @param string $p_sConnector_id
   * @param array $p_aParameters
   * @param array $p_aOptions
   * @access public
   * @return void
   */
  public function sendRequest($p_sFunction, $p_sConnector_id, $p_aParameters = array(), $p_aOptions = array()) {
    $p_aParameters['connectorId'] = $p_sConnector_id;
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
    // Add filters to the request if defined.
    if (count($this->m_aFilters) > 0) {
      $p_aParams['filtersXml'] = $this->getFiltersXML();
    }
  }

  // --------------------------------------------------------------
  // TEST
  // --------------------------------------------------------------

  /**
   * Outputs data result.
   */
  public function outputDataResult() {
    // Output it
    header("content-type: text/xml");
    print $this->getResultXML();
  }
}
