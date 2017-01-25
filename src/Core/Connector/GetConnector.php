<?php

/**
 * @file
 * Contains \Afas\Core\Connector\GetConnector.
 */

namespace Afas\Core\Connector;

use Afas\Core\Connector\ConnectorBase;
use Afas\Core\Filter\FilterContainerInterface;
use \DOMDocument;

class GetConnector extends ConnectorBase implements GetConnectorInterface {
  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  /**
   * Outputmode option: returns data as XML.
   *
   * @var integer
   */
  const OUTPUTMODE_XML = 1;

  /**
   * Outputmode option: returns data as text.
   *
   * @var integer
   */
  const OUTPUTMODE_TXT = 2;

  /**
   * Metadata option: no metadata included.
   *
   * @var integer
   */
  const METADATA_FALSE = 0;

  /**
   * Metadata option: metadata included.
   *
   * @var integer
   */
  const METADATA_TRUE = 1;

  /**
   * Outputoptions option: Microsoft DataSet.
   *
   * Only has effect if Outputmode is XML.
   *
   * @var integer
   */
  const OUTPUTOPTIONS_XML_MSDATA = 2;

  /**
   * Outputoptions option: Microsoft DataSet including empty values.
   *
   * Only has effect if output mode is XML.
   *
   * @var integer
   */
  const OUTPUTOPTIONS_XML_MSDATA_WITH_EMPTY_VALUES = 3;

  /**
   * Outputoptions option: data is separated using semicolons; dates and numbers
   * are formatted using locale settings.
   *
   * Only has effect if output mode is text.
   *
   * @var integer
   */
  const OUTPUTOPTIONS_TXT_SEMICOLON_LC = 1;

  /**
   * Outputoptions option: data is separated using tabs; dates and numbers are
   * formatted using locale settings.
   *
   * Only has effect if output mode is text.
   *
   * @var integer
   */
  const OUTPUTOPTIONS_TXT_TAB_LC = 2;

  /**
   * Outputoptions option: data is separated using semicolons; dates and numbers
   * are formatted using international settings:
   * - dd-mm-yy for dates;
   * - dot for decimal separators.
   *
   * Only has effect if output mode is text.
   *
   * @var integer
   */
  const OUTPUTOPTIONS_TXT_SEMICOLON = 3;

  /**
   * Outputoptions option: data is separated using tabs; dates and numbers are
   * formatted using international settings:
   * - dd-mm-yy for dates;
   * - dot for decimal separators.
   *
   * Only has effect if output mode is text.
   *
   * @var integer
   */
  const OUTPUTOPTIONS_TXT_TAB = 4;

  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * A filter container.
   *
   * @var \Afas\Core\Filter\FilterContainerInterface
   *   An instance of FilterContainerInterface.
   */
  protected $filterContainer;

  /**
   * The number of records to skip.
   * -1 means no skipping.
   *
   * @var int
   */
  protected $skip = -1;

  /**
   * The number of records to take.
   * -1 means all records taken.
   *
   * @var int
   */
  protected $take = -1;

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Gets records from a Profit GetConnector.
   *
   * @param string $connector_id
   *   The name of the GetConnector.
   * @param array $options
   *   Options for getting the data.
   *
   * @return \Afas\Core\Result\Result
   *   The result of the call.
   */
  public function getData($connector_id, $options = array()) {
    // Set connectorId.
    $arguments['connectorId'] = $connector_id;

    // Set options.
    $options += array(
      'Outputmode' => static::OUTPUTMODE_XML,
      'Metadata' => static::METADATA_FALSE,
      'Outputoptions' => static::OUTPUTOPTIONS_XML_MSDATA,
    );
    $options_str = '';
    foreach ($options as $option => $value) {
      $options_str .= "<$option>$value</$option>";
    }
    $arguments['options'] = "<options>$options_str</options>";

    // Send request.
    $this->_sendRequest('GetDataWithOptions', $arguments);
    return $this->getResult();
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets a filter container.
   *
   * @param \Afas\Core\Filter\FilterContainerInterface $filter_container
   *   A container containing filters.
   *
   * @return void
   */
  public function setFilterContainer(FilterContainerInterface $filter_container) {
    $this->filterContainer = $filter_container;
  }

  /**
   * Sets a range to use.
   *
   * @param int $skip
   *   The number of records to skip.
   * @param int $take
   *   (optional) The number of records to take.
   *   Defaults to take all records.
   *
   * @return void
   */
  public function setRange($skip, $take = -1) {
    $this->skip = $skip;
    $this->take = $take;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getLocation() {
    return $this->getServer()->getBaseUrl() . '/appconnectorget.asmx';
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
    $arguments['skip'] = $this->skip;
    $arguments['take'] = $this->take;
    return $arguments;
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Sends a SOAP request.
   *
   * @param string $function
   *   The function to call.
   * @param string $connector_id
   *   The get-connector to use.
   */
  public function sendRequest($function, $connector_id) {
    $arguments['connectorId'] = $connector_id;
    $this->_sendRequest($function, $arguments);
  }
}
