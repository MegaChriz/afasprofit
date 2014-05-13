<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterTest
 */

namespace Afas\Core\Filter;

use Afas\Core\Filter\Filter;

/**
 * @coversDefaultClass \Afas\Core\Filter\FilterContainer\Filter
 * @group AfasCoreFilter
 */
class FilterTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test if an exception is thrown when a bad name is passed to Filter.
   *
   * @expectedException \InvalidArgumentException
   */
  public function testBadFilterName() {
    $filter = new Filter(0);
  }

  /**
   * Test if an exception is thrown when a bad operator is passed to Filter.
   *
   * @dataProvider badFilterOperatorProvider()
   * @expectedException \InvalidArgumentException
   */
  public function testBadFilterOperator($operator) {
    $filter = new Filter('item_id', NULL, $operator);
  }

  /**
   * Data provider for testBadFilterOperator().
   */
  public function badFilterOperatorProvider() {
    return array(
      array(16),
      array('big explosion'),
      array('><'),
      array('?'),
      array(TRUE),
      array(FALSE),
    );
  }

  /**
   * @covers ::compile
   */
  public function testCompile() {
    $filter = new Filter('item_id');
    $expected = '<Field FieldId="item_id" OperatorType="8" />';
    $this->assertXmlStringEqualsXmlString($expected, $filter->compile());
  }
}
