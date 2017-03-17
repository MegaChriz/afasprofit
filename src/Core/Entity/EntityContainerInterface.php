<?php

namespace Afas\Core\Entity;

/**
 * Interface for entity container.
 */
interface EntityContainerInterface {
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Adds a child object by giving an entity type.
   *
   * @param string $entity_type
   *   The type of entity to add.
   * @param array $values
   *   (optional) The values to fill the new entity with.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The created entity.
   */
  public function add($entity_type, array $values = array());

  /**
   * Adds a child object by giving an instance of EntityInterface.
   *
   * @param \Afas\Core\Entity\EntityInterface $entity
   *   The entity to add.
   *
   * @return $this
   *   An instance of this class.
   */
  public function addObject(EntityInterface $entity);

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns all the objects that this container has.
   *
   * @return \Afas\Core\Entity\EntityInterface[]
   */
  public function getObjects();

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Return XML string.
   *
   * @return string
   *   XML generated string.
   */
  public function compile();
}
