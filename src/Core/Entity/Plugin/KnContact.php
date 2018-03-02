<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\EntityInterface;

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

    return $errors;
  }

}
