<?php

namespace Afas\Tests\Core\Mapping;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Mapping\EntityMappingInterface;
use Afas\Core\Mapping\EntityMappingFactory;
use Afas\Tests\TestBase;
use Afas\Tests\resources\DummyClass;
use Afas\Tests\resources\Mapping\DummyMapping;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Afas\Core\Mapping\EntityMappingFactory
 * @group AfasCoreMapping
 */
class EntityMappingFactoryTest extends TestBase {

  /**
   * @covers ::setClass
   * @covers ::__construct
   */
  public function testSetClass() {
    $factory = new EntityMappingFactory();
    $this->assertEquals($factory, $factory->setClass(DummyMapping::class));
  }

  /**
   * @covers ::setClass
   */
  public function testSetClassException() {
    $factory = new EntityMappingFactory();
    $this->setExpectedException(InvalidArgumentException::class, 'The given class Afas\\Tests\\resources\\DummyClass does not implement Afas\\Core\\Mapping\\EntityMappingInterface.');
    $this->assertEquals($factory, $factory->setClass(DummyClass::class));
  }

  /**
   * @covers ::createForEntity
   */
  public function testCreateForEntity() {
    $factory = new EntityMappingFactory();
    $entity = $this->getMock(EntityInterface::class);
    $this->assertInstanceOf(EntityMappingInterface::class, $factory->createForEntity($entity));
  }

}
