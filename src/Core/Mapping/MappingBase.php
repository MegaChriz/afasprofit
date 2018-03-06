<?php

namespace Afas\Core\Mapping;

/**
 * Base class for mapping classes.
 */
abstract class MappingBase implements MappingInterface {

  /**
   * A list of mappings.
   *
   * @var array
   */
  protected $cachedMappings;

  /**
   * Returns aliases for Profit fields.
   *
   * @return array
   *   A list of mappings.
   */
  abstract protected function getMappings();

  /**
   * {@inheritdoc}
   */
  public function map($key) {
    if (!isset($this->cachedMappings)) {
      $this->cachedMappings = $this->getMappings();
    }

    if (isset($this->cachedMappings[$key])) {
      return (array) $this->cachedMappings[$key];
    }
    return [$key];
  }

}
