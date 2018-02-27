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
  public function add($entity_type, array $values = []);

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
   * Returns if the given entity could be a valid child.
   *
   * @param \Afas\Core\Entity\EntityInterface $entity
   *   The entity to check.
   *
   * @return bool
   *   True if the entity is a valid child, false otherwise.
   */
  public function isValidChild(EntityInterface $entity);

  /**
   * Gets the field action.
   *
   * @return string
   *   The field's action: insert, update or delete.
   */
  public function getAction();

  /**
   * Converts the entity and all child entities to an array.
   *
   * @return mixed[]
   *   An array, representing the data of this entity.
   */
  public function toArray();

  /**
   * Gets container's type.
   *
   * @return string
   *   The container's type.
   */
  public function getType();

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Validates/corrects the structure of this element.
   *
   * This should be implemented to ensure that the structure is valid before the
   * data is send to Afas Profit.
   *
   * @return string[]
   *   An array of error messages.
   */
  public function validate();

}
