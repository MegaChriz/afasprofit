<?php

namespace Afas\Core\Entity;

use DOMDocument;

/**
 * Interface for entities.
 */
interface EntityInterface {

  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  /**
   * Inserts new records in Profit.
   *
   * @var string
   */
  const FIELDS_INSERT = 'insert';

  /**
   * Updates existing records in Profit.
   *
   * @var string
   */
  const FIELDS_UPDATE = 'update';

  /**
   * Deletes existing records in Profit.
   *
   * @var string
   */
  const FIELDS_DELETE = 'delete';

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns the identifier.
   *
   * @return string|int|null
   *   The entity identifier, or NULL if the object does not yet have an
   *   identifier.
   */
  public function id();

  /**
   * Returns whether the entity is new.
   *
   * @return bool
   *   TRUE if the entity is new, or FALSE if the entity has already been saved.
   */
  public function isNew();

  /**
   * Returns the value of a field.
   *
   * @param string $field_name
   *   The name of the field that should be returned.
   *
   * @return string|null
   *   The field if it exists, or NULL otherwise.
   */
  public function getField($field_name);

  /**
   * Gets the field action.
   *
   * @return string
   *   The field's action: insert, update or delete.
   *
   * @todo Maybe move to ElementInterface.
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
   * Converts the entity and all child entities to XML.
   *
   * @param \DOMDocument $doc
   *   (optional) An instance of DOMDocument.
   *
   * @return \DOMNode
   *   An instance of DOMNode.
   */
  public function toXml(DOMDocument $doc = NULL);

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Enforces an entity to be new.
   *
   * @param bool $value
   *   (optional) Whether the entity should be forced to be new. Defaults to
   *   TRUE.
   *
   * @return self
   *   An instance of this class.
   *
   * @see \Drupal\Core\Entity\EntityInterface::isNew()
   *
   * @todo Rename?
   */
  public function enforceIsNew($value = TRUE);

  /**
   * Sets the value of a field.
   *
   * @param string $field_name
   *   The name of the field that should be set.
   * @param string $value
   *   The value the field should be set to.
   *
   * @return $this
   *   An instance of this class.
   */
  public function setField($field_name, $value);

  /**
   * Removes a field.
   *
   * @param string $field_name
   *   The field to remove.
   *
   * @return $this
   *   An instance of this class.
   */
  public function removeField($field_name);

  /**
   * Sets field action.
   *
   * @param string $action
   *   The action to set.
   *
   * @return $this
   *   An instance of this class.
   *
   * @todo Maybe move to ElementInterface.
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
  // ACTION
  // --------------------------------------------------------------

  /**
   * Saves an entity permanently.
   *
   * When saving existing entities, the entity is assumed to be complete,
   * partial updates of entities are not supported.
   *
   * @return int
   *   Either SAVED_NEW or SAVED_UPDATED, depending on the operation performed.
   *
   * @throws \...Exception
   *   In case of failures an exception is thrown.
   *
   * @todo Child entities can probably not be saved.
   * @todo Because of above, move to other interface?
   */
  public function save();

  /**
   * Deletes an entity permanently.
   *
   * @throws \...Exception
   *   In case of failures an exception is thrown.
   */
  public function delete();

}
