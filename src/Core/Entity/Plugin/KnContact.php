<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Exception\UndefinedParentException;
use InvalidArgumentException;

/**
 * Class for a KnContact entity.
 */
class KnContact extends Relation {

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

    return parent::isValidChild($entity);
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
   * {@inheritdoc}
   */
  public function setField($key, $value) {
    switch ($key) {
      case 'ViKc':
        switch ($value) {
          case 'AFD':
          case 'PRS':
          case 'AFL':
          case 'ORG':
          case 'PER':
            break;

          default:
            throw new InvalidArgumentException(strtr('Invalid value for ViKc: !value', [
              '!value' => @(string) $value,
            ]));
        }
    }

    return parent::setField($key, $value);
  }

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

    // Check that at max one person exists.
    if (count($this->getObjectsOfType('KnPerson')) > 1) {
      $errors[] = 'A KnContact object may not contain more than one KnPerson object.';
    }

    try {
      $parent = $this->getParent()->getType();
    }
    catch (UndefinedParentException $exception) {
      // Parent may not be set. That's okay.
      $parent = NULL;
    }

    if ($parent == 'KnOrganisation') {
      // When parent is KnOrganisation, CdId is not available.
      $this->removeField('CdId');
    }

    switch ($this->getAction()) {
      case static::FIELDS_UPDATE:
      case static::FIELDS_DELETE:
        // Identification of a contact is required, unless KnOrganisation is
        // parent.
        if ($parent != 'KnOrganisation') {
          $id_fields = [
            'BcCoPer',
            'CdId',
            'ExAd',
          ];
          $found = FALSE;
          foreach ($id_fields as $id_field) {
            if ($this->getField($id_field)) {
              // Identification is okay. Break out of the loop.
              $found = TRUE;
              break;
            }
          }
          if (!$found) {
            $errors[] = strtr('When updating or deleting a contact, one of the following fields is required: !fields.', [
              '!fields' => implode(', ', $id_fields),
            ]);
          }
        }
        break;
    }

    return $errors;
  }

}
