<?php

/**
 * @file
 * Contains Afas.
 */

namespace Afas;

use Afas\Core\DependencyInjection\ContainerNotInitializedException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Afas {
  /**
   * The currently active container object, or NULL if not initialized yet.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
   */
  protected static $container;

  /**
   * Sets a new global container.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   A new container instance to replace the current.
   */
  public static function setContainer(ContainerInterface $container) {
    static::$container = $container;
  }

  /**
   * Unsets the global container.
   */
  public static function unsetContainer() {
    static::$container = NULL;
  }

  /**
   * Returns the currently active global container.
   *
   * @throws \Drupal\Core\DependencyInjection\ContainerNotInitializedException
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface|null
   */
  public static function getContainer() {
    if (static::$container === NULL) {
      throw new ContainerNotInitializedException('\Afas::$container is not initialized yet. \Afas::setContainer() must be called with a real container.');
    }
    return static::$container;
  }

  /**
   * Returns TRUE if the container has been initialized, FALSE otherwise.
   *
   * @return bool
   */
  public static function hasContainer() {
    return static::$container !== NULL;
  }

  /**
   * Retrieves a service from the container.
   *
   * Use this method if the desired service is not one of those with a dedicated
   * accessor method below. If it is listed below, those methods are preferred
   * as they can return useful type hints.
   *
   * @param string $id
   *   The ID of the service to retrieve.
   * @return mixed
   *   The specified service.
   */
  public static function service($id) {
    return static::getContainer()->get($id);
  }
}
