<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Entity\EntityCreateTrait as AfasCoreEntityCreateTrait;
use Afas\Core\Entity\EntityManagerInterface;
use Afas\Tests\TestBase;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Afas\Core\Entity\EntityCreateTrait
 * @group AfasCoreEntity
 */
class EntityCreateTraitTest extends TestBase {

  use EntityCreateTrait;

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

    $this->trait = $this->getMockForTrait(AfasCoreEntityCreateTrait::class);

    $this->trait->expects($this->any())
      ->method('addObject')
      ->will($this->returnValue($this->trait));
  }

  /**
   * @covers ::getObjectsOfType
   */
  public function testGetObjectsWithSingleType() {
    // Create a few entities.
    $entities = [
      $this->getMockedEntity([
        'getType' => 'Type1',
      ]),
      $this->getMockedEntity([
        'getType' => 'Type2',
      ]),
      $this->getMockedEntity([
        'getType' => 'Type1',
      ]),
    ];

    $this->trait->expects($this->exactly(2))
      ->method('getObjects')
      ->will($this->returnValue($entities));

    $expected = [
      $entities[0],
      $entities[2],
    ];
    $this->assertSame($expected, $this->trait->getObjectsOfType('Type1'));
    $this->assertSame([$entities[1]], $this->trait->getObjectsOfType('Type2'));
  }

  /**
   * @covers ::getObjectsOfType
   */
  public function testGetObjectsWithMultipleTypes() {
    // Create a few entities.
    $entities = [
      $this->getMockedEntity([
        'getType' => 'Type1',
      ]),
      $this->getMockedEntity([
        'getType' => 'Type2',
      ]),
      $this->getMockedEntity([
        'getType' => 'Type1',
      ]),
      $this->getMockedEntity([
        'getType' => 'Type3',
      ]),
    ];

    $this->trait->expects($this->exactly(2))
      ->method('getObjects')
      ->will($this->returnValue($entities));

    $expected = [
      $entities[0],
      $entities[2],
      $entities[3],
    ];
    $this->assertSame($expected, $this->trait->getObjectsOfType(['Type1', 'Type3']));

    $expected = [
      $entities[1],
      $entities[3],
    ];
    $this->assertSame($expected, $this->trait->getObjectsOfType(['Type2', 'Type3']));
  }

  /**
   * @covers ::getObjectsOfType
   */
  public function testGetObjectsOfTypeWithInvalidParameter() {
    $this->expectException(InvalidArgumentException::class);
    $this->trait->getObjectsOfType(TRUE);
  }

  /**
   * @covers ::hasObjectType
   */
  public function testHasObjectType() {
    // Create a few entities.
    $entities = [
      $this->getMockedEntity([
        'getType' => 'Type1',
      ]),
      $this->getMockedEntity([
        'getType' => 'Type2',
      ]),
      $this->getMockedEntity([
        'getType' => 'Type1',
      ]),
    ];

    $this->trait->expects($this->exactly(3))
      ->method('getObjects')
      ->will($this->returnValue($entities));

    $this->assertTrue($this->trait->hasObjectType('Type1'));
    $this->assertTrue($this->trait->hasObjectType('Type2'));
    $this->assertFalse($this->trait->hasObjectType('Type3'));
  }

  /**
   * @covers ::hasObjectType
   */
  public function testHasObjectTypeWithInvalidParameter() {
    $this->expectException(InvalidArgumentException::class);
    $this->trait->hasObjectType(['Dummy']);
  }

  /**
   * @covers ::add
   */
  public function testAdd() {
    $this->assertInstanceOf(EntityInterface::class, $this->trait->add('DummyEntityType'));
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    $this->assertTrue($this->trait->isValidChild($this->createMock(EntityInterface::class)));
  }

  /**
   * @covers ::getManager
   */
  public function testGetManager() {
    $this->assertInstanceOf(EntityManagerInterface::class, $this->trait->getManager());
  }

}
