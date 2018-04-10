<?php

namespace Afas\Core\Filter;

use Afas\Component\ItemList\ItemList;

/**
 * Class for a group of filters.
 *
 * @todo Maybe remove dependency on ItemList.
 */
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

  /**
   * The filter factory.
   *
   * @var FilterFactoryInterface
   */
  private $factory;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new FilterGroup object.
   *
   * @param string $name
   *   The name of this filter group.
   * @param \Afas\Core\Filter\FilterFactoryInterface $factory
   *   The factory to use for generating filter objects.
   */
  public function __construct($name, FilterFactoryInterface $factory) {
    $this->name = $name;
    $this->factory = $factory;
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $filter = $this->factory->createFilter($field, $value, $operator);
    $this->addItem($filter);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeFilter($index) {
    $this->removeItem($index);
    return $this;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return $this->getItems();
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
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
   * Implements PHP magic __toString().
   *
   * Converts the filter group to a string.
   *
   * @return string
   *   A string version of the filter group.
   */
  public function __toString() {
    $compiled = $this->compile();
    return !is_null($compiled) ? $compiled : '';
  }

}
