<?php

namespace Afas\Tests\resources\Entity;

use Afas\Core\Entity\EntityFactory;

/**
 * A mocked entity factory.
 *
 * This mock need to exist to be able to mock the static method
 * 'getPluginClass', which PHPUnit is not able to do on runtime.
 *
 * @see \Afas\Tests\Core\Entity\EntityFactoryTest::testCreateInstanceWithoutMapping()
 */
class EntityFactoryMock extends EntityFactory {

  /**
   * {@inheritdoc}
   */
  public static function getPluginClass($plugin_id, $plugin_definition = NULL, $required_interface = NULL) {
    // Always return class name of DummyEntity, which does not implement
    // \Afas\Core\Entity\EntityWithMappingInterface.
    return DummyEntity::class;
  }

}
