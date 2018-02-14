<?php

namespace Afas\Core\Query;

/**
 * Interface for queries.
 */
interface QueryInterface {

  /**
   * Runs the query against Profit.
   *
   * @return \Afas\Core\Result\ResultInterface|null
   *   A result from a Profit connector call, or NULL if the query is not valid.
   */
  public function execute();

}
