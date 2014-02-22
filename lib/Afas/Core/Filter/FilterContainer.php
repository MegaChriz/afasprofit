<?php

/**
 * @file
 * Contains \Afas\Core\Filter\FilterContainer.
 */

namespace Afas\Core\Filter;

use Afas\Component\ItemList\ItemList;
use Afas\Core\Filter\FilterContainerInterface;
use Afas\Core\Filter\FilterGroupInterface;
use Afas\Core\Filter\FilterFactoryInterface;
use Afas\Core\Filter\FilterFactory;

/**
 * Class containing filter groups.
 */
class FilterContainer extends ItemList implements FilterContainerInterface {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * @var FilterFactoryInterface $factory
   */
  private $factory;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * @param FilterFactoryInterface $factory
   *   (optional) The factory to use.
   *   Defaults to \Afas\Core\Filter\FilterFactory.
   */
  public function __construct(FilterFactoryInterface $factory = NULL) {
    if (!isset($factory)) {
      $factory = new FilterFactory();
    }
    $this->setFactory($factory);
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
   * @return FilterContainer
   *   Returns current instance.
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $this->currentGroup()->filter($field, $value, $operator);
    return $this;
  }

  /**
   * Adds a filter group.
   *
   * @param string $name
   *   (optional) The name of the filter group.
   *   Defaults to 'Filter N' where N is the number of filter groups currently defined.
   *
   * @return FilterGroupInterface
   *   Returns an new instance of FilterGroupInterface.
   * @todo Avoid new keyword.
   */
  public function group($name = NULL) {
    if (is_null($name)) {
      $name = 'Filter ' . ($this->count() + 1);
    }
    $group = $this->factory->createFilterGroup($name);
    $this->addItem($group, $group->getName());
    return $group;
  }

  /**
   * Removes a filter.
   *
   * @param int $index
   *   The id of the filter to remove.
   *
   * @return FilterContainer
   *   Returns current instance.
   */
  public function removeFilter($index) {
    $this->currentGroup()->removeFilter($index);
    return $this;
  }

  /**
   * Removes a filter group.
   *
   * @param string | FilterGroupInterface $group
   *   Either the ID of the group to remove
   *   or the group itself.
   *
   * @return FilterContainer
   *   Returns current instance.
   */
  public function removeGroup($group) {
    $name = NULL;
    if ($group instanceof FilterGroupInterface) {
      $name = $group->getName();
    }
    elseif (is_scalar($group)) {
      $name = $group;
    }
    $this->removeItem($name);
    return $this;
  }

  /**
   * Sets the factory that generates the objects.
   *
   * @param FilterFactoryInterface $factory
   *   The factory that generates filter and filter group objects.
   *
   * @return void
   */
  public function setFactory(FilterFactoryInterface $factory) {
    $this->factory = $factory;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns current group.
   *
   * @todo Test if this actually works.
   * @todo This should be improved. The pointer can't be set at all.
   */
  protected function currentGroup() {
    if (!$this->count()) {
      return $this->group();
    }
    return end($this->getItems());
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Return XML string.
   *
   * @return string
   *   XML generated string.
   */
  public function compile() {
    $output = '<Filters>';
    foreach ($this->getItems() as $filter_group) {
      $output .= $filter_group->compile();
    }
    $output .= '</Filters>';
    return $output;
  }

  /**
   * Implements PHP magic __toString method to convert the filter group to string.
   *
   * @return string
   *   A string version of the filter container.
   */
  public function __toString() {
    return $this->compile();
  }
}
