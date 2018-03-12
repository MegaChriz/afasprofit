<?php

namespace Afas\Component\ItemList;

/**
 * Interface for a list of data.
 */
interface ListInterface extends \Countable, \Traversable {

  /**
   * Returns the first item in this list.
   *
   * @return mixed
   *   The first item in this list.
   */
  public function first();

  /**
   * Returns the complete list.
   *
   * @return array
   *   A list of items.
   */
  public function getItems();

}
