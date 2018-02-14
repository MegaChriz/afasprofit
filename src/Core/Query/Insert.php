<?php

namespace Afas\Core\Query;

use Afas\Core\ServerInterface;

/**
 * Inserting new data into Profit.
 */
class Insert extends UpdateBase implements InsertInterface {

  /**
   * Constructs a new UpdateBase object.
   *
   * @param \Afas\Core\ServerInterface $server
   *   The server to send data to.
   * @param string $connector_id
   *   The name of the UpdateConnector.
   * @param array $data
   *   The data to insert.
   */
  public function __construct(ServerInterface $server, $connector_id, array $data) {
    parent::__construct($server, $connector_id);
    $this->entityContainer->fromArray($data);
  }

}
