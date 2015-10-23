<?php

/**
 * @file
 * Definition of \Afas\Core\Query\Get.
 */

namespace Afas\Core\Query;

use Afas\Afas;
use Afas\Core\Connector\GetConnector;
use Afas\Core\Filter\FilterContainer;
use Afas\Core\ServerInterface;

/**
 * Get data from Profit.
 */
class Get extends Query implements GetInterface {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * A filter container.
   *
   * @var FilterContainerInterface
   *   An instance of FilterContainerInterface.
   */
  protected $filterContainer;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Get object constructor.
   *
   * @param \Afas\Core\ServerInterface $server
   *   The server to send data to.
   * @param string $connector_id
   *   The name of the GetConnector.
   */
  public function __construct(ServerInterface $server, $connector_id) {
    $clientFactory = Afas::service('afas_soap_client_factory');
    $this->client = $clientFactory->create($server);
    $this->server = $server;
    $this->connectorId = $connector_id;
    $this->filterContainer = new FilterContainer();
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Implements Afas\Core\Query\GetInterface::filter().
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $this->filterContainer->filter($field, $value, $operator);
    return $this;
  }

  /**
   * Implements Afas\Core\Query\GetInterface::group().
   */
  public function group($name = NULL) {
    return $this->filterContainer->group($name);
  }

  /**
   * Implements \Afas\Core|Query\QueryInterface::execute().
   */
  public function execute() {
    $connector = new GetConnector($this->client, $this->server);
    $connector->setFilterContainer($this->filterContainer);
    $connector->sendRequest('GetData', $this->connectorId);
    return $connector->getResult();
  }
}
