<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Plugin\FbSubscriptionLines;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\FbSubscriptionLines
 * @group AfasCoreEntityPlugin
 */
class FbSubscriptionLinesTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new FbSubscriptionLines([], 'FbSubscriptionLines');
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    return [
      [
        [
          'DaSt is a required field for type FbSubscriptionLines.',
        ],
      ],
    ];
  }

  /**
   * @covers ::validate
   */
  public function testRemoveLineItemIdWhenInserting() {
    $this->entity->setField('Id', 'abc');
    $this->assertTrue($this->entity->fieldExists('Id'));
    $this->entity->validate();
    $this->assertFalse($this->entity->fieldExists('Id'));
  }

  /**
   * @covers ::validate
   */
  public function testLineItemIdRequiredWhenUpdating() {
    $this->entity->setField('DaSt', date('Y-m-d'));
    $this->entity->setAction(FbSubscriptionLines::FIELDS_UPDATE);
    $this->assertEquals(['Id is a required field when updating or deleting a subscription line.'], $this->entity->validate());
    $this->entity->setField('Id', 'abc');
    $this->assertEquals([], $this->entity->validate());
  }

  /**
   * @covers ::validate
   */
  public function testLineItemIdRequiredWhenDeleting() {
    $this->entity->setField('DaSt', date('Y-m-d'));
    $this->entity->setAction(FbSubscriptionLines::FIELDS_DELETE);
    $this->assertEquals(['Id is a required field when updating or deleting a subscription line.'], $this->entity->validate());
    $this->entity->setField('Id', 'abc');
    $this->assertEquals([], $this->entity->validate());
  }

}
