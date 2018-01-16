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
   * @param FilterFactoryInterface $factory
   *   The factory that generates filter and filter group objects.
   *
   * @return void
   */
  public function setFactory(FilterFactoryInterface $factory);

}
