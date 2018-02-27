<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\EntityInterface;

/**
 * Class for a FbSales entity.
 */
class FbSales extends Entity {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function isValidChild(EntityInterface $entity) {
    return $entity->getType() === 'FbSalesLines';
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
    return $this->add('FbSalesLines', $values);
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
        if (!$this->getField('DbId')) {
          $errors[] = strtr('!field is a required field when inserting an order.', [
            '!field' => 'DbId',
          ]);
        }
        break;

      default:
        // Don't allow "DbId" to be set when updating an order. Profit will not
        // accept the order if it is set. Even if it's equal to the current
        // value in Profit.
        if ($this->getField('DbId')) {
          $this->removeField('DbId');
        }
        // The "Re" field may not be changed when updating the order (for some
        // reason).
        $this->removeField('Re');
        // The "War" field may also not be changed when updating.
        $this->removeField('War');
        break;
    }

    return $errors;
  }

}
