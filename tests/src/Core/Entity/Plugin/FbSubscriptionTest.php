<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\Plugin\FbSubscription;
use Afas\Core\Entity\Plugin\FbSubscriptionLines;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\FbSubscription
 * @group AfasCoreEntityPlugin
 */
class FbSubscriptionTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new FbSubscription([], 'FbSubscription');
  }

  /**
   * @covers ::isValidChild
   */
  public function testIsValidChild() {
    $this->assertFalse($this->entity->isValidChild(new Entity([], 'DummyEntity')));
    $this->assertTrue($this->entity->isValidChild(new Entity([], 'FbSubscriptionLines')));
  }

  /**
   * @covers ::addLineItem
   */
  public function testAddLineItem() {
    $this->assertCount(0, $this->entity->getObjects());
    $this->assertInstanceOf(FbSubscriptionLines::class, $this->entity->addLineItem());
    $this->assertCount(1, $this->entity->getObjects());
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    $default_errors = [
      'VaIn' => 'VaIn is a required field for type FbSubscription.',
      'VaSu' => 'VaSu is a required field for type FbSubscription.',
      'BcId' => 'BcId is a required field for type FbSubscription.',
      'SuSt' => 'SuSt is a required field for type FbSubscription.',
      'DbId' => 'DbId is a required field when inserting a subscription.',
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
                'DbId' => 123456,
                'VaIn' => 'J1',
                'VaSu' => 'ONL',
                'BcId' => 1000001,
                'SuSt' => date('Y-m-d'),
              ],
            ],
          ],
        ],
      ],
      [
        [
          $default_errors['VaIn'],
          $default_errors['VaSu'],
          $default_errors['BcId'],
          $default_errors['SuSt'],
        ],
        [
          [
            'method' => 'setAction',
            'args' => [
              FbSubscription::FIELDS_UPDATE,
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
    $this->entity->setAction(FbSubscription::FIELDS_UPDATE);
    $this->entity->validate();

    $this->assertFalse($this->entity->fieldExists('DbId'));
  }

  /**
   * @covers ::validate
   */
  public function testDbIdIsRemovedWhenDeleting() {
    $this->entity->setField('DbId', 123456);
    $this->entity->setAction(FbSubscription::FIELDS_DELETE);
    $this->entity->validate();

    $this->assertFalse($this->entity->fieldExists('DbId'));
  }

  /**
   * @covers ::validate
   */
  public function testRenewCycleIsSetWhenRenewDataIsSet() {
    $this->assertFalse($this->entity->fieldExists('VaRe'));
    $this->entity->setField('DaRe', date('Y-m-d'));
    $this->entity->validate();
    $this->assertTrue($this->entity->fieldExists('VaRe'));
  }

}
