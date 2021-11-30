<?php

namespace Afas\Tests\Core\Query;

use Afas\Core\Entity\EntityContainerInterface;
use Afas\Core\Query\Delete;
use Afas\Core\Result\UpdateConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Query\Delete
 * @group AfasCoreQuery
 */
class DeleteTest extends QueryTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->query = $this->createQuery(Delete::class, [
      2 => [],
    ]);
  }

  /**
   * @covers ::__construct
   * @covers ::getEntityContainer
   * @covers \Afas\Core\Entity\EntityContainer::toArray
   */
  public function testConstruct() {
    $query = $this->createQuery(Delete::class, [
      2 => [],
    ]);

    $expected = [];
    $this->assertEquals($expected, $this->query->getEntityContainer()->toArray());
  }

  /**
   * @covers ::__construct
   * @covers ::getEntityContainer
   * @covers \Afas\Core\Entity\EntityContainer::compile
   */
  public function testConstructWithData() {
    $query = $this->createQuery(Delete::class, [
      2 => [
        'Foo' => 'Bar',
      ],
    ]);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Element><Fields Action="delete"><Foo>Bar</Foo></Fields></Element></Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $query->getEntityContainer()->compile());
  }

  /**
   * @covers ::__construct
   * @covers ::getEntityContainer
   * @covers \Afas\Core\Entity\EntityContainer::compile
   */
  public function testConstructWithMultipleData() {
    $query = $this->createQuery(Delete::class, [
      2 => [
        [
          'Foo' => 'Bar',
        ],
        [
          'Foo' => 'Baz',
        ],
      ],
    ]);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Element><Fields Action="delete"><Foo>Bar</Foo></Fields></Element><Element><Fields Action="delete"><Foo>Baz</Foo></Fields></Element></Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $query->getEntityContainer()->compile());
  }

  /**
   * @covers ::__construct
   * @covers ::convertAttributes
   * @covers ::getEntityContainer
   * @covers \Afas\Core\Entity\EntityContainer::compile
   */
  public function testConstructWithAttributes() {
    $query = $this->createQuery(Delete::class, [
      2 => [
        'FooId' => 123,
      ],
      3 => ['FooId'],
    ]);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Element FooId="123"><Fields Action="delete"/></Element></Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $query->getEntityContainer()->compile());
  }

  /**
   * @covers ::__construct
   * @covers ::convertAttributes
   * @covers ::getEntityContainer
   * @covers \Afas\Core\Entity\EntityContainer::compile
   */
  public function testConstructWithAttributesAndMultipleData() {
    $query = $this->createQuery(Delete::class, [
      2 => [
        [
          'FooId' => 123,
        ],
        [
          'FooId' => 124,
        ],
      ],
      3 => ['FooId'],
    ]);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Element FooId="123"><Fields Action="delete"/></Element><Element FooId="124"><Fields Action="delete"/></Element></Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $query->getEntityContainer()->compile());
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
    $result = $this->query->execute();
    $this->assertInstanceOf(UpdateConnectorResult::class, $result);
  }

  /**
   * @covers ::getEntityContainer
   */
  public function testGetEntityContainer() {
    $this->assertInstanceOf(EntityContainerInterface::class, $this->query->getEntityContainer());
  }

}
