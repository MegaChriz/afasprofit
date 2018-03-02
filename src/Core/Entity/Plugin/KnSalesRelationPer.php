<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\EntityInterface;

/**
 * Class for a KnSalesRelationPer entity.
 *
 * Class hierarchy:
 * - KnSalesRelationPer
 *   - KnPerson
 *     - KnBankAccount
 *     - KnBasicAddressAdr
 *     - KnBasicAddressPad.
 */
class KnSalesRelationPer extends KnSalesRelation {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function isValidChild(EntityInterface $entity) {
    switch ($entity->getType()) {
      case 'KnPerson':
        return TRUE;
    }

    return FALSE;
  }

  /**
   * Gets the person object, if one exists.
   *
   * @return \Afas\Core\Entity\EntityInterface|null
   *   A person entity or null if no such person exists yet.
   */
  public function getPerson() {
    $objects = $this->getObjectsOfType('KnPerson');
    if (!empty($objects)) {
      return reset($objects);
    }
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets person data.
   *
   * @param array $values
   *   (optional) The values to fill the person entity with.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The person entity where values for have been set.
   */
  public function setPersonData(array $values = []) {
    return $this->setSingleObjectData('KnPerson', $values);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = parent::validate();

    if ($this->getAction() == static::FIELDS_INSERT) {
      // When inserting, ensure there is a person.
      if (!$this->hasObjectType('KnPerson')) {
        $errors[] = 'An object of type KnSalesRelationPer does not contain a KnPerson object.';
      }
    }

    return $errors;
  }

}
