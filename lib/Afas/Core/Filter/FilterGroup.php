<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterGroup.
 */

namespace Afas\Core\Filter;

use Afas\Component\ItemList\ItemList;
use Afas\Core\Filter\FilterContainerInterface;
use Afas\Core\Filter\FilterGroupInterface;

class FilterGroup extends ItemList implements FilterGroupInterface {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The name of this filter group.
   *
   * @var string
   */
  private $name;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * FilterGroup object constructor.
   *
   * @param string $name
   *   The name of this filter group.
   */
  public function __construct($name) {
    $this->name = $name;
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Adds a single filter.
   *
   * @param string $field
   *   The name of the field to filter on.
   * @param mixed $value
   *   The value to test the field against.
   * @param mixed $operator
   *   The comparison operator, such as =, <, or >=.
   *
   * @todo Pass object creation to factory object.
   *
   * @return FilterGroup
   *   Returns current instance.
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $filter = new Filter($field, $value, $operator);
    $this->addItem($filter);
    return $this;
  }

  /**
   * Removes a filter.
   *
   * @param int $index
   *   The id of the filter to remove.
   *
   * @return FilterGroup
   *   Returns current instance.
   */
  public function removeFilter($index) {
    $this->removeItem($index);
    return $this;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

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
   *   XML generated string.
   */
  public function compile() {
    if ($this->count()) {
      $output = '<Filter FilterId="' . $this->name . '">';
      foreach ($this->getItems() as $filter) {
        $output .= $filter->compile();
      }
      $output .= '</Filter>';
      return $output;
    }
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
