<?php

namespace Afas\Tests\Core\Filter;

use Afas\Core\Filter\FilterContainer;
use Afas\Core\Filter\FilterFactoryInterface;
use Afas\Core\Filter\FilterGroupInterface;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Filter\FilterContainer
 * @group AfasCoreFilter
 */
class FilterContainerTest extends TestBase {

  /**
   * The filter container under test.
   *
   * @var \Afas\Core\Filter\FilterContainer
   */
  private $container;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->container = new FilterContainer();
  }

  /**
   * @covers ::__construct
   * @covers ::group
   */
  public function testConstruct() {
    $group = $this->createMock(FilterGroupInterface::class);
    $factory = $this->createMock(FilterFactoryInterface::class);

    $factory->expects($this->any())
      ->method('createFilterGroup')
      ->will($this->returnValue($group));

    $container = new FilterContainer($factory);
    $this->assertSame($group, $container->group());
  }

  /**
   * @covers ::filter
   */
  public function testFilter() {
    $this->assertSame($this->container, $this->container->filter('item_id'));
  }

  /**
   * @covers ::removeFilter
   * @covers ::getFilters
   */
  public function testRemoveFilter() {
    $this->container->filter('item_id');
    $this->assertCount(1, $this->container->getFilters());

    $this->assertSame($this->container, $this->container->removeFilter(0));
    $this->assertEquals([], $this->container->getFilters());
  }

  /**
   * @covers ::removeFilter
   * @covers ::getFilters
   */
  public function testRemoveFilterWithMultipleFilters() {
    $this->container->filter('item_id');
    $this->container->filter('name');
    $this->container->filter('foo');
    $filters = $this->container->getFilters();
    $this->assertCount(3, $filters);
    $this->assertEquals('item_id', $filters[0]->field);
    $this->assertEquals('name', $filters[1]->field);
    $this->assertEquals('foo', $filters[2]->field);

    $this->assertSame($this->container, $this->container->removeFilter(1));
    $this->assertSame([0 => $filters[0], 2 => $filters[2]], $this->container->getFilters());
  }

  /**
   * @covers ::group
   */
  public function testGroup() {
    $this->assertInstanceOf(FilterGroupInterface::class, $this->container->group());
  }

  /**
   * @covers ::removeGroup
   * @covers ::getGroups
   */
  public function testRemoveGroup() {
    $group = $this->container->group();
    $this->assertEquals(['Filter 1' => $group], $this->container->getGroups());

    // Remove group.
    $this->assertSame($this->container, $this->container->removeGroup($group));
    $this->assertEquals([], $this->container->getGroups());
  }

  /**
   * @covers ::removeGroup
   * @covers ::getGroups
   */
  public function testRemoveGroupWithTwoGroups() {
    $group1 = $this->container->group();
    $group2 = $this->container->group();
    $this->assertEquals(['Filter 1' => $group1, 'Filter 2' => $group2], $this->container->getGroups());

    // Remove group.
    $this->assertSame($this->container, $this->container->removeGroup($group1));
    $this->assertEquals(['Filter 2' => $group2], $this->container->getGroups());
  }

  /**
   * @covers ::removeGroup
   * @covers ::getGroups
   */
  public function testRemoveGroupByName() {
    $group1 = $this->container->group();
    $group2 = $this->container->group();
    $this->assertEquals(['Filter 1' => $group1, 'Filter 2' => $group2], $this->container->getGroups());

    // Remove group.
    $this->assertSame($this->container, $this->container->removeGroup('Filter 1'));
    $this->assertEquals(['Filter 2' => $group2], $this->container->getGroups());
  }

  /**
   * @covers ::setFactory
   * @covers ::group
   */
  public function testSetFactory() {
    $group = $this->createMock(FilterGroupInterface::class);
    $factory = $this->createMock(FilterFactoryInterface::class);

    $factory->expects($this->any())
      ->method('createFilterGroup')
      ->will($this->returnValue($group));

    $this->assertNotSame($group, $this->container->group());
    $this->container->setFactory($factory);
    $this->assertSame($group, $this->container->group());
  }

  /**
   * @covers ::setCurrentGroup
   * @covers ::currentGroup
   * @covers ::removeGroup
   */
  public function testSetCurrentGroup() {
    // Add a few groups.
    $group1 = $this->container->group();
    $group2 = $this->container->group();
    $group3 = $this->container->group();
    $group4 = $this->container->group();

    $this->assertSame($group4, $this->callProtectedMethod($this->container, 'currentGroup'));

    // Set group and assert current group.
    $this->container->setCurrentGroup($group1);
    $this->assertSame($group1, $this->callProtectedMethod($this->container, 'currentGroup'));

    // Remove second group. Assert that first group is still active.
    $this->container->removeGroup($group2);
    $this->assertSame($group1, $this->callProtectedMethod($this->container, 'currentGroup'));

    // Now remove first group as well and assert group 4 is now the active one.
    $this->container->removeGroup($group1);
    $this->assertSame($group4, $this->callProtectedMethod($this->container, 'currentGroup'));
  }

  /**
   * @covers ::currentGroup
   * @covers ::removeGroup
   */
  public function testCurrentGroup() {
    $this->assertInstanceOf(FilterGroupInterface::class, $this->callProtectedMethod($this->container, 'currentGroup'));

    // Add a group.
    $group = $this->container->group();
    $this->assertSame($group, $this->callProtectedMethod($this->container, 'currentGroup'));

    // Add another group.
    $group2 = $this->container->group();
    $current_group = $this->callProtectedMethod($this->container, 'currentGroup');
    $this->assertNotSame($group, $current_group);
    $this->assertSame($group2, $current_group);

    // Remove the second group.
    $this->container->removeGroup($group2);
    $this->assertSame($group, $this->callProtectedMethod($this->container, 'currentGroup'));
  }

  /**
   * @covers ::compile
   */
  public function testCompileWithoutFilters() {
    $this->assertNull($this->container->compile());
  }

  /**
   * @covers ::compile
   */
  public function testCompileWithFilters() {
    $this->container->filter('item_id');
    $expected = '<Filters><Filter FilterId="Filter 1"><Field FieldId="item_id" OperatorType="8"/></Filter></Filters>';
    $this->assertXmlStringEqualsXmlString($expected, $this->container->compile());
  }

  /**
   * @covers ::__toString
   */
  public function testToStringWithoutFilters() {
    $this->assertEquals('', (string) $this->container);
  }

  /**
   * @covers ::__toString
   */
  public function testToStringWithFilters() {
    $this->container->filter('item_id');
    $expected = '<Filters><Filter FilterId="Filter 1"><Field FieldId="item_id" OperatorType="8"/></Filter></Filters>';
    $this->assertXmlStringEqualsXmlString($expected, (string) $this->container);
  }

}
