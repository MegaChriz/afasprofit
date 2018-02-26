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
   */
  public function setEntityContainer(EntityContainerInterface $entity_container);

  /**
   * Executes update-connector.
   *
   * @param array $arguments
   *   (optional) The request's arguments.
   *
   * @return \Afas\Core\Result\ResultInterface
   *   The result of the call.
   */
  public function execute(array $arguments = array());

}
