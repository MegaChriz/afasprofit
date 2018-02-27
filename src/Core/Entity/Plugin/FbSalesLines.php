<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\EntityInterface;

/**
 * Class for a FbSalesLines entity.
 */
class FbSalesLines extends Entity {

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  protected function init() {
    // The item to order is an article by default.
    $this->setField('VaIt', 2);
    // By default the unit is 'apiece'.
    $this->setField('BiUn', 'Stk');
    // Set quantity to '1' by default.
    $this->setField('QuUn', 1);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function isValidChild(EntityInterface $entity) {
    switch ($entity->getType()) {
      case 'FbOrderBatchLines':
      case 'FbOrderSerialLines':
        return TRUE;
    }

    return FALSE;
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = parent::validate();

    // Round discount percentage on 2 digits if a percentage is given.
    if ($this->getField('PRDc')) {
      $this->setField('PRDc', round($this->getField('PRDc'), 2));
    }

    switch ($this->getAction()) {
      case static::FIELDS_INSERT:
        // Don't allow "GuLi" to be set when inserting an order line. This line
        // item ID should be defined by Profit.
        if ($this->fieldExists('GuLi')) {
          $this->removeField('GuLi');
        }
        break;

      default:
        // When updating or deleting an order line, the Profit line item ID must
        // be set.
        if (!$this->getField('GuLi')) {
          $errors[] = strtr('!field is a required field when updating or deleting an order line.', [
            '!field' => 'GuLi',
          ]);
        }
        // Some fields may not be changed upon update.
        $this->removeField('VaAD');
        $this->removeField('MaAD');
        break;
    }

    return $errors;
  }

}
