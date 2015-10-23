<?php

/**
 * @file
 * Definition of \Afas\Core\Entity\EntityInterface.
 */

namespace Afas\Core\Entity;

/**
 * Defines a common interface for all entity objects.
 */
interface EntityInterface {
  /**
   * Gets the identifier.
   *
   * @return string|int|null
   *   The entity identifier, or NULL if the object does not yet have an
   *   identifier.
   */
  public function id();

  /**
   * Gets the label of the entity.
   *
   * @return string|null
   *   The label of the entity, or NULL if there is no label defined.
   */
  public function label();

  /**
   * Loads an entity from Profit.
   *
   * @param mixed $id
   *   The id of the entity to load.
   *
   * @return static
   *   The entity object or NULL if there is no entity with the given ID.
   */
  public static function load($id);

  /**
   * Loads one or more entities.
   *
   * @param array $ids
   *   An array of entity IDs, or NULL to load all entities.
   *
   * @return static[]
   *   An array of entity objects indexed by their IDs.
   */
  public static function loadMultiple(array $ids = NULL);

  /**
   * Constructs a new entity object, without saving it in Profit.
   *
   * @param array $values
   *   (optional) An array of values to set, keyed by property name.
   *
   * @return static
   *   The entity object.
   */
  public static function create(array $values = array());

  /**
   * Saves an entity in Profit.
   *
   * When saving existing entities, the entity is assumed to be complete,
   * partial updates of entities are not supported.
   *
   * @return ???
   *
   * @throws Exception?
   *   In case of failures an exception is thrown.
   */
  public function save();

  /**
   * Deletes an entity from Profit.
   *
   * @throws Exception?
   *   In case of failures an exception is thrown.
   */
  public function delete();

  /**
   * Gets an array of all property values.
   *
   * @return mixed[]
   *   An array of property values, keyed by property name.
   */
  public function toArray();
}
