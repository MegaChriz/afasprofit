<?php

/**
 * @file
 * Contains \Afas\Core\DependencyInjection\ContainerNotInitializedException.
 */

namespace Afas\Core\DependencyInjection;

/**
 * Exception thrown when a method is called that requires a container, but the
 * container is not initialized yet.
 *
 * @see \Afas\Afas
 */
class ContainerNotInitializedException extends \RuntimeException {

}
