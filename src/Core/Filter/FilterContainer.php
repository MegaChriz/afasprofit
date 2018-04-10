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
   * @var \Afas\Core\Filter\FilterFactoryInterface
   */
  private $factory;

  /**
   * The current group.
   *
   * @var \Afas\Core\Filter\FilterGroupInterface
   */
  private $currentGroup;

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
    $this->setCurrentGroup($group);
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
    if ($this->currentGroup === $group) {
      $this->currentGroup = NULL;
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setFactory(FilterFactoryInterface $factory) {
    $this->factory = $factory;
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrentGroup(FilterGroupInterface $group) {
    $this->currentGroup = $group;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns current group.
   */
  protected function currentGroup() {
    if (!$this->count()) {
      return $this->group();
    }
    elseif (isset($this->currentGroup)) {
      return $this->currentGroup;
    }

    // Return the last group.
    $items = $this->getItems();
    return end($items);
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return $this->currentGroup()->getFilters();
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
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
   * Converts the filter container to a string.
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
