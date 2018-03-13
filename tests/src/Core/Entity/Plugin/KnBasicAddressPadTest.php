<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Plugin\KnBasicAddressPad;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\KnBasicAddressPad
 * @group AfasCoreEntityPlugin
 */
class KnBasicAddressPadTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new KnBasicAddressPad([], 'KnBasicAddressPad');
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    $default_errors = [
      'Ad' => 'Ad is a required field for type KnBasicAddressPad.',
      'HmNr' => 'HmNr is a required field for type KnBasicAddressPad.',
      'ZpCd' => 'ZpCd is a required field for type KnBasicAddressPad.',
      'CoId' => 'CoId is a required field for type KnBasicAddressPad.',
      'Rs' => "The field 'Rs' is required in a KnBasicAddressPad object when the field 'ResZip' is set to true."
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
                'Ad' => 'Mainstreet',
                'HmNr' => '123',
                'ZpCd' => '1234 AB',
                'Rs' => 'SomeCity',
                'CoId' => 'NL',
              ],
            ],
          ],
        ],
      ],
    ];
  }

}
