<?php

namespace Afas\Core\Filter;

use Afas\Component\ItemList\ItemList;

/**
 * Class containing filter groups.
 */
class FilterContainer extends ItemList implements FilterContainerInterface {

  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The filter factory.
   *
   * @var Afas\Core\Filter\FilterFactoryInterface
   */
  private $factory;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new FilterContainer object.
   *
   * @param \Afas\Core\Filter\FilterFactoryInterface $factory
   *   (optional) The filter factory to use.
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
   * {@inheritdoc}
   */
  public function filter($field, $value = NULL, $operator = NULL) {
    $this->currentGroup()->filter($field, $value, $operator);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeFilter($index) {
    $this->currentGroup()->removeFilter($index);
    return $this;
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
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
   * {@inheritdoc}
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
    $items = $this->getItems();
    return end($items);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function compile() {
    if ($this->count()) {
      $output = '<Filters>';
      foreach ($this->getItems() as $filter_group) {
        $output .= $filter_group->compile();
      }
      $output .= '</Filters>';
      return $output;
    }
  }

  /**
   * Implements PHP magic __toString().
   *
   * Converts the filter group to a string.
   *
   * @return string
   *   A string version of the filter container.
   */
  public function __toString() {
    $result = $this->compile();
    if (empty($result)) {
      return '';
    }
    return $result;
  }

}
