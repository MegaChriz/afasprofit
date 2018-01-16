<?php

namespace Afas\Core\Result;

use DOMDocument;
use DOMXPath;
use LSS\XML2Array;

/**
 * Default class for processing results from Profit connectors.
 *
 * Currently assumes that results come from a get connector.
 */
abstract class ResultBase implements ResultInterface {

  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The XML string result as returned by the soap client.
   *
   * @var string
   */
  protected $resultXml;

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
    $this->resultXml = $result_xml;
    $this->lastFunction = $last_function;
  }

  /**
   * {@inheritdoc}
   */
  public function getRaw() {
    $doc = new DOMDocument();
    file_put_contents('/tmp/' . $this->lastFunction . '.txt', $this->resultXml);
    $doc->loadXML($this->resultXml, LIBXML_PARSEHUGE);

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

    return $data[0]['#text'];
  }

  /**
   * {@inheritdoc}
   */
  public function asXml() {
    // Create XML Document.
    return '<?xml version="1.0" encoding="utf-8"?>' . $this->getRaw();
  }

  /**
   * {@inheritdoc}
   */
  public function getHeaders() {
    $headers = [];

    // Get the schema from the XML.
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = FALSE;
    $doc->loadXML($this->asXML(), LIBXML_PARSEHUGE);
    $schema = $doc->getElementsByTagName('schema')->item(0);

    if ($schema) {
      // Schema found. Get all elements.
      $xpath = new DOMXPath($doc);
      $entries = $xpath->query('//xs:sequence/xs:element/@name', $schema);
      foreach ($entries as $entry) {
        $headers[] = $entry->nodeValue;
      }
    }

    return $headers;
  }

  /**
   * Converts XML to array.
   *
   * @param DOMDocument|string $xml
   *   The XML to convert to an array.
   *   Given by reference to save memory.
   *
   * @return array
   *   The raw array data.
   */
  protected function getArrayData(&$xml) {
    return XML2Array::createArray($xml);
  }

}
