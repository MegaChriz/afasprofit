<?php

namespace Afas\Core\Mapping;

/**
 * Base class for mapping classes.
 */
abstract class MappingBase implements MappingInterface {

  /**
   * The defined mappings.
   *
   * Subclasses should override this variable to add in their mappings.
   *
   * @var array
   */
  protected $mappings = [];

  /**
   * Implements MappingInterface::map().
   */
  public function map($key) {
    if (isset($this->mappings[$key])) {
      return (array) $this->mapping[$key];
    }
    return [$key];
  }

}
