<?php

namespace Afas\Core\Connector;

use Afas\Core\Filter\FilterContainerInterface;
use Afas\Core\Result\GetConnectorResult;

/**
 * Class for the Profit GetConnector.
 */
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
   * Outputoptions option: data is separated using semicolons.
   *
   * Dates and numbers are formatted using locale settings.
   *
   * Only has effect if output mode is text.
   *
   * @var integer
   */
  const OUTPUTOPTIONS_TXT_SEMICOLON_LC = 1;

  /**
   * Outputoptions option: data is separated using tabs.
   *
   * Dates and numbers are formatted using locale settings.
   *
   * Only has effect if output mode is text.
   *
   * @var integer
   */
  const OUTPUTOPTIONS_TXT_TAB_LC = 2;

  /**
   * Outputoptions option: data is separated using semicolons.
   *
   * Dates and numbers are formatted using international settings:
   * - dd-mm-yy for dates;
   * - dot for decimal separators.
   *
   * Only has effect if output mode is text.
   *
   * @var integer
   */
  const OUTPUTOPTIONS_TXT_SEMICOLON = 3;

  /**
   * Outputoptions option: data is separated using tabs.
   *
   * Dates and numbers are formatted using international settings:
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
   */
  protected $filterContainer;

  /**
   * The number of records to skip.
   *
   * -1 means no skipping.
   *
   * @var int
   */
  protected $skip = -1;

  /**
   * The number of records to take.
   *
   * -1 means all records taken.
   *
   * @var int
   */
  protected $take = -1;

  /**
   * The fields by which to order this query.
   *
   * This is an associative array. The keys are the fields to order, and the
   * value is the direction to order, either 0 (descending) or 1 (ascending).
   *
   * @var array
   */
  protected $order = [];

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getData($connector_id, array $options = []) {
    // Set connectorId.
    $arguments['connectorId'] = $connector_id;

    // Set options.
    $options += [
      'Outputmode' => static::OUTPUTMODE_XML,
      'Metadata' => static::METADATA_TRUE,
      'Outputoptions' => static::OUTPUTOPTIONS_XML_MSDATA,
    ];

    $arguments['options'] = $this->compileOptions($options);

    // Send request.
    $this->soapSendRequest('GetDataWithOptions', $arguments);
    return $this->getResult();
  }

  /**
   * Compiles options for 'GetDataWithOptions' function.
   *
   * @param array $options
   *   (optional) Options for getting the data.
   *
   * @return string
   *   The compiled XML.
   */
  protected function compileOptions(array $options = []) {
    if (!empty($this->order)) {
      $options['Index'] = '';
      foreach ($this->order as $field => $operator) {
        $options['Index'] .= '<Field FieldId="' . $field . '" OperatorType="' . $operator . '"/>';
      }
    }

    $options_str = '';
    foreach ($options as $option => $value) {
      $options_str .= "<$option>$value</$option>";
    }
    return "<options>$options_str</options>";
  }

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
    $this->soapSendRequest($function, $arguments);
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function setFilterContainer(FilterContainerInterface $filter_container) {
    $this->filterContainer = $filter_container;
  }

  /**
   * {@inheritdoc}
   */
  public function setRange($skip, $take = -1) {
    $this->skip = $skip;
    $this->take = $take;
  }

  /**
   * {@inheritdoc}
   */
  public function setOrder(array $order) {
    foreach ($order as $field => $direction) {
      // Convert static::DESC to a string here cause a non-numeric string casted
      // to an integer is always 0 in PHP.
      if ($direction == 'DESC' || (string) $direction === (string) static::DESC) {
        $direction = static::DESC;
      }
      else {
        $direction = static::ASC;
      }
      $this->order[$field] = $direction;
    }
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getFilterContainer(): FilterContainerInterface {
    return $this->filterContainer;
  }

  /**
   * {@inheritdoc}
   */
  public function getResult() {
    list($result_xml, $last_function) = $this->getResultArguments();
    return new GetConnectorResult($result_xml, $last_function);
  }

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

}
