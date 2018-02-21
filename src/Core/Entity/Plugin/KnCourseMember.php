<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;

/**
 * Class for a KnCourseMember entity.
 *
 * @todo add tests.
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
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = parent::validate();

    // Required attributes.
    if (!$this->getAttribute('CrId')) {
      $errors[] = strtr('Attribute CrId is not set for !entity.', array('!entity' => $this->getEntityType()));
    }
    if (!$this->getAttribute('CdId')) {
      $errors[] = strtr('Attribute CdId is not set for !entity.', array('!entity' => $this->getEntityType()));
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

    // Required fields.
    $reqs = array(
      'BcCo',
      'SuDa',
      'Invo',
    );
    foreach ($reqs as $req) {
      if (!$this->fieldExists($req)) {
        $errors[] = strtr('!field is a required field.', array('!field' => $req));
      }
    }

    return $errors;
  }

}
