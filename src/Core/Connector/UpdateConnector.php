<?php

namespace Afas\Core\Connector;

use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\Entity\EntityContainer;
use Afas\Core\Entity\EntityContainerInterface;
use Afas\Core\ServerInterface;
use Afas\Core\Result\UpdateConnectorResult;

/**
 * Class for the Profit UpdateConnector.
 */
class UpdateConnector extends ConnectorBase implements UpdateConnectorInterface {

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
    if (isset($entity_container)) {
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
  public function getResult() {
    list($result_xml, $last_function) = $this->getResultArguments();
    return new UpdateConnectorResult($result_xml, $last_function);
  }

  /**
   * {@inheritdoc}
   */
  public function getLocation() {
    return $this->getServer()->getBaseUrl() . '/appconnectorupdate.asmx';
  }

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

  /**
   * {@inheritdoc}
   */
  public function getEntityContainer(): EntityContainerInterface {
    return $this->entityContainer;
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
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function execute(array $arguments = []) {
    $arguments += [
      'connectorType' => $this->connectorType,
      'connectorVersion' => 1,
    ];
    $this->soapSendRequest('Execute', $arguments);
    return $this->getResult();
  }

}
