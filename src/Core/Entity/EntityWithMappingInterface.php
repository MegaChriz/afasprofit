<?php

namespace Afas\Core\Entity;

use Afas\Core\Mapping\MappingInterface;

/**
 * Interface for entities that support mapping.
 */
interface EntityWithMappingInterface extends EntityInterface, MappingInterface {

  /**
   * Sets mapper.
   *
   * @param \Afas\Core\Mapping\MappingInterface $mapper
   *   The mapper to set.
   *
   * @return $this
   *   An instance of this class.
   */
  public function setMapper(MappingInterface $mapper);

  /**
   * Unsets mapper.
   *
   * @return $this
   *   An instance of this class.
   */
  public function unsetMapper();

}
