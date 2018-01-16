<?php

namespace Afas\Component\Test;

use Afas\Component\ItemList\ItemList;

/**
 * This class is used solely for playing with the list interface.
 */
class TestList extends ItemList {
  /**
   * Numerically indexed array items.
   *
   * @var array
   */
  protected $list = array();

  /**
   * Implements \ArrayAccess::offsetExists().
   */
  public function offsetExists($offset) {
    return isset($this->list) && array_key_exists($offset, $this->list) && $this->offsetGet($offset)->getValue() !== NULL;
  }

  /**
   * Implements \ArrayAccess::offsetUnset().
   */
  public function offsetUnset($offset) {
    if (isset($this->list)) {
      unset($this->list[$offset]);
    }
  }

  /**
   * Implements \ArrayAccess::offsetGet().
   */
  public function offsetGet($offset) {
    if (!is_numeric($offset)) {
      throw new \InvalidArgumentException('Unable to get a value with a non-numeric delta in a list.');
    }
    // Allow getting not yet existing items as well.
    // @todo: Maybe add a public createItem() method in addition?
    elseif (!isset($this->list[$offset])) {
      $this->list[$offset] = $this->createItem($offset);
    }
    return $this->list[$offset];
  }

  /**
   * Implements \ArrayAccess::offsetSet().
   */
  public function offsetSet($offset, $value) {
    if (!isset($offset)) {
      // The [] operator has been used so point at a new entry.
      $offset = $this->list ? max(array_keys($this->list)) + 1 : 0;
    }
    if (is_numeric($offset)) {
      // Support setting values via typed data objects.
      if ($value instanceof TestItem) {
        $value = $value->getValue();
      }
      $this->offsetGet($offset)->setValue($value);
    }
    else {
      throw new \InvalidArgumentException('Unable to set a value with a non-numeric delta in a list.');
    }
  }

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

  /**
   * Helper for creating a list item object.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface
   */
  protected function createItem($offset = 0, $value = NULL) {
    return \Afas::testList()->create($this, $offset, $value);
  }

}
