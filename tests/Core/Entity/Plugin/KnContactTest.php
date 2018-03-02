<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\Plugin\KnContact;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\KnContact
 * @group AfasCoreEntityPlugin
 */
class KnContactTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new KnContact([], 'KnContact');
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'DummyEntity')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBasicAddressAdr')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBasicAddressPad')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnPerson')));
  }

  /**
   * @covers ::getPerson
   */
  public function testGetPerson() {
    $person = new Entity([], 'KnPerson');
    $this->entity->addObject($person);
    $this->assertSame($person, $this->entity->getPerson());
  }

  /**
   * @covers ::getPerson
   */
  public function testGetPersonWithoutPerson() {
    $address = new Entity([], 'KnBasicAddressAdr');
    $this->entity->addObject($address);
    $this->assertNull($this->entity->getPerson());
  }

  /**
   * @covers ::setPersonData
   */
  public function testSetPersonData() {
    // Set name.
    $person = $this->entity->setPersonData([
      'FiNm' => 'Jan',
      'LaNm' => 'Jansen',
    ]);

    // Assert type.
    $this->assertEquals('KnPerson', $person->getType());

    // Assert fields.
    $this->assertEquals('Jan', $person->getField('FiNm'));
    $this->assertEquals('Jansen', $person->getField('LaNm'));

    // Assert that the object was added.
    $objects = $this->entity->getObjects();
    $this->assertSame($person, reset($objects));

    // Change first name.
    $this->entity->setPersonData([
      'FiNm' => 'Piet',
    ]);

    // Assert that first name was changed, but last name did not.
    $this->assertEquals('Piet', $person->getField('FiNm'));
    $this->assertEquals('Jansen', $person->getField('LaNm'));

    // Assert that there is only one object.
    $this->assertCount(1, $this->entity->getObjects());
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
        // A contact should not contain more than one person.
        [
          'A KnContact object may not contain more than one KnPerson object.',
        ],
        [
          [
            'method' => 'add',
            'args' => [
              'KnPerson',
            ],
          ],
          [
            'method' => 'add',
            'args' => [
              'KnPerson',
            ],
          ],
        ],
      ],
    ];
  }

}
