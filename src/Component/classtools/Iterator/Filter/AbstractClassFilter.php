<?php

namespace Afas\Component\classtools\Iterator\Filter;

use hanneskod\classtools\Iterator\ClassIterator;
use hanneskod\classtools\Iterator\Filter;
use hanneskod\classtools\Iterator\Filter\FilterTrait;

/**
 * Filter classes that are abstract.
 */
class AbstractClassFilter extends ClassIterator implements Filter {

  use FilterTrait;

  /**
   * Override ClassIterator::__construct().
   */
  public function __construct() {}

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    foreach ($this->getBoundIterator() as $class_name => $reflected_class) {
      try {
        if ($reflected_class->isAbstract()) {
          yield $class_name => $reflected_class;
        }
      }
      catch (ReflectionException $e) {
        // Ignore reflection exceptions.
      }
    }
  }

}
