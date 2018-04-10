<?php

namespace Afas\Core\Filter;

/**
 * Interface for objects that can contain filters.
 */
interface FilterableInterface {

  /**
   * Adds a single filter.
   *
   * @param string $field
   *   The name of the field to filter on.
   * @param mixed $value
   *   The value to test the field against.
   * @param mixed $operator
   *   The comparison operator, such as =, <, or >=.
   *
   * @return \Afas\Core\Filter\FilterableInterface
   *   Returns current instance.
   */
  public function filter($field, $value = NULL, $operator = NULL);

  /**
   * Removes a filter.
   *
   * @param int $index
   *   The id of the filter to remove.
   *
   * @return \Afas\Core\Filter\FilterableInterface
   *   Returns current instance.
   */
  public function removeFilter($index);

  /**
   * Returns a list of configured filters.
   *
   * @return \Afas\Core\Filter\FilterableInterface[]
   *   A list of filters.
   */
  public function getFilters();

}
