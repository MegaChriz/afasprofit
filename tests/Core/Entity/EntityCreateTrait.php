<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityInterface;

/**
 * Traits for creating entity mocks.
 */
trait EntityCreateTrait {

  /**
   * Returns a mocked entity.
   *
   * @param array $arguments
   *   (optional) Return values for other methods.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   A mocked entity.
   */
  protected function getMockedEntity(array $arguments = []) {
    $arguments += [
      'getFields' => [],
      'getObjects' => [],
      'getEntityType' => 'DummyType',
      'getType' => 'DummyType',
      'validate' => [],
    ];

    $entity = $this->getMock(EntityInterface::class);
    foreach ($arguments as $method => $return_value) {
      $entity->expects($this->any())
        ->method($method)
        ->will($this->returnValue($return_value));
    }

    return $entity;
  }

}
