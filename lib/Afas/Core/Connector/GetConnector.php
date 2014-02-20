<?php

/**
 * @file
 * Contains \Afas\Core\GetConnector.
 *
 * @todo Maybe move the filter logic out of get-connector.
 * @todo Put code for parsing the result out of this class.
 */

namespace Afas\Core\Connector;

use Afas\Core\Connector\ConnectorBase;
use Afas\Core\Filter\FilterContainerInterface;
use \DOMDocument;

class GetConnector extends ConnectorBase {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * A filter container.
   *
   * @var FilterContainerInterface
   * An instance of FilterContainerInterface.
   */
  protected $filterContainer;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets a filter container.
   *
   * @param FilterContainerInterface $filterContainer
   *   A container containing filters.
   *
   * @return void
   */
  public function setFilterContainer(FilterContainerInterface $filterContainer) {
    $this->filterContainer = $filterContainer;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

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
    if (isset($this->filterContainer)) {
      $arguments['filtersXml'] = $this->filterContainer->compile();
    }
    return $arguments;
  }
}
