<?php

namespace Afas\Core\Query;

/**
 * Interface for queries that send that data to Profit.
 */
interface UpdateBaseInterface extends QueryInterface {

  /**
   * Returns the entity container.
   *
   * @return \Afas\Core\Entity\EntityContainerInterface
   *   An entity container.
   */
  public function getEntityContainer();

}
