<?php

namespace Afas\Core\Filter;

use Afas\Core\CompilableInterface;

/**
 * Interface for a filter container.
 */
interface FilterContainerInterface extends CompilableInterface, FilterableInterface, GroupableInterface {

  /**
   * Sets the factory that generates the objects.
   *
   * @param \Afas\Core\Filter\FilterFactoryInterface $factory
   *   The factory that generates filter and filter group objects.
   */
  public function setFactory(FilterFactoryInterface $factory);

  /**
   * Sets the current active group on the container.
   *
   * @param \Afas\Core\Filter\FilterGroupInterface $group
   *   The group to set as the current active group.
   */
  public function setCurrentGroup(FilterGroupInterface $group);

}
