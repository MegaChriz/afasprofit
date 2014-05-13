<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterFactory.
 */

namespace Afas\Core\Filter;

use \Afas\Core\Filter\FilterFactoryInterface;
use \Afas\Core\Filter\Filter;
use \Afas\Core\Filter\FilterGroup;

/**
 * Class FilterFactory
 * @package Afas\Core\Filter
 */
class FilterFactory implements FilterFactoryInterface {
  /**
   * Implements FilterFactoryInterface::createFilter().
   */
  public function createFilter($field, $value = NULL, $operator = NULL) {
    return new Filter($field, $value, $operator);
  }

  /**
   * Implements FilterFactoryInterface::createFilterGroup().
   */
  public function createFilterGroup($name) {
    return new FilterGroup($name, $this);
  }
}
