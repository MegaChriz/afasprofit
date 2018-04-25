<?php

namespace Afas\Core\Entity;

use Afas\Afas;
use Afas\Component\Utility\ArrayHelper;
use Afas\Core\Exception\UndefinedParentException;
use Afas\Core\Mapping\MappingInterface;
use DOMDocument;
use InvalidArgumentException;

/**
 * Base class for entities.
 */
class Entity implements EntityWithMappingInterface {

  use ActionTrait;
  use EntityCreateTrait;
  use MustValidateTrait;

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
   * The container to which the entity belongs.
   *
   * @var \Afas\Core\Entity\EntityContainerInterface|null
   */
  protected $parent;

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

    // Setup mapper.
    $this->setMapper(Afas::service('afas.entity.mapping_factory')->createForEntity($this));

    // Let subclasses perform initialization.
    $this->init();

    // Set initial values.
    $this->setAction(static::FIELDS_INSERT);
    $this->fromArray($values);
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
    if (array_key_exists($name, $this->fields)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    return [];
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
  public function containsObject(EntityInterface $entity) {
    return isset($this->objects[spl_object_hash($entity)]);
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
    $fields_xml = $doc->createElement('Fields');
    $fields_xml->setAttribute('Action', $this->getAction());
    if (count($this->fields) > 0) {
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
    }
    $element_xml->appendChild($fields_xml);

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
  public function getParent() {
    if (!$this->parent) {
      throw new UndefinedParentException('This entity does not have a parent.');
    }
    return $this->parent;
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
      if (is_bool($value)) {
        $value = (int) $value;
      }
      if (!is_null($value)) {
        $this->fields[$key] = (string) $value;
      }
      else {
        $this->fields[$key] = NULL;
      }
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeField($field_name) {
    $keys = $this->map($field_name);
    foreach ($keys as $key) {
      unset($this->fields[$key]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setAttribute($key, $value) {
    $keys = $this->map($key);
    foreach ($keys as $key) {
      if (is_bool($value)) {
        $value = (int) $value;
      }
      $this->attributes[$key] = (string) $value;
    }
    return $this;
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
    if (!$this->isValidChild($entity)) {
      throw new InvalidArgumentException(strtr('!parent_type does not accept child objects of type !child_type.', [
        '!parent_type' => $this->getType(),
        '!child_type' => $entity->getType(),
      ]));
    }
    $this->objects[spl_object_hash($entity)] = $entity;
    $entity->setParent($this);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeObject(EntityInterface $entity) {
    unset($this->objects[spl_object_hash($entity)]);
  }

  /**
   * {@inheritdoc}
   */
  public function fromArray(array $data) {
    foreach ($data as $key => $value) {
      if (is_scalar($value) || is_null($value)) {
        $this->setField($key, $value);
      }
      elseif (is_array($value)) {
        if ($key == '@attributes') {
          foreach ($value as $attribute_name => $attribute_value) {
            $this->setAttribute($attribute_name, $attribute_value);
          }
        }
        else {
          if (ArrayHelper::isAssociative($value)) {
            $item = $this->add($key, $value);
            $item->setAction($this->getAction());
          }
          else {
            foreach ($value as $object_data) {
              $item = $this->add($key, $object_data);
              $item->setAction($this->getAction());
            }
          }
        }
      }
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setMapper(MappingInterface $mapper) {
    $this->mapper = $mapper;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function unsetMapper() {
    $this->mapper = NULL;
    return $this;
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

  /**
   * {@inheritdoc}
   */
  public function setParent(EntityContainerInterface $container) {
    // Ensure that the parent contains this entity.
    if (!$container->containsObject($this)) {
      throw new InvalidArgumentException('The given entity container does not appear to contain this entity.');
    }

    $this->parent = $container;
    return $this;
  }

  /**
   * Sets data for a child object for which only one may exist.
   *
   * @param string $entity_type
   *   The type of entity to set.
   * @param array $values
   *   The values to fill the entity with.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The created entity.
   *
   * @throws \InvalidArgumentException
   *   In case an unexpected entity type was given.
   */
  protected function setSingleObjectData($entity_type, array $values) {
    if (!is_string($entity_type)) {
      throw new InvalidArgumentException('Specified entity type should be a string.');
    }

    $objects = $this->getObjectsOfType($entity_type);
    if (empty($objects)) {
      return $this->add($entity_type, $values);
    }
    $object = reset($objects);
    $object->fromArray($values);
    return $object;
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = [];

    foreach ($this->getRequiredFields() as $field) {
      if (!$this->fieldExists($field)) {
        $errors[] = strtr('!field is a required field for type !type.', [
          '!field' => $field,
          '!type' => $this->getType(),
        ]);
      }
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function compile() {
    if ($this->isValidationEnabled()) {
      // Validation *must* pass.
      $this->mustValidate();
    }

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
