<?php

namespace Afas\Core\Entity;

use DOMDocument;
use Afas\Core\Mapping\MappingInterface;

/**
 * Base class for entities.
 */
class Entity implements EntityInterface, EntityContainerInterface, MappingInterface {

  use ActionTrait;
  use EntityCreateTrait;

  /**
   * The entity type.
   *
   * @var string
   */
  protected $entityType;

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
   * The mapper.
   *
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
    $this->entityType = $entity_type;

    // Set initial values.
    $this->fromArray($values);
    $this->setAction(static::FIELDS_INSERT);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getEntityType() {
    return $this->entityType;
  }

  /**
   * {@inheritdoc}
   */
  public function getField($field_name) {
    if (isset($this->fields[$field_name])) {
      return $this->fields[$field_name];
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getObjects() {
    return $this->objects;
  }

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    $return = $this->fields;
    foreach ($this->getObjects() as $object) {
      $return[$object->getEntityType()][] = $object->toArray();
    }
    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function toXml(DOMDocument $doc = NULL) {
    if (!isset($doc)) {
      $doc = new DOMDocument();
      $root = $doc->createElement($this->entityType);
      $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
      $doc->appendChild($root);
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
  public function removeField($field_name) {
    unset($this->fields[$field_name]);
  }

  /**
   * {@inheritdoc}
   */
  public function addObject(EntityInterface $entity) {
    $this->objects[] = $entity;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function fromArray(array $data) {
    foreach ($data as $key => $value) {
      if (is_scalar($value)) {
        $this->setField($key, $value);
      }
      elseif (is_array($value)) {
        foreach ($value as $object_data) {
          $this->add($key, $object_data);
        }
      }
    }
    return $this;
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
   * {@inheritdoc}
   */
  public function compile() {
    $doc = new DOMDocument();

    // Create root element.
    $root = $doc->createElement($this->entityType);
    $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $doc->appendChild($root);

    // Add entity XML.
    $node = $this->toXml($doc);
    $root->appendChild($node);

    return $doc->saveXML($root);
  }

  // --------------------------------------------------------------
  // UTIL
  // --------------------------------------------------------------

  /**
   * Updates childs.
   */
  protected function updateChilds() {
    foreach ($this->getObjects() as $object) {
      $object->setAction($this->getAction());
    }
  }

}
