<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\Discovery;
use Afas\Core\Entity\EntityFactory;
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
  public function setUp(): void {
    parent::setUp();

    $this->entityManager = new EntityManager();
  }

  /**
   * @covers ::getDiscovery
   */
  public function testGetDiscovery() {
    // First call.
    $discovery = $this->callProtectedMethod($this->entityManager, 'getDiscovery');
    $this->assertInstanceOf(Discovery::class, $discovery);

    // Second call should return same instance.
    $this->assertSame($discovery, $this->callProtectedMethod($this->entityManager, 'getDiscovery'));
  }

  /**
   * @covers ::getFactory
   */
  public function testGetFactory() {
    // First call.
    $factory = $this->callProtectedMethod($this->entityManager, 'getFactory');
    $this->assertInstanceOf(EntityFactory::class, $factory);

    // Second call should return same instance.
    $this->assertSame($factory, $this->callProtectedMethod($this->entityManager, 'getFactory'));
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
