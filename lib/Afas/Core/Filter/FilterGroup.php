<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterGroup.
 */

namespace Afas\Core\Filter;

use Afas\Component\ItemList\ItemList;

class FilterGroup extends ItemList {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  private $name;

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Creates a new instance of Filter.
   */
  protected function createItem() {
    // @todo implement!
  }

  /**
   * Return XML string.
   *
   * @return string
   *   XML.
   */
  public function compile() {
    if (!$this->name) {
      // @todo Move this logic.
      $this->name = "Filter1";
    }
    $output = '<Filter FilterId="' . $this->name . '">';
    foreach ($this->list as $filter) {
      $output .= $filter->compile();
    }
    $output .= '</Filter>';
    return $output;
  }

  /**
   * Implements PHP magic __toString method to convert the filter group to string.
   *
   * @return string
   *   A string version of the filter group.
   */
  public function __toString() {
    return $this->compile();
  }
}
