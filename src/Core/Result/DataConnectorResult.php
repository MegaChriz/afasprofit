<?php

namespace Afas\Core\Result;

use DOMComment;
use DOMDocument;
use DOMXpath;

/**
 * Class for processing results from a Profit DataConnector.
 */
class DataConnectorResult extends ResultBase {

  /**
   * {@inheritdoc}
   */
  public function asArray() {
    try {
      // Convert XML to array.
      $data = $this->getArrayData($this->asXml());
    }
    catch (EmptyException $e) {
      // Not an error.
      return [];
    }

    // Remove the metadata.
    unset($data['AfasDataConnector']['xs:schema']);

    $keys = array_keys($data['AfasDataConnector']);
    $key = reset($keys);
    if (!isset($data['AfasDataConnector'][$key][0])) {
      // There is only one item, make sure that there is a 0 item.
      $data['AfasDataConnector'][$key] = [$data['AfasDataConnector'][$key]];
    }

    return $data['AfasDataConnector'][$key];
  }

  /**
   * Converts XML to array and removes custom fields from schema.
   */
  public function removeCustomFieldsFromSchema() {
    $data = $this->asArray();
    if (empty($data)) {
      return [];
    }

    foreach ($data as &$row) {
      if (isset($row['Schema'])) {
        $doc = new DOMDocument();
        $doc->loadXML($row['Schema'], LIBXML_PARSEHUGE);

        $xpath = new DOMXpath($doc);
        // Register namespace.
        $xpath->registerNamespace('xsd', "http://www.w3.org/2001/XMLSchema");

        // Remove elements whose name is longer than 31 chars.
        $elements = $xpath->query("//xsd:element[string-length(@name) > 31]|//xsd:element[starts-with(@name, 'U0')]");
        foreach ($elements as $element) {
          $sibling = $element->previousSibling;
          if ($sibling instanceof DOMComment) {
            $element->parentNode->removeChild($sibling);
          }
          $element->parentNode->removeChild($element);
        }

        $row['Schema'] = '';
        foreach ($doc->childNodes as $node) {
          $row['Schema'] .= $doc->saveXML($node);
        }
      }
    }

    return $data;
  }

}
