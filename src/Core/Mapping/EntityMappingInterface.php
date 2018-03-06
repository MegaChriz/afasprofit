<?php

namespace Afas\Core\Mapping;

use Afas\Core\Entity\EntityInterface;

/**
 * Interface for entity mapping.
 */
interface EntityMappingInterface extends MappingInterface {

  /**
   * Constructs a new EntityMappingInterface object.
   *
   * @param \Afas\Core\Entity\EntityInterface $entity
   *   The entity for which to create a mapping interface.
   *
   * @return static
   *   An instance of this class.
   */
  public static function create(EntityInterface $entity);

}
