<?php

/**
 * @file
 * Contains \Afas\Component\ItemList\ItemList.
 */

namespace Afas\Component\ItemList;

abstract class ItemList implements \IteratorAggregate, ListInterface {
  /**
   * Numerically indexed array items.
   *
   * @var array
   */
  protected $list = array();

  /**
   * Implements \IteratorAggregate::getIterator().
   */
  public function getIterator() {
    return new \ArrayIterator($this->list);
  }

  /**
   * Implements \Countable::count().
   */
  public function count() {
    return isset($this->list) ? count($this->list) : 0;
  }
}
