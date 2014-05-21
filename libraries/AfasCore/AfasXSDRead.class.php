<?php
/**
 * @file
 * Class to read XSD Files of AFAS
 */

class AfasXSDRead {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * @var DOMDocument
   * @access private
   */
  private $dom;

  /**
   * @var DOMXPath
   * @access private
   */
  private $xpath;

  /**
   * Namespaces = array (className => namespace ), used in dirs/files generation
   * @var array
   * @access private
   */
  private $shortNamespaces;
  private $xmlSource;
  private $targetNamespace;

  /**
   * XSD root namespace alias (fx, xsd = http://www.w3.org/2001/XMLSchema)
   * @var string
   * @access private
   */
  private $xsdNs;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Object constructor
   * @param string $xml_string
   * @access public
   * @return void
   */
  public function __construct($xml_string) {
    $this->dom = new DOMDocument();
    $this->dom->loadXML($xml_string, LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NOENT | LIBXML_XINCLUDE);
    $this->xpath = new DOMXPath($this->dom);

    $this->targetNamespace = $this->getTargetNS($this->xpath);
    $this->shortNamespaces = $this->getNamespaces($this->xpath);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Haalt namespace op.
   * @param DOMXPath $xpath
   * @access private
   */
  private function getTargetNS($xpath) {
    $query = "//*[local-name()='schema' and namespace-uri()='http://www.w3.org/2001/XMLSchema']/@targetNamespace";
    $targetNs = $xpath->query($query);

    if ($targetNs) {
      foreach ($targetNs as $entry) {
        return $entry->nodeValue;
      }
    }
  }

  /**
   * Return array of namespaces of the document.
   *
   * @param DOMXPath $xpath
   *
   * @return array
   */
  public function getNamespaces($xpath) {
    $query   = "//namespace::*";
    $entries =  $xpath->query($query);
    $nspaces = array();

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

  // --------------------------------------------------------------
  // ACTION (experimental)
  // --------------------------------------------------------------

  /**
   * Get all nodes named 'element'.
   * @param DOMNode $node
   * @access private
   * @return array
   */
  private function getElements($node = NULL, &$array = array()) {
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
          $array[$name] = array();

          switch ($name) {
            case 'Fields':
              $this->readFields($entry, $array[$name]);
              break;

            default:
              // Save all attributes
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
   * @param object $node
   * @param array $array
   * @param array $descriptions
   * @param array $possible_values
   * @access private
   * @return void
   */
  private function readFields($node, &$array, &$descriptions = array(), &$possible_values = array()) {
    // Search for next element first
    $query = "*";
    $entries = $this->xpath->query($query, $node);
    $i = 0;
    $iDescriptionCount = 0;
    foreach ($entries as $entry) {
      // Save all comments
      $query = "comment()";
      $comments = $this->xpath->query($query, $entry);
      foreach ($comments as $comment) {
        if ($comment) {
          if (strpos($comment->nodeValue, 'Values:') === 0) {
            $possible_values[$iDescriptionCount -1] = $comment->nodeValue;
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
          $array[$name] = array();

          // Title
          $array[$name]['title'] = $name;

          // Type
          if ($type = $entry->getAttribute('type')) {
            // Type is defined as an attribute of entry
          }
          else {
            $query = '' . $this->xsdNs . ':simpleType/' . $this->xsdNs . ':restriction';
            if ($restriction = $this->readFirst($query, $entry)) {
              $type = $restriction->getAttribute('base');

              //$this->printXML($restriction->parentNode);

              // Save also minlength and maxlength
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

          // Description
          //$array[$name]['title'] = preg_replace('/\([^\)]*\)/', '', $descriptions[$i]);
          $array[$name]['description'] = $descriptions[$i];

          // Possible values
          if (isset($possible_values[$i])) {
            $array[$name]['possible_values'] = $possible_values[$i];
          }

          // Required
          if ($entry->getAttribute('minOccurs')) {
            $array[$name]['required'] = TRUE;
          }

          // Can be NULL
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
   * Reads first node
   * @param string $query
   * @param DOMNode $node
   * @access private
   * @return
   *   DOMNode in case something is found
   *   NULL otherwise
   */
  private function readFirst($query, DOMNode $node) {
    if ($items = $this->xpath->query($query, $node)) {
      if ($item = $items->item(0)) {
        return $item;
      }
    }
    return NULL;
  }

  // --------------------------------------------------------------
  // TEST
  // --------------------------------------------------------------

  /**
   * Test things
   * @access public
   * @return void
   */
  public function test() {
    /*
     //$query = "/".$this->xsdNs.":schema/".$this->xsdNs.":element";
     $query = $this->xsdNs.":element";
     $entries = $this->xpath->query($query);

     $connectors = array();
     foreach ($entries as $entry) {
     $name = $entry->getAttribute('name');
     $connectors[$name] = array();
     //$query = $this->xsdNs.":complexType/" . $this->xsdNs.":sequence/" . $this->xsdNs.":element";
     $query = "" . $this->xsdNs.":element";
     $query = "*";
     $more_entries = $this->xpath->query($query, $entry);
     foreach ($more_entries as $entry2) {
     $connectors[$name][$entry2->tagName][] = $entry2->getAttribute('name');
     }
     }

     //*/
    //$names = $this->getElementName($this->dom);

    $array = $this->getElements();
  }

  /**
   * Returns the Update Connector definition
   * @access public
   * @return array
   */
  public function getDefinitionArray() {
    return $this->getElements();
  }

  /**
   * Returns field definitions for a particular object.
   *
   * @param array $hierarchy
   *   The element to look for, specified in an array
   *   for where to look in the definition hierarchy.
   *   For example, to get fields for an object living
   *   at level 3, pass something like this:
   *   <code>
   *   array('level 1', 'level 2', 'level 3')
   *   </code>
   *
   * @access public
   * @return array
   */
  public function getFieldDefinition($hierarchy) {
    $definition['Objects'] = $this->getDefinitionArray();
    foreach ($hierarchy as $element_name) {
      $definition = $definition['Objects'][$element_name]['Element'];
    }
    return $definition['Fields'];
  }

  /**
   * Prints XML for test purposes
   * @param DOMNode $node
   * @access private
   * @return void
   */
  private function printXML(DOMNode $node) {
    $oDoc = $node->ownerDocument;
    $string = $oDoc->saveXML($node);

    header("content-type: text/xml");
    print '<?xml version="1.0" encoding="UTF-8"?>';
    print '<' . $this->xsdNs . ':root xmlns:' . $this->xsdNs . '="' .  $node->namespaceURI . '">';
    print $string;
    print '</' . $this->xsdNs . ':root>';
    die();
  }
}
