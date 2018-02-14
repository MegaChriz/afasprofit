<?php

namespace Afas\Core\Entity;

use Afas\Core\CompilableInterface;

/**
 * Interface for the entity container.
 */
interface EntityContainerInterface extends CompilableInterface {

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

  /**
   * Sets field action.
   *
   * @param string $action
   *   The action to set.
   *
   * @return $this
   *   An instance of this class.
   */
  public function setAction($action);

  /**
   * Loads data in from an array.
   *
   * @param array $data
   *   The data to load in.
   *
   * @return $this
   *   An instance of this class.
   *
   * @todo Maybe move to an other interface.
   */
  public function fromArray(array $data);

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns all the objects that this container has.
   *
   * @return \Afas\Core\Entity\EntityInterface[]
   *   An array of entities.
   */
  public function getObjects();

  /**
   * Gets the field action.
   *
   * @return string
   *   The field's action: insert, update or delete.
   */
  public function getAction();

}
