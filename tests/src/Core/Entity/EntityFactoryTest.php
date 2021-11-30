<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Entity\Discovery;
use Afas\Core\Entity\EntityFactory;
use Afas\Core\Entity\EntityWithMappingInterface;
use Afas\Core\Entity\Plugin\FbSales;
use Afas\Tests\resources\Entity\EntityFactoryMock;
use Afas\Tests\resources\Entity\DummyEntity;
use Afas\Tests\TestBase;
use LogicException;

/**
 * @coversDefaultClass \Afas\Core\Entity\EntityFactory
 * @group AfasCoreEntity
 */
class EntityFactoryTest extends TestBase {

  /**
   * The class under test.
   *
   * @var \Afas\Core\Entity\EntityFactory
   */
  protected $factory;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->factory = new EntityFactory(new Discovery(), EntityInterface::class);
  }

  /**
   * @covers ::createInstance
   */
  public function testCreateInstance() {
    $this->assertInstanceOf(EntityInterface::class, $this->factory->createInstance('Entity', [
      'values' => [],
      'entity_type' => 'DummyEntityType',
    ]));
  }

  /**
   * @covers ::createInstance
   */
  public function testCreateInstanceWithFbSalesPlugin() {
    $this->assertInstanceOf(FbSales::class, $this->factory->createInstance('FbSales', [
      'values' => [],
      'entity_type' => 'FbSales',
    ]));
  }

  /**
   * @covers ::createInstance
   */
  public function testCreateInstanceWithoutMapping() {
    $factory = new EntityFactoryMock(new Discovery(), EntityInterface::class);

    $entity = $factory->createInstance('Entity', [
      'values' => [],
      'entity_type' => 'DummyEntityType',
    ]);

    $this->assertInstanceOf(DummyEntity::class, $entity);
    $this->assertNotInstanceOf(EntityWithMappingInterface::class, $entity);
  }

  /**
   * Ensures that in the previous test DummyEntity::setMapper() is not called.
   *
   * @covers ::createInstance
   */
  public function testCreateInstanceWithoutMappingException() {
    $factory = new EntityFactoryMock(new Discovery(), EntityInterface::class);

    $entity = $factory->createInstance('Entity', [
      'values' => [],
      'entity_type' => 'DummyEntityType',
    ]);

    $this->expectException(LogicException::class);
    $entity->setMapper();
  }

}
