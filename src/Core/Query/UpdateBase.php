<?php

namespace Afas\Core\Query;

use Afas\Core\Connector\UpdateConnector;
use Afas\Core\Entity\EntityContainer;
use Afas\Core\ServerInterface;

/**
 * Inserting new data into Profit.
 */
class UpdateBase extends Query implements UpdateBaseInterface {

  /**
   * The name of the UpdateConnector.
   *
   * @var string
   */
  protected $connectorId;

  /**
   * An entity container.
   *
   * @var \Afas\Core\Entity\EntityContainerInterface
   */
  protected $entityContainer;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new UpdateBase object.
   *
   * @param \Afas\Core\ServerInterface $server
   *   The server to send data to.
   * @param string $connector_id
   *   The name of the UpdateConnector.
   */
  public function __construct(ServerInterface $server, $connector_id) {
    parent::__construct($server);
    $this->connectorId = $connector_id;
    $this->entityContainer = new EntityContainer($connector_id);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $connector = new UpdateConnector($this->getClient(), $this->server, $this->connectorId);
    $connector->setEntityContainer($this->entityContainer);
    $connector->execute();
    return $connector->getResult();
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getEntityContainer() {
    return $this->entityContainer;
  }

}
