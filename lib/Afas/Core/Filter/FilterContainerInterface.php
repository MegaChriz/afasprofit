<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterContainerInterface.
 */

namespace Afas\Core\Filter;

/**
 *
 */
interface FilterContainerInterface {
  /**
   * Return XML string.
   *
   * @return string
   *   XML generated string.
   */
  public function compile();
}