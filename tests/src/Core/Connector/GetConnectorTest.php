<?php

namespace Afas\Tests\Core\Connector;

use Afas\Core\Connector\GetConnector;
use Afas\Core\Filter\FilterContainerInterface;
use Afas\Core\Result\GetConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Connector\GetConnector
 * @group AfasCoreConnector
 */
class GetConnectorTest extends ConnectorTestBase {

  /**
   * @covers ::getData
   * @covers ::getResult
   */
  public function testGetData() {
    $connector = new GetConnector($this->client, $this->server);
    $result = $connector->getData('alpha');
    $this->assertInstanceOf(GetConnectorResult::class, $result);
  }

  /**
   * @covers ::setFilterContainer
   * @covers ::getFilterContainer
   * @covers ::getSoapArguments
   */
  public function testSetFilterContainer() {
    $filter_container = $this->createMock(FilterContainerInterface::class);
    $connector = new GetConnector($this->client, $this->server);
    $connector->setFilterContainer($filter_container);
    $this->assertSame($filter_container, $connector->getFilterContainer());

    $filter_container->expects($this->once())
      ->method('compile')
      ->will($this->returnValue('<Filters/>'));
    $expected = [
      'skip' => -1,
      'take' => -1,
      'token' => NULL,
      'filtersXml' => '<Filters/>',
    ];
    $this->assertEquals($expected, $this->callProtectedMethod($connector, 'getSoapArguments'));
  }

  /**
   * @covers ::setRange
   * @covers ::getSoapArguments
   */
  public function testSetRange() {
    $connector = new GetConnector($this->client, $this->server);

    // Defaults.
    $expected = [
      'skip' => -1,
      'take' => -1,
      'token' => NULL,
    ];
    $this->assertEquals($expected, $this->callProtectedMethod($connector, 'getSoapArguments'));

    // Skip the first 10 records.
    $connector->setRange(10);
    $expected = [
      'skip' => 10,
      'take' => -1,
      'token' => NULL,
    ];
    $this->assertEquals($expected, $this->callProtectedMethod($connector, 'getSoapArguments'));

    // Take 10 records.
    $connector->setRange(0, 10);
    $expected = [
      'skip' => 0,
      'take' => 10,
      'token' => NULL,
    ];
    $this->assertEquals($expected, $this->callProtectedMethod($connector, 'getSoapArguments'));
  }

  /**
   * @covers ::setOrder
   * @covers ::compileOptions
   */
  public function testSetOrder() {
    $connector = new GetConnector($this->client, $this->server);
    $connector->setOrder([
      'field' => 'ASC',
    ]);

    $expected = '<options>
			<Index>
			  <Field FieldId="field" OperatorType="1"/>
			</Index>
    </options>';
    $this->assertXmlStringEqualsXmlString($expected, $this->callProtectedMethod($connector, 'compileOptions'));
  }

  /**
   * @covers ::setOrder
   * @covers ::compileOptions
   */
  public function testSetOrderWithMultipleFields() {
    $connector = new GetConnector($this->client, $this->server);
    $connector->setOrder([
      'field1' => 'DESC',
      'field2' => 'ASC',
      'field3' => 'DESC',
    ]);

    $expected = '<options>
			<Index>
			  <Field FieldId="field1" OperatorType="0"/>
			  <Field FieldId="field2" OperatorType="1"/>
			  <Field FieldId="field3" OperatorType="0"/>
			</Index>
    </options>';
    $this->assertXmlStringEqualsXmlString($expected, $this->callProtectedMethod($connector, 'compileOptions'));
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
    $this->client->expects($this->once())
      ->method('__soapCall');

    $connector = new GetConnector($this->client, $this->server);
    $connector->sendRequest('GetData', 'dummy');
  }

  /**
   * @covers ::sendRequest
   */
  public function testSendRequestWithFilterContainer() {
    $this->client->expects($this->once())
      ->method('__soapCall');

    $connector = new GetConnector($this->client, $this->server);
    $filter_container = $this->createMock(FilterContainerInterface::class);
    $connector->setFilterContainer($filter_container);
    $connector->sendRequest('GetData', 'dummy');
  }

}
