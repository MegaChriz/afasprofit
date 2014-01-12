<?php

/**
 * @file
 * Contains \Afas\Component\ItemList\ListInterface.
 */

namespace Afas\Component\ItemList;

/**
 * Interface for a list of data.
 *
 * Based on \Drupal\Core\TypedData\ListInterface.
 */
interface ListInterface extends \ArrayAccess, \Countable, \Traversable {

  /**
   * Determines whether the list contains any non-empty items.
   *
   * @return boolean
   *   TRUE if the list is empty, FALSE otherwise.
   */
  //public function isEmpty();

  /**
   * Gets the definition of a contained item.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface
   *   The data definition of contained items.
   */
  //public function getItemDefinition();

  /**
   * React to changes to a child item.
   *
   * Note that this is invoked after any changes have been applied.
   *
   * @param $delta
   *   The delta of the item which is changed.
   */
  //public function onChange($delta);
}
