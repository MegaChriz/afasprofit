<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterFactoryInterface.
 */

namespace Afas\Core\Filter;

/**
 * Interface for factory's that create filters and filter groups.
 */
interface FilterFactoryInterface {
  /**
   * Creates a filter.
   *
   * @param string $field
   *   The name of the field to filter on.
   * @param mixed $value
   *   (optional) The value to test the field against.
   *   Defaults to NULL.
   * @param mixed $operator
   *   (optional) The comparison operator, such as =, <, or >=.
   *
   * @return \Afas\Core\Filter\FilterInterface
   *   A filter.
   */
  public function createFilter($field, $value = NULL, $operator = NULL);

  /**
   * Creates a filter group.
   *
   * @param string $name
   *   The name of this filter group.
   *
   * @return \Afas\Core\Filter\FilterGroupInterface
   *   A filter group.
   */
  public function createFilterGroup($name);
}
