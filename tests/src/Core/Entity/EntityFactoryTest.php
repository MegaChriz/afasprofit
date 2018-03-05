<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Entity\Discovery;
use Afas\Core\Entity\EntityFactory;
use Afas\Core\Entity\Plugin\FbSales;
use Afas\Tests\TestBase;

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
  public function setUp() {
    parent::setUp();

    $discovery = new Discovery();
    $this->factory = new EntityFactory($discovery, EntityInterface::class);
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

}
