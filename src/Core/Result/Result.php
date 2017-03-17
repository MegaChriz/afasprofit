<?php

namespace Afas\Core\Result;

use \DOMDocument;
use \DOMXPath;
use LSS\XML2Array;

/**
 * Default class for processing results from Profit connectors.
 *
 * Currently assumes that results come from a get connector.
 */
class Result implements ResultInterface {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The XML string result as returned by the soap client.
   *
   * @var string
   */
  protected $resultXML;

  /**
   * The last called function.
   *
   * @var string
   */
  private $lastFunction;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Result object constructor.
   *
   * @param string $result_xml
   *   The XML string result as returned by the soap client.
   * @param string $last_function
   *   The last called function.
   */
  public function __construct($result_xml, $last_function) {
    $this->resultXML = $result_xml;
    $this->lastFunction = $last_function;
  }

  /**
   * {@inheritdoc}
   */
  public function asXML() {
    $doc = new DOMDocument();
    $doc->loadXML($this->resultXML, LIBXML_PARSEHUGE);

    // Retrieve data result.
    // @todo More elegant way to solve this? If GetData is called, the result is in GetDataResult,
    // if GetDataWithOptions is called, the result is in GetDataWithOptionsResult.
    $list = $doc->getElementsByTagName($this->lastFunction . 'Result');
    $data = array();
    foreach ($list as $node) {
      foreach ($node->childNodes as $child) {
        $data[] = array($child->nodeName => $child->nodeValue);
      }
    }

    // Create XML Document.
    return '<?xml version="1.0" encoding="utf-8"?>' . $data[0]['#text'];
  }

  /**
   * {@inheritdoc}
   */
  public function getHeaders() {
    $headers = [];

    // Get the schema from the XML.
    $doc = new \DOMDocument();
    $doc->preserveWhiteSpace = FALSE;
    $doc->loadXML($this->asXML(), LIBXML_PARSEHUGE);
    $schema = $doc->getElementsByTagName('schema')->item(0);

    if ($schema) {
      // Schema found. Get all elements.
      $xpath = new \DOMXPath($doc);
      $entries = $xpath->query('//xs:sequence/xs:element/@name', $schema);
      foreach ($entries as $entry) {
        $headers[] = $entry->nodeValue;
      }
    }

    return $headers;
  }

  /**
   * {@inheritdoc}
   */
  public function asArray() {
    // Convert XML to array.
    $data = XML2Array::createArray($this->asXML());

    // Remove the metadata.
    unset($data['AfasGetConnector']['xs:schema']);

    if (empty($data['AfasGetConnector'])) {
      return [];
    }

    // Check if only one data row was given back. If so, adjust array so the result becomes:
    //
    // AfasGetConnector =>
    //   MyGetConnector =>
    //     0 =>
    //       column1 => value1
    //       column2 => value2
    //
    // instead of:
    //
    // AfasGetConnector =>
    //   MyGetConnector =>
    //     column1 => value1
    //     column2 => value2
    $keys = array_keys($data['AfasGetConnector']);
    $key = reset($keys);
    if (!isset($data['AfasGetConnector'][$key][0])) {
      // There is only one item, make sure that there is a 0 item.
      $data['AfasGetConnector'][$key] = [$data['AfasGetConnector'][$key]];
    }

    return $data;
  }
}
