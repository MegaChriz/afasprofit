<?php

namespace Afas\Tests\Core\Query;

use Afas\Core\Entity\EntityContainerInterface;
use Afas\Core\Query\Insert;
use Afas\Core\Result\ResultInterface;

/**
 * @coversDefaultClass \Afas\Core\Query\Insert
 * @group AfasCoreQuery
 */
class InsertTest extends QueryTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->query = $this->getMock(Insert::class, ['getClient'], [
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
    $this->assertInstanceOf(ResultInterface::class, $result);
  }

  /**
   * @covers ::getEntityContainer
   */
  public function testGetEntityContainer() {
    $this->assertInstanceOf(EntityContainerInterface::class, $this->query->getEntityContainer());
  }

}
