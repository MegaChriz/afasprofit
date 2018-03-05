<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\Plugin\KnSalesRelationOrg;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\KnSalesRelationOrg
 * @group AfasCoreEntityPlugin
 */
class KnSalesRelationOrgTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new KnSalesRelationOrg(['PaCd' => 30], 'KnSalesRelationOrg');
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'DummyEntity')));
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'KnBasicAddressAdr')));
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'KnBasicAddressPad')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'KnOrganisation')));
  }

  /**
   * @covers ::getOrganisation
   */
  public function testGetOrganisation() {
    $organisation = new Entity([], 'KnOrganisation');
    $this->entity->addObject($organisation);
    $this->assertSame($organisation, $this->entity->getOrganisation());
  }

  /**
   * @covers ::getOrganisation
   */
  public function testGetOrganisationWithoutOrganisation() {
    $this->assertNull($this->entity->getOrganisation());
  }

  /**
   * @covers ::setOrganisationData
   */
  public function testSetOrganisationData() {
    // Set name.
    $organisation = $this->entity->setOrganisationData([
      'Nm' => 'Example BV',
      'EmAd' => 'example-bv@example.com',
    ]);

    // Assert type.
    $this->assertEquals('KnOrganisation', $organisation->getType());

    // Assert fields.
    $this->assertEquals('Example BV', $organisation->getField('Nm'));
    $this->assertEquals('example-bv@example.com', $organisation->getField('EmAd'));

    // Assert that the object was added.
    $objects = $this->entity->getObjects();
    $this->assertSame($organisation, reset($objects));

    // Change first name.
    $this->entity->setOrganisationData([
      'Nm' => 'Example Holding BV',
    ]);

    // Assert that first name was changed, but last name did not.
    $this->assertEquals('Example Holding BV', $organisation->getField('Nm'));
    $this->assertEquals('example-bv@example.com', $organisation->getField('EmAd'));

    // Assert that there is only one object.
    $this->assertCount(1, $this->entity->getObjects());
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    return [
      [
        [
          'An object of type KnSalesRelationOrg does not contain a KnOrganisation object.',
        ],
      ],
    ];
  }

}
