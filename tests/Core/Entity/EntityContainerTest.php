<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Entity\EntityContainer;
use Afas\Core\Entity\EntityManagerInterface;
use Afas\Tests\TestBase;
use DOMDocument;
use InvalidArgumentException;
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

    $this->entity = $this->getMock(EntityInterface::class);
    $this->entity->expects($this->any())
      ->method('toXml')
      ->will($this->returnCallback([get_class($this), 'callbackEntityInterface__toXml']));

    $manager = $this->getMock(EntityManagerInterface::class);
    $manager->expects($this->any())
      ->method('createInstance')
      ->will($this->returnValue($this->entity));

    $this->container = new EntityContainer('DummyType', $manager);
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
   */
  public function testAddItem() {
    $this->setExpectedException(InvalidArgumentException::class);
    $method = new ReflectionMethod(EntityContainer::class, 'addItem');
    $method->setAccessible(TRUE);
    $method->invoke($this->container, 'Foo');
  }

  /**
   * @covers ::setAction
   * @covers ::getAction
   */
  public function testSetAction() {
    $this->assertSame($this->container, $this->container->setAction(EntityInterface::FIELDS_INSERT));
    $this->assertSame(EntityInterface::FIELDS_INSERT, $this->container->getAction());
    $this->assertSame($this->container, $this->container->setAction(EntityInterface::FIELDS_UPDATE));
    $this->assertSame(EntityInterface::FIELDS_UPDATE, $this->container->getAction());
    $this->assertSame($this->container, $this->container->setAction(EntityInterface::FIELDS_DELETE));
    $this->assertSame(EntityInterface::FIELDS_DELETE, $this->container->getAction());
  }

  /**
   * @covers ::setAction
   */
  public function testSetFailedAction() {
    $this->setExpectedException(InvalidArgumentException::class);
    $this->container->setAction('invalid');
  }

  /**
   * @covers ::fromArray
   * @covers ::compile
   * @dataProvider fromArrayDataProvider
   */
  public function testFromArray($expected, array $data) {
    $container = new EntityContainer('DummyType2');
    $container->fromArray([
      'Ab' => 'Foo',
      'Cd' => 'Bar',
    ]);
    $expected = '<DummyType2 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Element><Fields Action="insert"><Ab>Foo</Ab><Cd>Bar</Cd></Fields></Element></DummyType2>';
    $this->assertXmlStringEqualsXmlString($expected, $container->compile());
  }

  /**
   * Data provider for ::testFromArray.
   */
  public function fromArrayDataProvider() {
    return [
      [
        'expected' => '<DummyType2 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Element><Fields Action="insert"><Ab>Foo</Ab><Cd>Bar</Cd></Fields></Element></DummyType2>',
        'data' => [
          'Ab' => 'Foo',
          'Cd' => 'Bar',
        ],
      ],
      [
        'expected' => '<DummyType2 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Element><Fields Action="insert"><Ab>Foo</Ab><Cd>Bar</Cd></Fields></Element><Element><Fields Action="insert"><Ab>Baz</Ab></Fields></Element></DummyType2>',
        'data' => [
          [
            'Ab' => 'Foo',
            'Cd' => 'Bar',
          ],
          [
            'Ab' => 'Baz',
          ],
        ],
      ],
    ];
  }

  /**
   * @covers ::setManager
   * @covers ::__construct
   */
  public function testSetManager() {
    $manager = $this->getMock(EntityManagerInterface::class);
    $container = new EntityContainer('DummyType2');
    $container->setManager($manager);
    $this->assertEquals($manager, PHPUnit_Framework_Assert::readAttribute($container, 'manager'));
  }

  /**
   * @covers ::getObjects
   */
  public function testGetObjects() {
    // Create two objects.
    $objects = [
      $this->getMock(EntityInterface::class),
      $this->getMock(EntityInterface::class),
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
