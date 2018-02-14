<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\EntityInterface;

/**
 * Class for a FbSalesLines entity.
 *
 * @Entity (
 *   id = "FbSalesLines",
 * )
 */
class FbSalesLines extends Entity {

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
