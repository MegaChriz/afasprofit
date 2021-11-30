<?php

namespace Afas\Tests\Core\Connector;

use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\ServerInterface;
use Afas\Tests\TestBase;

/**
 * Base class for connector tests.
 */
abstract class ConnectorTestBase extends TestBase {

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
  public function setUp(): void {
    parent::setUp();
    $this->client = $this->createMock(SoapClientInterface::class);
    $this->server = $this->createMock(ServerInterface::class);
  }

}
