<?php

namespace Afas\Tests\Core\Query;

use Afas\Core\Entity\EntityContainerInterface;
use Afas\Core\Query\Update;
use Afas\Core\Result\UpdateConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Query\Update
 * @group AfasCoreQuery
 */
class UpdateTest extends QueryTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->query = $this->getMock(Update::class, ['getClient'], [
      $this->server,
      'Dummy',
      [],
    ]);

    $this->query->expects($this->any())
      ->method('getClient')
      ->will($this->returnValue($this->client));
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
    $result = $this->query->execute();
    $this->assertInstanceOf(UpdateConnectorResult::class, $result);
  }

  /**
   * @covers ::getEntityContainer
   */
  public function testGetEntityContainer() {
    $this->assertInstanceOf(EntityContainerInterface::class, $this->query->getEntityContainer());
  }

}
