<?php

/**
 * @file
 * Contains \Drupal\Core\Entity\Entity.
 */

namespace Afas\Core\Entity;

/**
 * Defines a base entity class.
 */
abstract class Entity implements EntityInterface {
  /**
   * Constructs an Entity object.
   *
   * @param array $values
   *   An array of values to set, keyed by property name.
   */
  public function __construct(array $values) {
    // Set initial values.
    foreach ($values as $key => $value) {
      $this->$key = $value;
    }
  }
}
