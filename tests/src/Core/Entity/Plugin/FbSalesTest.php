<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\Plugin\FbSales;
use Afas\Core\Entity\Plugin\FbSalesLines;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\FbSales
 * @group AfasCoreEntityPlugin
 */
class FbSalesTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new FbSales([], 'FbSales');
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'DummyEntity')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'FbSalesLines')));
  }

  /**
   * @covers ::setField
   */
  public function testSetDeCo() {
    $this->entity->setField('DeCo', FbSales::DELIVER_ORDER_COMPLETE);
    $this->assertEquals(FbSales::DELIVER_ORDER_COMPLETE, $this->entity->getField('DeCo'));
  }

  /**
   * @covers ::setField
   */
  public function testSetInvalidDeCo() {
    $this->expectException(InvalidArgumentException::class);
    $this->entity->setField('DeCo', 18);
  }

  /**
   * @covers ::addLineItem
   */
  public function testAddLineItem() {
    $this->assertCount(0, $this->entity->getObjects());
    $this->assertInstanceOf(FbSalesLines::class, $this->entity->addLineItem());
    $this->assertCount(1, $this->entity->getObjects());
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    return [
      [
        // When inserting an order, DbId should be required.
        [
          'DbId is a required field when inserting an order.',
        ],
      ],
      [
        [],
        [
          [
            'method' => 'setField',
            'args' => [
              'DbId',
              123456,
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
              FbSales::FIELDS_UPDATE,
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * @covers ::validate
   */
  public function testDbIdIsRemovedWhenUpdating() {
    $this->entity->setField('DbId', 123456);
    $this->entity->setAction(FbSales::FIELDS_UPDATE);
    $this->entity->validate();

    $this->assertFalse($this->entity->fieldExists('DbId'));
  }

  /**
   * @covers ::validate
   */
  public function testDbIdIsRemovedWhenDeleting() {
    $this->entity->setField('DbId', 123456);
    $this->entity->setAction(FbSales::FIELDS_DELETE);
    $this->entity->validate();

    $this->assertFalse($this->entity->fieldExists('DbId'));
  }

}
