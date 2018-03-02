<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\EntityInterface;

/**
 * Class for a KnSalesRelationOrg entity.
 *
 * Class hierarchy:
 * - KnSalesRelationOrg
 *   - KnOrganisation
 *     - KnBankAccount
 *     - KnBasicAddressAdr
 *     - KnBasicAddressPad
 *     - KnContact
 *       - KnBasicAddressAdr
 *       - KnBasicAddressPad
 *       - KnPerson
 *         - KnBankAccount
 *         - KnBasicAddressAdr
 *         - KnBasicAddressPad.
 */
class KnSalesRelationOrg extends KnSalesRelation {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function isValidChild(EntityInterface $entity) {
    switch ($entity->getType()) {
      case 'KnOrganisation':
        return TRUE;
    }

    return FALSE;
  }

  /**
   * Gets the organisation object, if one exists.
   *
   * @return \Afas\Core\Entity\EntityInterface|null
   *   A organisation entity or null if no such organisation exists yet.
   */
  public function getOrganisation() {
    $objects = $this->getObjectsOfType('KnOrganisation');
    if (!empty($objects)) {
      return reset($objects);
    }
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets organisation data.
   *
   * @param array $values
   *   (optional) The values to fill the person entity with.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The organisation entity where values for have been set.
   */
  public function setOrganisationData(array $values = []) {
    return $this->setSingleObjectData('KnOrganisation', $values);
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
      // When inserting, ensure there is an organisation.
      if (!$this->hasObjectType('KnOrganisation')) {
        $errors[] = 'An object of type KnSalesRelationOrg does not contain a KnOrganisation object.';
      }
    }

    return $errors;
  }

}
