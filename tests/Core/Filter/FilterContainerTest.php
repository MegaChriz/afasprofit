<?php

namespace Afas\Tests\Core\Filter;

use Afas\Core\Filter\FilterContainer;
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
   * A filter group.
   *
   * @var \Afas\Core\Filter\FilterGroupInterface
   */
  private $group;

  /**
   * Setup.
   */
  public function setUp() {
    parent::setUp();
    $filter = $this->getMock('Afas\Core\Filter\FilterInterface');
    $this->group = $this->getMock('Afas\Core\Filter\FilterGroupInterface');
    $factory = $this->getMock('Afas\Core\Filter\FilterFactoryInterface');

    $factory->expects($this->any())
      ->method('createFilter')
      ->will($this->returnValue($filter));
    $factory->expects($this->any())
      ->method('createFilterGroup')
      ->will($this->returnValue($this->group));

    $this->container = new FilterContainer($factory);
  }

  /**
   * @covers ::filter
   */
  public function testFilter() {
    $this->assertSame($this->container, $this->container->filter('item_id'));
  }

  /**
   * @covers ::group
   */
  public function testGroup() {
    $this->assertSame($this->group, $this->container->group());
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
    $expected = '<Filters></Filters>';
    $this->assertXmlStringEqualsXmlString($expected, $this->container->compile());
  }

}
