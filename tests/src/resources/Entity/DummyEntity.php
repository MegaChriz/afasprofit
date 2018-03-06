<?php

namespace Afas\Tests\resources\Entity;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Entity\EntityContainerInterface;
use DOMDocument;
use LogicException;

/**
 * A dummy entity implementation.
 *
 * @see \Afas\Tests\Core\Entity\EntityFactoryTest::testCreateInstanceWithoutMapping()
 */
class DummyEntity implements EntityInterface {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getEntityType() {}

  /**
   * {@inheritdoc}
   */
  public function getField($field_name) {}

  /**
   * {@inheritdoc}
   */
  public function getFields() {}

  /**
   * {@inheritdoc}
   */
  public function fieldExists($name) {}

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {}

  /**
   * {@inheritdoc}
   */
  public function getAttribute($name) {}

  /**
   * {@inheritdoc}
   */
  public function getAction() {}

  /**
   * {@inheritdoc}
   */
  public function toXml(DOMDocument $doc = NULL) {}

  /**
   * {@inheritdoc}
   */
  public function getParent() {}

  /**
   * {@inheritdoc}
   */
  public function getObjects() {}

  /**
   * {@inheritdoc}
   */
  public function getObjectsOfType($type) {}

  /**
   * {@inheritdoc}
   */
  public function hasObjectType($type) {}

  /**
   * {@inheritdoc}
   */
  public function containsObject(EntityInterface $entity) {}

  /**
   * {@inheritdoc}
   */
  public function isValidChild(EntityInterface $entity) {}

  /**
   * {@inheritdoc}
   */
  public function toArray() {}

  /**
   * {@inheritdoc}
   */
  public function getType() {}

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function setField($field_name, $value) {}

  /**
   * {@inheritdoc}
   */
  public function removeField($field_name) {}

  /**
   * {@inheritdoc}
   */
  public function setAttribute($name, $value) {}

  /**
   * {@inheritdoc}
   */
  public function removeAttribute($name) {}

  /**
   * {@inheritdoc}
   */
  public function setAction($action) {}

  /**
   * {@inheritdoc}
   */
  public function setParent(EntityContainerInterface $container) {}

  /**
   * {@inheritdoc}
   */
  public function add($entity_type, array $values = []) {}

  /**
   * {@inheritdoc}
   */
  public function addObject(EntityInterface $entity) {}

  /**
   * {@inheritdoc}
   */
  public function fromArray(array $data) {}

  /**
   * Dummy method for setting a mapper.
   *
   * @throws \LogicException
   *   In case this method gets called.
   */
  public function setMapper() {
    throw new LogicException('setMapper() should not be called on objects not implementing EntityWithMappingInterface.');
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validate() {}

  /**
   * {@inheritdoc}
   */
  public function compile() {}

  /**
   * {@inheritdoc}
   */
  public function save() {}

  /**
   * {@inheritdoc}
   */
  public function delete() {}

}
