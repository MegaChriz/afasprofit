<?php

namespace Afas\Core\Query;

use Afas\Afas;
use Afas\Core\Connector\UpdateConnector;
use Afas\Core\Element\ElementContainer;
use Afas\Core\Entity\EntityInterface;
use Afas\Core\ServerInterface;

/**
 * Send data to Profit.
 */
class Insert extends Query implements InsertInterface {
  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Insert object constructor.
   *
   * @param \Afas\Core\ServerInterface $server
   *   The server to send data to.
   * @param string $connector_id
   *   The name of the GetConnector.
   */
  public function __construct(ServerInterface $server, $connector_id, array $data, $mapper = NULL) {
    $clientFactory = Afas::service('afas_soap_client_factory');
    $this->client = $clientFactory->create($server);
    $this->server = $server;
    $this->connectorId = $connector_id;
    if (!empty($mapper)) {
      $this->mapper = $mapper;
    }
    // @todo ElementContainer doesn't exist yet.
    // @todo rename to entity container?
    $this->elementContainer = new ElementContainer($server, $mapper);
    $this->elementContainer->setAction(EntityInterface::FIELDS_INSERT);
    $this->elementContainer->fromArray($data);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Implements \Afas\Core\Query\QueryInterface::execute().
   */
  public function execute() {
    $connector = new UpdateConnector($this->client, $this->server);
    $connector->setElementContainer($this->elementContainer);
    $connector->sendRequest();
    return $connector->getResult();
  }
}
