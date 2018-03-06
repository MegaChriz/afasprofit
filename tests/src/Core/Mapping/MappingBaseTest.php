<?php

namespace Afas\Tests\Core\Mapping;

use Afas\Core\Mapping\MappingBase;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Mapping\MappingBase
 * @group AfasCoreMapping
 */
class MappingBaseTest extends TestBase {

  /**
   * @covers ::map
   */
  public function testMap() {
    $mapper = $this->getMockForAbstractClass(MappingBase::class);
    $mapper->expects($this->once())
      ->method('getMappings')
      ->will($this->returnValue([
        'Foo' => 'Bar',
        'Baz' => 'Qux',
      ]));

    // Assert that 'Foo' is being mapped to 'Bar'.
    $this->assertEquals(['Bar'], $mapper->map('Foo'));
    // Assert that 'Baz' is being mapped to 'Qux'.
    $this->assertEquals(['Qux'], $mapper->map('Baz'));

    // Assert that 'Qux' stays 'Qux'.
    $this->assertEquals(['Qux'], $mapper->map('Qux'));
  }

}
