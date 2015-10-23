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

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Result object constructor.
   *
   * @param string $result_xml
   *   The XML string result as returned by the soap client.
   */
  public function __construct($result_xml) {
    $this->resultXML = $result_xml;
  }

  /**
   * Implements ResultInterface::asXML().
   */
  public function asXML() {
    $doc = new DOMDocument();
    $doc->loadXML($this->resultXML, LIBXML_PARSEHUGE);

    // Retrieve data result.
    $list = $doc->getElementsByTagName('GetDataResult');
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
