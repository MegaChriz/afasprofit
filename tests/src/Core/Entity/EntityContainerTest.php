<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Entity\EntityContainer;
use Afas\Core\Entity\EntityManagerInterface;
use Afas\Core\Exception\EntityValidationException;
use Afas\Tests\TestBase;
use DOMDocument;
use Exception;
use InvalidArgumentException;
use PHPUnit_Framework_Assert;
use ReflectionMethod;

/**
 * @coversDefaultClass \Afas\Core\Entity\EntityContainer
 * @group AfasCoreEntity
 */
class EntityContainerTest extends TestBase {

  use EntityCreateTrait;

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

    // Create a mocked entity.
    $this->entity = $this->getMockedEntity();
    $this->entity->expects($this->any())
      ->method('toXml')
      ->will($this->returnCallback([get_class($this), 'callbackEntityInterface__toXml']));

    // Create a mocked entity manager.
    $manager = $this->getMock(EntityManagerInterface::class);
    $manager->expects($this->any())
      ->method('createInstance')
      ->will($this->returnValue($this->entity));

    // Create the entity container to test with.
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
    $this->assertSame($this->entity, $this->container->add('DummyType'));
  }

  /**
   * @covers ::addObject
   * @covers ::addItem
   */
  public function testAddObject() {
    $this->assertSame($this->container, $this->container->addObject($this->entity));
  }

  /**
   * @covers ::addObject
   * @covers ::__construct
   */
  public function testAddObjectWithInvalidObject() {
    $container = $this->getMock(EntityContainer::class, ['isValidChild'], [
      'DummyType',
    ]);
    $container->expects($this->once())
      ->method('isValidChild')
      ->will($this->returnValue(FALSE));

    $this->setExpectedException(InvalidArgumentException::class);
    $container->addObject($this->entity);
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
   * @covers ::fromArray
   */
  public function testFromArrayWithMultipleObjects() {
    $container = new EntityContainer('DummyType2');
    $container->fromArray([
      [
        'Ab' => 'Foo',
        'Cd' => 'Bar',
      ],
      [
        'Ab' => 'Qux',
        'Ef' => 'Baz',
      ],
    ]);
    $expected = '<DummyType2 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Element><Fields Action="insert"><Ab>Foo</Ab><Cd>Bar</Cd></Fields></Element><Element><Fields Action="insert"><Ab>Qux</Ab><Ef>Baz</Ef></Fields></Element></DummyType2>';
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
   * @covers ::getManager
   * @covers ::__construct
   */
  public function testGetManager() {
    $manager = $this->getMock(EntityManagerInterface::class);
    $container = new EntityContainer('DummyType2', $manager);
    $this->assertEquals($manager, $container->getManager());
  }

  /**
   * @covers ::getManager
   * @covers ::__construct
   */
  public function testGetManagerWithPassingItToTheConstructor() {
    $manager = $this->getMock(EntityManagerInterface::class);
    $container = new EntityContainer('DummyType2');
    $this->assertInstanceOf(EntityManagerInterface::class, $container->getManager());
  }

  /**
   * @covers ::getObjects
   */
  public function testGetObjects() {
    // Create two objects.
    $objects = [
      $this->getMockedEntity(),
      $this->getMockedEntity(),
    ];

    foreach ($objects as $object) {
      $this->container->addObject($object);
    }

    $this->assertEquals($objects, array_values($this->container->getObjects()));
  }

  /**
   * @covers ::toArray
   */
  public function testToArray() {
    $container = new EntityContainer('DummyType');
    $object = $container->add('DummyType');
    $expected = [
      'DummyType' => [
        [],
      ],
    ];
    $this->assertEquals($expected, $container->toArray());

    // Set a field.
    $object->setField('Ab', 'Foo');
    $expected = [
      'DummyType' => [
        [
          'Ab' => 'Foo',
        ],
      ],
    ];
    $this->assertEquals($expected, $container->toArray());

    // A second object.
    $container->add('DummyType', [
      'Cd' => 'Bar',
    ]);
    $expected = [
      'DummyType' => [
        [
          'Ab' => 'Foo',
        ],
        [
          'Cd' => 'Bar',
        ],
      ],
    ];
    $this->assertEquals($expected, $container->toArray());
  }

  /**
   * @covers ::getType
   */
  public function testGetType() {
    $this->assertEquals('DummyType', $this->container->getType());
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    // $this->entity is of the same type as $this->container, so should be good.
    $this->assertTrue($this->container->isValidChild($this->entity));
    // $entity2 is of a different type as $this->container, so should be
    // rejected.
    $entity2 = $this->getMockedEntity([
      'getEntityType' => 'Dummy2',
      'getType' => 'Dummy2',
    ]);
    $this->assertFalse($this->container->isValidChild($entity2));
  }

  /**
   * @covers ::containsObject
   */
  public function testContainsObject() {
    $subentity = $this->getMockedEntity();
    $this->assertFalse($this->container->containsObject($subentity));

    // Now add the subentity.
    $this->container->addObject($subentity);
    $this->assertTrue($this->container->containsObject($subentity));
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

  /**
   * @covers ::__toString
   */
  public function testToStringWithException() {
    $container = $this->getMock(EntityContainer::class, ['compile'], [
      'DummyType',
    ]);
    $container->expects($this->once())
      ->method('compile')
      ->will($this->throwException(new Exception()));

    $this->assertEquals('', (string) $container);
  }

  /**
   * @covers ::validate
   */
  public function testValidate() {
    $this->assertInternalType('array', $this->container->validate());
  }

  /**
   * @covers ::mustValidate
   */
  public function testMustValidate() {
    $container = $this->getMock(EntityContainer::class, ['validate'], ['DummyType']);
    $container->expects($this->once())
      ->method('validate')
      ->will($this->returnValue([]));

    $this->assertNull($this->callProtectedMethod($container, 'mustValidate'));
  }

  /**
   * @covers ::mustValidate
   */
  public function testMustValidateException() {
    $container = $this->getMock(EntityContainer::class, ['validate'], ['DummyType']);
    $container->expects($this->once())
      ->method('validate')
      ->will($this->returnValue(['An error.']));

    $this->setExpectedException(EntityValidationException::class);
    $this->callProtectedMethod($container, 'mustValidate');
  }

}
