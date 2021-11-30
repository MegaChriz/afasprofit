<?php

namespace Afas\Tests\Core\Connector;

use Afas\Core\Connector\UpdateConnector;
use Afas\Core\Entity\EntityContainer;
use Afas\Core\Entity\EntityContainerInterface;
use Afas\Core\Result\UpdateConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Connector\UpdateConnector
 * @group AfasCoreConnector
 */
class UpdateConnectorTest extends ConnectorTestBase {

  /**
   * @covers ::__construct
   * @covers ::getEntityContainer
   */
  public function testConstructWithoutEntityContainer() {
    // Ensure that there is always an entity container.
    $connector = new UpdateConnector($this->client, $this->server, 'FbSales');
    $this->assertInstanceOf(EntityContainerInterface::class, $connector->getEntityContainer());
  }

  /**
   * @covers ::__construct
   * @covers ::setEntityContainer
   * @covers ::getEntityContainer
   */
  public function testConstructWithEntityContainer() {
    // Pass entity container upon construction.
    $entity_container = $this->createMock(EntityContainerInterface::class);
    $connector = new UpdateConnector($this->client, $this->server, 'FbSales', $entity_container);
    $this->assertSame($entity_container, $connector->getEntityContainer());

    // New entity container.
    $entity_container2 = new EntityContainer('FbSales');
    $connector->setEntityContainer($entity_container2);
    $this->assertNotEquals($entity_container, $connector->getEntityContainer());
    $this->assertSame($entity_container2, $connector->getEntityContainer());
  }

  /**
   * @covers ::setEntityContainer
   * @covers ::getEntityContainer
   * @covers ::__construct
   */
  public function testSetEntityContainer() {
    $entity_container = $this->createMock(EntityContainerInterface::class);
    $connector = new UpdateConnector($this->client, $this->server, 'FbSales');
    $connector->setEntityContainer($entity_container);
    $this->assertSame($entity_container, $connector->getEntityContainer());
  }

  /**
   * @covers ::execute
   * @covers ::getResult
   * @covers ::__construct
   */
  public function testExecute() {
    $this->client->expects($this->once())
      ->method('__getLastResponse')
      ->will($this->returnValue($this->getFileContents('UpdateConnector/ExecuteResponse_empty.xml')));

    $connector = new UpdateConnector($this->client, $this->server, 'FbSales');
    $result = $connector->execute();
    $this->assertInstanceOf(UpdateConnectorResult::class, $result);

    $expected = [];
    $this->assertEquals($expected, $result->asArray());
  }

  /**
   * @covers ::getLocation
   * @covers ::__construct
   */
  public function testGetLocation() {
    $this->server->expects($this->once())
      ->method('getBaseUrl')
      ->will($this->returnValue('https://www.example.com'));

    $connector = new UpdateConnector($this->client, $this->server, 'FbSales');
    $this->assertEquals('https://www.example.com/appconnectorupdate.asmx', $connector->getLocation());
  }

}
