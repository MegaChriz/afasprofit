<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterFactory.
 */

namespace Afas\Core\Filter;

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
