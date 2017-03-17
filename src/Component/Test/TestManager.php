<?php

namespace Afas\Component\Test;

/**
 * This class is used solely for playing with the list interface.
 */
class TestManager {
  public function create($object, $offset, $value) {
    return new TestItem($value);
  }
}
