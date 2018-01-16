<?php

namespace Afas\Tests\Core\Filter;

use Afas\Core\Filter\FilterGroup;
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
   * A filter.
   *
   * @var \Afas\Core\Filter\FilterContainerInterface
   */
  private $filter;

  /**
   * Setup.
   */
  public function setUp() {
    parent::setUp();
    $this->filter = $this->getMock('Afas\Core\Filter\FilterInterface');
    $factory = $this->getMock('Afas\Core\Filter\FilterFactoryInterface');

    $factory->expects($this->any())
      ->method('createFilter')
      ->will($this->returnValue($this->filter));

    $this->group = new FilterGroup('my_name', $factory);
  }

  /**
   * @covers ::filter
   */
  public function testFilter() {
    $this->assertSame($this->group, $this->group->filter('item_id'));
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
    $expected = '<Filter FilterId="my_name"></Filter>';
    $this->assertXmlStringEqualsXmlString($expected, $this->group->compile());
  }

}
