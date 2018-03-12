<?php

namespace Afas\Component\ItemList;

/**
 * Base class for a list of items.
 */
abstract class ItemList implements \IteratorAggregate, ListInterface {

  /**
   * A list of array items.
   *
   * @var array
   */
  private $list = [];

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
   * {@inheritdoc}
   */
  public function first() {
    return reset($this->list);
  }

  /**
   * Adds an item to the list.
   *
   * @param mixed $item
   *   The item to add to the list.
   * @param scalar $key
   *   The identifier of the item in the list.
   *
   * @return self
   *   An instance of this class.
   */
  protected function addItem($item, $key = NULL) {
    if (is_null($key)) {
      $this->list[] = $item;
    }
    else {
      $this->list[$key] = $item;
    }
    return $this;
  }

  /**
   * Returns the given item from the list.
   *
   * @param scalar $key
   *   The identifier of the item in the list.
   *
   * @return mixed
   *   The item.
   *
   * @throws \Exception
   *   In case the requested item does not exist in the list.
   */
  protected function getItem($key) {
    if (!isset($this->list[$key])) {
      throw new \Exception(strtr("Item with the key '!key' does not exist.", [
        '!key' => @(string) $key,
      ]));
    }
    return $this->list[$key];
  }

  /**
   * {@inheritdoc}
   */
  public function getItems() {
    return $this->list;
  }

  /**
   * Removes an item from the list.
   *
   * @param scalar $key
   *   The identifier of the item in the list.
   *
   * @return self
   *   An instance of this class.
   */
  protected function removeItem($key) {
    unset($this->list[$key]);
    return $this;
  }

}
