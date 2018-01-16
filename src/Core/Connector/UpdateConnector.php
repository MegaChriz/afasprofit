<?php

namespace Afas\Core\Connector;

use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\Entity\EntityContainer;
use Afas\Core\Entity\EntityContainerInterface;
use Afas\Core\ServerInterface;

/**
 * Class for the Profit UpdateConnector.
 */
class UpdateConnector extends Connector implements UpdateConnectorInterface {

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
   * Constructs a new UpdateConnector object.
   *
   * @param \Afas\Component\Soap\SoapClientInterface $client
   *   A Soap Client.
   * @param \Afas\Core\ServerInterface $server
   *   An Afas server.
   * @param string $connector_type
   *   The update connector to use.
   * @param \Afas\Core\Entity\EntityContainerInterface $entity_container
   *   (optional) A container containing items to send to Profit.
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
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getLocation() {
    return $this->getServer()->getBaseUrl() . '/appconnectorupdate.asmx';
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function setEntityContainer(EntityContainerInterface $entity_container) {
    $this->entityContainer = $entity_container;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
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
