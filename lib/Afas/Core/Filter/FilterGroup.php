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
   * The container this group belongs to.
   *
   * @var FilterContainerInterface
   */
  private $container;

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
    $this->container = $container;
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
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $filter = new Filter($field, $value, $operator);
  }

  /**
   * Sets the parent of this filter group.
   *
   * @param FilterContainerInterface $container
   *   The container this group belongs to.
   */
  public function setParent(FilterContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * Removes this filter group from the container.
   */
  public function remove() {
    $this->getParent()->removeGroup($this);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns the parent of this filter group.
   *
   * @return FilterContainerInterface
   *   A filter container.
   * @throws Exception
   *   When the parent is not set.
   * @todo throw a specific exception.
   */
  public function getParent() {
    if ($this->container instanceof FilterContainerInterface) {
      return $this->container;
    }
    throw new Exception('This filter group does not belong to a container.')
  }

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
