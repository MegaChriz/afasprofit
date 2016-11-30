<?php

/**
 * @file
 * Contains \Afas\Core\Connector\GetConnectorTest.
 */

namespace Afas\Core\Connector;

use \PHPUnit_Framework_Assert;

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
    $result = $connector->getData('alpha');
    $this->assertInstanceOf('Afas\Core\Result\Result', $result);
  }

  /**
   * @covers ::setFilterContainer
   */
  public function testSetFilterContainer() {
    $filter_container = $this->getMock('Afas\Core\Filter\FilterContainerInterface');
    $connector = new GetConnector($this->client, $this->server);
    $connector->setFilterContainer($filter_container);
    $this->assertEquals($filter_container, PHPUnit_Framework_Assert::readAttribute($connector, 'filterContainer'));
  }

  /**
   * @covers ::getLocation
   */
  public function testGetLocation() {
    $this->server->expects($this->once())
      ->method('getBaseUrl')
      ->will($this->returnValue('https://www.example.com'));

    $connector = new GetConnector($this->client, $this->server);
    $this->assertEquals('https://www.example.com/appconnectorget.asmx', $connector->getLocation());
  }

  /**
   * @cover ::sendRequest
   */
  public function testSendRequest() {
    // First check if a request can be send without a filter container.
    $connector = new GetConnector($this->client, $this->server);
    $connector->sendRequest('GetData', 'dummy');

    // And now with it.
    $filter_container = $this->getMock('Afas\Core\Filter\FilterContainerInterface');
    $connector->setFilterContainer($filter_container);
    $connector->sendRequest('GetData', 'dummy');
  }
}
