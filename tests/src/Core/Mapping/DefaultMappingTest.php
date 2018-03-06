<?php

namespace Afas\Tests\Core\Mapping;

use Afas\Core\Entity\Entity;
use Afas\Core\Mapping\DefaultMapping;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Mapping\DefaultMapping
 * @group AfasCoreMapping
 */
class DefaultMappingTest extends TestBase {

  /**
   * @covers ::create
   */
  public function testCreate() {
    $entity = new Entity([], 'FbSales');

    $this->assertInstanceOf(DefaultMapping::class, DefaultMapping::create($entity));
  }

  /**
   * @covers ::getMappings
   * @covers ::__construct
   * @dataProvider dataProviderGetMappings
   */
  public function testGetMappings($entity_type) {
    $entity = new Entity([], $entity_type);

    $mapper = new DefaultMapping($entity);
    $mappings = $this->callProtectedMethod($mapper, 'getMappings');
    $this->assertInternalType('array', $mappings);
    $this->assertNotEmpty($mappings);
  }

  /**
   * Data provider for testGetMappings().
   */
  public function dataProviderGetMappings() {
    return [
      ['FbOrderBatchLines'],
      ['FbOrderSerialLines'],
      ['FbSales'],
      ['FbSalesLines'],
      ['FbSubscription'],
      ['FbSubscriptionLines'],
      ['KnBasicAddressAdr'],
      ['KnBasicAddressPad'],
      ['KnContact'],
      ['KnCourseMember'],
      ['KnOrganisation'],
      ['KnPerson'],
      ['KnSalesRelationOrg'],
      ['KnSalesRelationPer'],
      ['KnProvApplication'],
      ['KnSubject'],
      ['KnSubjectLink'],
    ];
  }

  /**
   * @covers ::getMappings
   * @covers ::__construct
   */
  public function testGetMappingsForUnknownEntityType() {
    $entity = new Entity([], 'DummyType');

    $mapper = new DefaultMapping($entity);
    $this->assertEquals([], $this->callProtectedMethod($mapper, 'getMappings'));
  }

}
