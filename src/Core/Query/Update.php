<?php

namespace Afas\Core\Query;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\ServerInterface;

/**
 * Updating existing data into Profit.
 */
class Update extends UpdateBase implements UpdateInterface {

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new Update object.
   *
   * @param \Afas\Core\ServerInterface $server
   *   The server to send data to.
   * @param string $connector_id
   *   The name of the UpdateConnector.
   * @param array $data
   *   The data to update.
   * @param array $attribute_keys
   *   (optional) The keys belonging to attributes.
   * @param array $entity_type_id
   *   (optional) The entity to insert.
   */
  public function __construct(ServerInterface $server, $connector_id, array $data, array $attribute_keys = [], string $entity_type_id = '') {
    parent::__construct($server, $connector_id, $data, $attribute_keys, $entity_type_id);
    $this->entityContainer->setAction(EntityInterface::FIELDS_UPDATE);
    $this->entityContainer->fromArray($data);
  }

}
