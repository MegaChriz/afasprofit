<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\Plugin\FbSalesLines;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\FbSalesLines
 * @group AfasCoreEntityPlugin
 */
class FbSalesLinesTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new FbSalesLines([], 'FbSalesLines');
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'DummyEntity')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'FbOrderBatchLines')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'FbOrderSerialLines')));
  }

  /**
   * @covers ::validate
   */
  public function testRoundPercentage() {
    $this->entity->setField('PRDc', 1.23456);
    $this->entity->validate();
    $this->assertEquals(1.23, $this->entity->getField('PRDc'));
  }

  /**
   * @covers ::validate
   */
  public function testRemoveLineItemIdWhenInserting() {
    $this->entity->setField('GuLi', 'abc');
    $this->assertTrue($this->entity->fieldExists('GuLi'));
    $this->entity->validate();
    $this->assertFalse($this->entity->fieldExists('GuLi'));
  }

  /**
   * @covers ::validate
   */
  public function testLineItemIdRequiredWhenUpdating() {
    $this->entity->setAction(FbSalesLines::FIELDS_UPDATE);
    $this->assertEquals(['GuLi is a required field when updating or deleting an order line.'], $this->entity->validate());
    $this->entity->setField('GuLi', 'abc');
    $this->assertEquals([], $this->entity->validate());
  }

  /**
   * @covers ::validate
   */
  public function testLineItemIdRequiredWhenDeleting() {
    $this->entity->setAction(FbSalesLines::FIELDS_DELETE);
    $this->assertEquals(['GuLi is a required field when updating or deleting an order line.'], $this->entity->validate());
    $this->entity->setField('GuLi', 'abc');
    $this->assertEquals([], $this->entity->validate());
  }

}
