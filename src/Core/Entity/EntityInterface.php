<?php

namespace Afas\Core\Entity;

use DOMDocument;

/**
 * Interface for entities.
 */
interface EntityInterface extends EntityContainerInterface {

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
   * Returns the type of this entity.
   *
   * @return string
   *   This entity's type.
   */
  public function getEntityType();

  /**
   * Returns the value of a field.
   *
   * @param string $field_name
   *   The name of the field.
   *
   * @return string|null
   *   The field value if it exists, or NULL otherwise.
   */
  public function getField($field_name);

  /**
   * Returns all field values.
   *
   * @return array
   *   An array of field values.
   */
  public function getFields();

  /**
   * Returns if a field exists.
   *
   * @param string $name
   *   The name of the field to check.
   *
   * @return bool
   *   True if the field exists.
   *   False otherwise.
   */
  public function fieldExists($name);

  /**
   * Returns the value of an attribute.
   *
   * @param string $name
   *   The name of the attribute.
   *
   * @return string|null
   *   The attribute value if it exists, or NULL otherwise.
   */
  public function getAttribute($name);

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
   * Sets an attribute for the Element-element.
   *
   * @param string $name
   *   The name of the attribute.
   * @param string $value
   *   The value of the attribute.
   *
   * @return $this
   *   An instance of this class.
   */
  public function setAttribute($name, $value);

  /**
   * Removes an attribute.
   *
   * @param string $name
   *   The name of the attribute to remove.
   *
   * @return $this
   *   An instance of this class.
   */
  public function removeAttribute($name);

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
