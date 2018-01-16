<?php

namespace Afas\Core\Connector;

use Afas\Core\Filter\FilterContainerInterface;

/**
 * Interface for the Profit GetConnector.
 */
interface GetConnectorInterface extends ConnectorInterface {

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Gets records from a Profit GetConnector.
   *
   * @param string $connector_id
   *   The name of the GetConnector.
   * @param array $options
   *   Options for getting the data.
   *
   * @return \Afas\Core\Result\Result
   *   The result of the call.
   */
  public function getData($connector_id, array $options = array());

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets a filter container.
   *
   * @param \Afas\Core\Filter\FilterContainerInterface $filter_container
   *   A container containing filters.
   *
   * @return void
   */
  public function setFilterContainer(FilterContainerInterface $filter_container);

  /**
   * Sets a range to use.
   *
   * @param int $skip
   *   The number of records to skip.
   * @param int $take
   *   (optional) The number of records to take.
   *   Defaults to take all records.
   *
   * @return void
   */
  public function setRange($skip, $take = -1);

}
