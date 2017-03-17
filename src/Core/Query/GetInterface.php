<?php

namespace Afas\Core\Query;

interface GetInterface extends QueryInterface {
  /**
   * Sets a range to use.
   *
   * @param int $offset
   *   The number of records to skip.
   * @param int $length
   *   The number of records to take.
   *
   * @return Afas\Core\Query\GetInterface
   *   Returns current instance.
   */
  public function range($offset, $length);

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
   * @return Afas\Core\Query\GetInterface
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

  /**
   * Returns the used filter container.
   *
   * @return \Afas\Core\Filter\FilterContainerInterface
   *   A filter container.
   */
  public function getFilterContainer();
}
