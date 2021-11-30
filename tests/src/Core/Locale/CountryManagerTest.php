<?php

namespace Afas\Tests\Core\Locale;

use Afas\Core\Locale\CountryManager;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Locale\CountryManager
 * @group AfasCoreLocale
 */
class CountryManagerTest extends TestBase {

  /**
   * The country manager under test.
   *
   * @var \Afas\Core\Locale\CountryManager
   */
  private $manager;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->manager = new CountryManager();
  }

  /**
   * @covers ::getList
   */
  public function testGetList() {
    $this->assertIsArray($this->manager->getList());
  }

  /**
   * @covers ::getListNum3toIso2
   */
  public function testGetListNum3toIso2() {
    $this->assertIsArray($this->manager->getListNum3toIso2());
  }

  /**
   * @covers ::getListFromCsv
   */
  public function testGetListFromCsv() {
    $list = $this->manager->getListFromCsv();
    $this->assertIsArray($list);

    // Assert keys.
    $expected = [
      'coid',
      'name',
      'eu',
      'iso-alpha2',
      'iso-alpha3',
      'iso-num3',
    ];
    $this->assertEquals($expected, array_keys($list[0]));
  }

}
