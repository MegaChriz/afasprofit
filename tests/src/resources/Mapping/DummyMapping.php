<?php

namespace Afas\Tests\resources\Mapping;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Mapping\EntityMappingInterface;

/**
 * A dummy mapping implementation.
 *
 * @see \Afas\Tests\Core\Mapping\EntityMappingFactoryTest::testSetClass()
 */
class DummyMapping implements EntityMappingInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(EntityInterface $entity) {
    return new static();
  }

  /**
   * {@inheritdoc}
   */
  public function map($key) {
    return [$key];
  }

}
