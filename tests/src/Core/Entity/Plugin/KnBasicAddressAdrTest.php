<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Plugin\KnBasicAddressAdr;
use UnexpectedValueException;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\KnBasicAddressAdr
 * @group AfasCoreEntityPlugin
 */
class KnBasicAddressAdrTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new KnBasicAddressAdr([], 'KnBasicAddressAdr');
  }

  /**
   * @covers ::getRequiredFields
   */
  public function testGetRequiredFieldsWhenInserting() {
    $this->entity->setAction(KnBasicAddressAdr::FIELDS_INSERT);
    $this->assertEquals(['Ad', 'HmNr', 'CoId'], $this->entity->getRequiredFields());
  }

  /**
   * @covers ::getRequiredFields
   */
  public function testGetRequiredFieldsWhenUpdating() {
    $this->entity->setAction(KnBasicAddressAdr::FIELDS_UPDATE);
    $this->assertEquals(['Ad', 'HmNr', 'CoId'], $this->entity->getRequiredFields());
  }

  /**
   * @covers ::getRequiredFields
   */
  public function testGetRequiredFieldsWhenDeleting() {
    $this->entity->setAction(KnBasicAddressAdr::FIELDS_DELETE);
    $this->assertEquals([], $this->entity->getRequiredFields());
  }

  /**
   * @covers ::setField
   */
  public function testSetAutoMailbox() {
    $this->assertEquals(0, $this->entity->getField('PbAd'));

    // When the phrase 'postbus' is used in the street name, it is assumed
    // to be a mailbox address.
    $this->entity->setField('Ad', 'Postbus');
    $this->assertEquals(1, $this->entity->getField('PbAd'));
  }

  /**
   * @covers ::setField
   */
  public function testSetCountry() {
    // Set to the Netherlands.
    $this->entity->setField('CoId', 'NL');
    $this->assertEquals('NL', $this->entity->getField('CoId'));
  }

  /**
   * @covers ::setField
   */
  public function testSetNumericCountry() {
    // Set to Belgium.
    $this->entity->setField('CoId', 56);
    $this->assertEquals('B', $this->entity->getField('CoId'));
  }

  /**
   * @covers ::setField
   */
  public function testSetIso2CountryThatProfitFailsToRecognize() {
    // Set to Belgium. Profit uses 'B' instead of 'BE' to identify Belgium.
    $this->entity->setField('CoId', 'BE');
    $this->assertEquals('B', $this->entity->getField('CoId'));
  }

  /**
   * @covers ::setField
   */
  public function testSetInvalidNumericCountry() {
    $this->expectException(UnexpectedValueException::class, 'No ISO Alpha-2 country code found for country no. 25389.');
    $this->entity->setField('CoId', 25389);
  }

  /**
   * @covers ::setField
   */
  public function testSetInvalidIso2Country() {
    $this->expectException(UnexpectedValueException::class, 'No Profit country code found for country code "QQ".');
    $this->entity->setField('CoId', 'QQ');
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    $default_errors = [
      'Ad' => 'Ad is a required field for type KnBasicAddressAdr.',
      'HmNr' => 'HmNr is a required field for type KnBasicAddressAdr.',
      'CoId' => 'CoId is a required field for type KnBasicAddressAdr.',
    ];

    return [
      'without values' => [
        array_values($default_errors),
      ],
      'Dutch address where city is searced by zip code' => [
        [],
        [
          [
            'method' => 'fromArray',
            'args' => [
              [
                'ResZip' => TRUE,
                'Ad' => 'Mainstreet',
                'HmNr' => '123',
                'ZpCd' => '1234 AB',
                'CoId' => 'NL',
              ],
            ],
          ],
        ],
      ],
      'Dutch address where city is taken as is' => [
        [],
        [
          [
            'method' => 'fromArray',
            'args' => [
              [
                'ResZip' => FALSE,
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
      'Dutch address that is missing a zip code' => [
        [
          'ZpCd is a required field in a KnBasicAddressAdr object for the given country: "NL".',
        ],
        [
          [
            'method' => 'fromArray',
            'args' => [
              [
                'Ad' => 'Mainstreet',
                'HmNr' => '123',
                'Rs' => 'SomeCity',
                'CoId' => 'NL',
              ],
            ],
          ],
        ],
      ],
      'Dutch address where city is missing and search by zip code is turned off' => [
        [
          "The field 'Rs' is required in a KnBasicAddressAdr object when the field 'ResZip' is set to false.",
        ],
        [
          [
            'method' => 'fromArray',
            'args' => [
              [
                'ResZip' => FALSE,
                'Ad' => 'Mainstreet',
                'HmNr' => '123',
                'ZpCd' => '1234 AB',
                'CoId' => 'NL',
              ],
            ],
          ],
        ],
      ],
      'Aruba address (no zip code)' => [
        [],
        [
          [
            'method' => 'fromArray',
            'args' => [
              [
                'Ad' => 'Foostraat',
                'HmNr' => '123',
                'Rs' => 'Oranjestad',
                'CoId' => 'AW',
              ],
            ],
          ],
        ],
      ],
      'Aruba address where city is missing' => [
        [
          "The field 'Rs' is required in a KnBasicAddressAdr object when the country does not have a zip code.",
        ],
        [
          [
            'method' => 'fromArray',
            'args' => [
              [
                'ResZip' => TRUE,
                'Ad' => 'Foostraat',
                'HmNr' => '123',
                'CoId' => 'AW',
              ],
            ],
          ],
        ],
      ],
    ];
  }

}
