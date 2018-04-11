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
   * Removes a child object by giving an instance of EntityInterface.
   *
   * @param \Afas\Core\Entity\EntityInterface $entity
   *   The entity to remove.
   *
   * @return $this
   *   An instance of this class.
   */
  public function removeObject(EntityInterface $entity);

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
   * Returns all objects of a certain type.
   *
   * @param string|array $type
   *   The type of object to look for. If a string is given, only one object
   *   type is searched. If an array is given, all parts in the array will be
   *   searched.
   *
   * @return \Afas\Core\Entity\EntityInterface[]
   *   A list of child objects of the given type(s).
   *
   * @throws \InvalidArgumentException
   *   In case an invalid parameter was given.
   */
  public function getObjectsOfType($type);

  /**
   * Returns if the container contains any objects of the given type.
   *
   * @param string $type
   *   The type of object to look for.
   *
   * @return bool
   *   True if the container has an object of the given type, false otherwise.
   *
   * @throws \InvalidArgumentException
   *   In case an invalid parameter was given.
   */
  public function hasObjectType($type);

  /**
   * Returns if the container contains the given object.
   *
   * @param \Afas\Core\Entity\EntityInterface $entity
   *   The entity to look for in the container.
   *
   * @return bool
   *   True if this container contains the given entity, false otherwise.
   */
  public function containsObject(EntityInterface $entity);

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

  /**
   * Enables validation during compiling.
   */
  public function enableValidation();

  /**
   * Disables validation during compiling.
   */
  public function disableValidation();

  /**
   * Returns if validation is enabled.
   *
   * @return bool
   *   True if validation is enabled, false otherwise.
   */
  public function isValidationEnabled();

}
