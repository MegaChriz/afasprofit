<?php

/**
 * @file
 * Contains AfasFilterGroup.
 */

/**
 * Class FilterGroup
 * @package Afas\Core\Filter
 * @todo Maybe remove dependency on ItemList.
 */
class AfasFilterGroup implements IteratorAggregate, Countable {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The name of this filter group.
   *
   * @var string
   */
  private $name;

  /**
   * List of filters.
   *
   * @var array
   */
  protected $filters = array();

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * FilterGroup object constructor.
   *
   * @param string $name
   *   The name of this filter group.
   *
   * @return AfasFilterGroup
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
   * @return self
   *   Returns current instance.
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $filter = new AfasFilter($field, $value, $operator);
    $this->filters[] = $filter;
    return $this;
  }

  /**
   * Alias of ::filter().
   */
  public function addFilter($field, $value = NULL, $operator = NULL) {
    return $this->filter($field, $value, $operator);
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
    unset($this->filters[$index]);
    return $this;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Implements IteratorAggregate::getIterator().
   */
  public function getIterator() {
    return new ArrayIterator($this->filters);
  }

  /**
   * Implements Countable::count().
   */
  public function count() {
    return isset($this->filters) ? count($this->filters) : 0;
  }

  /**
   * Returns the complete list.
   *
   * @return array
   *   A list of items.
   */
  protected function getFilters() {
    return $this->filters;
  }

  /**
   * Returns name of filter group.
   *
   * @return string
   *   The name of this filter group.
   */
  public function getName() {
    return $this->name;
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Returns XML string.
   *
   * @return string
   *   XML generated string.
   */
  public function compile() {
    if ($this->count()) {
      $output = '<Filter FilterId="' . $this->name . '">';
      foreach ($this->getFilters() as $filter) {
        $output .= $filter->compile();
      }
      $output .= '</Filter>';
      return $output;
    }
  }

  /**
   * Implements PHP magic __toString().
   *
   * Converts the filter group to a string.
   *
   * @return string
   *   A string version of the filter group.
   */
  public function __toString() {
    return $this->compile();
  }
}
