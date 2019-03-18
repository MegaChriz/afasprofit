<?php

namespace Afas\Tests\Core\Filter;

use Afas\Core\Filter\FilterFactory;
use Afas\Core\Filter\FilterFactoryInterface;
use Afas\Core\Filter\FilterGroup;
use Afas\Core\Filter\FilterInterface;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Filter\FilterGroup
 * @group AfasCoreFilter
 */
class FilterGroupTest extends TestBase {

  /**
   * The filter group under test.
   *
   * @var \Afas\Core\Filter\FilterGroup
   */
  private $group;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->group = new FilterGroup('my_name', new FilterFactory());
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $filter = $this->createMock(FilterInterface::class);
    $factory = $this->createMock(FilterFactoryInterface::class);

    $factory->expects($this->any())
      ->method('createFilter')
      ->will($this->returnValue($filter));

    $group = new FilterGroup('my_name', $factory);
    $group->filter('item_id');
    $this->assertSame([$filter], $group->getFilters());
  }

  /**
   * @covers ::filter
   */
  public function testFilter() {
    $this->assertSame($this->group, $this->group->filter('item_id'));
  }

  /**
   * @covers ::removeFilter
   * @covers ::getFilters
   */
  public function testRemoveFilter() {
    $this->group->filter('item_id');
    $this->assertCount(1, $this->group->getFilters());

    $this->assertSame($this->group, $this->group->removeFilter(0));
    $this->assertEquals([], $this->group->getFilters());
  }

  /**
   * @covers ::getName
   */
  public function testGetName() {
    $this->assertSame('my_name', $this->group->getName());
  }

  /**
   * @covers ::compile
   */
  public function testCompileWithoutFilters() {
    $this->assertNull($this->group->compile());
  }

  /**
   * @covers ::compile
   */
  public function testCompileWithFilters() {
    $this->group->filter('item_id');
    $expected = '<Filter FilterId="my_name"><Field FieldId="item_id" OperatorType="8"/></Filter>';
    $this->assertXmlStringEqualsXmlString($expected, $this->group->compile());
  }

  /**
   * @covers ::__toString
   */
  public function testToStringWithoutFilters() {
    $this->assertEquals('', (string) $this->group);
  }

  /**
   * @covers ::__toString
   */
  public function testToStringWithFilters() {
    $this->group->filter('item_id');
    $expected = '<Filter FilterId="my_name"><Field FieldId="item_id" OperatorType="8"/></Filter>';
    $this->assertXmlStringEqualsXmlString($expected, (string) $this->group);
  }

}
