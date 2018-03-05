<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\Plugin\KnSalesRelation;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\KnSalesRelation
 * @group AfasCoreEntityPlugin
 */
class KnSalesRelationTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return $this->getMockForAbstractClass(KnSalesRelation::class, [
      ['PaCd' => 30],
      'KnSalesRelation',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    return [
      [
        [],
      ],
      [
        ['Attribute DbId is not set for KnSalesRelation.'],
        [
          [
            'method' => 'setAction',
            'args' => [KnSalesRelation::FIELDS_UPDATE],
          ],
        ],
      ],
      [
        ['Attribute DbId is not set for KnSalesRelation.'],
        [
          [
            'method' => 'setAction',
            'args' => [KnSalesRelation::FIELDS_DELETE],
          ],
        ],
      ],
      [
        [
          'PaCd is a required field when IsDb is "true".',
          'VaDu is a required field when IsDb is "true".',
        ],
        [
          [
            'method' => 'removeField',
            'args' => ['PaCd'],
          ],
          [
            'method' => 'removeField',
            'args' => ['VaDu'],
          ],
        ],
      ],
    ];
  }

  /**
   * @covers ::validate
   */
  public function testDbIdIsRemovedWhenInserting() {
    $this->entity->setAttribute('DbId', 123456);
    $this->entity->validate();

    $this->assertNull($this->entity->getAttribute('DbId'));
  }

}
