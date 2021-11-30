<?php

namespace Afas\Tests\Core\Connector;

use Afas\Core\Connector\ConnectorBase;

/**
 * @coversDefaultClass \Afas\Core\Connector\ConnectorBase
 * @group AfasCoreConnector
 */
class ConnectorBaseTest extends ConnectorTestBase {

  /**
   * The connector under test.
   *
   * @var \Afas\Core\Connector\ConnectorBase
   */
  private $connector;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->connector = $this->getMockBuilder(ConnectorBase::class)
      ->setConstructorArgs([
        $this->client,
        $this->server,
      ])
      ->getMockForAbstractClass();
  }

  /**
   * @covers ::getServer
   */
  public function testGetServer() {
    $this->assertEquals($this->server, $this->connector->getServer());
  }

}
