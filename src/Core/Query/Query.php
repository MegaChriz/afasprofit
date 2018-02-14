<?php

namespace Afas\Core\Query;

use Afas\Afas;
use Afas\Core\ServerInterface;

/**
 * Base class for queries.
 */
abstract class Query implements QueryInterface {

  /**
   * The server to send data to.
   *
   * @var \Afas\Core\ServerInterface
   */
  protected $server;

  /**
   * Constructs a new Query object.
   *
   * @param \Afas\Core\ServerInterface $server
   *   The server to send data to.
   */
  public function __construct(ServerInterface $server) {
    $this->server = $server;
  }

  /**
   * Generates a soap client.
   *
   * @return \Afas\Component\Soap\SoapClientInterface
   *   A Soap Client.
   */
  protected function getClient() {
    return Afas::service('afas_soap_client_factory')->create($this->server);
  }

}
