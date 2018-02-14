<?php

namespace Afas\Component\ItemList;

/**
 * Interface for a list of data.
 */
interface ListInterface extends \Countable, \Traversable {

  /**
   * Returns the complete list.
   *
   * @return array
   *   A list of items.
   */
  public function getItems();

}
