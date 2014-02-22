<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterGroup.
 */

namespace Afas\Core\Filter;

use Afas\Component\ItemList\ItemList;
use Afas\Core\Filter\FilterContainerInterface;
use Afas\Core\Filter\FilterGroupInterface;

/**
 * Class FilterGroup
 * @package Afas\Core\Filter
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
   * @var FilterFactoryInterface $factory
   */
  private $factory;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * FilterGroup object constructor.
   *
   * @param string $name
   *   The name of this filter group.
   * @param \Afas\Core\Filter\FilterFactoryInterface $factory
   *   The factory to use for generating filter objects.
   *
   * @return \Afas\Core\Filter\FilterGroup
   */
  public function __construct($name, FilterFactoryInterface $factory) {
    $this->name = $name;
    $this->factory = $factory;
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Implements FilterGroupInterface::filter().
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $filter = $this->factory->createFilter($field, $value, $operator);
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

  /**
   * Implements FilterGroupInterface::getName().
   */
  public function getName() {
    return $this->name;
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Implements FilterGroupInterface::compile().
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
   * Implements PHP magic __toString() method to convert the filter group to string.
   *
   * @return string
   *   A string version of the filter group.
   */
  public function __toString() {
    return $this->compile();
  }
}
