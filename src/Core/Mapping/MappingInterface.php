<?php

namespace Afas\Core\Mapping;

/**
 * Interface for mapping data.
 */
interface MappingInterface {

  /**
   * Returns mapping keys.
   *
   * @param string $key
   *   The key to map.
   *
   * @return array
   *   The keys to map to.
   */
  public function map($key);

}
