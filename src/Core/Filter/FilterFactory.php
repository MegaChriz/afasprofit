<?php

namespace Afas\Core\Filter;

/**
 * Factory for generating filters and filter groups.
 */
class FilterFactory implements FilterFactoryInterface {

  /**
   * {@inheritdoc}
   */
  public function createFilter($field, $value = NULL, $operator = NULL) {
    return new Filter($field, $value, $operator);
  }

  /**
   * {@inheritdoc}
   */
  public function createFilterGroup($name) {
    return new FilterGroup($name, $this);
  }

}
