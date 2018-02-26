<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Entity\EntityCreateTrait;
use Afas\Core\Entity\EntityManagerInterface;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Entity\EntityCreateTrait
 * @group AfasCoreEntity
 */
class EntityCreateTraitTest extends TestBase {

  /**
   * The trait under test.
   *
   * @var \Afas\Core\Entity\EntityCreateTrait
   */
  protected $trait;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->trait = $this->getMockForTrait(EntityCreateTrait::class);

    $this->trait->expects($this->any())
      ->method('addObject')
      ->will($this->returnValue($this->trait));
  }

  /**
   * @covers ::add
   */
  public function testAdd() {
    $this->assertInstanceOf(EntityInterface::class, $this->trait->add('DummyEntityType'));
  }

  /**
   * @covers ::getManager
   */
  public function testGetManager() {
    $this->assertInstanceOf(EntityManagerInterface::class, $this->trait->getManager());
  }

}
