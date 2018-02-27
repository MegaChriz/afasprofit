<?php

namespace Afas\Core\Entity;

use Afas\Afas;

/**
 * Trait for creating new child entities.
 */
trait EntityCreateTrait {

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
