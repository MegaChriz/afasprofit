<?php

namespace Afas\Core\Query;

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
   * The name of the GetConnector.
   *
   * @var string
   */
  protected $connectorId;

  /**
   * A filter container.
   *
   * @var \Afas\Core\Filter\FilterContainerInterface
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

  /**
   * The fields by which to order this query.
   *
   * This is an associative array. The keys are the fields to order, and the
   * value is the direction to order, either ASC or DESC.
   *
   * @var array
   */
  protected $order = [];

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
    parent::__construct($server);
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
  public function orderBy($field, $direction = 'ASC') {
    $direction = strtoupper($direction) == 'DESC' ? 'DESC' : 'ASC';
    $this->order[$field] = $direction;
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
    $connector = new GetConnector($this->getClient(), $this->server);
    $connector->setFilterContainer($this->filterContainer);
    $connector->setRange($this->offset, $this->length);
    $connector->setOrder($this->order);
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

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return $this->getFilterContainer()->getFilters();
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return $this->getFilterContainer()->getGroups();
  }

}
