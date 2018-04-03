<?php

namespace Afas\Core\Query;

use Afas\Core\Filter\FilterableInterface;
use Afas\Core\Filter\GroupableInterface;

/**
 * Interface for get queries.
 */
interface GetInterface extends QueryInterface, FilterableInterface, GroupableInterface {

  /**
   * Sets a range to use.
   *
   * @param int $offset
   *   The number of records to skip.
   * @param int $length
   *   The number of records to take.
   *
   * @return \Afas\Core\Query\GetInterface
   *   Returns current instance.
   */
  public function range($offset, $length);

  /**
   * Order by a specific field.
   *
   * @param string $field
   *   The field on which to order.
   * @param string $direction
   *   The direction to sort. Legal values are "ASC" and "DESC". Any other value
   *   will be converted to "ASC".
   *
   * @return \Afas\Core\Query\GetInterface
   *   Returns current instance.
   */
  public function orderBy($field, $direction = 'ASC');

  /**
   * Returns the used filter container.
   *
   * @return \Afas\Core\Filter\FilterContainerInterface
   *   A filter container.
   */
  public function getFilterContainer();

}
