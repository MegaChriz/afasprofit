<?php

/**
 * @file
 * Definition of \Afas\Core\Query\QueryInterface.
 */

namespace Afas\Core\Query;

interface QueryInterface {
  /**
   * Runs the query against the profit.
   *
   * @return \Afas\Core\Result\ResultInterface|null
   *   A prepared statement, or NULL if the query is not valid.
   */
  public function execute();
}
