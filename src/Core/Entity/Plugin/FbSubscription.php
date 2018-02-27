<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\EntityInterface;

/**
 * Class for a FbSubscription entity.
 */
class FbSubscription extends Entity {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    return [
      // The subscription must have a cyclus for billing.
      'VaIn',
      // The subscription must be of a type.
      'VaSu',
      // The subscription must belong to a organisation or a person.
      'BcId',
      // The subscription must have a start date.
      'SuSt',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isValidChild(EntityInterface $entity) {
    return $entity->getType() === 'FbSubscriptionLines';
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Adds a line item.
   *
   * @param array $values
   *   (optional) The values to fill the new entity with.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The created line item.
   */
  public function addLineItem(array $values = []) {
    return $this->add('FbSubscriptionLines', $values);
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
        if (!$this->fieldExists('DbId')) {
          $errors[] = strtr('!field is a required field when inserting a subscription.', [
            '!field' => 'DbId',
          ]);
        }
        break;

      default:
        // Don't allow "DbId" to be set when updating a subscription. Profit
        // will not accept the subscription if it is set. Even if it's equal to
        // the current value in Profit.
        if ($this->fieldExists('DbId')) {
          $this->removeField('DbId');
        }
        break;
    }

    // Make sure a renew cyclus is set if a renew date is set.
    if ($this->getField('DaRe') && !$this->getField('VaRe')) {
      $this->setField('VaRe', $this->getField('VaIn'));
    }

    return $errors;
  }

}
