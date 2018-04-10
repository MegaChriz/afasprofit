<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\EntityInterface;
use InvalidArgumentException;

/**
 * Base class for relation entities, such as persons and organisations.
 */
abstract class Relation extends Entity {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function isValidChild(EntityInterface $entity) {
    switch ($entity->getType()) {
      case 'KnBasicAddressAdr':
      case 'KnBasicAddressPad':
        return TRUE;
    }

    return FALSE;
  }

  /**
   * Gets address object, if one exists.
   *
   * @param string $type
   *   (optional) The type of address to get:
   *   - address
   *   - postal_address
   *   Defaults to 'address'.
   *
   * @return \Afas\Core\Entity\EntityInterface|null
   *   An address entity or null if no such address exists yet.
   */
  public function getAddress($type = 'address') {
    $objects = $this->getObjectsOfType($this->resolveAddressType($type));
    if (!empty($objects)) {
      return reset($objects);
    }
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets address values.
   *
   * @param array $values
   *   The address values to set.
   * @param string $type
   *   (optional) The type of address to set:
   *   - address
   *   - postal_address
   *   Defaults to 'address'.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The address.
   */
  public function setAddress(array $values, $type = 'address') {
    return $this->setSingleObjectData($this->resolveAddressType($type), $values);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = parent::validate();

    switch ($this->getAction()) {
      case static::FIELDS_INSERT:
        // If the entity has an address, but no postal address then set 'PadAdr'
        // to TRUE, though only when inserting.
        if ($this->getAddress('address') && !$this->getAddress('postal_address')) {
          $this->setField('PadAdr', TRUE);
        }
        break;
    }

    return $errors;
  }

  // --------------------------------------------------------------
  // UTIL
  // --------------------------------------------------------------

  /**
   * Returns name of child object for a certain address type.
   *
   * @param string $type
   *   (optional) The type of address to set:
   *   - address
   *   - postal_address
   *   Defaults to 'address'.
   *
   * @return string
   *   Type of address.
   *
   * @throws \InvalidArgumentException
   *   In case an unknown address type was given.
   */
  protected function resolveAddressType($type) {
    switch ($type) {
      case 'address':
      case 'KnBasicAddressAdr':
        return 'KnBasicAddressAdr';

      case 'postal_address':
      case 'KnBasicAddressPad':
        return 'KnBasicAddressPad';

      default:
        throw new InvalidArgumentException(strtr("Unknown address type '!type'.", [
          '!type' => @(string) $type,
        ]));
    }
  }

}
