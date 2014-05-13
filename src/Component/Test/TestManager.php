<?php

/**
 * @file
 * Contains \Afas\Component\Test\TestManager.
 *
 * This class is used solely for playing with the list interface.
 */

namespace Afas\Component\Test;

class TestManager {
  public function create($object, $offset, $value) {
    return new TestItem($value);
  }
}
