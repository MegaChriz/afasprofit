<?php

namespace Afas\Tests\Core\Query;

use Afas\Core\Filter\FilterContainerInterface;
use Afas\Core\Filter\FilterGroupInterface;
use Afas\Core\Query\Get;
use Afas\Core\Result\GetConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Query\Get
 * @group AfasCoreQuery
 */
class GetTest extends QueryTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->query = $this->createQuery(Get::class);
  }

  /**
   * @covers ::__construct
   * @covers ::getFilterContainer
   */
  public function testConstruct() {
    $query = $this->createQuery(Get::class);
    $this->assertNull($this->query->getFilterContainer()->compile());
  }

  /**
   * @covers ::range
   */
  public function testRange() {
    $this->assertSame($this->query, $this->query->range(0, 10));
  }

  /**
   * @covers ::orderBy
   */
  public function testOrderBy() {
    $this->assertSame($this->query, $this->query->orderBy('item_id'));
  }

  /**
   * @covers ::filter
   */
  public function testFilter() {
    $this->assertSame($this->query, $this->query->filter('item_id', 'x', 'like'));
  }

  /**
   * @covers ::removeFilter
   */
  public function testRemoveFilter() {
    $this->assertSame($this->query, $this->query->removeFilter(1));
  }

  /**
   * @covers ::group
   */
  public function testGroup() {
    $this->assertInstanceOf(FilterGroupInterface::class, $this->query->group());
  }

  /**
   * @covers ::removeGroup
   */
  public function testRemoveGroup() {
    $this->assertSame($this->query, $this->query->removeGroup(1));
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
    $result = $this->query->execute();
    $this->assertInstanceOf(GetConnectorResult::class, $result);
  }

  /**
   * @covers ::getFilterContainer
   */
  public function testGetFilterContainer() {
    $this->assertInstanceOf(FilterContainerInterface::class, $this->query->getFilterContainer());
  }

}
