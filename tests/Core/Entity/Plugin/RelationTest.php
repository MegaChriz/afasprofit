<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\Plugin\Relation;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\Relation
 * @group AfasCoreEntityPlugin
 */
class RelationTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return $this->getMockForAbstractClass(Relation::class, [
      [],
      'RelationStub',
    ]);
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'DummyEntity')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBasicAddressAdr')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnBasicAddressPad')));
  }

  /**
   * @covers ::getAddress
   */
  public function testGetAddress() {
    $address = new Entity([], 'KnBasicAddressAdr');
    $this->entity->addObject($address);
    $this->assertSame($address, $this->entity->getAddress());
  }

  /**
   * @covers ::getAddress
   * @covers ::resolveAddressType
   */
  public function testGetAddressWithPostalAddress() {
    $address = new Entity([], 'KnBasicAddressPad');
    $this->entity->addObject($address);
    $this->assertSame($address, $this->entity->getAddress('postal_address'));
  }

  /**
   * @covers ::getAddress
   */
  public function testGetAddressWithoutAddress() {
    $this->assertNull($this->entity->getAddress());
  }

  /**
   * @covers ::setAddress
   * @covers ::resolveAddressType
   * @dataProvider dataProviderSetAddress
   */
  public function testSetAddress($expected_entity_type, $address_type = NULL) {
    $address_data = [
      'Ad' => 'Mainstreet',
      'HmNr' => 123,
      'ZpCd' => '1234 AB',
      'CoId' => 'NL',
    ];

    if ($address_type) {
      $address = $this->entity->setAddress($address_data, $address_type);
    }
    else {
      $address = $this->entity->setAddress($address_data);
    }

    // Assert entity type.
    $this->assertEquals($expected_entity_type, $address->getType());

    // Assert fields.
    $this->assertEquals('Mainstreet', $address->getField('Ad'));
    $this->assertEquals(123, $address->getField('HmNr'));
    $this->assertEquals('1234 AB', $address->getField('ZpCd'));
    $this->assertEquals('NL', $address->getField('CoId'));

    // Assert that the object was added.
    $objects = $this->entity->getObjects();
    $this->assertSame($address, reset($objects));

    // Change house number.
    if ($address_type) {
      $this->entity->setAddress([
        'HmNr' => 59,
      ], $address_type);
    }
    else {
      $this->entity->setAddress([
        'HmNr' => 59,
      ]);
    }

    // Assert that house number name was changed, but the rest was not.
    $this->assertEquals('Mainstreet', $address->getField('Ad'));
    $this->assertEquals(59, $address->getField('HmNr'));
    $this->assertEquals('1234 AB', $address->getField('ZpCd'));
    $this->assertEquals('NL', $address->getField('CoId'));

    // Assert that there is only one object.
    $this->assertCount(1, $this->entity->getObjects());
  }

  /**
   * Data provider for testSetAddress().
   */
  public function dataProviderSetAddress() {
    return [
      [
        'KnBasicAddressAdr',
      ],
      [
        'KnBasicAddressAdr',
        'address',
      ],
      [
        'KnBasicAddressAdr',
        'KnBasicAddressAdr',
      ],
      [
        'KnBasicAddressPad',
        'postal_address',
      ],
      [
        'KnBasicAddressPad',
        'KnBasicAddressPad',
      ],
    ];
  }

  /**
   * @covers ::setAddress
   * @covers ::resolveAddressType
   */
  public function testSetInvalidAddressType() {
    $this->setExpectedException(InvalidArgumentException::class);
    $this->entity->setAddress([
      'Ad' => 'Mainstreet',
      'HmNr' => 123,
      'ZpCd' => '1234 AB',
      'CoId' => 'NL',
    ], 'dummy');
  }

  /**
   * @covers ::validate
   */
  public function testAutoSetPadAdrWithSingleAddress() {
    $this->assertFalse($this->entity->fieldExists('PadAdr'));

    // Create an address, but not a postal address.
    $this->entity->setAddress([]);
    $this->entity->validate();
    $this->assertEquals(1, $this->entity->getField('PadAdr'));
  }

  /**
   * @covers ::validate
   */
  public function testNoAutoSetPadAdrWithoutAddress() {
    $this->entity->validate();
    $this->assertNull($this->entity->getField('PadAdr'));
  }

  /**
   * @covers ::validate
   */
  public function testNoAutoSetPadAdrWithTwoAddresses() {
    // Set two addresses.
    $this->entity->setAddress([]);
    $this->entity->setAddress([], 'postal_address');
    $this->entity->validate();
    $this->assertNull($this->entity->getField('PadAdr'));
  }

}
