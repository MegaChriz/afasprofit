<?php

/**
 * @file
 * Contains \Afas\Core\Connector\GetConnectorTest.
 */

namespace Afas\Core\Connector;

/**
 * @coversDefaultClass \Afas\Core\Connector\GetConnector
 */
class GetConnectorTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var \Afas\Component\Soap\SoapClientInterface
   */
  private $client;

  /**
   * @var \Afas\Core\ServerInterface
   */
  private $server;

  /**
   * Setups required dependencies for GetConnector class.
   */
  public function setUp() {
    parent::setUp();
    $this->client = $this->getMock('Afas\Component\Soap\SoapClientInterface');
    $this->server = $this->getMock('Afas\Core\ServerInterface');
  }

  /**
   * @covers ::getData
   */
  public function testGetData() {
    $connector = new GetConnector($this->client, $this->server);
    $connector->getData('alpha');
  }
}
