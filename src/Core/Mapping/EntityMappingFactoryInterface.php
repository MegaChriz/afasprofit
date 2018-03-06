<?php

namespace Afas\Core\Mapping;

use Afas\Core\Entity\EntityInterface;

/**
 * Interface for generating mapping classes.
 */
interface EntityMappingFactoryInterface {

  /**
   * Sets class to use for entity mapping.
   *
   * @param string $class
   *   The class to use as mapper.
   *
   * @return $this
   *   An instance of this class.
   *
   * @throws \InvalidArgumentException
   *   In case the class does not implement the expected interface.
   */
  public function setClass($class);

  /**
   * Creates a new mapping for the given entity.
   *
   * @param \Afas\Core\Entity\EntityInterface $entity
   *   The entity to create mapping for.
   *
   * @return \Afas\Core\Mapping\EntityMappingInterface
   *   An mapping object for entity mapping.
   */
  public function createForEntity(EntityInterface $entity);

}
