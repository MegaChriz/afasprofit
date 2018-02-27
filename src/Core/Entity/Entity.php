<?php

namespace Afas\Core\Entity;

use Afas\Core\Exception\EntityValidationException;
use Afas\Core\Mapping\MappingInterface;
use DOMDocument;

/**
 * Base class for entities.
 */
class Entity implements EntityInterface, MappingInterface {

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
   * List of attributes.
   *
   * @var array
   */
  protected $attributes = [];

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
    $this->init();

    // Set initial values.
    $this->fromArray($values);
    $this->setAction(static::FIELDS_INSERT);
  }

  /**
   * Sets initial values.
   *
   * Can be used by subclasses to do some initialization upon object creation.
   */
  protected function init() {}

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
  public function getType() {
    return $this->getEntityType();
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
  public function getFields() {
    return $this->fields;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldExists($name) {
    if (isset($this->fields[$name])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttribute($name) {
    if (isset($this->attributes[$name])) {
      return $this->attributes[$name];
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
    if (count($this->attributes)) {
      $return['@attributes'] = $this->attributes;
    }
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
    if (count($this->attributes) > 0) {
      foreach ($this->attributes as $name => $value) {
        $element_xml->setAttribute($name, $value);
      }
    }

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

  /**
   * {@inheritdoc}
   */
  public function validate() {
    return [];
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
  public function setAttribute($name, $value) {
    $this->attributes[$name] = (string) $value;
  }

  /**
   * {@inheritdoc}
   */
  public function removeAttribute($name) {
    unset($this->attributes[$name]);
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
        if ($key == '@attributes') {
          foreach ($value as $attribute_name => $attribute_value) {
            $this->setAttribute($attribute_name, $attribute_value);
          }
        }
        else {
          foreach ($value as $object_data) {
            $this->add($key, $object_data);
          }
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
    return [$key];
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
    // Validation *must* pass.
    $this->mustValidate();

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

  /**
   * Validates the entity and throws an exception if validation fails.
   *
   * @throws \Afas\Core\Exception\EntityValidationException
   *   When validation fails.
   */
  protected function mustValidate() {
    $errors = $this->validate();
    if (!empty($errors)) {
      throw new EntityValidationException($this, $errors);
    }
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
