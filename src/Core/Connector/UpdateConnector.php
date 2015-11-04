<?php

/**
 * @file
 * Contains \Afas\Core\Connector\UpdateConnector.
 */

namespace Afas\Core\Connector;

use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\Entity\EntityContainer;
use Afas\Core\Entity\EntityContainerInterface;
use Afas\Core\ServerInterface;

/**
 * Class UpdateConnector.
 * @package Afas\Core\Connector
 */
class UpdateConnector extends Connector {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The update connector to use.
   *
   * @var string
   */
  protected $connectorType;

  /**
   * An entity container.
   *
   * @var \Afas\Core\Entity\EntityContainerInterface
   *   An instance of EntityContainerInterface.
   */
  protected $entityContainer;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructor.
   *
   * @param \Afas\Component\Soap\SoapClientInterface $client
   *   A Soap Client.
   * @param \Afas\Core\ServerInterface $server
   *   An Afas server.
   * @param string $connector_type
   *   The update connector to use.
   * @param \Afas\Core\Entity\EntityContainerInterface $entity_container
   *   (optional) A container containing items to send to Profit.
   *
   * @return \Afas\Core\Connector\ConnectorBase
   */
  public function __construct(SoapClientInterface $client, ServerInterface $server, $connector_type, EntityContainerInterface $entity_container = NULL) {
    parent::__construct($client, $server);
    $this->connectorType = $connector_type;
    if (!isset($entity_container)) {
      $this->entityContainer = $entity_container;
    }
    else {
      $this->entityContainer = new EntityContainer($connector_type);
    }
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets a filter container.
   *
   * @param \Afas\Core\Entity\EntityContainerInterface $entity_container
   *   A container containing items to send to Profit.
   *
   * @return void
   */
  public function setEntityContainer(EntityContainerInterface $entity_container) {
    $this->entityContainer = $entity_container;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Overrides Connector::getSoapArguments().
   */
  protected function getSoapArguments() {
    $arguments = parent::getSoapArguments();
    $data = $this->entityContainer->compile();
    if (!empty($data)) {
      $arguments['dataXml'] = $data;
    }
    return $arguments;
  }
}
