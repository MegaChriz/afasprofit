<?php

namespace Afas\Core\XSD;

use DOMDocument;
use DOMNode;
use DOMXPath;

/**
 * Reads XSD files from Profit.
 *
 * @todo update coding standards, documentation.
 */
class Reader {

  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The DOM document.
   *
   * @var \DOMDocument
   */
  private $dom;

  /**
   * The DOM Xpath.
   *
   * @var \DOMXPath
   */
  private $xpath;

  /**
   * Namespaces listing.
   *
   * Namespaces = [className => namespace].
   * Used in dirs/files generation.
   *
   * @var array
   */
  private $shortNamespaces;
  private $xmlSource;
  private $targetNamespace;

  /**
   * XSD root namespace alias (fx, xsd = http://www.w3.org/2001/XMLSchema).
   *
   * @var string
   */
  private $xsdNs;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new Reader object.
   *
   * @param string $xml_string
   *   The XML string coming from a XSD file.
   */
  public function __construct($xml_string) {
    $this->dom = new DOMDocument();
    $this->dom->loadXML($xml_string, LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NOENT | LIBXML_XINCLUDE);
    $this->xpath = new DOMXPath($this->dom);

    $this->targetNamespace = $this->getTargetNs($this->xpath);
    $this->shortNamespaces = $this->getNamespaces($this->xpath);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Retrieves the used namespace.
   *
   * @param \DOMXPath $xpath
   *   The DOM Xpath object.
   */
  private function getTargetNs(DOMXPath $xpath) {
    $query = "//*[local-name()='schema' and namespace-uri()='http://www.w3.org/2001/XMLSchema']/@targetNamespace";
    $targetNs = $xpath->query($query);

    if ($targetNs) {
      foreach ($targetNs as $entry) {
        return $entry->nodeValue;
      }
    }
  }

  /**
   * Returns array of namespaces of the document.
   *
   * @param \DOMXPath $xpath
   *   The DOM Xpath object.
   *
   * @return array
   *   Returns the used namespaces in the document.
   */
  public function getNamespaces(DOMXPath $xpath) {
    $query   = "//namespace::*";
    $entries = $xpath->query($query);
    $nspaces = [];

    foreach ($entries as $entry) {
      if ($entry->nodeValue == "http://www.w3.org/2001/XMLSchema") {
        $this->xsdNs = preg_replace('/xmlns:(.*)/', "$1", $entry->nodeName);
      }
      if ($entry->nodeName != 'xmlns:xml') {
        if (preg_match('/:/', $entry->nodeName)) {
          $nodeName = explode(':', $entry->nodeName);
          $nspaces[$nodeName[1]] = $entry->nodeValue;
        }
        else {
          $nspaces[$entry->nodeName] = $entry->nodeValue;
        }
      }

    }
    return $nspaces;
  }

  /**
   * Returns all nodes that are named 'element'.
   *
   * @param \DOMNode $node
   *   (optional) A DOM node.
   * @param array $array
   *   The data export.
   *
   * @return array
   *   The elements of the dom node, as array data.
   */
  private function getElements(DOMNode $node = NULL, array &$array = []) {
    $query = "*";
    if ($node instanceof DOMNode) {
      $entries = $this->xpath->query($query, $node);
    }
    else {
      $entries = $this->xpath->query($query);
    }

    foreach ($entries as $entry) {
      switch ($entry->tagName) {
        case $this->xsdNs . ':element':
          $name = $entry->getAttribute('name');
          $array[$name] = [];

          switch ($name) {
            case 'Fields':
              $this->readFields($entry, $array[$name]);
              break;

            default:
              // Save all attributes.
              foreach ($entry->attributes as $attr_name => $attr_value) {
                $array[$name]['#' . $attr_name] = $attr_value->nodeValue;
              }

              $this->getElements($entry, $array[$name]);
              break;
          }
          break;

        default:
          $this->getElements($entry, $array);
          break;
      }
    }
    return $array;
  }

  /**
   * Reads fields of a node.
   *
   * @param \DOMNode $node
   *   A DOM node.
   * @param array $array
   *   The data export.
   * @param array $descriptions
   *   The collected field descriptions.
   * @param array $possible_values
   *   The collected possible values for a field.
   */
  private function readFields(DOMNode $node, array &$array, array &$descriptions = [], array &$possible_values = []) {
    // Search for next element first.
    $query = "*";
    $entries = $this->xpath->query($query, $node);
    $i = 0;
    $iDescriptionCount = 0;
    foreach ($entries as $entry) {
      // Save all comments.
      $query = "comment()";
      $comments = $this->xpath->query($query, $entry);
      foreach ($comments as $comment) {
        if ($comment) {
          if (strpos($comment->nodeValue, 'Values:') === 0) {
            $possible_values[$iDescriptionCount - 1] = $comment->nodeValue;
          }
          else {
            $descriptions[$iDescriptionCount] = $comment->nodeValue;
            $iDescriptionCount++;
          }
        }
      }

      switch ($entry->tagName) {
        case $this->xsdNs . ':element':
          $name = $entry->getAttribute('name');
          $array[$name] = [];

          // Title.
          $array[$name]['title'] = $name;

          // Type.
          if ($type = $entry->getAttribute('type')) {
            // Type is defined as an attribute of entry.
          }
          else {
            $query = '' . $this->xsdNs . ':simpleType/' . $this->xsdNs . ':restriction';
            if ($restriction = $this->readFirst($query, $entry)) {
              $type = $restriction->getAttribute('base');

              // Save also minlength and maxlength.
              foreach ($restriction->childNodes as $oChild) {
                switch ($oChild->tagName) {
                  case $this->xsdNs . ':maxLength':
                    $array[$name]['maxlength'] = $oChild->getAttribute('value');
                    break;

                  case $this->xsdNs . ':minLength':
                    $array[$name]['minlength'] = $oChild->getAttribute('value');
                    break;
                }
              }
            }
          }
          $type = str_ireplace($this->xsdNs . ':', '', $type);
          if (!$type) {
            $type = 'unknown';
          }
          $array[$name]['type'] = $type;

          // Description.
          $array[$name]['description'] = $descriptions[$i];

          // Possible values.
          if (isset($possible_values[$i])) {
            $array[$name]['possible_values'] = $possible_values[$i];
          }

          // Required.
          if ($entry->getAttribute('minOccurs')) {
            $array[$name]['required'] = TRUE;
          }

          // Can be NULL.
          if ($entry->getAttribute('nillable') == 'true') {
            $array[$name]['default_value'] = NULL;
          }

          break;

        default:
          $this->readFields($entry, $array, $descriptions, $possible_values);
          break;
      }
      $i++;
    }
  }

  /**
   * Returns the first item result of the given Xpath query.
   *
   * @param string $query
   *   The Xpath query.
   * @param \DOMNode $node
   *   A DOM node.
   *
   * @return \DOMNode|null
   *   A DOM node in case something is found, null otherwise.
   */
  private function readFirst($query, DOMNode $node) {
    if ($items = $this->xpath->query($query, $node)) {
      if ($item = $items->item(0)) {
        return $item;
      }
    }
    return NULL;
  }

  /**
   * Returns the Update Connector definition.
   *
   * @return array
   *   The Update Connector definition.
   */
  public function getDefinitionArray() {
    return $this->getElements();
  }

  /**
   * Returns field definitions for a particular object.
   *
   * @param array $hierarchy
   *   The element to look for, specified in an array for where to look in the
   *   definition hierarchy. For example, to get fields for an object living at
   *   level 3, pass something like this:
   *   @code
   *   array('level 1', 'level 2', 'level 3')
   *   @endcode
   *
   * @return array
   *   Field definitions.
   */
  public function getFieldDefinition(array $hierarchy) {
    $definition['Objects'] = $this->getDefinitionArray();
    foreach ($hierarchy as $element_name) {
      $definition = $definition['Objects'][$element_name]['Element'];
    }
    return $definition['Fields'];
  }

}
