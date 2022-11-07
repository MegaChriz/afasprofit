<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Core\Entity\Plugin\KnCourseMember;

/**
 * @coversDefaultClass \Afas\Core\Entity\Plugin\KnCourseMember
 * @group AfasCoreEntityPlugin
 */
class KnCourseMemberTest extends PluginTestBase {

  /**
   * {@inheritdoc}
   */
  protected function createEntity() {
    return new KnCourseMember([], 'KnCourseMember');
  }

  /**
   * {@inheritdoc}
   */
  public function dataProviderValidate() {
    $default_errors = [
      'BcCo' => 'BcCo is a required field for type KnCourseMember.',
      'CrId' => 'Attribute CrId is not set for KnCourseMember.',
      'CdId' => 'Attribute CdId is not set for KnCourseMember.',
    ];

    return [
      [
        // When inserting a member, some fields and attributes are required.
        array_values($default_errors),
      ],
      [
        [
          $default_errors['BcCo'],
          $default_errors['CrId'],
          $default_errors['CdId'],
        ],
      ],
      [
        // When updating a member, some fields and attributes are required.
        [
          $default_errors['BcCo'],
          $default_errors['CrId'],
          $default_errors['CdId'],
        ],
        [
          [
            'method' => 'setAction',
            'args' => [
              KnCourseMember::FIELDS_UPDATE,
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * @covers ::validate
   */
  public function testSetDiscount100PerCent() {
    $this->entity->setField('DfPr', 0);
    $this->entity->validate();
    $this->assertEquals(100, $this->entity->getField('DiPc'));
  }

  /**
   * @covers ::validate
   */
  public function testRoundPerCent() {
    $this->entity->setField('DiPc', 12.347);
    $this->entity->validate();
    $this->assertEquals(12.35, $this->entity->getField('DiPc'));
  }

}
