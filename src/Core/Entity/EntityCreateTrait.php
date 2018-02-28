<?php

namespace Afas\Core\Entity;

use Afas\Afas;
use InvalidArgumentException;

/**
 * Trait for creating new child entities.
 */
trait EntityCreateTrait {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  abstract public function getObjects();

  /**
   * {@inheritdoc}
   */
  public function getObjectsOfType($type) {
    if (is_string($type)) {
      $type = [$type];
    }
    if (!is_array($type)) {
      // No array. Unwanted.
      throw new InvalidArgumentException(get_class($this) . '::getObjectsOfType() only accepts an array or a string as parameter.');
    }

    $return = [];
    foreach ($this->getObjects() as $object) {
      if (in_array($object->getType(), $type)) {
        $return[] = $object;
      }
    }
    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function hasObjectType($type) {
    if (!is_string($type)) {
      // No string. Unwanted.
      throw new InvalidArgumentException(get_class($this) . '::hasObjectType() only accepts a string as parameter.');
    }

    foreach ($this->getObjects() as $object) {
      if ($object->getType() == $type) {
        return TRUE;
      }
    }

    return FALSE;
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function add($entity_type, array $values = []) {
    $entity = $this->getManager()->createInstance($entity_type, $values);
    $this->addObject($entity);
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  abstract public function addObject(EntityInterface $entity);

  /**
   * {@inheritdoc}
   */
  public function isValidChild(EntityInterface $entity) {
    // Allow this entity to be added by default.
    return TRUE;
  }

  /**
   * Gets the entity manager.
   *
   * @return \Afas\Core\Entity\EntityManagerInterface
   *   The entity manager.
   */
  public function getManager() {
    return Afas::service('afas.entity.manager');
  }

}
