<?php

/**
 * @file
 * Contains \Afas\Core\Plugin\Entity\FbSales.
 */

namespace Afas\Core\Plugin\Entity;

use Afas\Core\Entity\EntityBase;

/**
 *
 */
class FbSales extends EntityBase {
  /**
   * {@inheritdoc}
   */
  public function addObject(EntityInterface $entity) {
  }

  /**
   * Adds a line item.
   *
   * @param array $values
   *   (optional) The values to fill the new entity with.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The created line item.
   */
  public function addLineItem(array $values = array()) {
    return $this->add('FbSalesLines', $values);
  }
}
