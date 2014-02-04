<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterContainer.
 */

namespace Afas\Core\Filter;

use Afas\Component\ItemList\ItemList;
use Afas\Core\Filter\FilterContainerInterface;
use Afas\Core\Filter\FilterGroupInterface;

/**
 * Class containing filter groups.
 */
class FilterContainer extends ItemList implements FilterContainerInterface {
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
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $this->currentGroup()->filter($field, $value, $operator);
    return $this;
  }

  /**
   * Adds a filter group.
   *
   * @todo Avoid new keyword.
   */
  public function group($name = NULL) {
    if (is_null($name)) {
      $name = 'Filter ' . ($this->count() + 1);
    }
    $group = new FilterGroup($name);
    $this->list[] = $group;
    return $group;
  }

  /**
   * Removes a filter.
   *
   * @param int $index
   *   The id of the filter to remove.
   */
  public function removeFilter($index) {
    $this->currentGroup()->removeFilter($index);
  }

  /**
   * Removes a filter group.
   *
   * @param string | FilterGroupInterface $group
   *   Either the ID of the group to remove
   *   or the group itself.
   */
  public function removeGroup($group) {
    $name = NULL;
    if ($group instanceof FilterGroupInterface) {
      $name = $group->name;
    }
    elseif (is_scalar($group)) {
      $name = $group;
    }
    unset($this->list[$name]);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns current group.
   *
   * @todo Test if this actually works.
   */
  protected function currentGroup() {
    if (!$this->count()) {
      return $this->group();
    }
    return current($this->getIterator());
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Return XML string.
   *
   * @return string
   *   XML.
   */
  public function compile() {
    $output = '<Filters>';
    foreach ($this->list as $filter_group) {
      $output .= $filter_group->compile();
    }
    $output .= '</Filters>';
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