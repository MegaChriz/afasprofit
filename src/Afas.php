<?php

/**
 * @file
 * Contains Afas.
 */

namespace Afas;

class Afas {
  /**
   * Returns the typed data manager service.
   *
   * Use the typed data manager service for creating typed data objects.
   *
   * @return \Drupal\Core\TypedData\TypedDataManager
   *   The typed data manager.
   *
   * @see \Drupal\Core\TypedData\TypedDataManager::create()
   */
  public static function testList() {
    return new Afas\Component\Test\TestManager();
  }
}
