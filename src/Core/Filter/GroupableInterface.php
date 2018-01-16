<?php

namespace Afas\Core\Filter;

/**
 * Interface for objects that can contain filter groups.
 */
interface GroupableInterface {

  /**
   * Adds a filter group.
   *
   * @param string $name
   *   (optional) The name of the filter group.
   *   Defaults to 'Filter N' where N is the number of filter groups currently
   *   defined.
   *
   * @return \Afas\Core\Filter\FilterGroupInterface
   *   Returns an new filter group.
   */
  public function group($name = NULL);

  /**
   * Removes a filter group.
   *
   * @param string|FilterGroupInterface $group
   *   Either the ID of the group to remove
   *   or the group itself.
   *
   * @return \Afas\Core\Filter\GroupableInterface
   *   Returns current instance.
   */
  public function removeGroup($group);

}
