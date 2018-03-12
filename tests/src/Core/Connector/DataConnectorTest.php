<?php

namespace Afas\Tests\Core\Connector;

use Afas\Core\Connector\DataConnector;
use Afas\Core\Result\DataConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Connector\DataConnector
 * @group AfasCoreConnector
 */
class DataConnectorTest extends ConnectorTestBase {

  /**
   * @covers ::getXmlSchema
   * @covers ::getResult
   */
  public function testGetXmlSchema() {
    $this->client->expects($this->once())
      ->method('__getLastResponse')
      ->will($this->returnValue($this->getFileContents('DataConnector/ExecuteResponse.xml')));

    $connector = new DataConnector($this->client, $this->server);
    $result = $connector->getXmlSchema('KnCourseMember');
    $this->assertInstanceOf(DataConnectorResult::class, $result);
  }

  /**
   * @covers ::getLocation
   */
  public function testGetLocation() {
    $this->server->expects($this->once())
      ->method('getBaseUrl')
      ->will($this->returnValue('https://www.example.com'));

    $connector = new DataConnector($this->client, $this->server);
    $this->assertEquals('https://www.example.com/appconnectordata.asmx', $connector->getLocation());
  }

}
