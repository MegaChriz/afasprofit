<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\Plugin\KnContact;
use Afas\Core\Entity\Plugin\KnOrganisation;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\KnOrganisation
 * @group AfasCoreEntityPlugin
 */
class KnOrganisationTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new KnOrganisation([], 'KnOrganisation');
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'DummyEntity')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBankAccount')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBasicAddressAdr')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBasicAddressPad')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnContact')));
  }

  /**
   * @covers ::setField
   */
  public function testSetMatchOga() {
    $this->entity->setField('MatchOga', KnOrganisation::MATCH_ADDRESS);
    $this->assertEquals(KnOrganisation::MATCH_ADDRESS, $this->entity->getField('MatchOga'));
  }

  /**
   * @covers ::setField
   */
  public function testSetInvalidMatchOga() {
    $this->setExpectedException(InvalidArgumentException::class);
    $this->entity->setField('MatchOga', 18);
  }

  /**
   * @covers ::addContact
   */
  public function testAddContact() {
    $this->assertCount(0, $this->entity->getObjects());
    $this->assertInstanceOf(KnContact::class, $this->entity->addContact());
    $this->assertCount(1, $this->entity->getObjects());
    $this->entity->addContact();
    $this->assertCount(2, $this->entity->getObjects());
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    return [
      [
        [
          'An object of type KnOrganisation does not contain a KnBasicAddressAdr object.',
        ],
      ],
      [
        [],
        [
          [
            'method' => 'setAddress',
            'args' => [
              [
                'Ad' => 'Mainstreet',
                'HmNr' => 123,
                'ZpCd' => '1234 AB',
                'CoId' => 'NL',
              ],
            ],
          ],
        ],
      ],
      [
        [],
        [
          [
            'method' => 'setAction',
            'args' => [
              KnOrganisation::FIELDS_UPDATE,
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * @covers ::validate
   */
  public function testNoBcCoWhenInserting() {
    $this->entity->setField('BcCo', 12345);
    $this->entity->validate();
    $this->assertFalse($this->entity->fieldExists('BcCo'));
  }

  /**
   * @covers ::validate
   */
  public function testBcCoWhenUpdating() {
    $this->entity->setField('BcCo', 12345);
    $this->entity->setAction(KnOrganisation::FIELDS_UPDATE);
    $this->entity->validate();
    $this->assertTrue($this->entity->fieldExists('BcCo'));
  }

  /**
   * @covers ::validate
   * @dataProvider dataProviderSetDefaultMatchOga
   */
  public function testSetDefaultMatchOga($expected, array $values, $action = NULL) {
    if ($action) {
      $this->entity->setAction($action);
    }

    $this->entity->fromArray($values);
    $this->entity->validate();
    $this->assertEquals($expected, $this->entity->getField('MatchOga'));
  }

  /**
   * Data provider for testSetDefaultMatchOga().
   */
  public function dataProviderSetDefaultMatchOga() {
    return [
      [
        KnOrganisation::MATCH_NEW,
        [],
        KnOrganisation::FIELDS_INSERT,
      ],
      [
        KnOrganisation::MATCH_BCCO,
        [
          'BcCo' => 12345,
          'CcNr' => '03999990',
          'FiNr' => 'NL001234567B01',
        ],
        KnOrganisation::FIELDS_UPDATE,
      ],
      [
        KnOrganisation::MATCH_KVK,
        [
          'CcNr' => '03999990',
          'FiNr' => 'NL001234567B01',
        ],
        KnOrganisation::FIELDS_UPDATE,
      ],
      [
        KnOrganisation::MATCH_FISC,
        [
          'FiNr' => 'NL001234567B01',
        ],
        KnOrganisation::FIELDS_UPDATE,
      ],
    ];
  }

}
