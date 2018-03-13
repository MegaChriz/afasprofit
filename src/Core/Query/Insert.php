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
   * @param array $attribute_keys
   *   (optional) The keys belonging to attributes.
   */
  public function __construct(ServerInterface $server, $connector_id, array $data, array $attribute_keys = []) {
    parent::__construct($server, $connector_id, $data, $attribute_keys);
    $this->entityContainer->fromArray($data);
  }

}
