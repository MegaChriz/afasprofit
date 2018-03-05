<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\Discovery;
use Afas\Core\Entity\Plugin\FbSales;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Entity\Discovery
 * @group AfasCoreEntity
 */
class DiscoveryTest extends TestBase {

  /**
   * @covers ::__construct
   */
  public function test() {
    $discovery = new Discovery();
    $expected = [
      'class' => FbSales::class,
    ];
    $this->assertEquals($expected, $discovery->getDefinition('FbSales'));
  }

}
