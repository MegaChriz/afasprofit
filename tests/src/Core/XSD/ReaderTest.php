<?php

namespace Afas\Tests\Core;

use Afas\Core\XSD\Reader;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\XSD\Reader
 * @group AfasCoreXSD
 */
class ReaderTest extends TestBase {

  /**
   * Tests getting a definition from a XSD file.
   */
  public function testGetDefinitionArray() {
    $xml_string = $this->getFileContents('XSD/Foo.xsd');
    $reader = new Reader($xml_string);

    $expected = [
      'Foo' => [
        '#name' => 'Foo',
        'Element' => [
          '#name' => 'Element',
          '#maxOccurs' => 'unbounded',
          'Fields' => [
            'Bar' => [
              'title' => 'Bar',
              'maxlength' => 15,
              'minlength' => 1,
              'type' => 'string',
              'description' => 'The Bar field',
            ],
            'Qux' => [
              'title' => 'Qux',
              'maxlength' => 15,
              'minlength' => 1,
              'type' => 'string',
              'description' => 'The Qux field',
            ],
          ],
        ],
      ],
    ];
    $this->assertEquals($expected, $reader->getDefinitionArray());
  }

}
