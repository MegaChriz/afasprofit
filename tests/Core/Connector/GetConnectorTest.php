<?php

namespace Afas\Tests\Core\Connector;

use Afas\Core\Connector\GetConnector;
use Afas\Core\Filter\FilterContainerInterface;
use Afas\Core\Result\GetConnectorResult;
use PHPUnit_Framework_Assert;

/**
 * @coversDefaultClass \Afas\Core\Connector\GetConnector
 * @group AfasCoreConnector
 */
class GetConnectorTest extends ConnectorTestBase {

  /**
   * @covers ::getData
   */
  public function testGetData() {
    $connector = new GetConnector($this->client, $this->server);
    $result = $connector->getData('alpha');
    $this->assertInstanceOf(GetConnectorResult::class, $result);
  }

  /**
   * @covers ::setFilterContainer
   */
  public function testSetFilterContainer() {
    $filter_container = $this->getMock(FilterContainerInterface::class);
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
   * @covers ::sendRequest
   */
  public function testSendRequest() {
    // First check if a request can be send without a filter container.
    $connector = new GetConnector($this->client, $this->server);
    $connector->sendRequest('GetData', 'dummy');

    // And now with it.
    $filter_container = $this->getMock(FilterContainerInterface::class);
    $connector->setFilterContainer($filter_container);
    $connector->sendRequest('GetData', 'dummy');
  }

}
