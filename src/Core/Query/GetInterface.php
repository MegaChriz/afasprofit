<?php

/**
 * @file
 * Definition of \Afas\Core\Query\GetInterface.
 */

namespace Afas\Core\Query;

interface GetInterface extends QueryInterface {
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
   * @return Afas\Core\Query\Get
   *   Returns current instance.
   */
  public function filter($field, $value = NULL, $operator = '=');

  /**
   * Adds a filter group.
   *
   * @param string $name
   *   (optional) The name of the filter group.
   *   Defaults to 'Filter N' where N is the number of filter groups currently
   *   defined.
   *
   * @return Afas\Core\Filter\FilterGroupInterface
   *   Returns an new instance of FilterGroupInterface.
   */
  public function group($name = NULL);
}
