<?php

namespace Afas\Tests\Core\Entity\Plugin;

use Afas\Tests\TestBase;

/**
 * Base class for testing entity plugins.
 */
abstract class PluginTestBase extends TestBase {

  /**
   * The entity plugin to test.
   *
   * @var \Afas\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->entity = $this->createEntity();
  }

  /**
   * @covers ::getRequiredFields
   */
  public function testGetRequiredFields() {
    $this->assertInternalType('array', $this->entity->getRequiredFields());
  }

  /**
   * @covers ::validate
   * @covers ::init
   * @covers ::getRequiredFields
   *
   * @dataProvider dataProviderValidate
   */
  public function testValidate(array $expected_errors, array $calls = []) {
    foreach ($calls as $call) {
      call_user_func_array([$this->entity, $call['method']], $call['args']);
    }

    $this->assertEquals($expected_errors, $this->entity->validate());
  }

  /**
   * Data provider for ::validate().
   */
  public function dataProviderValidate() {
    return [
      [
        // No expected errors.
        [],
      ],
    ];
  }

  /**
   * Creates the entity plugin to test.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The created entity.
   */
  abstract protected function createEntity();

}
