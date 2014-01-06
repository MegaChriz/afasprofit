<?php

/**
 * @file
 * Contains \Afas\Core\GetConnector.
 *
 * @todo Maybe move the filter logic out of get-connector.
 * @todo Put code for parsing the result out of this class.
 */

namespace Afas\Core;

class GetConnector extends Connector {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * A list of filters.
   *
   * @var array
   * @todo class instead of array?
   */
  protected $filters;

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Adds a single filter.
   */
  public function addFilter(FilterInterface $filter) {
    // @todo implement.
  }

  /**
   * Adds a new filter group.
   */
  public function addFilterGroup(FilterGroupInterface $group) {
    // @todo implement.
  }

  /**
   * Removes a filter.
   */
  public function removeFilter($filter_id) {
    // @todo implement.
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns filters.
   *
   * @access public
   * @return array
   */
  public function getFilters() {
    return $this->filters;
  }

  /**
   * Returns Filters XML.
   *
   * @access public
   * @return string XML
   */
  public function getFiltersXML() {
    $output = '<Filters>';
    foreach ($this->filters as $filter_group) {
      $output .= $filter_group->getXML();
    }
    $output .= '</Filters>';
    return $output;
  }

  /**
   * Returns result.
   *
   * @todo Wrap result into object?
   */
  public function getResult() {
    return $this->client->__getLastResponse();
  }

  /**
   * Return response result as XML string.
   *
   * @access public
   * @return string XML
   *
   * @todo Don't instantiate DOMDocument if possible.
   * @todo Parse result in other class.
   */
  public function getResultXML() {
    $sXMLString = $this->client->__getLastResponse();
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
   *
   * @todo Don't instantiate XML_Unserializer if possible.
   * @todo Parse result in other class.
   */
  public function getResultArray() {
    $sXMLString = $this->getResultXML();
    $oParser = new XML_Unserializer();
    $oParser->unserialize($sXMLString);
    return $oParser->get_unserialized_data();
  }

  // --------------------------------------------------------------
  // GETTERS (protected)
  // --------------------------------------------------------------

  /**
   * Overrides Connector::getSoapArguments().
   */
  protected function getSoapArguments() {
    $arguments = parent::getSoapArguments();
    if (count($this->filters) > 0) {
      $arguments['filtersXml'] = $this->getFiltersXML();
    }
    return $arguments;
  }
}
