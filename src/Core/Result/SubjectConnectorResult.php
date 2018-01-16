<?php

namespace Afas\Core\Result;

use DOMDocument;
use DOMXpath;

/**
 * Class for processing results from a Profit SubjectConnector.
 */
class SubjectConnectorResult extends ResultBase {

  /**
   * {@inheritdoc}
   */
  public function asArray() {
    $doc = new DOMDocument();
    $doc->loadXML($this->asXml());

    switch ($doc->documentElement->nodeName) {
      case 'SubjectAttachmentInfo':
        $data = [];
        $xpath = new DOMXpath($doc);

        $elements = $xpath->query('//Attachment');
        foreach ($elements as $element) {
          if ($element->attributes->length) {
            $a = [];
            foreach ($element->attributes as $attr_name => $attr_node) {
              $a[$attr_name] = (string) $attr_node->value;
            }
            $data[] = $a;
          }
        }
        return $data;

      case 'SubjectAttachmentData':
        $data = [];
        $xpath = new DOMXpath($doc);

        $elements = $xpath->query('//Attachment');
        foreach ($elements as $element) {
          $a = [];
          if ($element->attributes->length) {
            foreach ($element->attributes as $attr_name => $attr_node) {
              $a[$attr_name] = (string) $attr_node->value;
            }
          }

          $a['Data'] = (string) $element->getElementsByTagName('Data')
            ->item(0)
            ->nodeValue;

          $data[] = $a;
        }
        return $data;
    }

    return $this->getArrayData($doc);
  }

}
