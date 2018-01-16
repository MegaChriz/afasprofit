<?php

namespace Afas\Core\Connector;

use Afas\Core\Entity\EntityContainerInterface;

/**
 * Interface for the Profit UpdateConnector.
 */
interface UpdateConnectorInterface extends ConnectorInterface {

  /**
   * Sets an entity container.
   *
   * @param \Afas\Core\Entity\EntityContainerInterface $entity_container
   *   A container containing items to send to Profit.
   *
   * @return void
   */
  public function setEntityContainer(EntityContainerInterface $entity_container);

}
