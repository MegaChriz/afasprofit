<?php

/**
 * @file
 * Contains \Afas\Core\Entity\EntityBase.
 */

namespace Afas\Core\Entity;

use \DOMDocument;
use Afas\Core\Entity\EntityInterface;
use Afas\Core\Mapping\MappingInterface;

class EntityBase implements EntityInterface, EntityContainerInterface, MappingInterface {
  /**
   * List of fields.
   *
   * @var array
   */
  protected $fields = [];

  /**
   * List of objects.
   *
   * @var array
   */
  protected $objects = [];

  /**
   * @var \Afas\Core\Mapping\MappingInterface
   */
  private $mapper;

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Implements EntityInterface::toXML().
   */
  public function toXML(DOMDocument $doc = NULL) {
    if (!isset($doc)) {
      $doc = new DOMDocument();
    }

    // Create Element.
    $element_xml = $doc->createElement('Element');
    // @todo Attributes.

    // Fields XML.
    if (count($this->fields) > 0) {
      $fields_xml = $doc->createElement('Fields');
      $fields_xml->setAttribute('Action', $this->getAction());
      foreach ($this->fields as $field_name => $field_value) {
        $field_xml = $doc->createElement($field_name);
        if ($field_value === '' || is_null($field_value)) {
          $field_xml->setAttribute('xsi:nil', 'true');
        }
        else {
          $text_xml = $doc->createTextNode($field_value);
          $field_xml->appendChild($text_xml);
        }
        $fields_xml->appendChild($field_xml);
      }
      $element_xml->appendChild($fields_xml);
    }

    // Objects XML.
    if (count($this->objects) > 0) {
      $objects_xml = $doc->createElement('Objects');

      // Sort objects by type first.
      $objects_by_type = [];
      foreach ($this->objects as $object) {
        $objects_by_type[$object->getEntityType()][] = $object;
      }

      // Generate XML for each object, grouped by type.
      foreach ($objects_by_type as $entity_type => $objects) {
        $entity_type_xml = $doc->createElement($entity_type);
        foreach ($objects as $object) {
          $object_xml = $object->toXML($doc);
          $entity_type_xml->appendChild($object_xml);
        }
        $objects_xml->appendChild($entity_type_xml);
      }

      $element->appendChild($objects_xml);
    }

    return $element;
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets a field.
   *
   * @param string $key
   *   The field to set.
   * @param string $value
   *   The field's value.
   *
   * @return void
   */
  public function setField($key, $value) {
    $keys = $this->map($key);
    foreach ($keys as $key) {
      $this->fields[$key] = (string) $value;
    }
  }

  /**
   * Sets mapper.
   */
  public function setMapper(MappingInterface $mapper) {
    $this->mapper = $mapper;
  }

  /**
   * Implements MappingInterface::map().
   */
  public function map($key) {
    if ($this->mapper instanceof MappingInterface) {
      return $this->mapper->map($key);
    }
    return array($key);
  }
}
