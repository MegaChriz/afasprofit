<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;

/**
 * Class for a FbSubscriptionLines entity.
 */
class FbSubscriptionLines extends Entity {

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function init() {
    // The item to order is an article by default.
    $this->setField('VaIt', 2);
    // The date of ordering is today by default.
    $this->setField('DaPu', date('Y-m-d'));
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    return [
      // The date that the subscription starts is mandatory.
      'DaSt',
    ];
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
        // Don't allow "Id" to be set when inserting a subscription line. This
        // line item ID should be defined by Profit.
        if ($this->fieldExists('Id')) {
          $this->removeField('Id');
        }
        break;

      default:
        // When updating or deleting a subscription line, the Profit line item
        // ID must be set.
        if (!$this->getField('Id')) {
          $errors[] = strtr('!field is a required field when updating or deleting a subscription line.', [
            '!field' => 'Id',
          ]);
        }
        break;
    }

    return $errors;
  }

}
