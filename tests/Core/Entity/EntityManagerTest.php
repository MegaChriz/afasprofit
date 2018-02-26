<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Entity\EntityManager;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Entity\EntityManager
 * @group AfasCoreEntity
 */
class EntityManagerTest extends TestBase {

  /**
   * The class under test.
   *
   * @var \Afas\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->entityManager = new EntityManager();
  }

  /**
   * @covers ::createInstance
   */
  public function testCreateInstance() {
    $this->assertInstanceOf(EntityInterface::class, $this->entityManager->createInstance('DummyEntityType'));
    $this->assertInstanceOf(EntityInterface::class, $this->entityManager->createInstance('FbSales'));
  }

  /**
   * @covers ::getFallbackPluginId
   */
  public function testGetFallbackPluginId() {
    $this->assertEquals('Entity', $this->entityManager->getFallbackPluginId('DummyEntityType'));
  }

}
