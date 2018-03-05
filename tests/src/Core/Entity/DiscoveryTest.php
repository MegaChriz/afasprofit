<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\Discovery;
use Afas\Core\Entity\Plugin\FbSales;
use Afas\Core\Entity\Plugin\KnPerson;
use Afas\Tests\TestBase;
use Afas\Tests\resources\Plugin\DummyPlugin;
use Afas\Tests\resources\Plugin\FbSales as FbSalesOverride;

/**
 * @coversDefaultClass \Afas\Core\Entity\Discovery
 * @group AfasCoreEntity
 */
class DiscoveryTest extends TestBase {

  /**
   * @covers ::__construct
   * @covers ::indexDir
   */
  public function test() {
    $discovery = new Discovery();
    $expected = [
      'class' => FbSales::class,
    ];
    $this->assertEquals($expected, $discovery->getDefinition('FbSales'));
  }

  /**
   * @covers ::indexDir
   */
  public function testIndexDir() {
    $discovery = new Discovery();
    $discovery->indexDir(dirname(dirname(__DIR__)) . '/resources/Plugin');

    // Ensure the abstract class is not in the definitions.
    $this->assertFalse($discovery->hasDefinition('AbstractPlugin'));

    // Ensure the dummy plugin was registered.
    $expected = [
      'class' => DummyPlugin::class,
    ];
    $this->assertEquals($expected, $discovery->getDefinition('DummyPlugin'));

    // Ensure the FbSales plugin got overridden.
    $expected = [
      'class' => FbSalesOverride::class,
    ];
    $this->assertEquals($expected, $discovery->getDefinition('FbSales'));

    // Ensure earlier registered plugins are still available.
    $expected = [
      'class' => KnPerson::class,
    ];
    $this->assertEquals($expected, $discovery->getDefinition('KnPerson'));
  }

}
