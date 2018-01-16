<?php

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
   * @var \Afas\Core\Filter\FilterContainerInterface
   *   An instance of FilterContainerInterface.
   */
  protected $filterContainer;

  /**
   * The number of records to skip.
   *
   * -1 means no skipping.
   *
   * @var int
   */
  protected $offset = -1;

  /**
   * The number of records to take.
   *
   * -1 means all records taken.
   *
   * @var int
   */
  protected $length = -1;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new Get object.
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
   * {@inheritdoc}
   */
  public function range($offset, $length = -1) {
    $this->offset = $offset;
    $this->length = $length;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $this->filterContainer->filter($field, $value, $operator);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeFilter($index) {
    $this->filterContainer->removeFilter($index);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function group($name = NULL) {
    return $this->filterContainer->group($name);
  }

  /**
   * {@inheritdoc}
   */
  public function removeGroup($group) {
    $this->filterContainer->removeGroup($group);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $connector = new GetConnector($this->client, $this->server);
    $connector->setFilterContainer($this->filterContainer);
    $connector->setRange($this->offset, $this->length);
    return $connector->getData($this->connectorId);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getFilterContainer() {
    return $this->filterContainer;
  }

}
