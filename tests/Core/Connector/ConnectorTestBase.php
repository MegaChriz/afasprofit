<?php

namespace Afas\Tests\Core\Connector;

use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\ServerInterface;
use Afas\Tests\TestBase;

/**
 * Base class for connector tests.
 */
class ConnectorTestBase extends TestBase {

  /**
   * The soap client.
   *
   * @var \Afas\Component\Soap\SoapClientInterface
   */
  protected $client;

  /**
   * The profit server.
   *
   * @var \Afas\Core\ServerInterface
   */
  protected $server;

  /**
   * Setups required dependencies.
   */
  public function setUp() {
    parent::setUp();
    $this->client = $this->getMock(SoapClientInterface::class);
    $this->server = $this->getMock(ServerInterface::class);
  }

}
