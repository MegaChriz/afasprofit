<?php

namespace Afas\Core\Query;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\ServerInterface;

/**
 * Delete existing data into Profit.
 */
class Delete extends UpdateBase implements DeleteInterface {

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new Delete object.
   *
   * @param \Afas\Core\ServerInterface $server
   *   The server to send data to.
   * @param string $connector_id
   *   The name of the UpdateConnector.
   * @param array $data
   *   The data to delete.
   * @param array $attribute_keys
   *   (optional) The keys belonging to attributes.
   * @param string $entity_type_id
   *   (optional) The entity type to insert or update.
   */
  public function __construct(ServerInterface $server, $connector_id, array $data, array $attribute_keys = [], string $entity_type_id = '') {
    parent::__construct($server, $connector_id, $data, $attribute_keys, $entity_type_id);
    $this->entityContainer->setAction(EntityInterface::FIELDS_DELETE);
    $this->entityContainer->fromArray($data);
  }

}
