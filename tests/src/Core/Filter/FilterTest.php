<?php

namespace Afas\Tests\Core\Filter;

use Afas\Core\Filter\Filter;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Filter\Filter
 * @group AfasCoreFilter
 */
class FilterTest extends TestBase {

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
    return [
      [16],
      ['big explosion'],
      ['><'],
      ['?'],
      [TRUE],
      [FALSE],
    ];
  }

  /**
   * @covers ::__get
   */
  public function testMagicGetter() {
    $filter = new Filter('item_id', 'x', 'like');
    $this->assertEquals('item_id', $filter->field);
    $this->assertEquals('x', $filter->value);
    $this->assertEquals(Filter::OPERATOR_CONTAINS, $filter->operator);
    $this->assertNull($filter->non_existing_property);
  }

  /**
   * @covers ::__set
   */
  public function testMagicSetter() {
    $filter = new Filter('item_id');
    $filter->field = 'other_field';
    $filter->value = 'x';
    $filter->operator = 'ends with';
    $expected = '<Field FieldId="other_field" OperatorType="13">x</Field>';
    $this->assertXmlStringEqualsXmlString($expected, $filter->compile());
  }

  /**
   * @covers ::compile
   */
  public function testCompile() {
    $filter = new Filter('item_id');
    $expected = '<Field FieldId="item_id" OperatorType="8" />';
    $this->assertXmlStringEqualsXmlString($expected, $filter->compile());
  }

  /**
   * @covers ::compile
   * @dataProvider filterDataProvider
   */
  public function testCompileWithSpecialChars($expected, $value = NULL, $operator = NULL) {
    $filter = new Filter('item_id', $value, $operator);
    $this->assertXmlStringEqualsXmlString($expected, $filter->compile());
  }

  /**
   * Data provider for testCompileWithSpecialChars().
   */
  public function filterDataProvider() {
    return [
      [
        '<Field FieldId="item_id" OperatorType="1">F&amp;C</Field>',
        'F&C',
      ],
      [
        '<Field FieldId="item_id" OperatorType="1">é</Field>',
        'é',
      ],
      [
        '<Field FieldId="item_id" OperatorType="1">&lt;</Field>',
        '<',
      ],
      [
        '<Field FieldId="item_id" OperatorType="1">&#x3B2;</Field>',
        'β',
      ],
    ];
  }

  /**
   * @covers ::__toString
   */
  public function testToString() {
    $filter = new Filter('item_id');
    $expected = '<Field FieldId="item_id" OperatorType="8" />';
    $this->assertXmlStringEqualsXmlString($expected, (string) $filter);
  }

}
