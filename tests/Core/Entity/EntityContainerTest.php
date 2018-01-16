<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityContainer;
use Afas\Tests\TestBase;
use DOMDocument;
use PHPUnit_Framework_Assert;
use ReflectionMethod;

/**
 * @coversDefaultClass \Afas\Core\Entity\EntityContainer
 * @group AfasCoreEntity
 */
class EntityContainerTest extends TestBase {

  /**
   * The entity container under test.
   *
   * @var \Afas\Core\Entity\EntityContainer
   */
  private $container;

  /**
   * The entity.
   *
   * @var \Afas\Core\Entity\EntityInterface
   */
  private $entity;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->entity = $this->getMock('Afas\Core\Entity\EntityInterface');
    $this->entity->expects($this->any())
      ->method('toXML')
      ->will($this->returnCallback([get_class($this), 'callbackEntityInterface__toXml']));

    $factory = $this->getMock('Afas\Core\Entity\EntityFactoryInterface');
    $factory->expects($this->any())
      ->method('createEntity')
      ->will($this->returnValue($this->entity));

    $this->container = new EntityContainer('DummyType', $factory);
  }

  /**
   * Callback function for \Afas\Core\Entity\EntityInterface::toXml().
   *
   * @param \DOMDocument $doc
   *   An instance of DOMDocument.
   *
   * @return \DOMNode
   *   An instance of DOMNode.
   */
  public static function callbackEntityInterface__toXml(DOMDocument $doc) {
    return $doc->createElement('Dummy');
  }

  /**
   * @covers ::add
   */
  public function testAdd() {
    $this->assertSame($this->entity, $this->container->add('Dummy'));
  }

  /**
   * @covers ::addObject
   * @covers ::addItem
   */
  public function testAddObject() {
    $this->assertSame($this->container, $this->container->addObject($this->entity));
  }

  /**
   * Ensure only EntityInterface objects can be added to the entity container.
   *
   * @covers ::addItem
   * @expectedException InvalidArgumentException
   */
  public function testAddItem() {
    $method = new ReflectionMethod('Afas\Core\Entity\EntityContainer', 'addItem');
    $method->setAccessible(TRUE);
    $method->invoke($this->container, 'Foo');
  }

  /**
   * @covers ::setFactory
   * @covers ::__construct
   */
  public function testSetFactory() {
    $factory = $this->getMock('Afas\Core\Entity\EntityFactoryInterface');
    $container = new EntityContainer('DummyType2');
    $container->setFactory($factory);
    $this->assertEquals($factory, PHPUnit_Framework_Assert::readAttribute($container, 'factory'));
  }

  /**
   * @covers ::getObjects
   */
  public function testGetObjects() {
    // Create two objects.
    $objects = [
      $this->getMock('Afas\Core\Entity\EntityInterface'),
      $this->getMock('Afas\Core\Entity\EntityInterface'),
    ];

    foreach ($objects as $object) {
      $this->container->addObject($object);
    }

    $this->assertEquals($objects, $this->container->getObjects());
  }

  /**
   * @covers ::compile
   */
  public function testCompileWithoutEntities() {
    $this->assertNull($this->container->compile());
  }

  /**
   * @covers ::compile
   */
  public function testCompileWithEntities() {
    $this->container->add('Dummy');
    $expected = '<DummyType xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Dummy /></DummyType>';
    $this->assertXmlStringEqualsXmlString($expected, $this->container->compile());
  }

  /**
   * @covers ::__toString
   * @covers ::__construct
   */
  public function testToString() {
    $this->container->add('Dummy');
    $expected = '<DummyType xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Dummy /></DummyType>';
    $this->assertXmlStringEqualsXmlString($expected, (string) $this->container);

    // Also test if an empty string is returned when container is empty.
    $container2 = new EntityContainer('DummyType2');
    $this->assertEquals('', (string) $container2);
  }

}
