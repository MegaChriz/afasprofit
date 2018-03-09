<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Plugin\KnBasicAddressAdr;

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
    $this->assertEquals(['Ad', 'HmNr', 'ZpCd'], $this->entity->getRequiredFields());
  }

  /**
   * @covers ::getRequiredFields
   */
  public function testGetRequiredFieldsWhenUpdating() {
    $this->entity->setAction(KnBasicAddressAdr::FIELDS_UPDATE);
    $this->assertEquals(['Ad', 'HmNr', 'ZpCd'], $this->entity->getRequiredFields());
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
    // Set to Belgium.
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
  public function testSetInvalidNumericCountry() {
    // Set to Belgium.
    $this->entity->setField('CoId', 25389);
    $this->assertNull($this->entity->getField('CoId'));
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    $default_errors = [
      'Ad' => 'Ad is a required field for type KnBasicAddressAdr.',
      'HmNr' => 'HmNr is a required field for type KnBasicAddressAdr.',
      'ZpCd' => 'ZpCd is a required field for type KnBasicAddressAdr.',
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
                'CoId' => 'NL',
              ],
            ],
          ],
        ],
      ],
    ];
  }

}
