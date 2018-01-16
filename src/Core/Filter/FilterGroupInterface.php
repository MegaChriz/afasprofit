<?php

namespace Afas\Core\Filter;

use Afas\Core\CompilableInterface;

/**
 * Interface for a group of filters.
 */
interface FilterGroupInterface extends CompilableInterface, FilterableInterface {

  /**
   * Returns name of filter group.
   *
   * @return string
   *   The name of this filter group.
   */
  public function getName();

}
