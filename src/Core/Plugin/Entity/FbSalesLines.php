<?php

namespace Afas\Core\Plugin\Entity;

use Afas\Core\Entity\EntityBase;

/**
 *
 */
class FbSalesLines extends EntityBase {
  /**
   * {@inheritdoc}
   */
  public function addObject(EntityInterface $entity) {
  }

  /**
   * Returns default values for this entity.
   *
   * @return array
   *   An array of default values.
   *
   * @todo Move to constructor, init() and/or interface?
   */
  public function defaults() {
    return array(
      'VaIt' => 2,
      'BiUn' => 'Stk',
    );
  }
}
