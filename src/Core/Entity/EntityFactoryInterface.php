<?php

/**
 * @file
 * Contains \Afas\Core\Entity\EntityFactoryInterface.
 */

namespace Afas\Core\Entity;

/**
 * Interface for entity factory.
 */
interface EntityFactoryInterface {
  /**
   * Creates an entity.
   *
   * @param string $entity_type
   *   The type of entity to create.
   * @param array $values
   *   (optional) The values to fill the new entity with.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The created entity.
   */
  public function createEntity($entity_type, array $values = array());
}
