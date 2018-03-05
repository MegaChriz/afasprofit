<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\Plugin\KnPerson;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\KnPerson
 * @group AfasCoreEntityPlugin
 */
class KnPersonTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new KnPerson([], 'KnPerson');
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'DummyEntity')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBankAccount')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBasicAddressAdr')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBasicAddressPad')));
  }

  /**
   * @covers ::setField
   */
  public function testSetMatchPer() {
    $this->entity->setField('MatchPer', KnPerson::MATCH_NAME_GENDER_EMAIL);
    $this->assertEquals(KnPerson::MATCH_NAME_GENDER_EMAIL, $this->entity->getField('MatchPer'));
  }

  /**
   * @covers ::setField
   */
  public function testSetInvalidMatchPer() {
    $this->setExpectedException(InvalidArgumentException::class);
    $this->entity->setField('MatchPer', 18);
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    return [
      [
        [
          'The person must either have intials (In) or a first name (FiNm).',
          'When inserting a new person, their last name (LaNm) is required.',
        ],
      ],
      [
        [],
        [
          [
            'method' => 'fromArray',
            'args' => [
              [
                'FiNm' => 'Jan',
                'LaNm' => 'Jansen',
              ],
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * @covers ::validate
   * @dataProvider dataProviderSetDefaultMatchPer
   */
  public function testSetDefaultMatchPer($expected, array $values, $action = NULL) {
    if ($action) {
      $this->entity->setAction($action);
    }

    $this->entity->fromArray($values);
    $this->entity->validate();
    $this->assertEquals($expected, $this->entity->getField('MatchPer'));
  }

  /**
   * Data provider for testSetDefaultMatchPer().
   */
  public function dataProviderSetDefaultMatchPer() {
    return [
      [
        KnPerson::MATCH_NEW,
        [],
        KnPerson::FIELDS_INSERT,
      ],
      [
        KnPerson::MATCH_BCCO,
        [
          'BcCo' => 12345,
          'SoSe' => 12345678,
        ],
        KnPerson::FIELDS_UPDATE,
      ],
      [
        KnPerson::MATCH_BSN,
        [
          'SoSe' => 12345678,
        ],
        KnPerson::FIELDS_UPDATE,
      ],
    ];
  }

  /**
   * @covers ::validate
   */
  public function testRequireAddressWhenParentIsKnSalesRelationPer() {
    // Create a KnSalesRelationPer object.
    $relation = new Entity([], 'KnSalesRelationPer');
    $relation->addObject($this->entity);
    $this->entity->fromArray([
      'FiNm' => 'Jan',
      'LaNm' => 'Jansen',
    ]);

    $expected = [
      'An object of type KnPerson does not contain a KnBasicAddressAdr object.',
    ];
    $this->assertEquals($expected, $this->entity->validate());

    // Now set an address.
    $this->entity->setAddress([
      'Ad' => 'Mainstreet',
      'HmNr' => 123,
      'ZpCd' => '1234 AB',
      'CoId' => 'NL',
    ]);
    $this->assertEquals([], $this->entity->validate());
  }

  /**
   * @covers ::validate
   */
  public function testNotRequireAddressWhenParentIsKnContact() {
    // Create a KnContact object.
    $contact = new Entity([], 'KnContact');
    $contact->addObject($this->entity);
    $this->entity->fromArray([
      'FiNm' => 'Jan',
      'LaNm' => 'Jansen',
    ]);

    $this->assertEquals([], $this->entity->validate());
  }

}
