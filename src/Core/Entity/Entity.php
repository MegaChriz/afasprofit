<?php

/**
 * @file
 * Contains \Afas\Core\Entity\Entity.
 */

namespace Afas\Core\Entity;

use \DOMDocument;
use Afas\Core\Entity\EntityInterface;
use Afas\Core\Mapping\MappingInterface;

class Entity implements EntityInterface, EntityContainerInterface, MappingInterface {
  /**
   * The entity type.
   *
   * @var string
   */
  protected $entityTypeId;

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
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs an Entity object.
   *
   * @param array $values
   *   An array of values to set, keyed by property name.
   * @param string $entity_type
   *   The type of the entity to create.
   */
  public function __construct(array $values, $entity_type) {
    $this->entityTypeId = $entity_type;
    // Set initial values.
    foreach ($values as $key => $value) {
      $this->setField($key, $value);
    }
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function id() {}

  /**
   * {@inheritdoc}
   */
  public function isNew() {}

  /**
   * {@inheritdoc}
   */
  public function getField($field_name) {}

  /**
   * Implements EntityContainerInterface::getObjects().
   */
  public function getObjects() {
    return $this->objects;
  }

  /**
   * {@inheritdoc}
   */
  public function getAction() {}

  /**
   * {@inheritdoc}
   */
  public function toArray() {}

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

      $element_xml->appendChild($objects_xml);
    }

    return $element_xml;
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function enforceIsNew($value = TRUE) {}

  /**
   * {@inheritdoc}
   */
  public function setField($key, $value) {
    $keys = $this->map($key);
    foreach ($keys as $key) {
      $this->fields[$key] = (string) $value;
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeField($field_name) {}

  /**
   * Implements EntityContainerInterface::add().
   */
  public function add($entity_type, array $values = array()) {
    $entity = $this->factory->createEntity($entity_type, $values);
    $this->addObject($entity);
    return $entity;
  }

  /**
   * Implements EntityContainerInterface::addObject().
   */
  public function addObject(EntityInterface $entity) {
    $this->objects[] = $entity;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setAction($action) {}

  /**
   * {@inheritdoc}
   */
  public function fromArray(array $data) {}

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

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function save() {}

  /**
   * {@inheritdoc}
   */
  public function delete() {}

  /**
   * Return XML string.
   *
   * @return string
   *   XML generated string.
   */
  public function compile() {
    $doc = new DOMDocument();

    // Create root element.
    $root = $doc->createElement($this->entityTypeId);
    $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $doc->appendChild($root);

    // Add entity XML.
    $node = $this->toXML($doc);
    $root->appendChild($node);

    return $doc->saveXML($root);
  }
}
