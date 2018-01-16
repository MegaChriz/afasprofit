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
   * Returns the used filter container.
   *
   * @return \Afas\Core\Filter\FilterContainerInterface
   *   A filter container.
   */
  public function getFilterContainer();

}
