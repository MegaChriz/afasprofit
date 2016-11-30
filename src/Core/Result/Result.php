<?php

/**
 * @file
 * Definition of \Afas\Core\Result\Result.
 */

namespace Afas\Core\Result;

use \DOMDocument;
use LSS\XML2Array;

/**
 *
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
   * Implements ResultInterface::asXML().
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
   * Implements ResultInterface::asArray().
   */
  public function asArray() {
    $xml = $this->asXML();
    return XML2Array::createArray($xml);
  }
}
