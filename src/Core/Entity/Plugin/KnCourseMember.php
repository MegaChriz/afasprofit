<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;

/**
 * Class for a KnCourseMember entity.
 */
class KnCourseMember extends Entity {

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  protected function init() {
    // Set subscription date to today.
    $this->setField('SuDa', date('Y-m-d'));

    // Enable invoice by default.
    $this->setField('Invo', 1);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    return [
      'BcCo',
      'SuDa',
      'Invo',
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

    // Required attributes.
    if (!$this->getAttribute('CrId')) {
      $errors[] = strtr('Attribute CrId is not set for !entity.', [
        '!entity' => $this->getEntityType(),
      ]);
    }
    if (!$this->getAttribute('CdId')) {
      $errors[] = strtr('Attribute CdId is not set for !entity.', [
        '!entity' => $this->getEntityType(),
      ]);
    }

    // When the price is zero, there is a discount of 100 percent.
    if ($this->fieldExists('DfPr') && $this->getField('DfPr') == 0) {
      $this->setField('DiPc', 100);
      $this->removeField('DfPr');
    }

    // Round discount percentage to 2 digits if a percentage is given.
    if ($this->getField('DiPc')) {
      $this->setField('DiPc', round($this->getField('DiPc'), 2));
    }

    return $errors;
  }

}
