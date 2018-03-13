<?php

namespace Afas\Tests\Component\Utility;

use Afas\Component\Utility\ArrayHelper;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Component\Utility\ArrayHelper
 * @group AfasComponentUtility
 */
class ArrayHelperTest extends TestBase {

  /**
   * @covers ::isAssociative
   */
  public function testIsAssociative() {
    // Single entity data.
    $entity_data = [
      'Foo' => 'Bar',
      'Baz' => 'Qux',
    ];
    $this->assertTrue(ArrayHelper::isAssociative($entity_data));

    // Array of entity data.
    $entities_data = [
      [
        'Foo' => 'Bar',
        'Baz' => 'Qux',
      ],
      [
        'Foo' => 'Bar2',
        'Baz' => 'Qux2',
      ],
    ];
    $this->assertFalse(ArrayHelper::isAssociative($entities_data));
  }

}
