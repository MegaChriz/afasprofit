<?php

namespace Afas\Core\Connector;

use Afas\Core\Filter\FilterContainerInterface;

/**
 * Interface for the Profit GetConnector.
 */
interface GetConnectorInterface extends ConnectorInterface {

  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  /**
   * Operator type for descending ordering.
   *
   * @var integer
   */
  const DESC = 0;

  /**
   * Operator type for ascending ordering.
   *
   * @var integer
   */
  const ASC = 1;

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Gets records from a Profit GetConnector.
   *
   * @param string $connector_id
   *   The name of the GetConnector.
   * @param array $options
   *   (optional) Options for getting the data.
   *
   * @return \Afas\Core\Result\ResultInterface
   *   The result of the call.
   */
  public function getData($connector_id, array $options = []);

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets a filter container.
   *
   * @param \Afas\Core\Filter\FilterContainerInterface $filter_container
   *   A container containing filters.
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
   */
  public function setRange($skip, $take = -1);

  /**
   * Sets the list of fields to order on.
   *
   * @param array $order
   *   An associative array whose keys are the fields to order, and the values
   *   are the direction to order. This may be:
   *   - 'ASC' or 1 for ascending;
   *   - 'DESC' or 0 for descending.
   */
  public function setOrder(array $order);

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns the filter container that is set.
   *
   * @return \Afas\Core\Filter\FilterContainerInterface
   *   The filter container that is set on this get connector.
   */
  public function getFilterContainer(): FilterContainerInterface;

}
