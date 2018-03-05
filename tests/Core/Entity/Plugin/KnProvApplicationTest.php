<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Plugin\KnProvApplication;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\KnProvApplication
 * @group AfasCoreEntityPlugin
 */
class KnProvApplicationTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new KnProvApplication([], 'KnProvApplication');
  }

  /**
   * @covers ::setField
   */
  public function testSetVaPt() {
    $this->entity->setField('VaPt', KnProvApplication::DOSSIER);
    $this->assertEquals(KnProvApplication::DOSSIER, $this->entity->getField('VaPt'));
  }

  /**
   * @covers ::setField
   */
  public function testSetInvalidVaPt() {
    $this->setExpectedException(InvalidArgumentException::class);
    $this->entity->setField('VaPt', 'Xa');
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    $default_errors = [
      'BcCo' => 'BcCo is a required field for type KnProvApplication.',
      'PvCd' => 'PvCd is a required field for type KnProvApplication.',
      'PvCt' => 'PvCt is a required field for type KnProvApplication.',
    ];

    return [
      [
        array_values($default_errors),
      ],
      [
        [],
        [
          [
            'method' => 'fromArray',
            'args' => [
              [
                'BcCo' => 123456,
                'PvCd' => KnProvApplication::VERKOOPORDER,
                'PvCt' => 234567,
                'VaPt' => KnProvApplication::EMAIL,
              ],
            ],
          ],
        ],
      ],
    ];
  }

}
