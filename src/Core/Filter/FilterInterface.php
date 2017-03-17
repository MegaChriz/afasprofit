<?php

namespace Afas\Core\Filter;

interface FilterInterface {
  /**
   * Returns XML string.
   *
   * @return string
   *   XML generated string.
   * @todo Maybe move to an other interface as FilterGroup implements this
   * method too?
   */
  public function compile();
}
