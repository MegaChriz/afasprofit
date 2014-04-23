<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterGroupInterface.
 */

namespace Afas\Core\Filter;

interface FilterGroupInterface {
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
   * @return self
   *   Returns current instance.
   */
  public function filter($field, $value = NULL, $operator = NULL);

  /**
   * Returns XML string.
   *
   * @return string
   *   XML generated string.
   * @todo Maybe move to an other interface as Filter implements this method
   * too?
   */
  public function compile();

  /**
   * Returns name of filter group.
   *
   * @return string
   *   The name of this filter group.
   */
  public function getName();
}
